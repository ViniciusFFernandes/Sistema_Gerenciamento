$(document).ready(function(){
    
    $("#pcom_frete").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });
    
    $("#pcom_valor_desconto").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#pcom_porc_desconto").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#pcom_total_pedido").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $('#idpedcompras').on('keydown', function (e) {
        if (e.keyCode === 13) {
            abrePedido();
        }
    });

    $("#pcit_unitario").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#pcit_desconto").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#pcit_desconto_porc").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#pcit_qte").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#pcit_total").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $("#pccon_valor").mask("9.999.999.999,99", 
        {translation: {
            '9': {
            pattern: /[0-9]/,
            optional: false
            }
        }, 
        reverse: true
    });

    $('#pesquisa').on('keydown', function(event) {
        if (event.keyCode == 13) { // Código da tecla "Enter" é 13
        buscaCadastro('pedcompra_grava.php');
        }
    });
    
 });
 
function testaDados(operacao){
    if ($("#pcom_idfornecedor").val() <= 0){
        alertaPequeno("Informe o cliente!");
        $("#pcom_fornecedor").focus();
        return;
    }
    chamaGravar(operacao);
}

function abrePedido(){
    if($("#idpedcompras").val() > 0){
        window.location.replace("pedcompras_edita.php?id_cadastro=" + $("#idpedcompras").val());
    }
}

function abreConta(idconta){
    window.location.replace("contapag_edita.php?id_cadastro=" + idconta);
}

function carregaComboBoxCC(tipo = ""){
    $("#comboBoxCC" + tipo).html("<br><img src='../icones/carregando.gif' width='20px'>");
    //
    var idbancos = '';
    if(tipo == ''){
        idbancos = $("#pcom_idbancos").val();
    }else{
        idbancos = $("#pccon_idbancos").val();
    }
    
    if(idbancos == '' || idbancos <= 0){
        $("#comboBoxCC" + tipo).html("<br><font color='red'>*</font> Selecione o banco");
    }else{
        $.post("pedcompras_grava.php", 
        {operacao: 'geraComboBoxCC', idbancos: idbancos, tipo: tipo},
        function(data){
            $("#comboBoxCC" + tipo).html(data);
        }, 'html');
    }
}

function buscaDadosProduto(){
    var idprodutos = $("#idprodutos").val();
    //
    $.post("pedcompras_grava.php", {
        operacao: 'buscaDadosProduto',
        idprodutos: idprodutos
    }, function(data){
        $("#pcit_qte").val(1);
        $("#pcit_unitario").val(data.prod_preco);
        $("#pcit_sigla_unidade").html(data.prod_unidade);
        $("#pcit_qte").attr("readonly", false);
        $("#pcit_unitario").attr("readonly", false);
        $("#pcit_desconto").attr("readonly", false);
        $("#pcit_desconto_porc").attr("readonly", false);
        calculoPreco();
    }, "json");
}

function calculoDesconto(tipo){
    var valor_unitario = toFloat($("#pcit_unitario").val());
    var qte = toFloat($("#pcit_qte").val());
    var valor_total = qte * valor_unitario;
    var valor = 0;
    var desconto = 0;
    //
    if(tipo == 'vlr'){
        valor = toFloat($("#pcit_desconto").val());
        desconto = (valor * 100) / valor_total;
        $("#pcit_desconto_porc").val(desconto.toLocaleString('pt-BR'));
    }else{
        valor = toFloat($("#pcit_desconto_porc").val());
        desconto = (valor * valor_total) / 100;
        $("#pcit_desconto").val(desconto.toLocaleString('pt-BR'));
    }
    //
    calculoPreco();
}

function calculoPreco(){
    var valor_unitario = toFloat($("#pcit_unitario").val());
    var qte = toFloat($("#pcit_qte").val());
    var desconto = toFloat($("#pcit_desconto").val());
    var valor_total = (qte * valor_unitario) - desconto;
    //
    $("#pcit_total").val(valor_total.toLocaleString('pt-BR'));
}

function atzTotalPedido(){
    var id_cadastro = $("#idpedcompras").val();
    //
    $.post("pedcompras_grava.php", {
        operacao: 'atzTotalPedido',
        id_cadastro: id_cadastro
    }, function(data){
        $("#pcom_total_pedido").val(data.pcom_total_pedido);
    }, "json");
}

function limpaModalProd(){
    $("#idpedcompras_itens").val('');
    $("#prod_nome").val('');
    $("#idprodutos").val('');
    $("#pcit_qte").val('');
    $("#pcit_unitario").val('');
    $("#pcit_desconto").val('');
    $("#pcit_desconto_porc").val('');
    $("#pcit_total").val('');
    $("#pcit_sigla_unidade").html('');
    $("#pcit_qte").attr("readonly", true);
    $("#pcit_unitario").attr("readonly", true);
    $("#pcit_desconto").attr("readonly", true);
    $("#pcit_desconto_porc").attr("readonly", true);
    $("#tituloModalProdutos").html("Inserir Produto");
}

