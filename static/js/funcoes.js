
$(function(){
    /*
     * Máscaras
     */
    /*
     * Ações automatizadas
     */
    
    //Focus no primeiro input disponível
    $(':input:enabled:visible:first').focus();

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
});


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
