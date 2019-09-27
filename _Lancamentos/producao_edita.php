<?php
  include_once("../_BD/conecta_login.php");
  include_once("../Class/autoComplete.class.php");
  include_once("../Class/produtos.class.php");
  //
  //Operações do banco de dados
  if(!empty($_REQUEST['idproducao'])){
    $sql = "SELECT *
            FROM producao
              LEFT JOIN produtos ON (idprodutos = pdc_idprodutos)
            WHERE idproducao = {$_REQUEST['idproducao']}";
    $reg = $db->retornaUmReg($sql);
  }
  //
  //Gera o autoComplete 
  $autoComplete = new autoComplete();
  $codigo_js = $autoComplete->gerar("pdc_produtos", "pdc_idprodutos", "produtos", "prod_nome", "idprodutos", "", "WHERE UPPER(prod_nome) LIKE UPPER('##valor##%') AND prod_tipo_produto = 'Producao Propria'");
  //
  //Monta variaveis de exibição
  $pdc_calcula_automatico = 'checked="checked"';
  if(!empty($reg['idproducao'])){ 
    //
    $produtos = new produtos($db, $util);
    $itensFormula = $produtos->getItensFormula($reg['pdc_idprodutos'], $reg['pdc_qte_produzida']);
    //
    if($reg['pdc_calcula_automatico'] == 'SIM'){
      $pdc_calcula_automatico = 'checked="checked"';
    }
    //
    $btnExcluir = '<button type="button" onclick="excluiCadastro()" class="btn btn-danger">Excluir</button>';
    $pdc_situacao = "Situação: " . $reg['pdc_situacao'];
  }

  if (isset($_SESSION['mensagem'])) {
    $msg = $util->mostraMensagem($_SESSION['tipoMsg'], $_SESSION['mensagem']);
    unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
  }
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $util->buscaHtml("lancamentos");
  $html = str_replace("##Mensagem##", $msg, $html);
  $html = str_replace("##autoComplete_Produto##", $codigo_js, $html);
  $html = str_replace("##idproducao##", $reg['idproducao'], $html);
  $html = str_replace("##pdc_situacao##", $pdc_situacao, $html);
  $html = str_replace("##pdc_data_abertura##", $util->convertData($reg['pdc_data_abertura']), $html);
  $html = str_replace("##pdc_data_fechamento##", $util->convertData($reg['pdc_data_fechamento']), $html);
  $html = str_replace("##pdc_produtos##", $reg['prod_nome'], $html);
  $html = str_replace("##pdc_idprodutos##", $reg['pdc_idprodutos'], $html);
  $html = str_replace("##pdc_qte_produzida##", $reg['pdc_qte_produzida'], $html);
  $html = str_replace("##pdc_calcula_automatico##", $pdc_calcula_automatico, $html);
  $html = str_replace("##ItensProducao##", $itensFormula, $html);
  $html = str_replace("##btnExcluir##", $btnExcluir, $html);
  echo $html;
  exit;
?>