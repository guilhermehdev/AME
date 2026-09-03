var mes = 0;
var ano;
var totalseg;
var totalter;
var totalqua;
var totalqui;
var totalsex;
var totalsab;
var totaldom;
var total;
var nseg;
var nter;
var nqua;
var nqui;
var nsex;
var nsab;
var ndom;
var datesseg = [];
var datester = [];
var datesqua = [];
var datesqui = [];
var datessex = [];
var datessab = [];
var datesdom = [];

$(document).ready(function () {
    $.ajaxSetup({
        cache: false,
    });
    GLOBAL_URL = $('#URL').val();
});

function ajax(url) {
    return $.ajax({type: 'GET', url: GLOBAL_URL + url, dataType: 'json'});
}

function post(url, strJSON, callbackMsg = false) {
    var JSONObject = JSON.parse(strJSON);

    $.ajax({
        type: 'POST',
        url: GLOBAL_URL + url,
        data: JSONObject,
        success: function (result) {
            if (callbackMsg)
                messagesHandler(result);
            return true;
        },
    }).done(function () {});
}

function abrirWhats() {

    const numero = "55" + $('#inp-tel').val().replace(/\D/g, '');
    const nome = document.getElementById("inp-nome").value;
    const selectData = document.getElementById("slct-espec-whatsapp");
    const espec = selectData.options[selectData.selectedIndex].text;
    const selectDataProf = document.getElementById("slct-prof-whatsapp");
    const prof = selectDataProf.options[selectDataProf.selectedIndex].text;
    const selectHora = document.getElementById("slct-hora-whatsapp");
    const hora = selectHora.options[selectHora.selectedIndex].text;
    const data = document.getElementById("inp-data-whatsapp").value;

    if (nome.length < 3) {
        alert("Selecione o Paciente");
        return;
    }

    if (numero.length < 12) {
        alert("Telefone inválido.");
        return;
    }

    if (!espec || selectData.selectedIndex === 0) {
        alert("Selecione a especialidade.");
        return;
    }

    if (!data) {
        alert("Selecione a data.");
        return;
    }

    if (!hora || selectHora.selectedIndex === 0) {
        alert("Selecione a hora.");
        return;
    }

    const msg =
            "Olá " + nome + "! Aqui é do AME de Peruibe.\n\n" +
            "Estamos confirmando sua consulta/exame de " + espec + ", DR " + prof + " agendada para:\n" +
            "Data: " + data + "\n" +
            "Hora: " + hora + "\n" +
            "Responda:\n\n" +
            "1 – Confirmo presença\n" +
            "2 – Não poderei comparecer\n\n" +
            "Sua resposta é muito importante para evitar faltas.\n\n" +
            "Obrigado.";

    const link = "https://wa.me/" + numero + "?text=" + encodeURIComponent(msg);
    window.open(link, "_blank");
}
;

$(document).on('click', '#open-whatsapp', function (event) {
    abrirWhats();
});

