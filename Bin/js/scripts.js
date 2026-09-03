var GLOBAL_URL;
var dialogLoad;
var $body = $('body');
var isConfirmed = null;
var activeInput;
var eacDataHolder;
var urlChart;
var time;

$(document).ready(function () {
    $.ajaxSetup({
        cache: false
    });
    setup();
    loadAutoComplete();  
    
});

$(document).on({
  ajaxStart: function () {
    time = setTimeout(function () {
      $body.addClass('loading');
    }, 3000);
  },
   ajaxStop: function () {
    clearTimeout(time);
    $body.removeClass('loading');   
  }
});

/* Functions */

//    
//function notify(){
//    const notify = new Notification("nova mensagem", {
//        body: "testandoo"
//    });
//}
//
//if (Notification.permission === 'granted'){
//    notify();
//} else if(Notification.permission !== 'denied') {
//    Notification.requestPermission().then(permission => {
//       notify();
//    });
//}

function setup() {
  GLOBAL_URL = $('#URL').val();

  $('.calendar').each(function () {
    $(this).datepicker({
      format: 'dd/mm/yyyy',
      todayHighlight: true,
      autoclose: true,
      showOnFocus: true,
      language: 'pt-BR',
      orientation: 'auto right',
      clearBtn: false,
      todayBtn: 'linked',
      // daysOfWeekDisabled: "0,6",
      daysOfWeekHighlighted: '0,6',
    });
  });

  $("[data-toggle='tooltip']").each(function () {
    $(this).tooltip({});
  });

  $("[data-toggle='popover']").each(function () {
    $(this).popover({
      content: $(this).data('content'),
      title: $(this).data('title'),
      trigger: 'manual',
      delay: { show: 1, hide: 10000 },
      animation: false,
    });
  });

  //$('.calendar').mask('99/99/9999',{placeholder:""});
  //$('.date').mask('99/99/9999', { placeholder: '' });
 // $('.tel').mask('(99)9999-9999?9', { placeholder: '' });
  $('.sus').mask('999 9999 9999 9999', { placeholder: '' });
  $('.hora').mask('99:99', { placeholder: '' });
  //$('.cpf').mask('999.999.999-99', { placeholder: '' });
 // $('.cep').mask('99999-999', { placeholder: '' });
}
function formatarCPF(cpf) {
  if (!cpf) return '';

  // remove tudo que não for número
  cpf = cpf.toString().replace(/\D/g, '');

  // se não tiver 11 dígitos, retorna original
  if (cpf.length !== 11) return cpf;

  // aplica a máscara 000.000.000-00
  return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

function formatCEP (cep){
    cep = cep.toString().replace(/\D/g, '');
    if (cep.length === 8) {
      cep = cep.replace(/^(\d{5})(\d{3})$/, "$1-$2");
      return cep;
    } 
}

function messagesHandler(data) {
  try {
    var jsonResult = $.parseJSON(data);
    var action = jsonResult.action;
    var content = jsonResult.content;
    var typemsg = jsonResult.typemsg;

    if (action == 'header') {
      window.location.replace(content);
    } else if (action == 'msg') {
      handleMSG(content, typemsg);
    } else if (action == 'modal') {
      var t;
      var types = [
        BootstrapDialog.TYPE_DEFAULT,
        BootstrapDialog.TYPE_INFO,
        BootstrapDialog.TYPE_PRIMARY,
        BootstrapDialog.TYPE_SUCCESS,
        BootstrapDialog.TYPE_WARNING,
        BootstrapDialog.TYPE_DANGER,
      ];

      switch (typemsg) {
        case 'default':
          t = 0;
          break;
        case 'info':
          t = 1;
          break;
        case 'primary':
          t = 2;
          break;
        case 'success':
          t = 3;
          break;
        case 'warning':
          t = 4;
          break;
        case 'danger':
          t = 5;
          break;
      }

      BootstrapDialog.alert({
        cssClass: 'sm-dialog',
        title: 'Mensagem',
        message:
          '<p class="" style="font-size:16px;">' + content + '</p>',
        type: types[t],
        closable: true,
        draggable: true,
      });
    }
  } catch (e) {}
}

function handleMSG(msg, type = 'info') {
  var container = $(
    "<div id='notification-bar' name='notification-bar' class='loading-msg alert alert-" +
      type +
      " alert-dismissible' role='alert' style=''><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" +
      msg +
      '</div>'
  );

  container.hide().appendTo($body).fadeIn('slow');
  setTimeout(function () {
    container.fadeOut('slow');
  }, 20000);
}

function sleep(miliseconds) {
  $body.addClass('loading');
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if (new Date().getTime() - start > miliseconds) {
      $body.removeClass('loading');
      break;
    }
  }
}

function uniKeyCode(event) {
  var key = event.which || event.keyCode; // event.keyCode is used for IE8 and earlier
  return key;
}

function addDinamicRow(container, tdContent) {
  var row = $('<tr></tr>').addClass('row');
  var td = $(tdContent);

  row.append(td);
  $('[name=' + container + ']').append(row);
}

