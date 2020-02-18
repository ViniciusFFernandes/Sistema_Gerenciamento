

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#mpag_nome").val() == ""){
    $("#mpag_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#mpag_nome").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}

