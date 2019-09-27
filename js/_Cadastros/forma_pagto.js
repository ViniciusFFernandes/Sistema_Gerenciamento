
window.setTimeout(function(){
 document.getElementById("botao_alerta").click();
}, 6000);

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#forp_nome").val() == ""){
    $("#forp_nome").css("border-color", "red");
    return;
  }else{
    $("#forp_nome").css("border-color", "green");
  }
  //
  if($("#forp_tipo option:selected").val() == ""){
   $("#forp_tipo").css("border-color", "red");
    return;
  }else{
    $("#forp_tipo").css("border-color", "green");
  }
  //
  $("#operacao").val('gravar')
  $("#cadastro_forma_pagto").submit();
}

function novaFormaPagto(){
  $("#operacao").val('novoCadastro')
  $("#cadastro_forma_pagto").submit();
}

function excluiCadastro(){
  var result = confirm("Não é indicado excluir uma cidade!\n\nDeseja excluir este cadastro?");
  if (result) {
    $("#operacao").val('excluiCad')
    $("#cadastro_forma_pagto").submit();
  }
}

function buscaforma_pagto(){
  var _pesquisa = $("#pesquisa").val();
  $.post("forma_pagto_grava.php",
  {operacao: "buscaFormaPagto", pesquisa: _pesquisa},
  function(result){
    $("#pesquisa").val("");
    $("#resultBusca").html(result);
  }, 'HTML');
}

function zeraBusca(){
  $("#resultBusca").html("");
}

function abreFormaPagto(id){
  var siteRetorno = 'forma_pagto_edita.php?idforma_pagto=' + id;
  $(location).attr('href', siteRetorno);
}