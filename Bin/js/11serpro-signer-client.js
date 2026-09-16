// http://usejsdoc.org/

/**
 * @classdesc Object used to comunicate with local WebSocket.
 * @class
 */
var SerproSignerClient = (function (SerproSignerPromise) {

	var ws;
	var defer = [];
	var uriServer = "wss://127.0.0.1:65156/signer/";
	var uriServerVerify = "http://127.0.0.1:65056/";
	var isDebug = false;

    /**
     * Log messages in console if in debug mode.
     *
     * @private
     * @param {string} message - Message to log.
     * @memberof SerproSignerClient
     */
	var l = function (message) {
		if (isDebug) {
			console.log(message); // eslint-disable-line no-console
		}
	};

	var services = {
		/**
         * Set URI to use in communication.
		 *
         * @instance
		 * @default wss://127.0.0.1:65156/signer/
         * @param {string} uriProtocol - Protocol to use.
         * @param {string} uriDns - DNS to use.
         * @param {string} uriPort - Post to use.
         * @param {string} uriPort - Path to use.
         * @memberof SerproSignerClient
         */
		setUriServer: function (uriProtocol, uriDns, uriPort, uriPath) {
			var uri = uriProtocol + "://" + uriDns + ":" + uriPort + uriPath;
			l("Setting URI to " + uri);
			uriServer = uri;
		},

        /**
         * Set URI to use to verify if Desktop Client is running.
		 *
         * @instance
		 * @default http://127.0.0.1:65056/
         * @param {string} uriProtocol - Protocol to use in verifycation.
         * @param {string} uriDns - DNS to use in verifycation.
         * @param {string} uriPort - Post to use in verifycation.
         * @param {string} uriPort - Path to use in verifycation.
         * @memberof SerproSignerClient
         */
		uriServerVerify: function (uriProtocol, uriDns, uriPort, uriPath) {
			var uri = uriProtocol + "://" + uriDns + ":" + uriPort + uriPath;
			l("Setting URI Verifycation to " + uri);
			uriServerVerify = uri;
		},

		/**
         * Set debug true or false.
         *
         * @instance
         * @param {boolean} isToDebug - Is to debug
         * @memberof SerproSignerClient
         */
		setDebug: function (isToDebug) {
			l("Setting debug on to " + (isToDebug ? "ON" : "OFF"));
			isDebug = isToDebug;
		},

        /**
         * Verify if Desktop Client is running using a image request
         * to http server. This technique is used because the HTTPS (http + ssl) may not be enabled.
         *
         * @instance
         * @memberof SerproSignerClient
         */
		verifyIsInstalledAndRunning: function () {

			var requestIdVerify = "error";

			if (defer[requestIdVerify] != undefined) {
				defer[requestIdVerify].reject("Other request is running");
			}

			defer[requestIdVerify] = new SerproSignerPromise();

			var imageVerify = new Image();

			imageVerify.onload = function () {
				l("App installed and running");
				defer[requestIdVerify].resolve(true);
				delete defer[requestIdVerify];
			}

			imageVerify.onerror = function () {
				l("App not installed and not running");
				defer[requestIdVerify].reject(false);
				delete defer[requestIdVerify];
			}

			imageVerify.src = uriServerVerify + 'verify.gif?t=' + new Date().getTime();

			return defer[requestIdVerify];
		},

		/**
         * Method used to start connection with local WebSocket server.
         *
         * @instance
         * @param {function} callbackOpen - Callback  invoked on OPEN connection.
         * @param {function} callbackClose - Callback invoked on CLOSE connection.
         * @param {function} callbackError - Callback invoked on ERROR connection.
         * @memberof SerproSignerClient
         */
		connect: function (callbackOpen, callbackClose, callbackError) {
			if (ws == null || ws.readyState != 1) {
				l("Connecting on " + uriServer);
				ws = new WebSocket(uriServer);

				ws.onopen = function (msg) {
					if (callbackOpen)
						callbackOpen(msg.target.readyState);
				};

				ws.onclose = function (msg) {
					if (callbackClose)
						callbackClose(msg.target.readyState);
				};

				ws.onmessage = function (response) {
					var objResponse = JSON.parse(response.data);

					// If has data and data.error is a business error
					if (objResponse !== undefined && objResponse.error !== undefined) {
						if (objResponse.requestId !== undefined && defer[objResponse.requestId].hasCallbackError()) {
							defer[objResponse.requestId].reject(objResponse);
						} else if (callbackError) {
							callbackError(objResponse);
						}
					} else {

						l("Receiving command with ID [" + objResponse.requestId + "]");

						if (objResponse.requestId != undefined && defer[objResponse.requestId] !== undefined) {
							defer[objResponse.requestId].resolve(objResponse);
						} else {
							l("No callback to success was defined.");
							l(objResponse)
						}
					}

					// Delete promisse of list
					if (objResponse.requestId !== undefined) {
						delete defer[objResponse.requestId];
					}
				};

				ws.onerror = function (response) {
					if (defer[response.requestId] !== undefined) {
						defer[response.requestId].reject(response);
					} else {
						l("No callback to success was defined. Generic error.");
						callbackError(response);
					}

					// Delete promisse of list
					if (response.requestId !== undefined) {
						delete defer[response.requestId];
					}
				};
			}
		},

		/**
         * Verify status of connection with WebSocket server.
         *
         * @instance
		 * @return {boolean} - True for connection is up, false if is down.
         * @memberof SerproSignerClient
         */
		isConnected: function () {
			if (ws != null)
				return ws.readyState == 1 ? true : false;
			return false;
		},

        /**
		 * Signer content using some parameters.
         *
         * @instance
		 * @param {string} type - Type of inputData (text, file, hash, base64)
		 * @param {string} inputData - The data to sign, user in hash, text or base64. According to setted type
		 * @param {string} textEncoding - Encondig for type Text (ex:UTF-8, ISO-8859-1,...)
		 * @param {string} outputDataType - Type returned (base64 or file). Default base64
		 * @param {string} attached - if set to TRUE, define if the inputData will be attached into signature
		 * @return Promisse - The promisse when is finished.
		 * @memberof SerproSignerClient
		 */
		sign: function (type, inputData, textEncoding, outputDataType, attached) {
			var signerCommand = {
				command: 'sign',
				type: type,
				inputData: inputData,
				textEncoding: textEncoding,
				outputDataType: outputDataType,
				attached : attached
			}
			$('sign_command').val(JSON.stringify(signerCommand))
			var promise = services.execute(signerCommand);
			return promise;
		},

        /**
		 * Verify signature, returns user and certificate info.
         *
         * @instance
		 * @param {string} type - Type of inputData (text, hash, base64)
		 * @param {string} inputData - The data to verify, into a defined type, if validating a attached signature, leave blank
		 * @param {string} inputSignature - A signature to be verify encoded into base64 
		 * @param {string} textEncoding - Encondig for type Text (ex:UTF-8, ISO-8859-1,...)
		 * @param {string} algorithmOIDHash - The OID of algorithm for hash type and inputData
		 * @return Promisse - The promisse when is finished.
		 * @memberof SerproSignerClient
		 */
		verify: function (type, inputData, inputSignature, textEncoding, algorithmOIDHash) {
			console.log('###', arguments)
			var signerCommand = {
				command: 'verify',
				type: type,
				inputData: inputData,
				inputSignature : inputSignature,
				textEncoding: textEncoding,
				algorithmOIDHash: algorithmOIDHash
			}
			var promise = services.execute(signerCommand);
			return promise;
		},

        /**
	 * Get the content attached into a signature
         *
         * @instance
		 * @param {string} inputSignature - An attached signature (encoded into base64)
		 * @return Promisse - The promisse when is finished.
		 * @memberof SerproSignerClient
		 */
		attached: function (inputSignature) {
			var signerCommand = {
				command: 'attached',
				inputSignature : inputSignature
			}
			var promise = services.execute(signerCommand);
			return promise;
		},



        /**
		 * List all available commands.
         *
         * @instance
		 * @return Promisse - The promisse when is finished.
		 * @memberof SerproSignerClient
		 */
		list: function () {
			var listCommand = {
				command: 'list'
			}
			var promise = services.execute(listCommand);
			return promise;
		},

        /**
		 * Generic method to sendo commands to Desktop Server.
         *
         * @instance
		 * @param {json} request - Request JSON content all attributes to run.
	         * @return Promisse - The promisse when it is finished.
		 * @memberof SerproSignerClient
		 */
		execute: function (request) {
			if (!services.isConnected()) {
				var errorId = new Date().getTime();
				defer[errorId] = new SerproSignerPromise();

				// Return Defer and 500ms after send REJECT
				var intervalError = setInterval(function () {
					defer[errorId].reject({ error: "A connection to the sign server has not been started" });

					// Delete promisse of list
					delete defer[errorId];

					// Limpa o Interval
					clearInterval(intervalError);
				}, 500);

				return defer[errorId];
			} else {
				// If id doesnt exists, create
				if (request.requestId === undefined || request.requestId === "") {
					request.requestId = new Date().getTime();
				}

				l("Sending command [" + request.command + "] with ID [" + request.requestId + "] to URI [" + uriServer + "]");
				l(request);

				defer[request.requestId] = new SerproSignerPromise();

				ws.send(JSON.stringify(request));

				return defer[request.requestId];
			}
		}

	};

	return services;
})(window.SerproSignerPromise);
window.SerproSignerClient = SerproSignerClient;
