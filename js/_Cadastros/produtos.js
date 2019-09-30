
window.setTimeout(function(){
 document.getElementById("botao_alerta").click();
}, 6000);

 $(document).ready(function(){
    $("#prod_preco_tabela").mask("9999999999.99", 
          {translation: {
              '9': {
                pattern: /[0-9]/,
                optional: false
              }
            }, 
          reverse: true
          });
    $("#pfor_qte").mask("9999999999.99", 
          {translation: {
              '9': {
                pattern: /[0-9]/,
                optional: false
              }
            }, 
          reverse: true
          });
    $("#pfor_porc_perca").mask("9999999999.99", 
          {translation: {
              '9': {
                pattern: /[0-9]/,
                optional: false
              }
            }, 
          reverse: true
          });
 });

function testaDados(){
  // alert('sadnsa');
  // return;
  if($("#prod_nome").val() == ""){
    $("#prod_nome").css("border-color", "red");
    return;
  }else{
    $("#prod_nome").css("border-color", "green");
  }
  //
  if($("#prod_idunidade").val() == ""){
    $("#prod_idunidade").css("border-color", "red");
    return;
  }else{
    $("#prod_idunidade").css("border-color", "green");
  }
  //
  if($("#prod_idgrupos").val() == ""){
    $("#prod_idgrupos").css("border-color", "red");
    return;
  }else{
    $("#prod_idgrupos").css("border-color", "green");
  }
  //
  $("#operacao").val('gravar')
  $("#cadastro_produtos").submit();
}

function novoProduto(){
  $("#operacao").val('novoCadastro')
  $("#cadastro_produtos").submit();
}

function excluiCadastro(){
  var result = confirm("Não é indicado excluir um Produto!\n\nDeseja excluir este cadastro?");
  if (result) {
    $("#operacao").val('excluiCad')
    $("#cadastro_produtos").submit();
  }
}

function excluirItemFormula(idItemFormula){
  var result = confirm("Deseja excluir este item?");
  if (result) {
    $.post("produtos_grava.php", {operacao: 'excluirItemFormula', idItemFormula: idItemFormula},
      function(data){
        $("#itemFormula_" + idItemFormula).remove();
        alert("Item removido com sucesso!");
      }, "html");
  }
}

function buscaProdutos(){
  var _pesquisa = $("#pesquisa").val();
  $.post("produtos_grava.php",
  {operacao: "buscaProdutos", pesquisa: _pesquisa},
  function(result){
    $("#pesquisa").val("");
    $("#resultBusca").html(result);
  }, 'HTML');
}

function zeraBusca(){
  $("#resultBusca").html("");
}

function abreProdutos(id){
  var siteRetorno = 'produtos_edita.php?idprodutos=' + id;
  $(location).attr('href', siteRetorno);
}

function gravaItensFormula(){
  var pfor_porc_perca = 0;
  if($("#idprodutos").val() == ''){
    alert("Nenhum produto base selecionado!");
    return;
  }
  if($("#pfor_idprodutos").val() == ''){
    alert("Selecione um item antes de inserir!");
    return;
  }
  if($("#pfor_qte").val() == '' || $("#pfor_qte").val() <= 0){
    alert("Informe a quantidade!");
    return;
  }
  if($("#pfor_porc_perca").val() > 0){
    pfor_porc_perca = $("#pfor_porc_perca").val();
  }
  $.post("produtos_grava.php",
        {operacao: 'gravarItem',
        pfor_idproduto_final: $("#idprodutos").val(),
        pfor_idprodutos: $("#pfor_idprodutos").val(),
        pfor_qte: $("#pfor_qte").val(),
        pfor_porc_perca: pfor_porc_perca},
        function(data){
          $("#tableItensFormula").append(data);
        }, "html");
}