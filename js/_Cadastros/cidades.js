
window.setTimeout(function(){
 document.getElementById("botao_alerta").click();
}, 6000);

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#cid_nome").val() == ""){
    $("#cid_nome").css("border-color", "red");
    return;
  }else{
    $("#cid_nome").css("border-color", "green");
  }
  //
  if($("#cid_idestados").val() == ""){
    alert("Por favor, selecione um estado!")
    return;
  }
  //
  $("#operacao").val('gravar')
  $("#cadastro_cidades").submit();
}

function novaCidade(){
  $("#operacao").val('novoCadastro')
  $("#cadastro_cidades").submit();
}

function excluiCadastro(){
  var result = confirm("Não é indicado excluir uma cidade!\n\nDeseja excluir este cadastro?");
  if (result) {
    $("#operacao").val('excluiCad')
    $("#cadastro_cidades").submit();
  }
}

function buscaCidades(){
  var _pesquisa = $("#pesquisa").val();
  $.post("cidades_grava.php",
  {operacao: "buscaCidades", pesquisa: _pesquisa},
  function(result){
    $("#pesquisa").val("");
    $("#resultBusca").html(result);
  }, 'HTML');
}

function zeraBusca(){
  $("#resultBusca").html("");
}

function abreCidades(id){
  var siteRetorno = 'cidades_edita.php?idcidades=' + id;
  $(location).attr('href', siteRetorno);
}