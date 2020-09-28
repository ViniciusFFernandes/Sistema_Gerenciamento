$(document).ready(function(){
    $("#ctrc_vlr_bruto").mask("9999999999.99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
    $("#ctrc_vlr_juros").mask("9999999999.99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
    $("#ctrc_porc_juros").mask("9999999999.99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
    $("#ctrc_vlr_desconto").mask("9999999999.99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
    $("#ctrc_porc_desconto").mask("9999999999.99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
 });

function testaDados(operacao){
    if($("#ctrc_vencimento").val() == ''){
        alert("Selecione o vencimento!");
        $("#ctrc_vencimento").focus();
        return;
    }
    if ($("#ctrc_idcliente").val() <= 0){
        alert("Informe o cliente!");
        $("#ctrc_cliente").focus();
        return;
    }
    if ($("#ctrc_vlr_bruto").val() <= 0){
        alert("Informe o valor!");
        $("#ctrc_cliente").focus();
        return;
    }
    chamaGravar(operacao);
}

function carregaComboBoxCC(){
    $("#comboBoxCC").html("<br><img src='../icones/carregando.gif' width='20px'>");
    //
    var idbancos = $("#ctrc_idbancos").val();
    if(idbancos == '' || idbancos <= 0){
        $("#comboBoxCC").html("<br><font color='red'>*</font> Selecione o banco");
    }else{
        $.post("contarec_grava.php", 
        {operacao: 'geraComboBoxCC', idbancos: idbancos},
        function(data){
            $("#comboBoxCC").html(data);
        }, 'html');
    }
}

function calculoJuros(tipo){
    var valor_total = $("#ctrc_vlr_bruto").val();
    var valor = 0;
    var juros = 0;
    if(tipo == 'vlr'){
        valor = $("#ctrc_vlr_juros").val();
        juros = (valor * 100) / valor_total;
        $("#ctrc_porc_juros").val(juros);
    }else{
        valor = $("#ctrc_porc_juros").val();
        juros = (valor * valor_total) / 100;
        $("#ctrc_vlr_juros").val(juros);
    }
    
}

function calculoDesconto(tipo){
    var valor_total = $("#ctrc_vlr_bruto").val();
    var valor = 0;
    var desconto = 0;
    if(tipo == 'vlr'){
        valor = $("#ctrc_vlr_desconto").val();
        desconto = (valor * 100) / valor_total;
        $("#ctrc_porc_desconto").val(desconto);
    }else{
        valor = $("#ctrc_porc_desconto").val();
        desconto = (valor * valor_total) / 100;
        $("#ctrc_vlr_desconto").val(desconto);
    }
}