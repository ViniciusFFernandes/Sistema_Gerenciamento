$( document ).ready(function() {
  $('#pesquisa').on('keydown', function(event) {
    if (event.keyCode == 13) { // Código da tecla "Enter" é 13
      buscaCadastro('unidades_grava.php');
    }
  });
});

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#uni_nome").val() == ""){
    $("#uni_nome").css("border-color", "red");
    return;
  }else{
    $("#uni_nome").css("border-color", "green");
  }
  //
  if($("#uni_sigla").val() == ""){
    $("#uni_sigla").css("border-color", "red");
    return;
  }else{
    $("#uni_sigla").css("border-color", "green");
  }
  //
 chamaGravar('gravar');
}
