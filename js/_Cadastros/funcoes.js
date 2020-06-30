
function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#func_nome").val() == ""){
    $("#func_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#func_nome").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}
