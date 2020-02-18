
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