function gravarProduto(){
    var id_cadastro = $("#idpedcompras_itens").val();
    var idprodutos = $("#idprodutos").val();
    var idpedcompras = $("#idpedcompras").val();
    var pcit_qte = $("#pcit_qte").val();
    var pcit_unitario = $("#pcit_unitario").val();
    var pcit_desconto = $("#pcit_desconto").val();
    var pcit_desconto_porc = $("#pcit_desconto_porc").val();
    var pcit_sigla_unidade = $("#pcit_sigla_unidade").html();
    //
    $.post("pedcompras_grava.php", {
        operacao: 'gravarProduto',
        id_cadastro: id_cadastro,
        idpedcompras: idpedcompras,
        idprodutos: idprodutos,
        pcit_qte: pcit_qte,
        pcit_unitario: pcit_unitario,
        pcit_desconto: pcit_desconto,
        pcit_desconto_porc: pcit_desconto_porc,
        pcit_sigla_unidade: pcit_sigla_unidade
    }, function(data){
        if(data.retorno == 'ok'){
            limpaModalProd();
            attListaProdutos();
            if(id_cadastro > 0){
                $('#modalProdutos').modal('hide');
            }
        }else{
            alertaGrande('Erro ao inserir produto');
            console.log(data.msg);
        }
    }, "json");
}

function editarItem(idpedcompras_itens){
    //
    $("#tituloModalProdutos").html("Editar Produto");
    //
    $.post("pedcompras_grava.php", {
        operacao: 'editarProduto',
        id_cadastro: idpedcompras_itens
    }, function(data){
            $('#modalProdutos').modal('show');
            //
            $("#idpedcompras_itens").val(idpedcompras_itens);
            $("#prod_nome").val(data.prod_nome);
            $("#idprodutos").val(data.pcit_idprodutos);
            $("#pcit_qte").val(data.pcit_qte);
            $("#pcit_unitario").val(data.pcit_vlr_unitario);
            $("#pcit_desconto").val(data.pcit_valor_desconto);
            $("#pcit_desconto_porc").val(data.pcit_porc_desconto);
            $("#pcit_sigla_unidade").html(data.pcit_unidade_sigla);
            $("#pcit_total").val(data.pcit_total_item);
            $("#pcit_qte").attr("readonly", false);
            $("#pcit_unitario").attr("readonly", false);
            $("#pcit_desconto").attr("readonly", false);
            $("#pcit_desconto_porc").attr("readonly", false);
            //
    }, "json");
}


function excluiItem(idpedcompras_itens){
    //
    $.post("pedcompras_grava.php", {
        operacao: 'excluirProduto',
        id_cadastro: idpedcompras_itens
    }, function(data){
        if(data.retorno == 'ok'){
            attListaProdutos();
        }else{
            alertaGrande('Erro ao excluir produto');
            console.log(data.msg);
        }
    }, "json");
}


function attListaProdutos(){
    var idpedcompras = $("#idpedcompras").val();
    //
    $.post("pedcompras_grava.php", {
        operacao: 'attlistaProdutos',
        idpedcompras: idpedcompras
    }, function(data){
        $("#listaProdutos").html(data)
    }, "html");
    //
    atzTotalPedido();
}

function gerarParcelas(){
    //
    $("#btnGerarParcelas").html('<img src="../icones/carregando.gif" width="20px"> Gerando Parcelas...');
    $("#btnGerarParcelas").attr("disabled", true);
    //
    var idpedcompras = $("#idpedcompras").val();
    var pcom_com_entrada = '';
    if($('#pcom_com_entrada').is(":checked")){
        pcom_com_entrada = 'SIM';
    }
    //
    $.post("pedcompras_grava.php", {
        operacao: 'gerarParcelas',
        pcom_idmeio_pagto: $("#pcom_idmeio_pagto option:selected").val(),
        pcom_idforma_pagto: $("#pcom_idforma_pagto option:selected").val(),
        pcom_idbancos: $("#pcom_idbancos option:selected").val(),
        pcom_idcc: $("#pcom_idcc option:selected").val(),
        pcom_idtipo_contas: $("#pcom_idtipo_contas option:selected").val(),
        pcom_qte_parcelas: $("#pcom_qte_parcelas").val(),
        pcom_com_entrada: pcom_com_entrada,
        idpedcompras: idpedcompras
    }, function(data){
        $("#btnGerarParcelas").html('<i class="fas fa-percent"></i> Parcelar');
        $("#btnGerarParcelas").attr("disabled", false);
        //
        if(data.erro == 1){
            if(data.msgErro != ""){
                alertaGrande(data.msgErro);
            }else{
                alertaGrande("Erro ao gerar parcelas!");
            }
        }else{
            attListaContas();
        }
    }, 'json');
}

