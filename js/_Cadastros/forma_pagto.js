
function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#forp_nome").val() == ""){
    $("#forp_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#forp_nome").css("border-color", "green");
  }
  //
  if($("#forp_tipo option:selected").val() == ""){
   $("#forp_tipo").css("border-color", "red");
   alertaPequeno("Por favor, informe um tipo!");
   return;
  }else{
    $("#forp_tipo").css("border-color", "green");
  }
  //
  chamaGravar('gravar');
}