function getCellValuefromTableRow(IdTable, find) {
  var table = $(document.getElementById(IdTable));
  var arrDataCell = [];

  if ($(table).children('tr').length > 0) {
    $(table)
      .find('tr')
      .each(function () {
        arrDataCell.push($(this).find(find).html());
      });
  } else {
    callModal(
      null,
      'advice-dialog',
      'Atenção',
      5,
      0,
      '<h5 class="text-muted">Selecione uma opção!</h5>',
      '',
      false,
      ''
    );
    return false;
  }

  return arrDataCell;
}

function loadAutoComplete() {
  autoComplete('Agendasame/pacientes', 'name', 200, 'inp-pac', 'id_pac');
  autoComplete(
    'Agendasame/pacientes',
    'name',
    200,
    'inp-consulta-pac',
    'id_consulta_pac'
  );
}

function autoComplete(url, field, maxLength, inputName, holder) {
  var input = $('[name=' + inputName + ']');
  var inputPlaceholder = $('[name=' + inputName + ']').attr('placeholder');
  var dataHolder = $('[name=' + holder + ']');
  var dataurl = GLOBAL_URL + url;
  var containerList = $('.easy-autocomplete-container').find('ul');
  var selectedText = $(containerList).children('li.selected').text();

  var options = {
    url: dataurl,

    getValue: field,
    requestDelay: 1000,

    list: {
      onLoadEvent: function () {
        input.popover('hide');
        if (selectedText != input.val()) {
          dataHolder.val('');
          eacDataHolder = dataHolder;
        }
      },
      onSelectItemEvent: function () {
        var value = input.getSelectedItemData().id;
        dataHolder.val(value).trigger('change');
        eacDataHolder = dataHolder;
      },

      onHideListEvent: function () {
        if (input.hasClass('insert-eac')) {
          if (eacDataHolder != null) {
            if (eacDataHolder.val() === '') {
              var url =
                input.data('insert') +
                "/'" +
                input.val().replace(/\s/g, '+') +
                "'";

              if (input.val().length > 0) {
                callModal(
                  null,
                  'sm-dialog',
                  'Atenção',
                  5,
                  0,
                  '',
                  url,
                  true,
                  'Valor <b>' + input.val() + '</b> não encontrado! Cadastrar?'
                );
              }
            }
          }
        }
      },

      onMouseOverEvent: function () {
        var value = input.getSelectedItemData().name;
        input.val(value);
      },

      match: {
        enabled: true,
      },

      maxNumberOfElements: maxLength,

      showAnimation: {
        type: 'slide',
        time: 200,
      },
      hideAnimation: {
        type: 'slide',
        time: 200,
      },
    },
    theme: 'plate-dark',
    placeholder: inputPlaceholder,
  };

  input.easyAutocomplete(options);
}

function clearInput(input) {
  if (input != null) {
    input.val('');
  }
}

function setFocus(input) {
  if (input != null) {
    setTimeout(function () {
      $('input[name=' + input.attr('name') + ']').focus();
    }, 500);
  }
}

function pad(num, size) {
  var s = num + '';
  while (s.length < size) s = '0' + s;
  return s;
}

function validateData(dates, input, output) {
  var text;
  var y;
  var m;
  var d;
  var date = new Date();

  try {
    if (input === 'EN') {
      text = dates;
      comp = text.split('-');
      y = parseInt(comp[0], 10);
      m = parseInt(comp[1], 10);
      d = parseInt(comp[2], 10);
      date = new Date(y, m - 1, d);
    }
    if (input === 'BR') {
      text = dates;
      comp = text.split('/');
      d = parseInt(comp[0], 10);
      m = parseInt(comp[1], 10);
      y = parseInt(comp[2], 10);
      date = new Date(y, m - 1, d);
    }

    if (
      date.getFullYear() === y &&
      date.getMonth() + 1 === m &&
      date.getDate() === d
    ) {
      if (output === 'EN') {
        return [y, pad(m, 2), pad(d, 2)].join('-');
      } else if (output === 'BR') {
        return [pad(d, 2), pad(m, 2), y].join('/');
      }
    } else {
      return false;
    }
  } catch (e) {
    return false;
  }
}

function submit(formdata, action, clearFields = false, form = null) {
  //console.log(clearFields);
  $.ajax({
    type: 'POST',
    url: action,
    data: formdata,
    success: function (result) {
      console.log(result);
        messagesHandler(result);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
     // handleMSG('Erro: ' + XMLHttpRequest.responseText, 'danger');
        messagesHandler(XMLHttpRequest);
    },
  })
    .done(function () {
      if (clearFields) {
        reset(form);
      }
      //callModal(null,'advice-dialog','Aten��o',3,0,'Conclu�do com sucesso!','',false,'');
    })
    .fail(function (result) {
       messagesHandler(result);
    });
}

