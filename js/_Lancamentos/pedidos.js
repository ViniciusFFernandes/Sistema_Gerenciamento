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

    $("#peit_unitario").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#peit_desconto").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#peit_desconto_porc").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#peit_qte").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#peit_total").mask("9.999.999.999,99", 
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

function buscaDadosProduto(){
    var idprodutos = $("#idprodutos").val();
    //
    $.post("pedidos_grava.php", {
        operacao: 'buscaDadosProduto',
        idprodutos: idprodutos
    }, function(data){
        $("#peit_qte").val(1);
        $("#peit_unitario").val(data.prod_preco);
        $("#peit_sigla_unidade").html(data.prod_unidade);
        $("#peit_qte").attr("readonly", false);
        $("#peit_unitario").attr("readonly", false);
        $("#peit_desconto").attr("readonly", false);
        $("#peit_desconto_porc").attr("readonly", false);
        calculoPreco();
    }, "json");
}

function calculoDesconto(tipo){
    var valor_unitario = toFloat($("#peit_unitario").val());
    var qte = toFloat($("#peit_qte").val());
    var valor_total = qte * valor_unitario;
    var valor = 0;
    var desconto = 0;
    //
    if(tipo == 'vlr'){
        valor = toFloat($("#peit_desconto").val());
        desconto = (valor * 100) / valor_total;
        $("#peit_desconto_porc").val(desconto.toLocaleString('pt-BR'));
    }else{
        valor = toFloat($("#peit_desconto_porc").val());
        desconto = (valor * valor_total) / 100;
        $("#peit_desconto").val(desconto.toLocaleString('pt-BR'));
    }
    //
    calculoPreco();
}

function calculoPreco(){
    var valor_unitario = toFloat($("#peit_unitario").val());
    var qte = toFloat($("#peit_qte").val());
    var desconto = toFloat($("#peit_desconto").val());
    var valor_total = (qte * valor_unitario) - desconto;
    //
    $("#peit_total").val(valor_total.toLocaleString('pt-BR'));
}

function gravarProduto(){
    var id_cadastro = $("#idpedidos_itens").val();
    var idprodutos = $("#idprodutos").val();
    var idpedidos = $("#idpedidos").val();
    var peit_qte = $("#peit_qte").val();
    var peit_unitario = $("#peit_unitario").val();
    var peit_desconto = $("#peit_desconto").val();
    var peit_desconto_porc = $("#peit_desconto_porc").val();
    //
    $.post("pedidos_grava.php", {
        operacao: 'gravarProduto',
        id_cadastro: id_cadastro,
        idpedidos: idpedidos,
        idprodutos: idprodutos,
        peit_qte: peit_qte,
        peit_unitario: peit_unitario,
        peit_desconto: peit_desconto,
        peit_desconto_porc: peit_desconto_porc
    }, function(data){
        if(data.retorno == 'ok'){
            $("#idpedidos_itens").val('');
            $("#prod_nome").val('');
            $("#idprodutos").val('');
            $("#peit_qte").val('');
            $("#peit_unitario").val('');
            $("#peit_desconto").val('');
            $("#peit_desconto_porc").val('');
            $("#peit_total").val('');
            $("#peit_sigla_unidade").html('');
            $("#peit_qte").attr("readonly", true);
            $("#peit_unitario").attr("readonly", true);
            $("#peit_desconto").attr("readonly", true);
            $("#peit_desconto_porc").attr("readonly", true);
            //
            attListaProdutos();
        }else{
            alertaGrande('Erro ao inserir produto');
            console.log(data.msg);
        }
    }, "json");
}

function attListaProdutos(){
    var idpedidos = $("#idpedidos").val();
    //
    $.post("pedidos_grava.php", {
        operacao: 'attlistaProdutos',
        idpedidos: idpedidos
    }, function(data){
        $("#listaProdutos").html(data)
    }, "html");
}
