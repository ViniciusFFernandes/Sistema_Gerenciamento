
window.setTimeout(function(){
    document.getElementById("botao_alerta").click();
}, 5000);


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
    $("#operacao").val(operacao);
    $("#cadastro_producao").submit();
}

function excluiCadastro() {
    var result = confirm("Não é indicado excluir uma producao!\n\nDeseja excluir este cadastro?");
    if (result) {
        $("#operacao").val('excluiCad');
        $("#cadastro_producao").submit();
    }
}

function novaProducao() {
    $("#operacao").val('novoCadastro');
    $("#cadastro_producao").submit();
}

function buscaProducao() {
    var _pesquisa = $("#pesquisa").val();
    $.post("producao_grava.php",
        { operacao: "buscaProducao", pesquisa: _pesquisa },
        function (result) {
            $("#pesquisa").val("");
            $("#resultBusca").html(result);
        }, 'HTML');
}

function zeraBusca() {
    $("#resultBusca").html("");
}

function abreProducao(id) {
    var siteRetorno = 'producao_edita.php?idproducao=' + id;
    $(location).attr('href', siteRetorno);
}
// function setaDataHora(){
//     var data = new Date(),
//         dia = data.getDate(),
//         mes = data.getMonth() + 1,
//         ano = data.getFullYear(),
//         hora = data.getHours(),
//         minutos = data.getMinutes();
//     //
//     var dataFormatada = [dia, mes, ano].join('/') + ' ' + [hora, minutos].join(':');
//     //
//     $("#fopo_horario").val(dataFormatada);
// }