$(document).on('click', '#btn-gerarAgenda', function (event) {
    event.preventDefault;
    var obj = $(this);
    var url = $(obj).attr('href');
    var params = $(obj).data('params');
    var redirect_url = $(obj).data('redirect-url');
    var redirect_container = $(obj).data('redirect-target');
    var redirect_params = $(obj).data('redirect-params');
    var paramarray = [];
    var item;

    if (
            $('#slct-unidade').val() > 0 &&
            $('#slct-espec').val() > 0 &&
            $('#slct-prof').val() > 0
            ) {
        optional = getCellValuefromTableRow('tbl-datas', '.td-data');
        if (optional === false) {
            return;
        }
    } else {
        callModal(
                null,
                'advice-dialog',
                'Atenção',
                5,
                0,
                'Selecione uma <strong>UNIDADE</strong>, uma <strong>ESPECIALIDADE</strong> e um <strong>PROFISSIONAL </strong>',
                '',
                false,
                ''
                );
        return;
    }

    if (optional !== null) {
        $.each(optional, function (i, value) {
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

    ajax(
            url + '/' + eachParams(params) + optionalParams,
            true,
            false,
            redirect_container,
            redirect_url,
            redirect_params
            );
    $('#tbl-datas tr').remove();
});

$(document).on('click', '#btn-salvarPerda', function (event) {
    event.preventDefault;
    var obj = $(this);
    var url = $(obj).attr('href');
    var params = $(obj).data('params');
    var redirect_url = $(obj).data('redirect-url');
    var redirect_container = $(obj).data('redirect-target');
    var redirect_params = $(obj).data('redirect-params');
    var paramarray = [];
    var item;

    if (
            $('#slct-unidade').val() > 0 &&
            $('#slct-espec').val() > 0 &&
            $('#slct-prof').val() > 0
            ) {
        optional = getCellValuefromTableRow('tbl-datas', '.td-data');
        if (optional === false) {
            return;
        }
    } else {
        callModal(
                null,
                'advice-dialog',
                'Atenção',
                5,
                0,
                'Selecione uma <strong>UNIDADE</strong>, uma <strong>ESPECIALIDADE</strong> e um <strong>PROFISSIONAL </strong>',
                '',
                false,
                ''
                );
        return;
    }

    if (optional !== null) {
        $.each(optional, function (i, value) {
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

    ajax(
            url + '/' + eachParams(params) + optionalParams,
            true,
            false,
            redirect_container,
            redirect_url,
            redirect_params
            );
    $('#tbl-datas tr').remove();
});

$('.calendar').on('changeDate', function () {
    if ($(this).hasClass('dinamicrows')) {
        var data = $(this).datepicker('getFormattedDate');
        var container = $(this).data('redirect-target');

        var button = $(
                '<td><button id="btn-remHorario" class="btn btn-danger delRow" type="button"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td><td class="td-data" data-data=' +
                validateData(data, 'BR', 'EN') +
                '>' +
                data +
                '</td>'
                );

        if (data != '') {
            addDinamicRow(container, button);
        }
    }
});

$(document).on('click', '.delRow', function (event) {
    event.preventDefault;
    var obj = $(this);

    $(this).closest('tr').remove();
});

$(document).on('click', '#btn-searchAgendasGeradas', function (event) {
    event.preventDefault;
    msg =
            '{"action":"modal","content":"Selecione a data <b>Inicial</b> e <b>Final</b>","typemsg":"danger"}';
    msg1 =
            '{"action":"modal","content":"Selecione uma <b>Unidade</b>","typemsg":"danger"}';

    if ($('#dt-init').val() == '' || $('#dt-fin').val() == '') {
        messagesHandler(msg);
        return;
    } else {
        if ($('#slct-unidade').val() == '') {
            messagesHandler(msg1);
        }
    }
});

$(document).on('click', '#btn-salvargrid', function (event) {
    event.preventDefault;

    if (
            $('#slct-dias').val() > 0 &&
            $('#slct-espec').val() > 0 &&
            $('#slct-prof').val() > 0 &&
            $('#inp-limite').val() > 0
            ) {
    } else {
        callModal(
                null,
                'advice-dialog',
                'Atenção',
                5,
                0,
                'Selecione uma <strong>ESPECIALIDADE</strong>, um <strong>PROFISSIONAL</strong> o <strong>DIA </strong> e digite o <strong>LIMITE</strong> diário.',
                '',
                false,
                ''
                );
        return;
    }
});

function loadDatas(mes, ano) {
    $('#dias-selecionados').html('');
    $.each(countDaysgetDates(mes, ano), function (index, value) {
        addDinamicRow(
                'dias-selecionados',
                '<td><button id="btn-remData" class="btn btn-danger delRow" type="button"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;</td><td class="td-data" data-data=' +
                validateData(value, 'BR', 'EN') +
                '>' +
                validateData(value, 'EN', 'BR') +
                '</td>'
                );
    });
}

$(document).on('change', '#slct-mes-grd', function (event) {
    event.preventDefault;
    var prof = $('#slct-prof :selected').text();
    var espec = $('#slct-espec :selected').text();
    var mes = $('#slct-mes-grd').val();
    var ano = $('#slct-ano-grd').val();

    if ($(this).val() != '') {
        $('#nomeEspec').html(espec);
        $('#nomeProf').html(prof);

        loadDatas(mes, ano);
    } else {
    }
    $('#div-container-distribuidor-vagas').fadeOut('slow');
});

$(document).on('click', '#btn-restauradias', function (event) {
    event.preventDefault;
    var mes = $('#slct-mes-grd').val();
    var ano = $('#slct-ano-grd').val();

    loadDatas(mes, ano);
});

$(document).on('change', '#slct-prof', function (event) {
    event.preventDefault;

    $('#slct-mes-grd').val('');
    $('#grades-geradas').fadeOut('slow');
});

$(document).on('change', '#slct-mes-grd', function (event) {
    event.preventDefault;
    $('#grades-geradas').fadeIn('slow');
    if ($('#slct-mes-grd').val() !== '') {
        $('#btn-restauradias').fadeIn('slow');
    } else {
        $('#btn-restauradias').fadeOut('slow');
        $('#dias-selecionados').html('');
    }
});

function diasNoMes(mes, ano) {
    var data = new Date(ano, mes, 0);
    return data.getDate();
}

$(document).on('click', '#btn-salvardias', function (event) {
    event.preventDefault;
    var datas = getCellValuefromTableRow('dias-selecionados', '.td-data');

    var days = [];
    var dates = [];

    if (
            $('#slct-espec').val() !== '' &&
            $('#slct-prof').val() !== '' &&
            $('#slct-mes-grd').val() !== ''
            ) {
        for (var i = 0; i < datas.length; i++) {
            var dt = datas[i].split('/');
            var y = dt[2];
            var m = dt[1] - 1;
            var d = dt[0];

            days.push(new Date(y, m, d).getDay());
            var day = new Date(y, m, d).getDate();
            var month = new Date(y, m, d).getMonth();
            var year = new Date(y, m, d).getFullYear();

            fulldate = year + '-' + pad(Number(month + 1), 2) + '-' + pad(day, 2);

            dates.push(fulldate);
        }

        $('#div-container-distribuidor-vagas').fadeIn('slow');
        //console.log(days);
        selectDays(days, dates);
    } else {
        return;
    }
});

function countDaysgetDates(mes, ano) {
    totalseg = 0;
    totalter = 0;
    totalqua = 0;
    totalqui = 0;
    totalsex = 0;
    totalsab = 0;
    totaldom = 0;
    total = 0;

    var ndays = diasNoMes(parseInt(mes) + 1, ano);
    var days = [];
    var dates = [];

    for (var i = 0; i < ndays; i++) {
        days.push(new Date(ano, mes, i + 1).getDay());
        var day = new Date(ano, mes, i + 1).getDate();
        var month = new Date(ano, Number(mes), i + 1).getMonth();
        var year = new Date(ano, mes, i + 1).getFullYear();

        fulldate = year + '-' + pad(Number(month + 1), 2) + '-' + pad(day, 2);

        dates.push(fulldate);
    }

    //console.log(days);
    return dates;
}

function selectDays(days, selecteddays) {
    nseg = 0;
    nter = 0;
    nqua = 0;
    nqui = 0;
    nsex = 0;
    nsab = 0;
    ndom = 0;
    datesseg = [];
    datester = [];
    datesqua = [];
    datesqui = [];
    datessex = [];
    datessab = [];
    datesdom = [];

    for (var i = 0; i < days.length; i++) {
        if (days[i] == 1) {
            nseg++;
            datesseg.push(selecteddays[i]);
        }
        if (days[i] == 2) {
            nter++;
            datester.push(selecteddays[i]);
        }
        if (days[i] == 3) {
            nqua++;
            datesqua.push(selecteddays[i]);
        }
        if (days[i] == 4) {
            nqui++;
            datesqui.push(selecteddays[i]);
        }
        if (days[i] == 5) {
            nsex++;
            datessex.push(selecteddays[i]);
        }
        if (days[i] == 6) {
            nsab++;
            datessab.push(selecteddays[i]);
        }
        if (days[i] == 0) {
            ndom++;
            datesdom.push(selecteddays[i]);
        }
    }
    //console.log(datesseg,datester,datesqua,datesqui,datessex);
}

function nvagas(limite, cls, idUnidade, uniqueid, ano) {
    var nday = 0;
    var mes = $('#slct-mes-grd').val();

    countDaysgetDates(mes, ano);

    $('.' + cls).each(function () {
        nday = nday + Number($(this).val());

        if (nday > limite) {
            $('#' + cls).addClass('text-danger');
            //callModal(null,'advice-dialog','Atenção',5,0,'Limite de vagas diário <b>('+limite+')</b> alcançado!','',false,'');
        } else if (nday == limite) {
            $('#' + cls)
                    .removeClass('text-danger')
                    .addClass('text-success')
                    .css('font-weight', 'normal');
        } else {
            $('#' + cls)
                    .removeClass('text-danger text-success')
                    .addClass('text-primary')
                    .css('font-weight', 'normal');
        }
        $('#' + cls).html(nday);
        $('#limite' + cls).html('<span class="text-success">' + limite + '</span>');
    });

    $('#vagas[data-uniqueid]').each(function () {
        if ($(this).data('uniqueid') == uniqueid) {
            if ($(this).hasClass('seg')) {
                totalseg = (totalseg + Number($(this).val())) * nseg;
            }
            if ($(this).hasClass('ter')) {
                totalter = (totalter + Number($(this).val())) * nter;
            }
            if ($(this).hasClass('qua')) {
                totalqua = (totalqua + Number($(this).val())) * nqua;
            }
            if ($(this).hasClass('qui')) {
                totalqui = (totalqui + Number($(this).val())) * nqui;
            }
            if ($(this).hasClass('sex')) {
                totalsex = (totalsex + Number($(this).val())) * nsex;
            }
            if ($(this).hasClass('sab')) {
                totalsab = (totalsab + Number($(this).val())) * nsab;
            }
            if ($(this).hasClass('dom')) {
                totaldom = (totaldom + Number($(this).val())) * ndom;
            }

            total = totalseg + totalter + totalqua + totalqui + totalsex;

            $('#total-' + idUnidade)
                    .html(total)
                    .css('font-weight', 'bold');
        }
    });
}

$(document).on('input', '#vagas', function (event) {
    event.preventDefault;
    var limite = $(this).data('limite');
    var cls = $(this).attr('class');
    var idUnidade = $(this).data('idunidade');
    var uniqueid = $(this).data('uniqueid');
    var ano = $('[name=' + $(this).data('ano') + ']').val();

    nvagas(limite, cls, idUnidade, uniqueid, ano);
});

function postHelper(
        idunidade,
        idespec,
        idprof,
        iddia,
        totalmes,
        vagas,
        arrDatas,
        i,
        vagasutilizadas,
        callbackMsg = false
        ) {
    post(
            'Agendasame/savedist',
            '{"unidade":"' +
            idunidade +
            '","espec":"' +
            idespec +
            '","prof":"' +
            idprof +
            '","dia":"' +
            iddia +
            '","total":"' +
            totalmes +
            '","vagas":"' +
            vagas +
            '","data":"' +
            arrDatas[i] +
            '","utilizadas":"' +
            vagasutilizadas +
            '"}',
            callbackMsg
            );
}

$(document).on('click', '#btn-salvar-distribuido', function (event) {
    event.preventDefault;
    var obj = $(this);
    var idunidade;
    var mes = $('#slct-mes-grd').val();
    var ano = $('#slct-ano-grd').val();
    var form = obj.closest('form');
    var formdata = $('#frm-dist :input[name=idunidade]').serializeArray();

    countDaysgetDates(mes, ano);

    $.each(formdata, function (key, value) {
        idunidade = value['value'];

        $('#vagas[data-idunidade]').each(function () {
            var totalmes = $('#total-' + idunidade).html();
            var idespec = $(this).data('idespec');
            var idprof = $(this).data('idprof');
            var iddia = $(this).data('iddia');

            if ($(this).data('idunidade') == idunidade) {
                //console.log($(this).val());
                if ($(this).hasClass('seg')) {
                    for (var i = 0; i < datesseg.length; i++) {
                        postHelper(
                                idunidade,
                                idespec,
                                idprof,
                                iddia,
                                totalmes,
                                $(this).val(),
                                datesseg,
                                i,
                                $('#seg').html()
                                );
                    }
                }
                if ($(this).hasClass('ter')) {
                    for (var i = 0; i < datester.length; i++) {
                        postHelper(
                                idunidade,
                                idespec,
                                idprof,
                                iddia,
                                totalmes,
                                $(this).val(),
                                datester,
                                i,
                                $('#ter').html()
                                );
                    }
                }
                if ($(this).hasClass('qua')) {
                    for (var i = 0; i < datesqua.length; i++) {
                        postHelper(
                                idunidade,
                                idespec,
                                idprof,
                                iddia,
                                totalmes,
                                $(this).val(),
                                datesqua,
                                i,
                                $('#qua').html()
                                );
                    }
                }
                if ($(this).hasClass('qui')) {
                    for (var i = 0; i < datesqui.length; i++) {
                        postHelper(
                                idunidade,
                                idespec,
                                idprof,
                                iddia,
                                totalmes,
                                $(this).val(),
                                datesqui,
                                i,
                                $('#qui').html()
                                );
                    }
                }
                if ($(this).hasClass('sex')) {
                    for (var i = 0; i < datessex.length; i++) {
                        postHelper(
                                idunidade,
                                idespec,
                                idprof,
                                iddia,
                                totalmes,
                                $(this).val(),
                                datessex,
                                i,
                                $('#sex').html()
                                );
                    }
                }
                if ($(this).hasClass('sab')) {
                    for (var i = 0; i < datessab.length; i++) {
                        postHelper(
                                idunidade,
                                idespec,
                                idprof,
                                iddia,
                                totalmes,
                                $(this).val(),
                                datessab,
                                i,
                                $('#sab').html()
                                );
                    }
                }
                if ($(this).hasClass('dom')) {
                    for (var i = 0; i < datesdom.length; i++) {
                        postHelper(
                                idunidade,
                                idespec,
                                idprof,
                                iddia,
                                totalmes,
                                $(this).val(),
                                datesdom,
                                i,
                                $('#dom').html()
                                );
                    }
                }
            }
        });
    });
    messagesHandler(
            '{"action":"modal","content":"Salvo com sucesso!","typemsg":"success"}'
            );
    //console.log(datesseg,datester);
});

$(document).on('click', '#btn-send-pac', function (event) {
    event.preventDefault;
    var obj = $(this);
    var urlroot = obj.data('url');
    var id = obj.data('id');
    var nasc = $('#inp-dtnasc-' + id).val();
    var nome = $('#inp-paciente-' + id).val();

    if (
            $('#inp-dtnasc-' + id).val().length < 10 ||
            $('#inp-paciente-' + id).val().length < 5
            ) {
        messagesHandler(
                '{"action":"modal","content":"Verificar campos <strong>Nascimento e Nome</strong>!<br><br>Não podem ser vazios.","typemsg":"danger"}'
                );
    } else {
        strJSON =
                '{"id":"' +
                id +
                '","nasc":"' +
                nasc +
                '","nome":"' +
                nome +
                '","sts":"1"}';
        var JSONObject = JSON.parse(strJSON);

        $.ajax({
            type: 'POST',
            url: urlroot + 'Agendasame/updatevagaubs',
            data: JSONObject,
            success: function (result) {
                $('#btn-send-icon-' + id)
                        .removeClass('glyphicon glyphicon-cloud-upload')
                        .addClass('glyphicon glyphicon-ok');
                $('[name=btn-send-pac-' + id + ']')
                        .removeClass('btn btn-danger')
                        .addClass('btn btn-primary');
                $('#pac-status-' + id)
                        .html('ENVIADO...')
                        .removeClass('text-danger')
                        .addClass('text-primary');
            },
        }).done(function () {});
    }
});

function updatePacNullHelper(id) {
    $('#btn-send-icon-' + id)
            .removeClass('glyphicon glyphicon-ok')
            .addClass('glyphicon glyphicon-user');
    $('[name=btn-send-pac-' + id + ']')
            .removeClass('btn btn-primary')
            .addClass('btn btn-danger');
    $('#pac-status-' + id)
            .html('DISPONÍVEL')
            .addClass('text-danger');
}

function updatePacNull(id, urlroot) {
    var JSONObject = JSON.parse(
            '{"id":"' + id + '","nasc":"0000-00-00","nome":"","sts":"0"}'
            );

    $.ajax({
        type: 'POST',
        url: urlroot + 'Agendasame/updatevagaubs',
        data: JSONObject,
        success: function (result) {
            updatePacNullHelper(id);
        },
    }).done(function () {});
}

var nasc;
var nome;

$(document).on('focusin', 'input[name=inp-paciente]', function (event) {
    event.preventDefault;
    var obj = $(this);
    var id = obj.data('id');
    nome = obj.val() + id;
});

$(document).on('blur', 'input[name=inp-paciente]', function (event) {
    event.preventDefault;
    var obj = $(this);
    var id = obj.data('id');
    var urlroot = $('#btn-send-pac').data('url');

    if (nome !== obj.val() + id) {
        updatePacNullHelper(id);
    }

    if (obj.val() == '') {
        updatePacNull(id, urlroot);
    }
});

$(document).on('input', 'input[name=inp-paciente]', function (event) {
    event.preventDefault;
    var obj = $(this);
    var id = obj.data('id');

    updatePacNullHelper(id);
});

$(document).on('focusin', 'input[name=inp-dtnasc]', function (event) {
    event.preventDefault;
    var obj = $(this);
    var id = obj.data('id');

    nasc = obj.val() + id;
});

$(document).on('blur', 'input[name=inp-dtnasc]', function (event) {
    event.preventDefault;
    var obj = $(this);
    var id = obj.data('id');
    var urlroot = $('#btn-send-pac').data('url');

    if (nasc !== obj.val() + id) {
        updatePacNullHelper(id);
    }

    if (obj.val() == '//') {
        updatePacNull(id, urlroot);
    }
});

$(document).on('change', '#slct-unidade-ubs', function (event) {
    event.preventDefault;

    $('#slct-mes-ubs').val('');
    $('#vagas-ubs').fadeOut(600);
});

$(document).on('change', '#slct-unidade-patrimonio', function (event) {
    //event.preventDefault;
    var obj = $(this);
    var id = obj.val();


    populateSelect(GLOBAL_URL + 'Daopatrimonio/slctSalas/' + id, 'slct-salas', 'descricao');

});

$(document).on('change', '#slct-desfecho', function (event) {
    event.preventDefault;

    if ($(this).val() == 1) {
        $('#inp-tempo').prop('disabled', false);
        $('#slct-tempo').prop('disabled', false);
        $('#inp-data-retorno').prop('disabled', false);
    } else {
        $('#inp-tempo').prop('disabled', true);
        $('#slct-tempo').prop('disabled', true);
        $('#inp-data-retorno').prop('disabled', true);
        $('#inp-data-retorno').val('');
    }
});

$(document).on('change', '#slct-unidade,#slct-espec,#slct-prof', function (
        event
        ) {
    event.preventDefault;

    $('#slct-mes-perdas').val('');
    $('#perdas').fadeOut(600);
});

$(document).on('click', '#btn-agenda-pac', function (event) {
    event.preventDefault;
    var obj = $(this);
    var id = obj.data('id');

    var JSONObject = JSON.parse('{"id":"' + id + '","sts":"2"}');

    if (
            $('#inp-dtnasc-' + id).val().length < 10 ||
            $('#inp-paciente-' + id).val().length < 5
            ) {
        messagesHandler(
                '{"action":"modal","content":"Verificar campos <strong>Nascimento e Nome</strong>!<br><br>Não podem ser vazios.","typemsg":"danger"}'
                );
    } else {
        $.ajax({
            type: 'POST',
            url: GLOBAL_URL + 'Agendasame/updateAgendaUBS',
            data: JSONObject,
            success: function (result) {
                $('#btn-send-icon-' + id)
                        .removeClass('glyphicon glyphicon-ok')
                        .addClass('glyphicon glyphicon-saved');
                $('[name=btn-send-pac-' + id + ']')
                        .removeClass('btn btn-primary btn-danger')
                        .addClass('btn btn-success');
                $('#pac-status-' + id)
                        .html('AGENDADO')
                        .addClass('text-success');
                $('#inp-dtnasc-' + id).attr('disabled', 'disabled');
                $('#inp-paciente-' + id).attr('disabled', 'disabled');
                $('#btn-send-icon-' + id).attr('disabled', 'disabled');
                $('[name=btn-send-pac-' + id + ']').attr('disabled', 'disabled');
            },
        }).done(function () {});
    }
});

$(document).on('click', '#btn-del-agenda-pac', function (event) {
    event.preventDefault;
    var obj = $(this);
    var id = obj.data('id');

    var JSONObject = JSON.parse('{"id":"' + id + '","sts":"1"}');

    if (
            $('#inp-dtnasc-' + id).val().length < 10 ||
            $('#inp-paciente-' + id).val().length < 5
            ) {
        messagesHandler(
                '{"action":"modal","content":"Verificar campos <strong>Nascimento e Nome</strong>!<br><br>Não podem ser vazios.","typemsg":"danger"}'
                );
    } else {
        $.ajax({
            type: 'POST',
            url: GLOBAL_URL + 'Agendasame/updateAgendaUBS',
            data: JSONObject,
            success: function (result) {
                $('#btn-send-icon-' + id)
                        .removeClass('glyphicon glyphicon-saved')
                        .addClass('glyphicon glyphicon-ok');
                $('[name=btn-send-pac-' + id + ']')
                        .removeClass('btn btn-success btn-danger')
                        .addClass('btn btn-primary');
                $('#pac-status-' + id)
                        .html('ENVIADO')
                        .removeClass('text-success text-danger')
                        .addClass('text-primary');
                $('#inp-dtnasc-' + id).removeAttr('disabled');
                $('#inp-paciente-' + id).removeAttr('disabled');
                $('#btn-send-icon-' + id).removeAttr('disabled');
                $('[name=btn-send-pac-' + id + ']').removeAttr('disabled');
            },
        }).done(function () {});
    }
});

$(document).on('click', '#refresh-agendamentos-aguardando', function () {
    location.reload();
});

function newtab(url, params = null) {
    if (params != null) {
        window.open(GLOBAL_URL + url + '/' + eachParams(params), '_blank');
    } else {
        window.open(GLOBAL_URL + url, '_blank');
    }
    $body.removeClass('loading');
}

$(document).on('click', '#btn-imp-perdas', function () {
    var url = $(this).data('url');
    var params = $(this).data('params');

    if ($('#slct-mes-perdas').val() !== '') {
        newtab(url, params);
    } else {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>Mês </strong><br>Não pode ser vazio.","typemsg":"danger"}'
                );
    }
});

$(document).on('click', '#btn-imp-grade,#btn-imp-grade-resumo', function () {
    var url = $(this).data('url');
    var params = $(this).data('params');

    if ($('#slct-mes-grd').val() !== '') {
        newtab(url, params);
    } else {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>Mês </strong><br>Não pode ser vazio.","typemsg":"danger"}'
                );
    }
});

$(document).on('click', '#btn-menu-ubs', function () {
    var url = $(this).data('url');
    var params = $(this).data('params');

    newtab(url, params);
});

$(document).on('click', '#adm-link', function () {
    var url = $(this).data('url');
    var params = $(this).data('params');

    newtab(url, params);
});

$(document).on('click', '#btn-imp-inventario', function () {
    var url = $(this).data('url');
    var params = $(this).data('params');

    newtab(url, params);
});

$(document).on('changeDate', '#dtp-calendario-ame', function (event) {
    event.preventDefault;
    var data = $(this).datepicker('getFormattedDate');
    var input = $(this);

    $('#dash-agendas').load(
            GLOBAL_URL + 'Notificacoes/agendas/' + validateData(data, 'BR', 'EN')
            );
});

$('#inp-pac').on('change', function () {
    var id_pac = $('#id_pac').val();
    $('#search-nasc-pac').val('');
    $('#container-retornos').fadeIn('slow');
    $('#retornos-cadastrados').load(GLOBAL_URL + 'Retornos/get/' + id_pac);
});

$('#inp-consulta-pac').on('change', function () {
    var id_pac = $('#id_consulta_pac').val();
    $('#container-consultas').fadeIn('slow');
    $('#container-consultas').load(
            GLOBAL_URL + 'Retornos/getconsulta/' + id_pac + '/null/null'
            );
});

$('#inp-consulta-pac').on('click', function () {
    $('#inp-consulta-nasc').val('');
});

$('#inp-consulta-nasc').on('click', function () {
    $('#inp-consulta-pac').val('');
});

$(document).on('click', '#btn-submit-paciente, #btn-update-paciente', function () {
    var url = $(this).attr('href') + '/';
    var params = $(this).data('params');

    if ($('#inp-nome').val().length < 10) {

        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>Nome </strong><br>Não pode ser vazio ou conter somente o primeiro nome.","typemsg":"danger"}'
                );
        return;

    } else if ($('#inp-cad-dtnasc').val().length < 10) {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>Data de Nascimento </strong><br>Não pode ser vazio ou conter data inválida.","typemsg":"danger"}'
                );
        $('#inp-cad-dtnasc').focus();
        return;

    } else if ($('#inp-cpf').val().length < 14) {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>CPF </strong><br>Não pode ser vazio.","typemsg":"danger"}'
                );
        return;

    } else if ($('#inp-sexo').val() != 'M' && $('#inp-sexo').val() != 'F') {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>Sexo </strong><br>Não pode ser vazio.","typemsg":"danger"}'
                );
        $('#inp-sexo').focus();
        return;

    } else if ($('#inp-tel').val().length < 10) {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>Contato </strong><br>Não pode ser vazio.","typemsg":"danger"}'
                );
        $('#inp-tel').focus();
        return;

    } else if ($('#inp-mae').val().length < 4) {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>Nome da mãe </strong><br>Não pode ser vazio.","typemsg":"danger"}'
                );
        $('#inp-mae').focus();
        return;

    } else {
        $.ajax({
            type: 'GET',
            url: GLOBAL_URL + url + eachParams(params),
            success: function (result) {
                closeModal();
            },
        }).done(function () {
            messagesHandler(
                    '{"action":"modal","content":"Salvo com sucesso!","typemsg":"success"}'
                    );
            $('#inp-id').val('');
            $('#inp-nome').val('');
            $('#inp-cad-dtnasc').val('');
            $('#inp-pront').val('');
            $('#inp-cpf').val('');
            $('#inp-tel').val('');
            $('#inp-mae').val('');
            $('#inp-sexo').val('');
            
            $('#inp-cep').val('');
            $('#inp-tipo-log').val('');
            $('#inp-logra').val('');
            $('#inp-numero').val('');
            $('#inp-bairro').val('');
            $('#inp-complemento').val('');
            
            $('#btn-submit-paciente').show();
            $('#btn-update-paciente').hide();
            $('#btn-cancel-update').hide();
            $('#btn-delete-paciente').addClass('hidden');
        });
    }
}
);