function request(obj, type = 'POST') {
  var params = '';
  var arrayParams = $(obj).data('params');
  var url = $(obj).data('url');

  switch (type) {
    case 'POST':
      var data = JSON.parse(paramsHandle(type, arrayParams));
      break;
    case 'GET':
      var data = null;
      params = paramsHandle(type, arrayParams);
      break;
  }

  $.ajax({
    type: type,
    url: GLOBAL_URL + url + params,
    data: data,
    success: function (result) {},
    error: function (request, status, erro) {
      return false;
    },
  })
    .done(function () {})
    .fail(function () {
      return false;
    });

  return true;
}

function reset(form) {
  $(':input,input[type=hidden]', form)
    .not(':button, :submit, :reset, .select')
    .val('')
    .removeAttr('checked')
    .removeAttr('selected');
  $('.select', form)
    .prop('selectedIndex', 0)
    .not('.select')
    .append('<option value="">---------------</option>');
  $(form).find(':input:visible').first().focus();
  $('.popover').hide();
}

function getData(url, form) {
  $.getJSON(GLOBAL_URL + url)
    .done(function (data) {
      if (data.length > 0) {
        $.each(data[0], function (key, value) {
          var altValue;
          if (validateData(value, 'EN', 'BR')) {
            altValue = validateData(value, 'EN', 'BR');
          } else {
            altValue = value;
          }
          $('[name=' + form + ']')
            .find('[name=' + key + ']')
            .val(altValue);
        });
      } else {
        callModal(
          null,
          'advice-dialog',
          'Atenção',
          5,
          0,
          '<h5 class="text-muted">Nenhum registro encontrado...</h5>',
          '',
          false,
          ''
        );
      }
    })
    .fail(function (textStatus, errorThrown) {
      //handleMSG("Error: " + textStatus, errorThrown);
    });
  $body.removeClass('loading');
}

function getLength() {
  $(document).on('paste keyup', 'input', function (event) {
    var obj = $('[name=' + event.target.name + ']');
    var minLength = $(obj).attr('minlength');
    var inputLength = $(obj).val().length;

    if ($(obj).hasClass('on-input')) {
      if (inputLength == minLength) {
        return true;
      }
    }
  });
}

function week(value) {
  var semana = [
    'DOMINGO',
    'SEGUNDA',
    'TERÇA',
    'QUARTA',
    'QUINTA',
    'SEXTA',
    'SABADO',
  ];
  var data = new Date(validateData(value, 'EN', 'EN').split('-'));
  var day = data.getDay();

  return semana[day];
}

function closeModal() {
  BootstrapDialog.closeAll();
}

function paramsHandle(type, params) {
  var arrayGet = new Array();
  var obj = {};

  $.each(params, function (index, value) {
    var input = $('[name=' + value + ']');

    if (input.val() != '') {
      try {
        if (input.val() == null) {
          obj[index] = value;
        } else if (validateData(input.val(), 'BR', 'EN')) {
          obj[index] = validateData(input.val(), 'BR', 'EN');
        } else if ($.isNumeric(input.val())) {
          obj[index] = input.val();
        } else {
          if (input.is(':radio')) {
            obj[index] =
              "'" + $('input[name=' + value + ']:checked').val() + "'";
          } else if (obj.is(':checkbox')) {
            obj[index] = input.is(':checked');
          } else {
            obj[index] = input.val().replace('/', '_').replace(/\s/g, '+');
          }
        }
      } catch (e) {
        obj[index] = obj;
      }
      var vals = Object.values(obj);
      arrayGet.push(vals);
    }
  });

  switch (type) {
    case 'POST':
      return JSON.stringify(obj);
      break;
    case 'GET':
      return '/' + arrayGet.join('/');
      break;
  }
}

function eachParams(params) {
  var paramarray = [];
  var item;
  
  $.each(params, function (key, obj) {    
   // console.log($('[name=' + obj + ']').val());
    //$body.addClass('loading');
    
    try {
      if ($('[name=' + obj + ']').val() == null) {
        item = obj;
      } else if (
        validateData($('[name=' + obj + ']').val(), 'BR', 'EN') &&
        !$('[name=' + obj + ']').is('textarea')
      ) {
        item = validateData($('[name=' + obj + ']').val(), 'BR', 'EN');
      } else if ($.isNumeric($('[name=' + obj + ']').val())) {
        item = $('[name=' + obj + ']').val();
      } else {
        if ($('[name=' + obj + ']').is(':radio')) {
          item = "'" + $('input[name=' + obj + ']:checked').val() + "'";
        } else if ($('[name=' + obj + ']').is(':checkbox')) {
          item = $('input[name=' + obj + ']').is(':checked');
        } else if ($('[name=' + obj + ']').is('textarea')) {
          item = $('[name=' + obj + ']')
            .val()
            .replace(/[/]/g, '{bar}');
        } else {
          item =
            "'" +
            $('[name=' + obj + ']')
              .val()
              .replace('/', '_')
              .replace(/\s/g, '+') +
            "'";
        }
      }
    } catch (e) {
      item = obj;
    }

    paramarray.push(item);
  });

  //console.log(paramarray);

  return paramarray.join('/');
}

