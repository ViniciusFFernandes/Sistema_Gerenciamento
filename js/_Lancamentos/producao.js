function testaDados(operacao){
    if($("#pdc_idprodutos").val() <= 0){
        alert("Selecione um item para produzir!");
        $("#pdc_produtos").focus();
        return;
    }
    if ($("#pdc_qte_produzida").val() <= 0){
        alert("Informe a quantidade a ser produzida!");
        $("#pdc_qte_produzida").focus();
        return;
    }
    chamaGravar(operacao);
}