$(document).on('click', '#btn-submit-paciente-modal', function () {
    var url = $(this).attr('href') + '/';
    var params = $(this).data('params');

    if ($('#inp-nome-modal').val().length < 4) {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>Nome </strong><br>Não pode ser vazio ou conter somente o primeiro nome.","typemsg":"danger"}'
                );
       //  $('#inp-nome-modal').focus();
        return;
    } else if ($('#inp-cad-dtnasc-modal').val().length < 10) {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>Data de Nascimento </strong><br>Não pode ser vazio ou conter data inválida.","typemsg":"danger"}'
                );
      //  $('#inp-cad-dtnasc-modal').focus();
        return;
    } else if ($('#inp-sexo-modal').val() !== 'M' && $('#inp-sexo-modal').val() !== 'F') {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>Sexo </strong><br>Não pode ser vazio.","typemsg":"danger"}'
                );
      //  $('#inp-sexo-modal').focus();
        return;
    }else {
        $.ajax({
            type: 'GET',
            url: GLOBAL_URL + url + eachParams(params),
            success: function (result) {
                closeModal();
            },
        }).done(function () {
            messagesHandler('{"action":"modal","content":"Salvo com sucesso!","typemsg":"success"}');
            $('#inp-nome').val('');
            $('#inp-cad-dtnasc').val('');    
            $('#inp-cad-dtnasc-modal').val('');
            $('#inp-nome-modal').val('');
            $('#inp-cpf-modal').val('');
            $('#inp-mae-modal').val('');
            $('#inp-tel-modal').val('');            
            $('#inp-cep-modal').val('');
            $('#inp-numero-modal').val('');           
        });
    }
}
);