function attListaContas(){
    // $("#listaContas").html('<img src="../icones/carregando.gif" width="25px"> Buscando Parcelas...');
    //
    var idpedcompras = $("#idpedcompras").val();
    //
    $.post("pedcompras_grava.php", {
        operacao: 'attParcelas',
        idpedcompras: idpedcompras
    }, function(data){
        $("#listaContas").html(data)
    }, 'html');
}

function alteraFormaPagto(){
    $.post("pedcompras_grava.php", {
        operacao: 'retornaTipoFormaPagto',
        idforma_pagto: $("#pcom_idforma_pagto option:selected").val()
    }, function(data){
        if(data.retorno != 'Parcelamento Livre'){
            $("#pcom_qte_parcelas").attr("readonly", true);
        }else{
            $("#pcom_qte_parcelas").attr("readonly", false);
        }
    }, 'json');
}

function excluiConta(idpedcompras_contas){
    $.post("pedcompras_grava.php", {
        operacao: 'excluirConta',
        id_cadastro: idpedcompras_contas
    }, function(data){
        if(data.retorno == 'ok'){
            attListaContas();
        }else{
            alertaGrande('Erro ao excluir conta');
            console.log(data.msg);
        }
    }, "json");
}

function limpaModalConta(){
    $("#idpedcompras_contas").val('');
    $("#pccon_vencimento_dias").val('');
    $("#pccon_vencimento").val('');
    $("#pccon_valor").val('');
    $("#comboBoxMeioPagtoModal").html('');
    $("#comboBoxTipoContaModal").html('');
    $("#comboBoxBancosModal").html('');
    $("#comboBoxCCModal").html('');
}

function editarConta(idpedcompras_contas){
    limpaModalConta()
    //
    $.post("pedcompras_grava.php", {
        operacao: 'editarContas',
        id_cadastro: idpedcompras_contas
    }, function(data){
            $('#modalContas').modal('show');
            //
            $("#idpedcompras_contas").val(data.idpedcompras_contas);
            $("#pccon_vencimento_dias").val(data.pccon_vencimento_dias);
            $("#pccon_vencimento").val(data.pccon_vencimento);
            $("#pccon_valor").val(data.pccon_valor);
            $("#comboBoxMeioPagtoModal").html(data.comboBoxMeioPagtoModal);
            $("#comboBoxTipoContaModal").html(data.comboBoxTipoContaModal);
            $("#comboBoxBancosModal").html(data.comboBoxBancosModal);
            $("#comboBoxCCModal").html(data.comboBoxCCModal);
            //
    }, "json");
}

function attDataVencto(tipo){
    var dataAtual = moment();
    var dataVencto = moment($("#pccon_vencimento").val());
    if(tipo == 'dias'){
        var dataCalculada = dataAtual.add($("#pccon_vencimento_dias").val(), 'days'); 
        $("#pccon_vencimento").val(dataCalculada.format('YYYY-MM-DD'));
    }
    if(tipo == 'data'){
        var diferencaDias = dataVencto.diff(dataAtual, 'days');
        diferencaDias = diferencaDias + 1;
        if(diferencaDias <= 0){
            $("#pccon_vencimento").val(dataAtual.format('YYYY-MM-DD'));
            $("#pccon_vencimento_dias").val(0);
        }else{
            $("#pccon_vencimento_dias").val(diferencaDias);
        }
    }
}

function gravarConta(){
    $.post("pedcompras_grava.php", {
        operacao: 'gravarConta',
        id_cadastro: $("#idpedcompras_contas").val(),
        pccon_vencimento_dias: $("#pccon_vencimento_dias").val(),
        pccon_vencimento: $("#pccon_vencimento").val(),
        pccon_valor: $("#pccon_valor").val(),
        pcom_idmeio_pagto: $("#pccon_idmeio_pagto").val(),
        pcom_idbancos: $("#pccon_idbancos").val(),
        pcom_idcc: $("#pccon_idcc").val(),
        pcom_idtipo_contas: $("#pccon_idtipo_contas").val()
    }, function(data){
            if(data.retorno == 'ok'){
                attListaContas();
                $('#modalContas').modal('hide');
            }else{
                alertaGrande('Erro ao gravar conta');
                console.log(data.msg);
            }
    }, "json");
}