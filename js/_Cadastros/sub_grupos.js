
function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#subg_nome").val() == ""){
    $("#subg_nome").css("border-color", "red");
    return;
  }else{
    $("#subg_nome").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}
