$( document ).ready(function() {
  $('#pesquisa').on('keydown', function(event) {
    if (event.keyCode == 13) { // Código da tecla "Enter" é 13
      buscaCadastro('forma_pagto_grava.php');
    }
  });
});

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#forp_nome").val() == ""){
    $("#forp_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#forp_nome").css("border-color", "green");
  }
  //
  if($("#forp_tipo option:selected").val() == ""){
   $("#forp_tipo").css("border-color", "red");
   alertaPequeno("Por favor, informe um tipo!");
   return;
  }else{
    $("#forp_tipo").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}
