(function (window, $) {
    'use strict';

    var filaConexao = [];
    var conectando = false;
    var pastaOciHandle = null;
    var diretoriosOci = {};

    function mostrarStatus(mensagem, tipo) {
        var $status = $('.serpro-status');

        if (!$status.length) {
            return;
        }

        $status.removeClass('alert-info alert-success alert-warning alert-danger')
            .addClass('alert-' + (tipo || 'info'))
            .text(mensagem)
            .show();
    }

    function mostrarLinkAutorizacao(exibir) {
        $('.serpro-autorizacao').toggle(!!exibir);
    }

    function concluirFila(erro) {
        var fila = filaConexao.splice(0, filaConexao.length);

        $.each(fila, function (_, item) {
            if (erro) {
                item.erro(erro);
            } else {
                item.sucesso();
            }
        });
    }

    function conectar(onSuccess, onError) {
        if (!window.SerproSignerClient) {
            onError({ mensagem: 'A biblioteca do Assinador SERPRO não foi carregada.' });
            return;
        }

        if (window.SerproSignerClient.isConnected()) {
            onSuccess();
            return;
        }

        filaConexao.push({ sucesso: onSuccess, erro: onError });

        if (conectando) {
            return;
        }

        conectando = true;
        mostrarStatus('Verificando o Assinador SERPRO...', 'info');

        window.SerproSignerClient.setDebug(false);
        window.SerproSignerClient.setUriServer('wss', '127.0.0.1', 65156, '/signer');

        window.SerproSignerClient.verifyIsInstalledAndRunning()
            .success(function () {
                mostrarStatus('Conectando ao Assinador SERPRO...', 'info');

                window.SerproSignerClient.connect(function (estado) {
                    conectando = false;

                    if (estado === 1) {
                        mostrarLinkAutorizacao(false);
                        mostrarStatus('Assinador SERPRO conectado. Confirme a assinatura no BirdID.', 'success');
                        concluirFila();
                    } else {
                        var erro = { mensagem: 'O Assinador SERPRO foi desconectado.' };
                        mostrarStatus(erro.mensagem, 'warning');
                        concluirFila(erro);
                    }
                }, function () {
                    conectando = false;
                    var erro = {
                        mensagem: 'A conexão com o Assinador SERPRO foi encerrada. Autorize o certificado local do SERPRO e tente novamente.'
                    };
                    mostrarLinkAutorizacao(true);
                    mostrarStatus(erro.mensagem, 'warning');
                    concluirFila(erro);
                }, function () {
                    conectando = false;
                    var erro = { mensagem: 'O navegador ainda não está autorizado a acessar o Assinador SERPRO.' };
                    mostrarLinkAutorizacao(true);
                    mostrarStatus(erro.mensagem + ' Autorize o acesso e tente novamente.', 'warning');
                    concluirFila(erro);
                });
            })
            .error(function () {
                conectando = false;
                var erro = {
                    mensagem: 'Não foi possível acessar o Assinador SERPRO. Abra o aplicativo e autorize o certificado local.'
                };
                mostrarLinkAutorizacao(true);
                mostrarStatus(erro.mensagem, 'warning');
                concluirFila(erro);
            });
    }

    function arrayBufferParaBase64(buffer) {
        var bytes = new Uint8Array(buffer);
        var tamanho = 0x8000;
        var partes = [];

        for (var i = 0; i < bytes.length; i += tamanho) {
            partes.push(String.fromCharCode.apply(null, bytes.subarray(i, i + tamanho)));
        }

        return window.btoa(partes.join(''));
    }

    function baixarPdf(base64, nomeArquivo) {
        var link = document.createElement('a');
        link.href = 'data:application/pdf;base64,' + base64;
        link.download = nomeArquivo;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function base64ParaBlob(base64) {
        var binario = window.atob(base64);
        var tamanho = 1024 * 1024;
        var partes = [];

        for (var inicio = 0; inicio < binario.length; inicio += tamanho) {
            var fim = Math.min(inicio + tamanho, binario.length);
            var bytes = new Uint8Array(fim - inicio);

            for (var indice = inicio; indice < fim; indice++) {
                bytes[indice - inicio] = binario.charCodeAt(indice);
            }

            partes.push(bytes);
        }

        return new Blob(partes, { type: 'application/pdf' });
    }

    async function obterDiretorioOci(diretorioBase, pasta) {
        var chavePasta = String(pasta || '');
        if (diretoriosOci[chavePasta]) {
            return diretoriosOci[chavePasta];
        }

        var diretorio = diretorioBase;
        var partes = chavePasta
            .replace(/\\/g, '/')
            .split('/')
            .filter(function (parte) {
                return parte && parte !== '.' && parte !== '..';
            });

        for (var indice = 0; indice < partes.length; indice++) {
            diretorio = await diretorio.getDirectoryHandle(partes[indice], { create: true });
        }

        diretoriosOci[chavePasta] = diretorio;
        return diretorio;
    }

    async function salvarPdfNaPasta(diretorioBase, base64, nomeArquivo, pasta) {
        var diretorio = await obterDiretorioOci(diretorioBase, pasta);
        var arquivo = await diretorio.getFileHandle(nomeArquivo, { create: true });
        var gravador = await arquivo.createWritable();

        await gravador.write(base64ParaBlob(base64));
        await gravador.close();
    }

    async function removerPdfDaPasta(pasta, nomeArquivo) {
        if (!pastaOciHandle || !nomeArquivo) {
            return;
        }

        try {
            var diretorio = await obterDiretorioOci(pastaOciHandle, pasta);
            await diretorio.removeEntry(nomeArquivo);
        } catch (ex) {
            // O arquivo pode ter sido substituído ou removido pelo SERPRO.
        }
    }

    function carregarPdf(url, sucesso, erro) {
        var request = new XMLHttpRequest();

        request.open('GET', url, true);
        request.responseType = 'arraybuffer';

        request.onload = function () {
            if (request.status >= 200 && request.status < 300) {
                sucesso(arrayBufferParaBase64(request.response));
            } else {
                erro({ mensagem: 'Não foi possível carregar o PDF para assinatura.' });
            }
        };

        request.onerror = function () {
            erro({ mensagem: 'Falha de comunicação ao carregar o PDF.' });
        };

        request.send();
    }

    function assinarPdf(base64, sucesso, erro) {
        conectar(function () {
            window.SerproSignerClient.sign('pdf', base64, null, 'base64', false)
                .success(function (resposta) {
                    if (resposta.actionCanceled) {
                        erro({ mensagem: 'A assinatura foi cancelada.' });
                        return;
                    }

                    sucesso(resposta.signature);
                })
                .error(erro);
        }, erro);
    }

    function salvarNoServidor(url, base64, nomeArquivo, pasta, nomeOriginal, etapa, sucesso, erro) {
        var urlBase = window.GLOBAL_URL || $('#URL').val() || '';

        $.ajax({
            url: urlBase + url,
            type: 'POST',
            dataType: 'json',
            data: {
                pdf: base64,
                nome: nomeArquivo,
                pasta: pasta || '',
                original: nomeOriginal || '',
                etapa: etapa || ''
            }
        }).done(sucesso).fail(erro);
    }

    function consultarPdfOci(jobId, tentativa, sucesso, erro) {
        var urlBase = window.GLOBAL_URL || $('#URL').val() || '';

        $.ajax({
            url: urlBase + 'OCI/consultarPdf/' + encodeURIComponent(jobId),
            type: 'GET',
            dataType: 'json'
        }).done(function (resposta) {
            if (resposta && resposta.status === 'concluido' && resposta.urlPdf) {
                sucesso(resposta);
                return;
            }

            if (resposta && resposta.status === 'erro') {
                erro({ mensagem: resposta.mensagem || 'O serviço não conseguiu gerar o PDF.' });
                return;
            }

            if (tentativa >= 100) {
                erro({ mensagem: 'O serviço demorou mais que o esperado para gerar o PDF.' });
                return;
            }

            window.setTimeout(function () {
                consultarPdfOci(jobId, tentativa + 1, sucesso, erro);
            }, 3000);
        }).fail(function () {
            erro({ mensagem: 'Não foi possível consultar o resultado da geração do PDF.' });
        });
    }

    window.solicitarPdfOci = function (ids, sucesso, erro) {
        var urlBase = window.GLOBAL_URL || $('#URL').val() || '';

        $.ajax({
            url: urlBase + 'OCI/solicitarPdf',
            type: 'POST',
            dataType: 'json',
            data: {
                idsOCI: JSON.stringify(ids || [])
            }
        }).done(function (resposta) {
            if (!resposta || resposta.erro || !resposta.jobId) {
                (erro || function () {})(resposta || { mensagem: 'Não foi possível solicitar o PDF.' });
                return;
            }

            consultarPdfOci(resposta.jobId, 0, sucesso || function () {}, erro || function () {});
        }).fail(function () {
            (erro || function () {})({ mensagem: 'Falha de comunicação com o sistema.' });
        });
    };

    $(document).on('click', '.btn-finalizar-gerar-pdf-oci', function (evento) {
        evento.preventDefault();

        var $botao = $(this);
        var ids = $botao.attr('data-ids') || '[]';

        try {
            ids = JSON.parse(ids);
        } catch (ex) {
            mostrarStatus('Os IDs das OCIs são inválidos.', 'danger');
            return;
        }

        if (!Array.isArray(ids) || !ids.length) {
            mostrarStatus('Selecione ao menos uma OCI para gerar o PDF.', 'warning');
            return;
        }

        $botao.prop('disabled', true);
        mostrarStatus('Solicitando a geração do PDF...', 'info');

        window.solicitarPdfOci(ids, function (resposta) {
            habilitarAssinaturaOci(resposta.urlPdf, resposta.pdfNome);
            mostrarStatus('PDF completo gerado. Agora ele pode ser assinado.', 'success');
            $botao.prop('disabled', false);
        }, function (resposta) {
            mostrarStatus((resposta && resposta.mensagem) || 'Não foi possível gerar o PDF.', 'danger');
            $botao.prop('disabled', false);
        });
    });

    function nomeAssinado(nome) {
        return nome.replace(/\.pdf$/i, '') + '_assinado.pdf';
    }

    // Será chamado pelo retorno da integração PHP ↔ VB quando o PDF completo estiver pronto.
    window.habilitarAssinaturaOci = function (urlPdf, nomeArquivo) {
        var $botao = $('#btn-assinar-pdf-oci');

        if (!$botao.length || !urlPdf) {
            return;
        }

        $botao.attr('data-pdf-url', urlPdf)
            .attr('data-pdf-name', nomeArquivo || 'OCI_assinado.pdf')
            .prop('disabled', false)
            .attr('title', 'Assinar o PDF completo com o certificado do médico');
        $('#acoes-fila-oci .text-muted').text('PDF completo recebido. Confirme a assinatura pelo BirdID.');
    };

    function restaurarBotaoAssinatura($botao) {
        $botao.prop('disabled', false).text($botao.data('texto-original') || 'Assinar agora');
    }

    function assinarBotao($botao, baixarResultado, sucesso, erro) {
        var urlPdf = $botao.attr('data-pdf-url');
        var nomeArquivo = $botao.attr('data-pdf-name') || 'documento.pdf';

        if (!urlPdf) {
            erro({ mensagem: 'O endereço do PDF não foi informado.' });
            return;
        }

        $botao.prop('disabled', true)
            .data('texto-original', $botao.text())
            .text('Assinando...');

        carregarPdf(urlPdf, function (base64) {
            assinarPdf(base64, function (pdfAssinado) {
                var nomeAssinadoPdf = nomeAssinado(nomeArquivo);
                var urlSalvar = $botao.attr('data-salvar-url');
                var pastaPdf = $botao.attr('data-pdf-pasta') || '';
                var nomeOriginalPdf = $botao.attr('data-pdf-original') || nomeArquivo;
                var etapaPdf = $botao.attr('data-pdf-etapa') || '';

                if (!urlSalvar) {
                    if (baixarResultado) {
                        baixarPdf(pdfAssinado, nomeAssinadoPdf);
                    }
                    restaurarBotaoAssinatura($botao);
                    sucesso({ mensagem: 'PDF assinado com sucesso.' });
                    return;
                }

                salvarNoServidor(urlSalvar, pdfAssinado, nomeAssinadoPdf, pastaPdf, nomeOriginalPdf, etapaPdf, function (resposta) {
                    if (resposta && resposta.erro) {
                        restaurarBotaoAssinatura($botao);
                        erro({ mensagem: resposta.mensagem || 'Não foi possível salvar o PDF assinado.' });
                        return;
                    }

                    if (baixarResultado) {
                        baixarPdf(pdfAssinado, nomeAssinadoPdf);
                    }
                    removerPdfDaLista($botao);
                    if (typeof window.carregarPdfsOci === 'function') {
                        window.carregarPdfsOci();
                    }
                    restaurarBotaoAssinatura($botao);
                    sucesso(resposta || { mensagem: 'PDF assinado e salvo com sucesso.' });
                }, function () {
                    restaurarBotaoAssinatura($botao);
                    erro({ mensagem: 'PDF assinado, mas não foi possível salvá-lo no servidor.' });
                });
            }, function (resposta) {
                restaurarBotaoAssinatura($botao);
                erro(resposta || { mensagem: 'Não foi possível assinar o PDF.' });
            });
        }, function (resposta) {
            restaurarBotaoAssinatura($botao);
            erro(resposta || { mensagem: 'Não foi possível carregar o PDF.' });
        });
    }

    function removerPdfDaLista($botao) {
        var $item = $botao.closest('.pdf-oci-item');
        var $grupo = $item.closest('.pdfs-oci-grupo');
        var $container = $item.closest('.dashboard-pdf-alert');

        $item.remove();

        var quantidadeRestante = $grupo.find('.pdf-oci-item').length;
        if (quantidadeRestante === 0) {
            $grupo.remove();
        } else {
            $grupo.find('.badge').text(quantidadeRestante);
        }

        atualizarSelecaoPdfs($container);

        if (!$container.find('.pdf-oci-item').length) {
            $container.fadeOut(150, function () {
                $(this).remove();
            });
        }
    }

    function atualizarSelecaoPdfs($container) {
        var $checks = $container.find('.check-pdf-oci');
        var $selecionados = $checks.filter(':checked');
        var desabilitarAcoes = $selecionados.length === 0;
        $container.find('.btn-baixar-lote-serpro, .btn-enviar-pdfs-assinados').prop('disabled', desabilitarAcoes);

        var todosMarcados = $checks.length > 0 && $selecionados.length === $checks.length;
        $container.find('.check-selecionar-todos-pdfs').prop('checked', todosMarcados);
    }

    function obterSelecaoPdfs($container) {
        var botoes = [];
        var nomes = [];
        var pastas = [];
        var etapas = [];

        $container.find('.check-pdf-oci:checked').each(function () {
            var $item = $(this).closest('.pdf-oci-item');
            var $botao = $item.find('.btn-assinar-serpro');
            var pasta = $botao.attr('data-pdf-pasta') || '';
            var nome = $botao.attr('data-pdf-name') || '';
            var etapa = $botao.attr('data-pdf-etapa') || '';

            if ($botao.length) {
                botoes.push($botao[0]);
                nomes.push(nome);
                if ($.inArray(pasta, pastas) === -1) {
                    pastas.push(pasta);
                }
                if ($.inArray(etapa, etapas) === -1) {
                    etapas.push(etapa);
                }
            }
        });

        return { botoes: botoes, nomes: nomes, pastas: pastas, etapas: etapas };
    }

    function baixarLotePorDownload($botoes, indice, $botaoLote, $container) {
        if (indice >= $botoes.length) {
            $botaoLote.prop('disabled', false).text('Baixar selecionados');
            atualizarSelecaoPdfs($container);
            mostrarStatus('PDFs baixados. Abra o SERPRO, escolha Assinar em lote e selecione todos os arquivos baixados.', 'success');
            return;
        }

        var $botaoPdf = $($botoes[indice]);
        var urlPdf = $botaoPdf.attr('data-pdf-url');
        var nomeArquivo = $botaoPdf.attr('data-pdf-name') || ('documento_' + (indice + 1) + '.pdf');
        mostrarStatus('Baixando PDF ' + (indice + 1) + ' de ' + $botoes.length + '...', 'info');

        carregarPdf(urlPdf, function (base64) {
            baixarPdf(base64, nomeArquivo);
            window.setTimeout(function () {
                baixarLotePorDownload($botoes, indice + 1, $botaoLote, $container);
            }, 350);
        }, function (resposta) {
            $botaoLote.prop('disabled', false).text('Baixar selecionados');
            atualizarSelecaoPdfs($container);
            mostrarStatus((resposta && resposta.mensagem) || 'Não foi possível baixar um dos PDFs.', 'danger');
        });
    }

    async function baixarLoteParaSerpro($botoes, $botaoLote, $container) {
        if (!window.showDirectoryPicker) {
            baixarLotePorDownload($botoes, 0, $botaoLote, $container);
            return;
        }

        try {
            if (!pastaOciHandle) {
                var pastaBaseEscolhida = await window.showDirectoryPicker({ mode: 'readwrite' });
                pastaOciHandle = pastaBaseEscolhida;
            }

            for (var indice = 0; indice < $botoes.length; indice++) {
                var $botaoPdf = $($botoes[indice]);
                var urlPdf = $botaoPdf.attr('data-pdf-url');
                var nomeArquivo = $botaoPdf.attr('data-pdf-name') || ('documento_' + (indice + 1) + '.pdf');
                var pasta = $botaoPdf.attr('data-pdf-pasta') || '';

                mostrarStatus('Salvando PDF ' + (indice + 1) + ' de ' + $botoes.length + '...', 'info');

                var base64 = await new Promise(function (resolve, reject) {
                    carregarPdf(urlPdf, resolve, reject);
                });

                await salvarPdfNaPasta(pastaOciHandle, base64, nomeArquivo, pasta);
            }

            $botaoLote.prop('disabled', false).text('Baixar selecionados');
            atualizarSelecaoPdfs($container);
            mostrarStatus('PDFs organizados na pasta escolhida. Abra o SERPRO e use "Assinar em lote".', 'success');
        } catch (ex) {
            $botaoLote.prop('disabled', false).text('Baixar selecionados');
            atualizarSelecaoPdfs($container);

            if (ex && ex.name === 'AbortError') {
                mostrarStatus('A escolha da pasta foi cancelada.', 'warning');
            } else if (ex && (ex.name === 'SecurityError' || ex.name === 'NotAllowedError')) {
                mostrarStatus('O Chrome bloqueou essa pasta. Escolha uma pasta própria, como C:\\OCI.', 'warning');
            } else {
                mostrarStatus((ex && ex.message) || 'Não foi possível salvar os PDFs na pasta escolhida.', 'danger');
            }
        }
    }

    async function selecionarPdfsAssinados($input, pasta, etapa, $container) {
        if (!window.showOpenFilePicker || !pastaOciHandle) {
            $input.data('etapa-lote', etapa);
            $input.trigger('click');
            return;
        }

        try {
            var diretorioInicial = diretoriosOci[String(pasta || '')] || pastaOciHandle;
            var handles = await window.showOpenFilePicker({
                multiple: true,
                startIn: diretorioInicial,
                excludeAcceptAllOption: true,
                types: [{
                    description: 'Arquivos PDF',
                    accept: { 'application/pdf': ['.pdf'] }
                }]
            });
            var arquivos = [];

            for (var indice = 0; indice < handles.length; indice++) {
                arquivos.push(await handles[indice].getFile());
            }

            if (arquivos.length) {
                enviarPdfsAssinados($input, arquivos, pasta, etapa, $container);
            }
        } catch (ex) {
            if (!ex || ex.name !== 'AbortError') {
                mostrarStatus('Não foi possível abrir a pasta dos PDFs assinados.', 'danger');
            }
        }
    }

    function enviarPdfsAssinados($input, arquivos, pasta, etapa, $container, indice, enviados) {
        indice = indice || 0;
        enviados = enviados || 0;

        if (indice >= arquivos.length) {
            $input.val('');
            atualizarSelecaoPdfs($container);
            if (typeof window.carregarPdfsOci === 'function') {
                window.carregarPdfsOci();
            }
            var mensagem = etapa === 'autorizador'
                ? enviados + ' PDF(s) concluído(s) pelo médico autorizador e enviado(s) ao histórico.'
                : enviados + ' PDF(s) assinados pelo executante e encaminhados aos médicos autorizadores.';
            mostrarStatus(mensagem, 'success');
            return;
        }

        var urlBase = window.GLOBAL_URL || $('#URL').val() || '';
        var dados = new FormData();
        dados.append('pasta', pasta || '');
        dados.append('etapa', etapa || '');
        dados.append('arquivo', arquivos[indice], arquivos[indice].name);

        mostrarStatus('Enviando PDF assinado ' + (indice + 1) + ' de ' + arquivos.length + '...', 'info');
        $.ajax({
            url: urlBase + 'OCI/salvarPdfAssinadoLote',
            type: 'POST',
            data: dados,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(function (resposta) {
            if (!resposta || resposta.erro) {
                mostrarStatus((resposta && resposta.mensagem) || 'Não foi possível importar os PDFs assinados.', 'danger');
                return;
            }

            $.each(resposta.arquivos || [], function (_, nomeArquivo) {
                $container.find('.btn-assinar-serpro').each(function () {
                    if ($(this).attr('data-pdf-name') === nomeArquivo) {
                        removerPdfDaLista($(this));
                    }
                });

                removerPdfDaPasta(pasta, nomeArquivo);
            });

            removerPdfDaPasta(pasta, arquivos[indice].name);

            enviarPdfsAssinados($input, arquivos, pasta, etapa, $container, indice + 1, enviados + 1);
        }).fail(function (xhr) {
            var resposta = {};
            try {
                resposta = JSON.parse(xhr.responseText || '{}');
            } catch (ex) {
                resposta = {};
            }
            mostrarStatus(resposta.mensagem || 'Falha de comunicação ao enviar os PDFs assinados.', 'danger');
        }).always(function () {
            atualizarSelecaoPdfs($container);
        });
    }

    $(document).on('change', '.check-selecionar-todos-pdfs', function () {
        var $container = $(this).closest('.dashboard-pdf-alert');
        $container.find('.check-pdf-oci:not(:disabled)').prop('checked', $(this).prop('checked'));
        atualizarSelecaoPdfs($container);
    });

    $(document).on('change', '.check-pdf-oci', function () {
        atualizarSelecaoPdfs($(this).closest('.dashboard-pdf-alert'));
    });

    $(document).on('click', '.btn-baixar-lote-serpro', function (evento) {
        evento.preventDefault();

        var $botaoLote = $(this);
        var $container = $botaoLote.closest('.dashboard-pdf-alert');
        var selecao = obterSelecaoPdfs($container);

        if (!selecao.botoes.length) {
            mostrarStatus('Selecione ao menos um PDF para assinar.', 'warning');
            return;
        }

        $botaoLote.prop('disabled', true).text('Baixando...');
        baixarLoteParaSerpro(selecao.botoes, $botaoLote, $container);
    });

    $(document).on('click', '.btn-enviar-pdfs-assinados', function (evento) {
        evento.preventDefault();

        var $botao = $(this);
        var $container = $botao.closest('.dashboard-pdf-alert');
        var selecao = obterSelecaoPdfs($container);

        if (!selecao.botoes.length) {
            mostrarStatus('Selecione os PDFs correspondentes ao lote assinado.', 'warning');
            return;
        }

        if (selecao.pastas.length !== 1) {
            mostrarStatus('Para enviar os assinados, selecione PDFs de uma única data por vez.', 'warning');
            return;
        }

        if (selecao.etapas.length !== 1) {
            mostrarStatus('Separe os PDFs do executante e do autorizador em envios diferentes.', 'warning');
            return;
        }

        var $input = $container.find('.input-pdfs-assinados');
        $input
            .data('pasta-lote', selecao.pastas[0])
            .data('etapa-lote', selecao.etapas[0])
            .data('container-pdfs', $container);

        selecionarPdfsAssinados($input, selecao.pastas[0], selecao.etapas[0], $container);
    });

    $(document).on('change', '.input-pdfs-assinados', function () {
        var $input = $(this);
        var arquivos = this.files || [];
        var pasta = $input.data('pasta-lote') || '';
        var etapa = $input.data('etapa-lote') || '';
        var $container = $input.data('container-pdfs');

        if (!arquivos.length || !$container || !etapa) {
            return;
        }

        enviarPdfsAssinados($input, arquivos, pasta, etapa, $container);
    });

    $(document).on('click', '.btn-assinar-serpro', function (evento) {
        evento.preventDefault();

        var $botao = $(this);
        mostrarStatus('Carregando PDF...', 'info');
        assinarBotao($botao, true, function (resposta) {
            mostrarStatus((resposta && resposta.mensagem) || 'PDF assinado e salvo com sucesso. O download foi iniciado.', 'success');
        }, function (resposta) {
            mostrarStatus((resposta && resposta.mensagem) || 'Não foi possível assinar o PDF.', 'danger');
        });
    });
})(window, window.jQuery);
