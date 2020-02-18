

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#uni_nome").val() == ""){
    $("#uni_nome").css("border-color", "red");
    return;
  }else{
    $("#uni_nome").css("border-color", "green");
  }
  //
  if($("#uni_sigla").val() == ""){
    $("#uni_sigla").css("border-color", "red");
    return;
  }else{
    $("#uni_sigla").css("border-color", "green");
  }
  //
 chamaGravar('gravar');
}
