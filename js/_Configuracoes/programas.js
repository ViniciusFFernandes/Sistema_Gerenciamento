$( document ).ready(function() {
  $('#pesquisa').on('keydown', function(event) {
    if (event.keyCode == 13) { // Código da tecla "Enter" é 13
      buscaCadastro('programas_grava.php');
    }
  });
});

function testaDados(){
  //
  chamaGravar('gravar');
}