function populateSelect(url, nameSelect, displayFieldSlct) {
  var $select = $('[name=' + nameSelect + ']');
  
   $.getJSON(url, function (data) { 
    $select.html('');
    $select.append( '<option value="">--------------</option>');
    $.each(data, function (i, field) { 
        
      if (validateData(field[displayFieldSlct], 'EN', 'BR')) {
        $displayMember =
          validateData(field[displayFieldSlct], 'EN', 'BR') +
          ' - ' +
          week(validateData(field[displayFieldSlct], 'EN', 'EN'));
      } else {
        $displayMember = field[displayFieldSlct];
      }

      $select.append(       
        '<option value="' + field.id + '">' + $displayMember + '</option>' 
      );      
    });
  }).done(function (data) {
      $select.val('');
  }).fail(function (jqXHR, textStatus, errorThrown) {
    console.error("Status HTTP:", jqXHR.status);
    console.error("Erro na requisição:", textStatus, errorThrown);
    console.error("Resposta do servidor:", jqXHR.responseText);    
  });
}

function call(obj, checkInp, input, optionalParams = null, refresh = null) {
  var paramarray = [];
  var item;

  $('.popover').hide();

  if (checkInp != null) {
    var inputLength = $(input).val().length;

    if ($(input).is('input')) {
      var minLength = $(input).attr('minlength');

      if (inputLength < minLength) {
        callModal(
          obj,
          'advice-dialog',
          'Atenção',
          5,
          0,
          '<b>' +
            $(input).attr('placeholder') +
            '</b> não pode ser vazio ou conter menos de <b>' +
            minLength +
            '</b> caracteres!',
          '',
          false,
          ''
        );
        return false;
      }
    } else if ($(input).is('select')) {
      if (inputLength == 0) {
        callModal(
          obj,
          'advice-dialog',
          'Atenção',
          5,
          0,
          'Selecione um opção!',
          '',
          false,
          ''
        );
        $(obj).prop('checked', false);
        return false;
      }
    }
  }

  if (obj.attr('id') === 'home') {
    window.location = GLOBAL_URL + $(obj).data('location');
    return false;
  }

  if (optionalParams !== null) {
    $.each(optionalParams, function (i, value) {
      if (validateData(value, 'BR', 'EN')) {
        item = "'" + validateData(value, 'BR', 'EN') + "'";
      } else {
        item = "'" + value + "'";
      }
      paramarray.push(item);
    });

    optionalParams = '/' + paramarray.join('/');
  } else {
    optionalParams = '';
  }

  if (obj.hasClass('call-data')) {
    var params = $(obj).data('params');
    var url = $(obj).attr('href');
    var url_newtab = $(obj).data('url');
    var form = $(obj).data('form');
    var redirect = $(obj).data('redirect');
    var redirect_url = $(obj).data('redirect-url');
    var redirect_container = $(obj).data('redirect-target');
    var close_modal = $(obj).data('modal-close');       

    if (redirect !== '' && redirect !== null) {
      url = url + '/' + eachParams(params) + optionalParams;
    
      if (redirect === 'getdata') {
        getData(url, form);

        if (redirect_url != null && redirect_url !== '') {
          forward(obj, 'reload', redirect_url);
        }
      } else if (redirect === 'load') {         
          
        $('[name=' + redirect_container + ']').fadeOut(1, function () {
          $('[name=' + redirect_container + ']').load(
            GLOBAL_URL + url + optionalParams,
            function () {
              $('[name=' + redirect_container + ']').fadeIn(1);
            }
          );
        });
      } else if (redirect === 'none') {
        $.ajax({
          type: 'GET',
          url: GLOBAL_URL + url + '/' + eachParams(params) + optionalParams,
          success: function (result) {
            $body.removeClass('loading');
          },
        });
      } else {
        forward(obj, redirect, url);
      }

      $body.removeClass('loading');
    } else if (obj.hasClass('newtab')) {
      window.open(GLOBAL_URL + url_newtab + '/' + eachParams(params), '_blank');
    } else {
      $.ajax({
        type: 'GET',
        url: GLOBAL_URL + url + '/' + eachParams(params) + optionalParams,
        success: function (result) {
          //                   if(params == '') {
          //                       $('[name=' + display + ']').html(result);
          //                   } else {
          BootstrapDialog.alert({
            cssClass: 'sm-dialog',
            title: 'Mensagem',
            message:
              '<div class="text-orange" style="font-size: 16px;">' +
              result +
              '</div>',
            type: BootstrapDialog.TYPE_SUCCESS,
            closable: true,
            draggable: true,
          });

          $body.removeClass('loading');
          //                    }
        },
      });
    }

    if (close_modal) {
      close_all = BootstrapDialog.closeAll();
    }
  }

  if (obj.hasClass('call-modal')) {
    var cls = $(obj).data('modal-cls');
    var title = $(obj).data('modal-title');
    var type = $(obj).data('modal-type');
    var size = $(obj).data('modal-size');
    var msg = $(obj).data('modal-msg');
    var url = $(obj).data('modal-href');
    var redirect = $(obj).data('redirect');
    var redirect_url = $(obj).data('modal-redirect-url');
    var redirect_params = $(obj).data('modal-redirect-params');
    var params = $(obj).data('modal-params');
    var confirm = $(obj).data('modal-confirm');
    var question = $(obj).data('modal-question');
    var close_all = $(obj).data('modal-close');

    //console.log(url);

    if (params != '') {
      url = url + '/' + eachParams(params);
    }
    if (redirect_params != '') {
      redirect_url = redirect_url + '/' + eachParams(redirect_params);
    } else {
      $.ajax({
        type: 'GET',
        url: GLOBAL_URL + url + '/' + eachParams(params) + optionalParams,
        success: function (result) {
          if ($(obj).hasClass('modal-callback')) {
            var types = [
              BootstrapDialog.TYPE_DEFAULT,
              BootstrapDialog.TYPE_INFO,
              BootstrapDialog.TYPE_PRIMARY,
              BootstrapDialog.TYPE_SUCCESS,
              BootstrapDialog.TYPE_WARNING,
              BootstrapDialog.TYPE_DANGER,
            ];

            if ($(obj).hasClass('modal-callback default')) {
              var t = 0;
            } else if ($(obj).hasClass('modal-callback info')) {
              var t = 1;
            } else if ($(obj).hasClass('modal-callback primary')) {
              var t = 2;
            } else if ($(obj).hasClass('modal-callback success')) {
              var t = 3;
            } else if ($(obj).hasClass('modal-callback warning')) {
              var t = 4;
            } else if ($(obj).hasClass('modal-callback danger')) {
              var t = 5;
            } else if ($(obj).hasClass('modal-callback')) {
              var t = 0;
            }

            var type = types[t];

            BootstrapDialog.alert({
              cssClass: 'sm-dialog',
              title: 'Mensagem',
              message:
                '<div class="text-orange" style="font-size: 16px;">' +
                result +
                '</div>',
              type: type,
              closable: true,
              draggable: true,
            });
          }
        },
      }).done(function () {
        $body.removeClass('loading');
      });

      return;
    }

    callModal(
      obj,
      cls,
      title,
      type,
      size,
      msg,
      url,
      confirm,
      question,
      close_all,
      redirect,
      redirect_url
    );
  }
}