$(document).on('shown.bs.modal', '.modal', function () {
    $('#inp-cad-dtnasc-modal').val($('#inp-cad-dtnasc').val());
    $('#inp-nome-modal').val($('#inp-nome').val());
});

$(document).on('click', '#btn-submit-servidor, #btn-update-servidor', function () {
    var url = $(this).attr('href') + '/';
    var params = $(this).data('params');

    if ($('#inp-nome-servidor').val().length < 3) {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>Nome </strong><br>Não pode ser vazio.","typemsg":"danger"}'
                );
        return;

    } else if ($('#inp-cpf-servidor').val().length < 14) {
        messagesHandler(
                '{"action":"modal","content":"Verificar campo <strong>CPF </strong><br>Não pode ser vazio.","typemsg":"danger"}'
                );
        return;

    } else {
        $.ajax({
            type: 'GET',
            url: GLOBAL_URL + url + eachParams(params),
            success: function () {
                closeModal();
            }
        }).done(function () {
            messagesHandler(
                    '{"action":"modal","content":"Salvo com sucesso!","typemsg":"success"}'
                    );
            $('#inp-id').val('');
            $('#inp-nome-servidor').val('');
            $('#inp-cpf-servidor').val('');
        });
    }
}
);

$(document).on('click', '#btn-submit-usuario, #btn-update-usuario', function () {
    var url = $(this).attr('href') + '/';
    var params = $(this).data('params');

    if ($('#inp-nome-usuario').val().length < 3) {
        messagesHandler('{"action":"modal","content":"Verificar campo <strong>Nome </strong><br>Não pode ser vazio.","typemsg":"danger"}');
        return;

    } else if ($('#inp-cpf-usuario').val().length < 11) {
        messagesHandler('{"action":"modal","content":"Verificar campo <strong>CPF </strong><br>Não pode ser vazio.","typemsg":"danger"}');
        return;

    } else if ($('#inp-pass-usuario').val().length < 6 || $('#inp-pass2-usuario').val().length < 6) {
        messagesHandler('{"action":"modal","content":"Verificar campo <strong>Senha </strong><br>Não pode ser vazio ou conter menos de 6 caracteres.","typemsg":"danger"}');
        return;

    } else if ($('#inp-pass-usuario').val() != $('#inp-pass2-usuario').val()) {
        messagesHandler('{"action":"modal","content":"Senhas não conferem!","typemsg":"danger"}');
        return;

    } else {
        $.ajax({
            type: 'GET',
            url: GLOBAL_URL + url + eachParams(params),
            success: function () {
                closeModal();
            }
        }).done(function () {
            messagesHandler(
                    '{"action":"modal","content":"Salvo com sucesso!","typemsg":"success"}'
                    );
            $('#inp-id').val('');
            $('#inp-nome-usuario').val('');
            $('#inp-cpf-usuario').val('');
            $('#inp-pass-usuario').val('');
            $('#inp-pass2-usuario').val('');
            $('#inp-cadastro-usuario').prop('checked', false);

            $('#btn-submit-usuario').show();
            $('#btn-update-usuario').hide();
            $('#btn-cancel-update-usuario').hide();
            $('#btn-delete-usuario').addClass('hidden');

        });
    }
}
);

