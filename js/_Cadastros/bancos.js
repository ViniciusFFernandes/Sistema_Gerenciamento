
function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#banc_nome").val() == ""){
    $("#banc_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#banc_nome").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}