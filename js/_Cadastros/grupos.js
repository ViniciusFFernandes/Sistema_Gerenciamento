$( document ).ready(function() {
  $('#pesquisa').on('keydown', function(event) {
    if (event.keyCode == 13) { // Código da tecla "Enter" é 13
      buscaCadastro('grupos_grava.php');
    }
  });
});

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#grup_nome").val() == ""){
    $("#grup_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#grup_nome").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}
