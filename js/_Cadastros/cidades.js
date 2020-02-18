
function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#cid_nome").val() == ""){
    $("#cid_nome").css("border-color", "red");
    alertaPequeno("Por favor, informe um nome!");
    return;
  }else{
    $("#cid_nome").css("border-color", "green");
  }
  //
  if($("#cid_idestados").val() == ""){
    $("#cid_nome").css("border-color", "red");
    alertaPequeno("Por favor, selecione um estado!");
    return;
  }else{
    $("#cid_idestados").css("border-color", "green");
  }
  //
 chamaGravar('gravar');
}