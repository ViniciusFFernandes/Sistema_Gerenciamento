$( document ).ready(function() {
    $('#pesquisa').on('keydown', function(event) {
      if (event.keyCode == 13) { // Código da tecla "Enter" é 13
        buscaCadastro('coleta_grava.php');
      }
    });

    $('#idcoleta').on('keydown', function (e) {
      if (e.keyCode === 13) {
          abreColeta();
      }
  });
});

function abreColeta(){
  if($("#idcoleta").val() > 0){
      window.location.replace("coleta_edita.php?id_cadastro=" + $("#idcoleta").val());
  }
}

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