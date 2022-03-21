$(document).ready(function(){
    
    $("#ped_frete").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
    
    $("#ped_valor_desconto").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#ped_porc_desconto").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#ped_total_pedido").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $('#idpedidos').on('keydown', function (e) {
        if (e.keyCode === 13) {
            abrePedido();
        }
    });
 });

function testaDados(operacao){
    if ($("#ped_idcliente").val() <= 0){
        alertaPequeno("Informe o cliente!");
        $("#ped_cliente").focus();
        return;
    }
    chamaGravar(operacao);
}

function abrePedido(){
    if($("#idpedidos").val() > 0){
        window.location.replace("pedidos_edita.php?id_cadastro=" + $("#idpedidos").val());
    }
}

function carregaComboBoxCC(){
    $("#comboBoxCC").html("<br><img src='../icones/carregando.gif' width='20px'>");
    //
    var idbancos = $("#ped_idbancos").val();
    if(idbancos == '' || idbancos <= 0){
        $("#comboBoxCC").html("<br><font color='red'>*</font> Selecione o banco");
    }else{
        $.post("pedidos_grava.php", 
        {operacao: 'geraComboBoxCC', idbancos: idbancos},
        function(data){
            $("#comboBoxCC").html(data);
        }, 'html');
    }
}

function gravarProduto(){
    var id_cadastro = ;
    var idprodutos = ;
    var peit_qte = ;
    var peit_unitario = ;
    var peit_desconto = ;
    var peit_desconto_porc = ;
    var peit_total = ;
}

function buscaDadosProduto(){
    var idprodutos = ;
}