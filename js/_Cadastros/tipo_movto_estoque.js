
function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#time_nome").val() == ""){
    $("#time_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#time_nome").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}

function selecionaTipo(tipo){
  if(tipo == 'entrada'){
    $("#time_saida").attr("checked", false);
  }
  if(tipo == 'saida'){
    $("#time_entrada").attr("checked", false);
  }
}