$(document).on('click', '#btn-salvarEncaixe', function () {
    var url = $(this).attr('href') + '/';
    var params = $(this).data('params');

    if ($('#slct-espec-encaixe').val() == '') {
        messagesHandler('{"action":"modal","content":"Verificar campo <strong>Especialidade </strong><br>Não pode ser vazio.","typemsg":"danger"}');
        return;

    } else if ($('#slct-prof-encaixe').val() == '') {
        messagesHandler('{"action":"modal","content":"Verificar campo <strong>Profissional </strong><br>Não pode ser vazio.","typemsg":"danger"}');
        return;

    } else if ($('#dtp-data-encaixe').val() == '') {
        messagesHandler('{"action":"modal","content":"Selecione uma <strong>Data </strong><br>Não pode ser vazio.","typemsg":"danger"}');
        return;

    } else if ($('#qtd-encaixes').val() == '') {
        messagesHandler('{"action":"modal","content":"Verificar campo <strong>Qtd </strong><br>Não pode ser vazio.","typemsg":"danger"}');
        return;

    } else {
        $.ajax({
            type: 'GET',
            url: GLOBAL_URL + url + eachParams(params),
            success: function () {
                messagesHandler('{"action":"modal","content":"Salvo com sucesso!","typemsg":"success"}');
            }
        }).done(function () {
            setTimeout(function () {
                $('#container-encaixes').load(GLOBAL_URL + 'Agendasame/getEncaixes/' + $('#slct-espec-encaixe').val() + '/' + $('#slct-prof-encaixe').val() + '/' + validateData($('[name=dtp-data-encaixe]').val(), 'BR', 'EN') + '/' + validateData($('[name=dtp-data-encaixe]').val(), 'BR', 'EN'));
            }, 1000);
        });
    }
}
);

$(document).on('click', '#btn-update-retorno', function () {
    var url = $(this).data('url') + '/';
    var params = $(this).data('edit-params');

    $.ajax({
        type: 'GET',
        url: GLOBAL_URL + url + eachParams(params),
        success: function (result) {
            closeModal();
        },
    }).done(function () {
        messagesHandler(
                '{"action":"modal","content":"Alterado com sucesso!","typemsg":"success"}'
                );
    });
});

$(document).on('click', '#btn-save-aviso', function () {
    var url = $(this).attr('href') + '/';
    var params = $(this).data('params');

    if ($('#area-painel-mensagem').val().length > 4) {
        //console.log(eachParams(params));

        $.ajax({
            type: 'GET',
            url: GLOBAL_URL + url + eachParams(params),
            success: function (result) {
                messagesHandler(
                        '{"action":"modal","content":"Salvo com sucesso!","typemsg":"success"}'
                        );
            },
        }).done(function (result) {
            if (result) {
                setTimeout(function () {
                    location.reload();
                }, 1500);
            }
        });
    } else {
        messagesHandler(
                '{"action":"modal","content":"Digite sua mensagem!","typemsg":"danger"}'
                );
    }
});

$('#slct-aviso-sts').on('change', function () {
    setTimeout(function () {
        location.reload();
    }, 1500);
});

