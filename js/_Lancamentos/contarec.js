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