function forward(obj, redirect, url) {
  var new_url = GLOBAL_URL + url;

  if ($(obj).is('select')) {
    var $select = $(obj).data('redirect');
    var display = $(obj).data('display');
    var redirect_url = $(obj).data('redirect-url');
    var redirect_params = $(obj).data('redirect-params');
    var redirect_container = $(obj).data('redirect-target');

    if (redirect === 'header') {
      window.open(new_url, '_blank');
      $body.removeClass('loading');
    } else if (redirect === 'reload') {
      setTimeout(function () {
        $('[name=' + display + ']').load(new_url);
      }, 1);
    } else if (typeof redirect === 'undefined') {
      window.location = new_url;
    } else if (redirect === 'refresh') {
      setTimeout(location.reload(), 1);
    } else {
      populateSelect(new_url, $select, display);
    }
    if (redirect_url !== '') {
      setTimeout(function () {
        $('[name=' + redirect_container + ']').load(
          GLOBAL_URL + redirect_url + '/' + eachParams(redirect_params)
        );
      }, 1);
    }
  } else if (
    $(obj).is('button') ||
    $(obj).is('input[type=checkbox]') ||
    $(obj).is('input[type=radio]')
  ) {
    var form = $(obj).data('modal-form');
    //var params = $(obj).data('params');
    var redirect_url_btn = $(obj).data('redirect-url');
    var redirect_params_btn = $(obj).data('redirect-params');
    var redirect_container_btn = $(obj).data('redirect-target');

    if (redirect === 'header') {
      if ($(obj).hasClass('newtab')) {
        window.open(new_url, '_blank');
        $body.removeClass('loading');
      } else {    
        window.location.assign(new_url);
      }
    } else if (redirect === 'exetoload') {
      $.ajax({
        type: 'GET',
        url: new_url,
        success: function (result) {
          if ($(obj).hasClass('modal-callback')) {
            var types = [
              BootstrapDialog.TYPE_DEFAULT,
              BootstrapDialog.TYPE_INFO,
              BootstrapDialog.TYPE_PRIMARY,
              BootstrapDialog.TYPE_SUCCESS,
              BootstrapDialog.TYPE_WARNING,
              BootstrapDialog.TYPE_DANGER,
            ];

            if ($(obj).hasClass('modal-callback default')) {
              var t = 0;
            } else if ($(obj).hasClass('modal-callback info')) {
              var t = 1;
            } else if ($(obj).hasClass('modal-callback primary')) {
              var t = 2;
            } else if ($(obj).hasClass('modal-callback success')) {
              var t = 3;
            } else if ($(obj).hasClass('modal-callback warning')) {
              var t = 4;
            } else if ($(obj).hasClass('modal-callback danger')) {
              var t = 5;
            } else if ($(obj).hasClass('modal-callback')) {
              var t = 0;
            }

            var type = types[t];

            BootstrapDialog.alert({
              cssClass: 'sm-dialog',
              title: 'Aviso',
              message:
                '<p class="text-orange" style="font-size: 16px;">' +
                result +
                '</p>',
              type: type,
              closable: true,
              draggable: true,
            });
          }
        },
      })
        .done(function () {
          if (redirect_container_btn != '') {
            $('[name=' + redirect_container_btn + ']').load(
              GLOBAL_URL +
                redirect_url_btn +
                '/' +
                eachParams(redirect_params_btn)
            );
          }
        })
        .fail(function () {});

      return;
    } else if (redirect === 'reload') {
      setTimeout(function () {
        $('[name=' + redirect_container_btn + ']').load(
          new_url + '/' + eachParams(redirect_params_btn)
        );
      }, 1);
    } else if (redirect === 'getdata') {
      getData(url, form);
    } else if (redirect === 'refresh') {
      setTimeout(location.reload(), 1);
    }
  }

  if ($(obj).is('span')) {
    if (redirect === 'refresh') {
      setTimeout(location.reload(), 1);
    }
  }

  $body.removeClass('loading');
}

