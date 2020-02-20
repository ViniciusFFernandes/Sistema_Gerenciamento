function novoCadastro(){
    $("#operacao").val('novoCadastro')
    $("#form_edita").submit();
}

function excluiCadastro(){
    var titulo = "<b>Deseja excluir este cadastro?</b>";
    var msg = "<b style='color: red;'>Atenção:</b> Não aconselhamos a exclusão de nenhuma cadastro do sistema!";
    var resultado = confirmar(msg, titulo, "chamaGravar('excluiCad')", "tada");    
  }

function buscaCadastro(link){
    $("#resultBusca").html('<img src="../icones/carregando2.gif" width="20px"> Carregando...');
    var _pesquisa = $("#pesquisa").val();
    $.post(link,
    {operacao: "buscaCadastro", pesquisa: _pesquisa},
    function(result){
      $("#pesquisa").val("");
      $("#resultBusca").html(result);
    }, 'HTML');
  }
  
function zeraBusca(){
    $("#resultBusca").html("");
  }
  
function abreCadastro(id, link){
    var siteRetorno = link + '?id_cadastro=' + id;
    $(location).attr('href', siteRetorno);
}

function chamaGravar(operacao){
    $("#operacao").val(operacao)
    $("#form_edita").submit();
}