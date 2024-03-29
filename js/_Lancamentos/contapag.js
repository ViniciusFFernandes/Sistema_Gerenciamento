$(document).ready(function(){
    $("#ctpg_vlr_bruto").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
    $("#ctpg_vlr_juros").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
    $("#ctpg_porc_juros").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
    $("#ctpg_vlr_desconto").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
    $("#ctpg_porc_desconto").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#vlr_pagamento").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#vlr_multa").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#vlr_desconto").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $('#idcontapag').on('keydown', function (e) {
        if (e.keyCode === 13) {
            abreConta();
        }
    });

    $('#pesquisa').on('keydown', function(event) {
        if (event.keyCode == 13) { // Código da tecla "Enter" é 13
        buscaCadastro('contapag_grava.php');
        }
    });
    
 });

function testaDados(operacao){
    if($("#ctpg_vencimento").val() == ''){
        alertaPequeno("Selecione o vencimento!");
        $("#ctpg_vencimento").focus();
        return;
    }
    if ($("#ctpg_idcliente").val() <= 0){
        alertaPequeno("Informe o cliente!");
        $("#ctpg_cliente").focus();
        return;
    }
    if (toFloat($("#ctpg_vlr_bruto").val()) <= 0 || $("#ctpg_vlr_bruto").val() == ''){
        alertaPequeno("Informe o valor da conta!");
        $("#ctpg_cliente").focus();
        return;
    }
    chamaGravar(operacao);
}

function carregaComboBoxCC(tipo = ""){
    $("#comboBoxCC" + tipo).html("<br><img src='../icones/carregando.gif' width='20px'>");
    //
    var idbancos = $("#ctpg_idbancos" + tipo).val();
    if(idbancos == '' || idbancos <= 0){
        $("#comboBoxCC" + tipo).html("<br><font color='red'>*</font> Selecione o banco");
    }else{
        $.post("contapag_grava.php", 
        {operacao: 'geraComboBoxCC', idbancos: idbancos, tipo: tipo},
        function(data){
            $("#comboBoxCC" + tipo).html(data);
        }, 'html');
    }
}

function calculoJuros(tipo){
    var valor_total = toFloat($("#ctpg_vlr_bruto").val());
    var valor = 0;
    var juros = 0;
    if(tipo == 'vlr'){
        valor = toFloat($("#ctpg_vlr_juros").val());
        juros = (valor * 100) / valor_total;
        $("#ctpg_porc_juros").val(juros.toLocaleString('pt-BR'));
    }else{
        valor = toFloat($("#ctpg_porc_juros").val());
        juros = (valor * valor_total) / 100;
        $("#ctpg_vlr_juros").val(juros.toLocaleString('pt-BR'));
    }
    
}

function calculoDesconto(tipo){
    var valor_total = toFloat($("#ctpg_vlr_bruto").val());
    var valor = 0;
    var desconto = 0;
    if(tipo == 'vlr'){
        valor = toFloat($("#ctpg_vlr_desconto").val());
        desconto = (valor * 100) / valor_total;
        $("#ctpg_porc_desconto").val(desconto.toLocaleString('pt-BR'));
    }else{
        valor = toFloat($("#ctpg_porc_desconto").val());
        desconto = (valor * valor_total) / 100;
        $("#ctpg_vlr_desconto").val(desconto.toLocaleString('pt-BR'));
    }
}

function abreConta(){
    if($("#idcontapag").val() > 0){
        window.location.replace("contapag_edita.php?id_cadastro=" + $("#idcontapag").val());
    }
}

function abrirHistoricoConta(){
    $("#conteudoHistoricoConta").html("Buscando histórico, aguarde... <img src='../icones/carregando.gif' width='25px;'>");
    $.post("contapag_grava.php",
            {operacao: 'buscarHistorico', id_cadastro: $("#id_cadastro").val()},
            function(data){
              $("#conteudoHistoricoConta").html(data);
            }, "html");
}

function efetuarPagamento(){
    var meio_pagto = $("#ctpg_idmeio_pagtoModal").val();
    var cc = $("#idcc_pagamento").val();
    var valor = toFloat($("#vlr_pagamento").val());
    var multa = toFloat($("#vlr_multa").val());
    var desconto = toFloat($("#vlr_desconto").val());
    var valor_conta = toFloat($("#ctpg_vlr_devedor").val());data_pagto
    var data_pagto = $("#data_pagto").val();
    
    if((valor_conta + multa - desconto) < valor){
        alertaPequeno("Você está tentando receber um valor maior!");
        return;
    }

    if((valor + multa - desconto) <= 0){
        alertaPequeno("A soma dos valores não deve ser menor ou igual a zero!");
        return;
    }

    if(valor == ''){
        alertaPequeno("Informe o valor que está pagando!");
        return;
    }

    if(data_pagto == ''){
        alertaPequeno("Selecione uma data de pagamento!");
        return;
    }

    $("#formPagamento").submit();
}

function calculaMultaDesconto(){
    var valor_conta = toFloat($("#ctpg_vlr_devedor").val());
    var valor = toFloat($("#vlr_pagamento").val());
    $("#vlr_desconto").val(0);
    $("#vlr_multa").val(0);
    //
    if(valor_conta > valor){
        var desconto = valor_conta - valor;
        $("#vlr_desconto").val(desconto.toLocaleString('pt-BR'));
    }
    if(valor_conta < valor){
        var multa = valor - valor_conta;
        $("#vlr_multa").val(multa.toLocaleString('pt-BR'));
    }

}