function callModal(
  obj,
  classe,
  title,
  type,
  size,
  msg,
  url,
  confirm,
  question,
  close,
  redirect,
  redirect_url
) {
  var types = [
    BootstrapDialog.TYPE_DEFAULT,
    BootstrapDialog.TYPE_INFO,
    BootstrapDialog.TYPE_PRIMARY,
    BootstrapDialog.TYPE_SUCCESS,
    BootstrapDialog.TYPE_WARNING,
    BootstrapDialog.TYPE_DANGER,
  ];
  var sizes = [
    BootstrapDialog.SIZE_NORMAL,
    BootstrapDialog.SIZE_WIDE,
    BootstrapDialog.SIZE_LARGE,
  ];

  var t = types[type];
  var s = sizes[size];
  var content;
  var close_all;

  if (confirm === true) {
    dialogLoad = BootstrapDialog.confirm({
      cssClass: classe,
      type: t,
      title: title,
      size: s,
      message: question,
      draggable: true,
      btnCancelLabel: 'Não',
      btnOKLabel: 'Sim',
      btnOKClass: 'btn-success',
      autodestroy: true,
      callback: function (result) {
        if (result) {
          //$('<div class=\"\"></div>').load(GLOBAL_URL + (url));
          $.ajax({
            type: 'GET',
            url: GLOBAL_URL + url,
            success: function (result) {
              if (eacDataHolder != null) {
                eacDataHolder.val(result);
              }
              if (close) {
                close_all = BootstrapDialog.closeAll();
              }
              if (result != '') {
                messagesHandler(
                  '{"action":"msg","content":"' +
                    result +
                    '","typemsg":"danger"}'
                );
              }
            },
            error: function () {
              //alert("Ocorreu um erro ao carregar os dados.");
            },
          });

          $(document).on('hidden.bs.modal', function () {
            if (obj !== null) {
              forward(obj, redirect, redirect_url);
            }
          });
          close_all;
          isConfirmed = true;

          //clearInput(activeInput);
          //console.log(url);
        } else {
          isConfirmed = false;
          //clearInput(activeInput);
          setFocus(activeInput);
        }
      },
    });
    $body.removeClass('loading');
    return;
  }

  if (close) {
    close_all = BootstrapDialog.closeAll();
  }

  if (url === '') {
    content = msg;
  } else {
    content = $('<div></div>').load(GLOBAL_URL + url);
  }

  dialogLoad = BootstrapDialog.show({
    cssClass: classe,
    type: t,
    title: title,
    size: s,
    message: content,
    closable: true,
    draggable: true,
    autodestroy: true,
    buttons: [
      {
        label: 'Fechar',
        action: function (dialogRef) {
          $('.popover').hide();
          dialogRef.close();
          close_all;
        },
      },
    ],
  });
  loadAutoComplete();
  if (obj !== null) {
    forward(obj, redirect, redirect_url);
  }
}

/* End Functions */

/* Listeners */

$(document).on('hide.bs.modal', function () {
  $('.popover').hide();
});

$(document).on('shown.bs.modal', function (e) {
  $body.removeClass('loading');
  setup();
  loadAutoComplete();

  var inp = $(e.currentTarget).find('.modal-body :input:visible');

  if (!inp.hasClass('calendar')) {
    inp.first().focus();
  }
});

