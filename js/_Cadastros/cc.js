
function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#cc_nome").val() == ""){
    $("#cc_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#cc_nome").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}