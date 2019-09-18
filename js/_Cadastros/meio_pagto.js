
window.setTimeout(function(){
 document.getElementById("botao_alerta").click();
}, 6000);

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#mpag_nome").val() == ""){
    $("#mpag_nome").css("border-color", "red");
    return;
  }else{
    $("#mpag_nome").css("border-color", "green");
  }
  //
  $("#operacao").val('gravar')
  $("#cadastro_meio_pagto").submit();
}

function novoMeioPagto(){
  $("#operacao").val('novoCadastro')
  $("#cadastro_meio_pagto").submit();
}

function excluiCadastro(){
  var result = confirm("Não é indicado excluir uma Unidade!\n\nDeseja excluir este cadastro?");
  if (result) {
    $("#operacao").val('excluiCad')
    $("#cadastro_meio_pagto").submit();
  }
}

function buscaMeioPagto(){
  var _pesquisa = $("#pesquisa").val();
  $.post("meio_pagto_grava.php",
  {operacao: "buscaMeioPagto", pesquisa: _pesquisa},
  function(result){
    $("#pesquisa").val("");
    $("#resultBusca").html(result);
  }, 'HTML');
}

function zeraBusca(){
  $("#resultBusca").html("");
}

function abreMeioPagto(id){
  var siteRetorno = 'meio_pagto_edita.php?idmeio_pagto=' + id;
  $(location).attr('href', siteRetorno);
}