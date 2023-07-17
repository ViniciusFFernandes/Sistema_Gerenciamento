$( document ).ready(function() {
  $('#pesquisa').on('keydown', function(event) {
    if (event.keyCode == 13) { // Código da tecla "Enter" é 13
      buscaCadastro('tipo_contas_grava.php');
    }
  });
});

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#tico_nome").val() == ""){
    $("#tico_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#tico_nome").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}

function selecionaTipo(tipo){
  if(tipo == 'vale'){
    $("#tico_tipo_extra").attr("checked", false);
    $("#tico_tipo_salario").attr("checked", false);
  }
  if(tipo == 'extra'){
    $("#tico_tipo_vale").attr("checked", false);
    $("#tico_tipo_salario").attr("checked", false);
  }
  if(tipo == 'salario'){
    $("#tico_tipo_vale").attr("checked", false);
    $("#tico_tipo_extra").attr("checked", false);
  }
}