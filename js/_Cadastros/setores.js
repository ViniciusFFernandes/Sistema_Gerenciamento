
function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#set_nome").val() == ""){
    $("#set_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#set_nome").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}
