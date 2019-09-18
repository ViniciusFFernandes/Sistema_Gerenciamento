
window.setTimeout(function(){
 document.getElementById("botao_alerta").click();
}, 6000);

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
  $("#operacao").val('gravar')
  $("#cadastro_sub_grupos").submit();
}

function novoSubGrupo(){
  $("#operacao").val('novoCadastro')
  $("#cadastro_sub_grupos").submit();
}

function excluiCadastro(){
  var result = confirm("Não é indicado excluir uma Unidade!\n\nDeseja excluir este cadastro?");
  if (result) {
    $("#operacao").val('excluiCad')
    $("#cadastro_sub_grupos").submit();
  }
}

function buscaSubGrupos(){
  var _pesquisa = $("#pesquisa").val();
  $.post("sub_grupos_grava.php",
  {operacao: "buscaSubGrupos", pesquisa: _pesquisa},
  function(result){
    $("#pesquisa").val("");
    $("#resultBusca").html(result);
  }, 'HTML');
}

function zeraBusca(){
  $("#resultBusca").html("");
}

function abreSubGrupos(id){
  var siteRetorno = 'sub_grupos_edita.php?idsubgrupos=' + id;
  $(location).attr('href', siteRetorno);
}