
window.setTimeout(function(){
 document.getElementById("botao_alerta").click();
}, 6000);

 $(document).ready(function(){
    $("#prod_qte_estoque").mask("9999999999.99", 
          {translation: {
              '9': {
                pattern: /[0-9]/,
                optional: false
              }
            }, 
          reverse: true
          }); 
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

function mostraFormula(){
  if($('#formulaCabecalho').is(':visible')){
    $('#formulaCabecalho').hide();
    $('#formulaItens').hide();
    $('#btnExibeFormula').html('<img src="../icones/visivel.png">');
  }else{
    $('#formulaCabecalho').show();
    $('#formulaItens').show();
    $('#btnExibeFormula').html('<img src="../icones/invisivel.png">');
  }
}

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
  // if($("#prod_idgrupos").val() == ""){
  //   $("#prod_idgrupos").css("border-color", "red");
  //   return;
  // }else{
  //   $("#prod_idgrupos").css("border-color", "green");
  // }
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