$('[name=link-confirm-new-message]').on('click', function () {
    var params = $(this).data('params');
    var id = $(this).data('id');

    $.ajax({
        type: 'GET',
        url: GLOBAL_URL + 'Notificacoes/confirmnewmessage/' + eachParams(params),
        success: function (result) {
            $('#link-confirm-new-message-' + id).hide();
            $('#container-message-' + id).html(
                    '<span class="label label-default"><i>Lida</i> <span id="message-checked" class="glyphicon glyphicon-ok"></span></span>'
                    );
        },
    }).done(function (result) {});
});

$(document).on('click', '[name=btn-select-paciente]', function () {
    var id = $(this).data('id');
    var params = $('#btn-select-paciente-' + id).data('params');

    $('#inp-id').val(id);
    $('#inp-nome').val(params['nome']);
    $('#inp-cad-dtnasc').val(validateData(params['dtnasc'], 'EN', 'BR'));
    $('#inp-pront').val(params['pront']);
    $('#inp-cpf').val(formatarCPF(params['cpf']));
    $('#inp-tel').val(params['contato']);
    $('#inp-mae').val(params['mae']);
    $('#inp-sexo').val(params['sexo']);

    if (params['idLogra'] != 0) {
        $('#inp-cep').val(params['cep']);

        const select = $('#inp-tipo-log');
        // procura o option com o mesmo texto e seleciona
        select.find('option').each(function () {
            if ($(this).text().toUpperCase() === params['tipo']) {
                $(this).prop('selected', true);
                return false; // interrompe o loop
            }
        });

        $('#inp-logra').val(params['logradouro']);
        $('#inp-bairro').val(params['bairro']);
        $('#inp-numero').val(params['numero']);
        $('#inp-complemento').val(params['complemento']);
    }

    $('#btn-submit-paciente').hide();
    $('#btn-update-paciente').show();
    $('#btn-cancel-update').show();
    $('#btn-delete-paciente').removeClass('hidden');
    closeModal();
});

$(document).on('click', '[name=btn-select-servidor]', function () {
    var id = $(this).data('id');
    var params = $('#btn-select-servidor-' + id).data('params');

    $('#inp-id').val(id);
    $('#inp-nome-servidor').val(params['nome']);
    $('#inp-cpf-servidor').val(params['cpf']);

    $('#btn-submit-servidor').hide();
    $('#btn-update-servidor').show();
    $('#btn-cancel-update-servidor').show();
    $('#btn-delete-servidor').removeClass('hidden');
    closeModal();
});

$(document).on('click', '[name=btn-select-usuario]', function () {
    var id = $(this).data('id');
    var params = $('#btn-select-usuario-' + id).data('params');

    $('#inp-id').val(id);
    $('#inp-nome-usuario').val(params['nome']);
    $('#inp-cpf-usuario').val(params['cpf']);
    if (params['cad'] == 1) {
        $('#inp-cadastro-usuario').prop('checked', true);
    } else {
        $('#inp-cadastro-usuario').prop('checked', false);
    }

    $('#inp-pass-usuario').val(params['pass']);
    $('#inp-pass2-usuario').val(params['pass']);

    $('#btn-submit-usuario').hide();
    $('#btn-update-usuario').show();
    $('#btn-cancel-update-usuario').show();
    $('#btn-delete-usuario').removeClass('hidden');
    closeModal();
});

$(document).on('click', '#btn-cancel-update, #btn-cancel-update-servidor, #btn-cancel-update-usuario', function () {
    $('#inp-id').val('');
    $('#inp-nome').val('');
    $('#inp-cpf').val('');
    $('#inp-mae').val('');
    $('#inp-sexo').val('');
    $('#inp-cep').val('');
    $('#inp-tipo-log').val('');
    $('#inp-logra').val('');
    $('#inp-numero').val('');
    $('#inp-bairro').val('');
    $('#inp-complemento').val('');

    $('#inp-nome-servidor').val('');
    $('#inp-cpf-servidor').val('');

    $('#inp-nome-usuario').val('');
    $('#inp-cpf-usuario').val('');
    $('#inp-pass-usuario').val('');
    $('#inp-pass2-usuario').val('');
    $('#inp-cadastro-usuario').prop('checked', false);

    $('#inp-cad-dtnasc').val('');
    $('#inp-pront').val('');
    $('#inp-tel').val('');
    $('#btn-submit-paciente').show();
    $('#btn-update-paciente').hide();
    $('#btn-cancel-update').hide();
    $('#btn-delete-paciente').addClass('hidden');

    $('#btn-submit-servidor').show();
    $('#btn-update-servidor').hide();
    $('#btn-cancel-update-servidor').hide();
    $('#btn-delete-servidor').addClass('hidden');

    $('#btn-submit-usuario').show();
    $('#btn-update-usuario').hide();
    $('#btn-cancel-update-usuario').hide();
    $('#btn-delete-usuario').addClass('hidden');
});

$(document).on('click', '#btn-show-usuario-pass', function () {
    if ($('#inp-pass-usuario').attr('type') == 'password') {
        $('#inp-pass-usuario').attr('type', 'text');
        $('#span-pass').removeClass('glyphicon glyphicon-eye-open').addClass('glyphicon glyphicon-eye-close');
    } else {
        $('#inp-pass-usuario').attr('type', 'password');
        $('#span-pass').removeClass('glyphicon glyphicon-eye-close').addClass('glyphicon glyphicon-eye-open');
    }
});

$(document).on('click', '#btn-show-usuario-pass2', function () {
    if ($('#inp-pass2-usuario').attr('type') == 'password') {
        $('#inp-pass2-usuario').attr('type', 'text');
        $('#span-pass2').removeClass('glyphicon glyphicon-eye-open').addClass('glyphicon glyphicon-eye-close');
    } else {
        $('#inp-pass2-usuario').attr('type', 'password');
        $('#span-pass2').removeClass('glyphicon glyphicon-eye-close').addClass('glyphicon glyphicon-eye-open');
    }
});

$(document).on('click', '#btn-save-retorno', function (event) {
    event.preventDefault();
    let data = $('#inp-data-consulta').val();
    let pac = $('#inp-pac').val();
    let prof = $('#slct-prof').val();

    if (pac.length < 3) {
        messagesHandler(
                '{"action":"modal","content":"Selecione um Paciente!","typemsg":"danger"}'
                );
        return;
    } else if (data.length < 10) {
        messagesHandler(
                '{"action":"modal","content":"Digite ou selecione uma data!","typemsg":"danger"}'
                );
        return;
    } else if (prof == '') {
        messagesHandler(
                '{"action":"modal","content":"Selecione um Profissional!","typemsg":"danger"}'
                );
        return;
    } else {
        messagesHandler(
                '{"action":"modal","content":"Cadastrado com sucesso!","typemsg":"success"}'
                );
    }
});

$(document).on('click', '#btn-save-patrimonio', function () {
    setTimeout(function () {
        $('#inp-pat').val('');
        $('#inp-qtd').val(1);
    }, 1500);
});

$(document).on('change', '#slct-espec-encaixe', function (
        event
        ) {
    event.preventDefault;
    let url = $(this).attr('href');
    let idEspec = $(this).val();

    populateSelect(GLOBAL_URL + url + '/' + idEspec, 'slct-prof-encaixe', 'nome');
});

$(document).on('change', '#slct-oci-proced', function (
        event
        ) {
    event.preventDefault;
    let url = $(this).attr('href');
    let idOCI = $(this).val();

    populateSelect(GLOBAL_URL + url + '/' + idOCI, 'slct-oci-cid', 'descricao');
    populateSelect(GLOBAL_URL + url + '/' + idOCI, 'slct-oci-cid-sec', 'descricao');
    populateSelect(GLOBAL_URL + 'OCI/getProcedSec/' + idOCI, 'slct-oci-proced-sec', 'descricao');
});

