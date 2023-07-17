$( document ).ready(function() {
  $('#pesquisa').on('keydown', function(event) {
    if (event.keyCode == 13) { // Código da tecla "Enter" é 13
      buscaCadastro('cidades_grava.php');
    }
  });
});

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#cid_nome").val() == ""){
    $("#cid_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#cid_nome").css("border-color", "green");
  }
  //
  if($("#cid_idestados").val() == ""){
    $("#cid_nome").css("border-color", "red");
    alertaPequeno("Por favor, selecione um estado!");
    return;
  }else{
    $("#cid_idestados").css("border-color", "green");
  }
  //
 chamaGravar('gravar');
}