$(document).on('click', 'span', function (event) {
  event.preventDefault;
  var obj = $(this);
  var form = obj.closest('form');

  if (obj.hasClass('refresh')) {
    location.reload();
  }

  if (obj.hasClass('reset')) {
    reset(form);
  }
});

$(document).on('input', 'input', function (event) {
  event.preventDefault;
  var obj = $(this);
  if (obj.val().length > 0) {
    activeInput = obj;
  }
});

$(document).on('changeDate', 'input', function (event) {
  event.preventDefault;
  var data = $(this).datepicker('getFormattedDate');
  var input = $(this);

  input.val('');

  //$(input).closest('div').find('button').addClass('btn btn-danger');

  $(input.val(validateData(data, 'EN', 'EN')));

  if (input.hasClass('dinamic-btn')) {
    $(input).closest('div').find('button').addClass('btn btn-danger');
  }
});

$(document).on('click', 'button', function (event) {
  event.preventDefault();

  var obj = $(this);
  var checkInp = $(this).data('check-input');
  if (checkInp != null) {
    var input = $('[name=' + checkInp + ']');
  }
  var form = obj.closest('form');
  var inpDate = form.find('.date');
  var action = form.attr('action');
  var closeModal = obj.data('modal-close');
  var optional = null;
  var formdata = form.serializeArray();
  var reset = false;

  if (obj.hasClass('reset')) {
    reset = true;
  }

  if (obj.hasClass('submit')) {
    $(form).validate_popover({
      popoverPosition: 'top',
      ignore: '.date',
    });

    if ($(inpDate).length) {
      if (inpDate.val().length < 10) {
        $(inpDate).popover('show');
      } else {
        $(inpDate).popover('hide');
      }
    }

    if (
      eacDataHolder != null &&
      eacDataHolder.data('required') == true &&
      eacDataHolder.val() == ''
    ) {
      callModal(
        null,
        'sm-dialog',
        'Atenção',
        5,
        0,
        'Valor de <b>' +
          $(eacDataHolder).attr('placeholder') +
          '</b> não encontrado! Selecione um item da lista ou cadastre o novo valor.',
        '',
        false,
        ''
      );
      return;
    }

    if ($(form).valid()) {
      event.preventDefault();
      submit(formdata, action, reset, form);
      if (obj.hasClass('refresh')) {
        setTimeout(function () {
          location.reload();
        }, 1);
      }
      if (obj.hasClass('call-data')) {
        setTimeout(function () {
          call(obj, null, null);
        }, 1);
      }
    } else {
      $body.removeClass('loading');
      return false;
    }

    return;
  } else if (obj.hasClass('print')) {
    window.print();
  }

  if (obj.hasClass('refresh')) {
    location.reload();
  }

  if (obj.hasClass('chart')) {
    callChart($(this).data('url-chart'));
  }

  var link = obj.closest('a');
  var linkcollapse = link.attr('aria-expanded');

  if (typeof linkcollapse == 'undefined' || linkcollapse == 'false') {
    call(obj, checkInp, input, optional);
  }
});

$(document).on('change', 'select', function (event) {  
  var obj = $(this);
  call(obj, null, null);
});

$(document).on('click', 'a', function (event) { 
  var obj = $(this);
  call(obj, null, null);
});

$(document).on('keypress', 'input', function (event) {  
  var obj = $(this);
  var target;

  target = obj.closest('div').find('button');
  if (target.length == 0) {
    target = obj.closest('div').next().find('button');
  }

  if (uniKeyCode(event) == 13) {
    $(target).trigger('click');
    return false;
  }
});

$(document).on('change', 'input[type=checkbox]', function (event) { 
  var obj = $(this);
  call(obj, null, null);
});

$(document).on('click', 'input[type=radio]', function (event) {
 
  $('input[type=radio]').prop('checked', false);
  var obj = $(this);
  var checkInp = $(this).data('check-input');
  if (checkInp != null) {
    var input = $('[name=' + checkInp + ']');
  }
  $(obj).prop('checked', true);
  call(obj, checkInp, input);
});

$(document).on('click', '#notification-bar', function (event) {
 
  var obj = $(this);

  if (obj.is(':visible')) {
    obj.fadeOut('slow');
  }
});

$(document).on('change', '#slct-prof-pendente', function () {
  $('#slct-mes-pendente').val('');
  $('#container-pendentes').fadeOut();
});

$(document).on('change', '#slct-mes-pendente', function () {
  if ($('#slct-mes-pendente').val() != '') {
    var url = GLOBAL_URL + 'Retornos/getAlerts/';
    var params = $(this).data('params');
    var redirect = '#container-pendentes';

    if ($('#slct-prof-pendente').val() != '') {
      $(redirect).fadeIn(100);
      $(redirect).load(url + eachParams(params));
    }
  } else {
    $('#container-pendentes').fadeOut();
  }
});