$(document).on('click', '#btn-add-proced-sec', function () {
    var url = $(this).attr('href') + '/';
    var params = $(this).data('params');

    if ($('#inp-id').val() == '') {
        messagesHandler('{"action":"modal","content":"Selecione um <strong>Paciente </strong>","typemsg":"danger"}');
        return;

    } else if ($('#dtp-data-oci').val() == '') {
        messagesHandler('{"action":"modal","content":"Verificar campo <strong>Data da OCI </strong>","typemsg":"danger"}');
        return;

    } else if ($('#slct-oci-proced').val() == '') {
        messagesHandler('{"action":"modal","content":"Selecione um  <strong>Procedimento </strong>","typemsg":"danger"}');
        return;

    } else if ($('#slct-oci-proced-sec').val() == '') {
        messagesHandler('{"action":"modal","content":"Selecione um <strong>Procedimento Secundário </strong>","typemsg":"danger"}');
        return;

    } else {
        $.ajax({
            type: 'GET',
            url: GLOBAL_URL + url + eachParams(params),
            success: function () {
                // messagesHandler('{"action":"modal","content":"Salvo com sucesso!","typemsg":"success"}');
            }
        }).done(function () {
            setTimeout(function () {
                $('#proceds-secs').load(GLOBAL_URL + 'OCI/loadProcedSec/' + validateData($('[name=dtp-data-oci]').val(), 'BR', 'EN') + '/' + $('#inp-id').val() + '/' + $('#inp-medico').val());
            }, 1);
        });
    }
}
);

$(document).on('click', '#btn-add-oci', function () {
    var url = $(this).attr('href') + '/';
    var params = $(this).data('params');

    if ($('#inp-cad-dtnasc').val() == '') {
        messagesHandler('{"action":"modal","content":"Verificar campo <strong>Nascimento </strong>","typemsg":"danger"}');
        return;

    } else if ($('#inp-nome').val() == '') {
        messagesHandler('{"action":"modal","content":"Verificar campo <strong>Nome </strong>","typemsg":"danger"}');
        return;

    } else if ($('#dtp-data-oci').val() == '') {
        messagesHandler('{"action":"modal","content":"Verificar campo <strong>Data da OCI </strong>","typemsg":"danger"}');
        return;

    } else if ($('#slct-oci-proced').val() == '') {
        messagesHandler('{"action":"modal","content":"Selecione um  <strong>Procedimento </strong>","typemsg":"danger"}');
        return;

    } else if ($('#slct-oci-cid').val() == '') {
        messagesHandler('{"action":"modal","content":"Selecione um <strong>CID </strong>","typemsg":"danger"}');
        return;

    } else {
        $.ajax({
            type: 'GET',
            url: GLOBAL_URL + url + eachParams(params),
            success: function () {
                // messagesHandler('{"action":"modal","content":"Salvo com sucesso!","typemsg":"success"}');
            }
        }).done(function () {
            $('#proceds-secs').html('');
            setTimeout(function () {
                $('#container-fila-oci').load(GLOBAL_URL + 'OCI/loadFila/' + validateData($('[name=dtp-data-oci]').val(), 'BR', 'EN') + '/' + $('#inp-medico').val() + '/' + $('#slct-oci-proced').val());
            }, 1);
        });
    }
}
);

function getTodayDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0'); // Mês (0-11) + 1
    const day = String(today.getDate()).padStart(2, '0'); // Dia
    return `${day}-${month}-${year}`; // Formato padrão ISO (YYYY-MM-DD)
}

function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf))
        return false;

    let soma = 0;
    for (let i = 0; i < 9; i++)
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    let resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11)
        resto = 0;
    if (resto !== parseInt(cpf.charAt(9)))
        return false;

    soma = 0;
    for (let i = 0; i < 10; i++)
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11)
        resto = 0;

    return resto === parseInt(cpf.charAt(10));
}

function searchByCEP(inputCEP) {
    var params = $(inputCEP).data('params');

    $.ajax({
        type: 'GET',
        url: GLOBAL_URL + 'Pacientes/cep/' + eachParams(params),
        dataType: 'json',
        success: function (data) {
            // Preenche os campos automaticamente
            if (data.length > 0) {
                $(inputCEP).val(data[0].cep);

                // recebe tipo como texto (ex: "RUA", "AVENIDA")
                const tipoRecebido = data[0].tipo.toUpperCase();
                const select = $('#inp-tipo-log');

                // procura o option com o mesmo texto e seleciona
                select.find('option').each(function () {
                    if ($(this).text().toUpperCase() === tipoRecebido) {
                        $(this).prop('selected', true);
                        return false; // interrompe o loop
                    }
                });
                // dispara evento change, se houver listeners
                select.trigger('change');
                $('#inp-id-logra').val(data[0].id);
                $('#inp-id-logra-modal').val(data[0].id);
                $('#inp-logra').val(data[0].logradouro);
                $('#inp-bairro').val(data[0].bairro);
            } else {
                showPopupDynamic(inputCEP, 'CEP inválido');
                $(inputCEP).val('');
            }
        },
        error: function (xhr, status, error) {
            console.error('Erro:', status, error);
        }
    }).done(function (result) {
    });
}

let timeoutBusca = null;

function searchByLogra(inputLogradouro) {
    clearTimeout(timeoutBusca);
    var params = $(inputLogradouro).data('params');

    if (params.length < 3) {
        $('#resultados').hide();
        return;
    }

    timeoutBusca = setTimeout(() => {
        $.ajax({
            type: 'GET',
            url: GLOBAL_URL + 'Pacientes/logradouro/' + eachParams(params),
            dataType: 'json',
            success: function (data) {
                if (!data || data.length === 0) {
                    $('#resultados').hide();
                    return;
                }

                let html = `
                                <table style="width:100%; border-collapse:collapse;">
                                    <thead>
                                        <tr style="background:#eee; text-align:left;">
                                            <th style="padding:4px; width:80px;">CEP</th>
                                            <th style="padding:4px; width:80px;">Tipo</th>
                                            <th style="padding:4px; width:350px;">Logradouro</th>
                                            <th style="padding:4px;">Bairro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

                data.forEach((item) => {
                    html += `
                                    <tr class="linha-endereco" style="cursor:pointer;" 
                                        data-id="${item.id}" 
                                        data-cep="${item.cep}" 
                                        data-tipo="${item.tipo}" 
                                        data-logra="${item.logradouro}" 
                                        data-bairro="${item.bairro}">
                                        <td style="padding:4px;">${item.cep}</td>
                                        <td style="padding:4px;">${item.tipo}</td>
                                        <td style="padding:4px;">${item.logradouro}</td>
                                        <td style="padding:4px;">${item.bairro}</td>
                                    </tr>
                                `;
                });

                html += '</tbody></table>';
                $('#resultados').html(html);

                const input = $('#inp-logra');
                const pos = input.offset();

                $('#resultados').show();
            },
            error: function (xhr, status, error) {
                console.error('Erro:', status, error);
            }
        }).done(function (result) {
        });
    }, 400); // delay de 400ms entre digitações
}

function searchByBairro(inputBairro) {
    clearTimeout(timeoutBusca);
    var params = $(inputBairro).data('params');

    if (params.length < 3) {
        $('#resultados').hide();
        return;
    }

    timeoutBusca = setTimeout(() => {
        $.ajax({
            type: 'GET',
            url: GLOBAL_URL + 'Pacientes/bairro/' + eachParams(params),
            dataType: 'json',
            success: function (data) {
                if (!data || data.length === 0) {
                    $('#resultados-bairro').hide();
                    return;
                }

                let html = `
                                <table style="width:100%; border-collapse:collapse;">
                                    <thead>
                                        <tr style="background:#eee; text-align:left;">
                                            <th style="padding:4px; width:80px;">CEP</th>
                                            <th style="padding:4px; width:80px;">Tipo</th>
                                            <th style="padding:4px; width:350px;">Logradouro</th>
                                            <th style="padding:4px;">Bairro</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

                data.forEach((item) => {
                    html += `
                                    <tr class="linha-endereco" style="cursor:pointer;" 
                                        data-id="${item.id}" 
                                        data-cep="${item.cep}" 
                                        data-tipo="${item.tipo}" 
                                        data-logra="${item.logradouro}" 
                                        data-bairro="${item.bairro}">
                                        <td style="padding:4px;">${item.cep}</td>
                                        <td style="padding:4px;">${item.tipo}</td>
                                        <td style="padding:4px;">${item.logradouro}</td>
                                        <td style="padding:4px;">${item.bairro}</td>
                                    </tr>
                                `;
                });

                html += '</tbody></table>';
                $('#resultados-bairro').html(html);

                const input = $('#inp-bairro');
                const pos = input.offset();

                $('#resultados-bairro').show();
            },
            error: function (xhr, status, error) {
                console.error('Erro:', status, error);
            }
        }).done(function (result) {
        });
    }, 400); // delay de 400ms entre digitações
}

