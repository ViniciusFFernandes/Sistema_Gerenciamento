function testaDados(operacao){
    if($("#cole_idprodutos").val() <= 0){
        alertaPequeno("Selecione o item coletado!");
        $("#cole_produtos").focus();
        return;
    }
    if ($("#cole_qte").val() <= 0){
        alertaPequeno("Informe a quantidade coletada!");
        $("#cole_qte").focus();
        return;
    }
    chamaGravar(operacao);
}