$( document ).ready(function() {
  $('#pesquisa').on('keydown', function(event) {
    if (event.keyCode == 13) { // Código da tecla "Enter" é 13
      buscaCadastro('sub_grupos_grava.php');
    }
  });
});

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#subg_nome").val() == ""){
    $("#subg_nome").css("border-color", "red");
    return;
  }else{
    $("#subg_nome").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}
