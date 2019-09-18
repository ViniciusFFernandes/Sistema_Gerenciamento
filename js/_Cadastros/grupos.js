
window.setTimeout(function(){
 document.getElementById("botao_alerta").click();
}, 6000);

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#grup_nome").val() == ""){
    $("#grup_nome").css("border-color", "red");
    return;
  }else{
    $("#grup_nome").css("border-color", "green");
  }
  //
  $("#operacao").val('gravar')
  $("#cadastro_grupos").submit();
}

function novoGrupo(){
  $("#operacao").val('novoCadastro')
  $("#cadastro_grupos").submit();
}

function excluiCadastro(){
  var result = confirm("Não é indicado excluir uma Unidade!\n\nDeseja excluir este cadastro?");
  if (result) {
    $("#operacao").val('excluiCad')
    $("#cadastro_grupos").submit();
  }
}

function buscaGrupos(){
  var _pesquisa = $("#pesquisa").val();
  $.post("grupos_grava.php",
  {operacao: "buscaGrupos", pesquisa: _pesquisa},
  function(result){
    $("#pesquisa").val("");
    $("#resultBusca").html(result);
  }, 'HTML');
}

function zeraBusca(){
  $("#resultBusca").html("");
}

function abreGrupos(id){
  var siteRetorno = 'grupos_edita.php?idgrupos=' + id;
  $(location).attr('href', siteRetorno);
}