$(document).on('click', '.linha-endereco', function () {
    const id = $(this).data('id');
    const cep = $(this).data('cep');
    const tipo = $(this).data('tipo');
    const logra = $(this).data('logra');
    const bairro = $(this).data('bairro');

    $('#inp-id-logra').val(id);
    $('#inp-cep').val(cep);
    $('#inp-logra').val(logra);
    $('#inp-bairro').val(bairro);

    // seleciona o tipo no select
    $('#inp-tipo-log option').each(function () {
        if ($(this).text().toUpperCase() === tipo.toUpperCase()) {
            $(this).prop('selected', true);
            return false;
        }
    });
    $('#resultados').hide();
    $('#resultados-bairro').hide();
});

// Fecha a lista se clicar fora
$(document).on('click', function (e) {
    if (!$(e.target).closest('#resultados, #inp-logra,#inp-bairro').length) {
        $('#resultados').hide();
        $('#resultados-bairro').hide();
    }
});

$(document).on('input', '#inp-cep-modal, #inp-cep', function () {
    let cep = $(this).val().replace(/\D/g, '');
    cep = cep.substring(0, 8);
    if (cep.length > 5) {
        cep = cep.substring(0, 5) + '-' + cep.substring(5);
    }
    $(this).val(cep);
});

$('#inp-cep').on('blur', function () {
    searchByCEP(this);
    $('#resultados').hide();
    $('#resultados-bairro').hide();
});

$('#inp-cep').on('keypress', function (e) {
    if (e.which === 13) { // código da tecla Enter
        e.preventDefault(); // evita enviar form
        searchByCEP(this);
        $('#resultados').hide();
        $('#resultados-bairro').hide();
    }
});

$(document).on('blur', '#inp-cep-modal', function (e) {
    if (this.value.length == 9) {
        searchByCEP(this);
    }
});

$('#inp-logra').on('keyup', function (e) {
    searchByLogra(this);
    $('#resultados-bairro').hide();
});

$('#inp-bairro').on('keyup', function (e) {
    searchByBairro(this);
    $('#resultados').hide();
});

$(document).on('change', '#slct-espec-whatsapp', function (
        event
        ) {
    event.preventDefault;
    let espec = $(this).val();
});

$(document).on('change', '#slct-oci-proced', function (event) {
    event.preventDefault;
    setTimeout(function () {
        $('#container-fila-oci').load(GLOBAL_URL + 'OCI/loadFila/' + validateData($('[name=dtp-data-oci]').val(), 'BR', 'EN') + '/' + $('#inp-medico').val() + '/' + $('#slct-oci-proced').val());
    }, 1);
    if ($(this).val() == 5) {
        $("#slct-oci-proced-sec").prop("disabled", false);
        $("#inp-qtd-proced-sec").prop("disabled", false);
        $("#btn-add-proced-sec").prop("disabled", false);
    } else {
        $("#slct-oci-proced-sec").prop("disabled", true);
        $("#inp-qtd-proced-sec").prop("disabled", true);
        $("#btn-add-proced-sec").prop("disabled", true);
    }
});

$(document).on('change', '#dtp-data-oci', function (event) {
    event.preventDefault;
    setTimeout(function () {
        $('#container-fila-oci').load(GLOBAL_URL + 'OCI/loadFila/' + validateData($('[name=dtp-data-oci]').val(), 'BR', 'EN') + '/' + $('#inp-medico').val() + '/' + $('#slct-oci-proced').val());
    }, 1);
});

$(document).on('click', '#btn-show-paciente-form', function (e) {
    e.preventDefault;
    $('#hidden-cad-pac').toggle();
});

$(document).on('input', ' #inp-cpf-modal', function () {
    const cpf = $(this).val();

    if (cpf.length == 14) {
        if (!validarCPF(cpf)) {
            showPopupDynamic(this, 'CPF inválido');
            $(this).val('');
        } 
    }
});

$(document).on('input', '#inp-cpf', function () {
    const cpf = $(this).val();

    if (cpf.length == 14) {
        if (!validarCPF(cpf)) {
            showPopupDynamic(this, 'CPF inválido');
            $(this).val('');
        } else {
             getPacienteData(cpf);
        }
    }
});

function getPacienteData(cpf){
    var cpfnumebers = cpf.replace(/\D/g, '');
     ajax('Pacientes/get/' + cpfnumebers).done(function(result){  
        if (!result || result.length === 0) { 
            return;
        }
            var id = (result[0].id); 
            var nasc = validateData((result[0].dtnasc),'EN','BR');    
            var nome = (result[0].nome);          
            var cpf = (result[0].cpf);    
            var mae = (result[0].mae);  
            var sexo = (result[0].sexo);    
            var tel = (result[0].tel);  
            var CEP = (result[0].CEP);    
            var numero = (result[0].numero);  
            var complemento = (result[0].complemento);    
            var pront = (result[0].pront);  
            var sus = (result[0].sus);    
            var obs = (result[0].obs);  
            
            $('#inp-id').val(id);
            $('#inp-cad-dtnasc').val(nasc);
            $('#inp-nome').val(nome);
            $('#inp-mae').val(mae);
            $('#inp-sexo').val(sexo);
            $('#inp-tel').val(tel);
            $('#inp-pront').val(pront);            
            $('#inp-cep').val(CEP);
             searchByCEP($('#inp-cep'));
            $('#inp-numero').val(numero);
            $('#inp-complemento').val(complemento);
           // $('#inp-cad-dtnasc').val();
           // $('#inp-cad-dtnasc').val();
        
    }).fail(function(xhr){
        console.log(xhr.responseText);     
    });
}

function CartaoSus() {
    var id = $('#inp-id').val();
    var cpf = $('#inp-cpf').val().replace(/\D/g, '');
    var Inpsexo = $('#inp-sexo').val();

    if (cpf.length < 11) {
        messagesHandler('{"action":"modal","content":"Digite o <strong>CPF </strong>","typemsg":"danger"}');
        $('#inp-cpf').focus();
        return;
    } 
    if (id.length == 0 && cpf.length < 11) {
        messagesHandler('{"action":"modal","content":"Selecione um <strong>Paciente </strong>","typemsg":"danger"}');
        return;
    }
    if (Inpsexo != 'M' && Inpsexo != 'F') {
        messagesHandler('{"action":"modal","content":"Selecione o <strong>Sexo </strong>","typemsg":"danger"}');
        $('#inp-sexo').focus();
        return;
    }
   //ajax('Pacientes/cadsus/' + cpf).done(function(paciente){       
       // window.open('http://localhost:8080/sus?cpf=' + encodeURIComponent(cpf) + '&sexo=' + encodeURIComponent(Inpsexo), '_blank');
  // });   
  
   window.open('http://' + window.location.hostname + ':8080/sus?cpf=' + encodeURIComponent(cpf) + '&sexo=' + encodeURIComponent(Inpsexo), '_blank');
      
}