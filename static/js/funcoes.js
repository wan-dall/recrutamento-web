$(function(){
    /*
     * Máscaras
     */
    /*
     * Ações automatizadas
     */

    //remove autocomplete dos campos;
   $(':input').each(function(){
       $(this).attr('autocomplete', 'off');
       //$(this).attr('title', 'off');
   });
    //desabilita voltar com backspace
    $(document).on("keydown", function (e) {
        if (e.which === 8 && !$(e.target).is("input, textarea") || $(e.target).is("input[readonly], input[disabled]")) {
            e.preventDefault();
        }
    });

    $('.btn_voltar').on('click keypress', function(){
        //carregandoOn();
    	var destino = $(this).attr('destino');
    	voltar(destino);
    });

    $('.btn_voltar_aviso').on('click keypress', function(){
        if (confirm('Deseja mesmo sair desta página? Todo o conteúdo não salvo será perdido.')){
            //carregandoOn();
            var destino = $(this).attr('destino');
            voltar(destino);
        }
    });

    $('.btn_fechar').on('click keypress', function(){
        window.close();
    });

    $('.btn_fechar_aviso').on('click keypress', function(){
        if (confirm('Deseja fechar esta janela?\n\nOs dados não salvos serão perdidos.')){
            window.close();
        }
    });

    $('.btn_salvar').on('click keypress', function(){
        var acao = $(this).attr('acao');
        var destino = $(this).attr('destino');
        var $botao = $(this);
        if (acao){
            enviaForm3(0, acao, $(this), function(data){
                if (isEmpty(data)){
                    if (destino){
                        document.location.href = destino;
                    } else {
                        document.location.reload();
                    }
                } else {
                    $('#flash').html(data);
                    $botao.attr('disabled', false);
                }
            });
        }
    });

    $('[data-toggle="tooltip"]').tooltip();
    $('#menu_bottom').on('click', '.encolhe_menu', function(){
        $(this).removeClass('encolhe_menu').addClass('estica_menu');
        $(this).html('>');
        $('.menu').removeClass('col-md-2').addClass('col-md-1');
        $('.conteudo').removeClass('col-md-8 col-md-offset-2').addClass('col-md-9 col-md-offset-1');
    });
    $('#menu_bottom').on('click', '.estica_menu', function(){
        $(this).removeClass('estica_menu').addClass('encolhe_menu');
        $(this).html('<');
        $('.menu').removeClass('col-md-1').addClass('col-md-2');
        $('.conteudo').removeClass('col-md-9 col-md-offset-1').addClass('col-md-8 col-md-offset-2');
    });

    $('#topo').on("click", ".exibe_modal", function(e) {
        e.preventDefault();
        var destino = $(this).attr("href");
        var busca = $(this).attr("busca");
        $(this).attr("busca", '');
        //Adiciona parametros extras de configurações da busca
        if (!isEmpty(busca)) {
            destino += (destino.indexOf('?') > 0 ? '&' : '?') + 'busca=' + busca;
        }
        console.log(destino);
        var titulo = $(this).attr("titulo");
        var tamanho = $(this).attr("tamanho");
        var $modal = $("#modal_template");
        var modal_body = $(".modal-body");
        var modal_titulo = $("#modal_titulo");

        $.ajax({
            url: destino,
            method: 'GET',
            timeout: 25000,
            error: function (e) {
                $modal.modal('hide');
                alertUI("Erro ao tentar buscar dados para modal.");
            },
            beforeSend: function () {
                carregandoOn();
            }
        }).done(function (dados) {
            modal_body.empty();
            modal_titulo.html(titulo);
            modal_body.html(dados);
            console.log(dados)
            //reseta tamanho do modal
            $(".modal-dialog").attr("class", "modal-dialog");
            if (!isEmpty(tamanho)) {
                $(".modal-dialog").addClass(tamanho);
            }
            $modal.modal('show');
            $modal.on("shown.bs.modal", function () {
                $('.input_busca').focus().select();
            });
            carregandoOff();
        })
    })

    $(document).on("click keypress", ".fechar_modal", function () {
        $("#modal_template").modal('hide');
    })
});

function voltar(pag){
    if (pag){
        location.href = pag;
    } else {
        history.back();
    }
}

function modal_confirm(texto, titulo, tamanho){
    var $modal = $("#modal_template");
    var modal_body = $(".modal-body");
    var modal_titulo = $("#modal_titulo");

    modal_body.empty();
    modal_titulo.html(titulo);
    var conteudo = '<p>' + texto + '</p>';
    conteudo += '<p class="botoes">';
    conteudo +=     '<button type="button" id="yes" class="btn btn-primary">ok</button>';
    conteudo +=     '<button type="button" id="no" class="btn btn-default">Cancelar</button>';
    conteudo += '</p>';

    modal_body.html(conteudo);
    $('#yes').on('click', function(){
        //$("#modal_template").modal('hide');
    });
    $('#no').on('click', function(){
        $("#modal_template").modal('hide');
    });

    //reseta tamanho do modal
    $(".modal-dialog").attr("class", "modal-dialog");
    if (!isEmpty(tamanho)){
        $(".modal-dialog").addClass(tamanho);
    }
    $modal.modal('show');
    $modal.on("shown.bs.modal", function(){
        $('#no').focus();
    });
    carregandoOff();
}