$(document).on('click', '.btn-update-obs', function () {
  var id = $(this).attr('id');
  var obj = document.getElementById(id);
  var href = GLOBAL_URL + $(obj).attr('href') + '/';
  var params = $(obj).data('params');
  var posturl = GLOBAL_URL + $(obj).data('post-url') + '/';
  var postparams = $(obj).data('post-params');

  $.ajax({
    type: 'GET',
    url: href + eachParams(params),
    success: function (result) {
      $('#container-pendentes').load(
        posturl + eachParams(postparams),
        function () {
          $('#container-pendentes').fadeIn(500);
        }
      );
    },
  }).done(function () {
    callModal(
      null,
      'sm-dialog',
      'Mensagem',
      3,
      0,
      'Obs salva com sucesso!',
      '',
      false,
      ''
    );
  });
});

$(document).on('click', '#btn-recuperar-senha', function (e) {  
    var idUser = $('[name=slct-user-login]').val();

    if (!idUser) {
        callModal(
            null, 'advice-dialog', 'Atenção', 5, 0,
            '<h5 class="text-muted">Selecione um profissional antes de continuar!</h5>',
            '', false, ''
        );
        return;
    }    
});

//-----------MASCARAS---------------------------------------//

$(document).on('input', '.data-br', function () {

    let valor = $(this).val().replace(/\D/g, '');

    valor = valor.substring(0, 8);

    if (valor.length > 4) {
        valor = valor.replace(/(\d{2})(\d{2})(\d+)/, '$1/$2/$3');
    } else if (valor.length > 2) {
        valor = valor.replace(/(\d{2})(\d+)/, '$1/$2');
    }

    $(this).val(valor);

});

$(document).on('input', '.cpf-mask', function () {

    let cpf = $(this).val().replace(/\D/g, '');

    cpf = cpf.substring(0, 11);

    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

    $(this).val(cpf);

});

$(document).on('input', '.tel-mask', function () {

    let tel = $(this).val().replace(/\D/g, '');

    tel = tel.substring(0, 11);

    if (tel.length > 10) {

        tel = tel.replace(
            /^(\d{2})(\d{5})(\d+)/,
            '($1) $2-$3'
        );

    } else if (tel.length > 6) {

        tel = tel.replace(
            /^(\d{2})(\d{4})(\d+)/,
            '($1) $2-$3'
        );

    } else if (tel.length > 2) {

        tel = tel.replace(
            /^(\d{2})(\d+)/,
            '($1) $2'
        );

    }

    $(this).val(tel);

});

$(document).on('input', '.numeric-mask', function () {
    this.value = this.value.replace(/\D/g, '');
});

//-----------FIM MASCARAS---------------------------------------//


/* End Listeners */

function showPopup(inputId, message, duration = 2500) {
  const popup = document.querySelector(`#popup-${inputId}`);
  if (!popup) return;

  popup.textContent = message;
  popup.style.display = 'block';

  setTimeout(() => {
    popup.style.display = 'none';
  }, duration);
}

function showPopupDynamic(input, message, duration = 2500) {
  const $input = $(input);
  const $popup = $('<div class="tooltip-dynamic"></div>').text(message);
  $('body').append($popup);

  const offset = $input.offset();
  const inputHeight = $input.outerHeight();
  const inputWidth = $input.outerWidth();
  const popupWidth = $popup.outerWidth();
  const popupHeight = $popup.outerHeight();

  const windowHeight = $(window).height();
  const windowWidth = $(window).width();

  let top, left, arrowDir;

  // Decide posição automaticamente
  if (offset.top + inputHeight + popupHeight + 10 < windowHeight) {
    // abaixo
    top = offset.top + inputHeight + 6;
    left = offset.left + inputWidth / 2 - popupWidth / 2;
    arrowDir = 'top';
  } else if (offset.top - popupHeight - 10 > 0) {
    // acima
    top = offset.top - popupHeight - 6;
    left = offset.left + inputWidth / 2 - popupWidth / 2;
    arrowDir = 'bottom';
  } else if (offset.left + inputWidth + popupWidth + 10 < windowWidth) {
    // direita
    top = offset.top + inputHeight / 2 - popupHeight / 2;
    left = offset.left + inputWidth + 6;
    arrowDir = 'left';
  } else {
    // esquerda (fallback)
    top = offset.top + inputHeight / 2 - popupHeight / 2;
    left = offset.left - popupWidth - 6;
    arrowDir = 'right';
  }

  $popup.css({ top, left }).addClass('show');

  // seta direcional
  const arrowSize = 6;
  const after = $popup.get(0).style;
  if (arrowDir === 'top') after.setProperty('--arrow', `${arrowSize}px solid transparent; border-top-color: #333`);
  if (arrowDir === 'bottom') after.setProperty('--arrow', `${arrowSize}px solid transparent; border-bottom-color: #333`);
  if (arrowDir === 'left') after.setProperty('--arrow', `${arrowSize}px solid transparent; border-left-color: #333`);
  if (arrowDir === 'right') after.setProperty('--arrow', `${arrowSize}px solid transparent; border-right-color: #333`);

  setTimeout(() => $popup.fadeOut(200, () => $popup.remove()), duration);
}


