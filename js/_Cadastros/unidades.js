
window.setTimeout(function(){
 document.getElementById("botao_alerta").click();
}, 6000);

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
  $("#operacao").val('gravar')
  $("#cadastro_unidades").submit();
}

function novaUnidade(){
  $("#operacao").val('novoCadastro')
  $("#cadastro_unidades").submit();
}

function excluiCadastro(){
  var result = confirm("Não é indicado excluir uma Unidade!\n\nDeseja excluir este cadastro?");
  if (result) {
    $("#operacao").val('excluiCad')
    $("#cadastro_unidades").submit();
  }
}

function buscaUnidades(){
  var _pesquisa = $("#pesquisa").val();
  $.post("cadastro_unidades_grava.php",
  {operacao: "buscaUnidades", pesquisa: _pesquisa},
  function(result){
    $("#pesquisa").val("");
    $("#resultBusca").html(result);
  }, 'HTML');
}

function zeraBusca(){
  $("#resultBusca").html("");
}

function abreUnidades(id){
  var siteRetorno = 'cadastro_unidades.php?idunidades=' + id;
  $(location).attr('href', siteRetorno);
}