function obrigatorios(nm_form) {
    for (var i = 0; i < nm_form.length; i++) {
        o = nm_form[i];
        o.style.border = '1px solid #000';
        //o.style.border = 'inherit';
        if ((o.getAttribute('obrigatorio') !== null) && (o.value.trim().length === 0) && (o.disabled === false)) {
            o.style.border = '1px solid red';
            alert('O campo "' + o.getAttribute('legenda').toUpperCase() + '" deve ser preenchido');
            if ((o.getAttribute('alvo') !== null) && (o.getAttribute('alvo').trim() !== '')) {
                document.getElementById(o.getAttribute('alvo')).focus();
            } else {
                o.focus();
            }
            return false;
        }
    }
    return true;
}

function retornoErro(json, botao, callback) {
    var retorno = false;
    console.log(json);
    if (json.erro.length > 0) {
        cErro = "Programa '" + json.info.origem + "': <br /><b>";
        for (e = 0; e < json.erro.length; e++) {
            cErro += ' - ' + json.erro[e].descricao + "<br />";
        }
        cErro += '</b>'
        console.log("Erro: " + cErro);
        //chama callback para controle personalizado na exibicao do erro;
        if (callback && typeof (callback) === "function") {
            callback(cErro);
        } else {
            //TODO: remover no futuro.
            try {
                esconde('carregando');
            } catch (e) {
            }
            alertUI(cErro);
            botao.attr('disabled', false);
            //TODO: remover no futuro.
            try {
                esconde('carregando');
            } catch (e) {
            }
        }
        retorno = true;
    }
    console.log('retorno: ' + retorno);
    return retorno;
}

function enviaForm3(formNum_Data, action, botao, callback, callbackErro, valores) {
    switch (typeof (formNum_Data)) {
        case 'string':
        case 'number':
            var form = document.forms[formNum_Data];
            //var dados = $(form).serialize();
            var dados = new FormData($('form')[formNum_Data]);
            //})
            var lValidaForm = validaForm(form);
            break;
        case 'object':
            var form = document.forms[0];
            //var dados = formNum_Data;
            var dados = new FormData();
            for (chave in formNum_Data) {
                dados.append(chave, formNum_Data[chave]);
            }
            var lValidaForm = true;
            //carregandoOn();
            break;
    }
    console.log(dados);

    //carregandoOn();
    if (lValidaForm) {
        $.ajax({
            url: action,
            method: "POST",
            data: dados,
            //para upload
            contentType: false,
            processData: false,
            context: this,
            timeout: 30000,
            //dataType: 'json',
            error: function (e) {
                //alertUI("Erro ao tentar enviar formulario: " + e.statusText + "<br /><br />Por favor, tente novamente em alguns segundos.");
                alert("Erro ao tentar enviar formulario: " + e.statusText + "<br /><br />Por favor, tente novamente em alguns segundos.");
                if (botao) {
                    botao.attr('disabled', false);
                }
            },
            beforeSend: function () {
                //carregandoOn();
                if (botao) {
                    botao.attr('disabled', true);
                }
            },
            complete: function () {
                //carregandoOff();
                //try {
                //	esconde('carregando');
                //} catch(e){}
                //botao.attr('disabled', false);
            }
        }).done(function (dados) {
            console.log('done');
            //var dados = $.parseJSON(dados.replace(/(\r|\n)/gm, ""));
            /*
            if (!retornoErro(dados, botao, callbackErro)) {
                console.log('retonro ok');
                if (callback && typeof (callback) === "function") {
                    console.log(dados);
                    callback(dados.sucesso.cod, dados.sucesso.msg, botao, dados.info.origem, dados);
                }
            }
            */
           if (callback && typeof (callback) === "function") {
                console.log(dados);
                callback(dados);
            }
        });
    }
}

function validaForm(form) {
    if (obrigatorios(form)) {
        try {
            console.log('tenta carregando');
            mostra('carregando');
        } catch (e) {

            console.log('carregando2');
            //carregandoOn();
        }
        //carregandoOn();
        return true;
    } else {
        try {
            esconde('carregando');
        } catch (e) {
            carregandoOff();
        }
        return false;
    }
}

function isEmpty(mixed_var) {
    var undef, key, i, len;
    var emptyValues = [undef, null, false, ""];

    for (i = 0, len = emptyValues.length; i < len; i++) {
        if (mixed_var === emptyValues[i]) {
            return true;
        }
    }
    if (typeof mixed_var === "object") {
        for (key in mixed_var) {
            // TODO: should we check for own properties only?
            //if (mixed_var.hasOwnProperty(key)) {
            return false;
            //}
        }
        return true;
    }
    return false;
}

function replaceAll(string, token, newtoken) {
    while (string.indexOf(token) !== -1) {
        string = string.replace(token, newtoken);
    }
    return string;
}

function scrollTo(id) {
    $('html, body').animate({scrollTop: $(id).offset().top}, 'fast');
}
