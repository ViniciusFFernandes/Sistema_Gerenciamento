<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
include_once("../Class/producao.class.php");
// print_r($_REQUEST);
// exit;
  if ($_POST['operacao'] == "buscaProducao") {
    $sql = "SELECT *, DATE_FORMAT(STR_TO_DATE(pdc_data_abertura, '%Y-%m-%d'), '%d/%m/%Y') as pdc_abertura
            FROM producao 
              LEFT JOIN produtos ON (idprodutos = pdc_idprodutos)";

    if ($_POST['pesquisa'] != "") {
        $sql .= " WHERE idproducao LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR pdc_idprodutos LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR prod_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idproducao'] = "width='6%'";
    $dados['prod_nome'] = "";
    $dados['pdc_abertura'] = "width='15%'";
    $dados['pdc_situacao'] = "width='10%'";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, "abreProducao");
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location: ../_Lancamentos/producao_edita.php');
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("producao", "idproducao");
    //
    if(empty($_POST['pdc_data_abertura'])){
      $dataAbertura = " NOW() ";
    }else{
      $dataAbertura = $util->dgr($_POST['pdc_data_abertura']);
    }
    //verifica se devera redefinir os itens
      $sql = "SELECT pdc_idprodutos FROM producao WHERE idproducao = {$id}";
      if($db->retornaUmCampoSql($sql, "pdc_idprodutos") != $_POST['pdc_idprodutos']){
        $redefineItens = true;
      }
    //
    unset($dados);
    $dados['id']                      = $_POST['idproducao'];
  	$dados['pdc_data_abertura']       = $dataAbertura;
    $dados['pdc_data_fechamento']     = $util->dgr($_POST['pdc_data_fechamento']);
    $dados['pdc_idprodutos']          = $util->igr($_POST['pdc_idprodutos']);
    $dados['pdc_qte_produzida']       = $util->vgr($_POST['pdc_qte_produzida']);
    $db->gravarInserir($dados, true);
    //
    $producao = new producao($db, $util);
    //
  	if ($_POST['idproducao'] > 0) {
      $id = $_POST['idproducao'];
      //
      if($redefineItens){
        $producao->insereItens($id);
      }
    }else{
      $id = $db->getUltimoID();
      $producao->insereItens($id);
  }
    header('location: ../_Lancamentos/producao_edita.php?idproducao=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("producao", "idproducao");
    $db->excluir($_POST['idproducao'], "Excluir");
    if($db->erro()){
        $util->mostraErro("Erro ao excluir producao<br>Operação cancelada!");
        exit;
    }
    header('location:../_Lancamentos/producao_edita.php');
    exit;
  }

if ($_POST['operacao'] == 'fechar'){
  $sql = "SELECT pdc_situacao FROM producao WHERE idproducao = {$_POST['idproducao']}";
  if($db->retornaUmCampoSql($sql, "pdc_idprodutos") == "Fechada"){
        $util->mostraErro("Esta produção já está fechada!");
        exit;
    }
  //
  $db->beginTransaction();
  //
  $permiteEstoqueNegativo = $parametros->buscaValor("empresa: permite trabalhar com estoque negativo");
  $sql = "SELECT * FROM producao_itens WHERE pdci_idproducao = {$_POST['idproducao']}";
  $res = $db->consultar($sql);
  foreach ($res as $reg) {
    $sql = "SELECT prod_qte_estoque FROM produtos WHERE idprodutos = {$reg['pdci_idprodutos']}";
    $qteEstoque = $db->retornaUmCampoSql($sql, "prod_qte_estoque");
    $novoEstoque = $qteEstoque - $reg['pdci_qte'];
    if($novoEstoque < 0 AND $permiteEstoqueNegativo == "NAO"){
      $db->rollBack();
      $util->mostraErro("Operação atual reduzira seu estoque para um valor negativo!<br>Operação cancelada!");
      exit;
    }
    //
    $db->setTabela("produtos", "idprodutos");
    //
    unset($dados);
    $dados['id']                = $reg['pdci_idprodutos'];
  	$dados['prod_qte_estoque'] 	= $util->vgr($novoEstoque);
    //
    $db->gravarInserir($dados, true);
    if($db->erro()){
      $db->rollBack();
      $util->mostraErro("Erro ao redefinir estoque!<br>Operação cancelada!");
      exit;
    }
  }
  $sql = "SELECT * FROM producao WHERE idproducao = {$_POST['idproducao']}";
  $reg = $db->retornaUmReg($sql);
  //
  $sql = "SELECT prod_qte_estoque FROM produtos WHERE idprodutos = {$reg['pdc_idprodutos']}";
  $qteEstoque = $db->retornaUmCampoSql($sql, "prod_qte_estoque");
  $novoEstoque = $reg['pdc_qte_produzida'] + $qteEstoque;
  //
  $db->setTabela("produtos", "idprodutos");
  //
  unset($dados);
  $dados['id']                = $reg['pdc_idprodutos'];
  $dados['prod_qte_estoque'] 	= $util->vgr($novoEstoque);
  //
  $db->gravarInserir($dados, true);
   if($db->erro()){
      $db->rollBack();
      $util->mostraErro("Erro ao redefinir estoque!<br>Operação cancelada!");
      exit;
    }
  //
  $db->setTabela("producao", "idproducao");
  //
  unset($dados);
  $dados['id']                  = $reg['idproducao'];
  $dados['pdc_situacao'] 	      = $util->sgr("Fechada");
  $dados['pdc_data_fechamento'] = " NOW() ";
  //
  $db->gravarInserir($dados, true);
   if($db->erro()){
      $db->rollBack();
      $util->mostraErro("Erro ao fechar produção!<br>Operação cancelada!");
      exit;
    }
  $db->commit();
  header('location: ../_Lancamentos/producao_edita.php?idproducao=' . $reg['idproducao']);
  exit;
}

if ($_POST['operacao'] == 'reabrir'){
  $sql = "SELECT pdc_situacao FROM producao WHERE idproducao = {$_POST['idproducao']}";
  if($db->retornaUmCampoSql($sql, "pdc_idprodutos") == "Aberta"){
        $util->mostraErro("Esta produção já está aberta!");
        exit;
    }
  //
  $db->beginTransaction();
  //
  $permiteEstoqueNegativo = $parametros->buscaValor("empresa: permite trabalhar com estoque negativo");
  $sql = "SELECT * FROM producao_itens WHERE pdci_idproducao = {$_POST['idproducao']}";
  $res = $db->consultar($sql);
  foreach ($res as $reg) {
    $sql = "SELECT prod_qte_estoque FROM produtos WHERE idprodutos = {$reg['pdci_idprodutos']}";
    $qteEstoque = $db->retornaUmCampoSql($sql, "prod_qte_estoque");
    $novoEstoque = $qteEstoque + $reg['pdci_qte'];
    //
    $db->setTabela("produtos", "idprodutos");
    //
    unset($dados);
    $dados['id']                = $reg['pdci_idprodutos'];
  	$dados['prod_qte_estoque'] 	= $util->vgr($novoEstoque);
    //
    $db->gravarInserir($dados, true);
    if($db->erro()){
      $db->rollBack();
      $util->mostraErro("Erro ao redefinir estoque!<br>Operação cancelada!");
      exit;
    }
  }
  $sql = "SELECT * FROM producao WHERE idproducao = {$_POST['idproducao']}";
  $reg = $db->retornaUmReg($sql);
  //
  $sql = "SELECT prod_qte_estoque FROM produtos WHERE idprodutos = {$reg['pdc_idprodutos']}";
  $qteEstoque = $db->retornaUmCampoSql($sql, "prod_qte_estoque");
  $novoEstoque = $qteEstoque - $reg['pdc_qte_produzida'];
  if($novoEstoque < 0 AND $permiteEstoqueNegativo == "NAO"){
    $db->rollBack();
    $util->mostraErro("Operação atual reduzira seu estoque para um valor negativo!<br>Operação cancelada!");
    exit;
  }
  //
  $db->setTabela("produtos", "idprodutos");
  //
  unset($dados);
  $dados['id']                = $reg['pdc_idprodutos'];
  $dados['prod_qte_estoque'] 	= $util->vgr($novoEstoque);
  //
  $db->gravarInserir($dados, true);
   if($db->erro()){
      $db->rollBack();
      $util->mostraErro("Erro ao redefinir estoque!<br>Operação cancelada!");
      exit;
    }
  //
  $db->setTabela("producao", "idproducao");
  //
  unset($dados);
  $dados['id']                  = $reg['idproducao'];
  $dados['pdc_situacao'] 	      = $util->sgr("Aberta");
  $dados['pdc_data_fechamento'] = " NULL ";
  //
  $db->gravarInserir($dados, true);
   if($db->erro()){
      $db->rollBack();
      $util->mostraErro("Erro ao reabrir produção!<br>Operação cancelada!");
      exit;
    }
  $db->commit();
  header('location: ../_Lancamentos/producao_edita.php?idproducao=' . $reg['idproducao']);
  exit;
}

echo "Operação enviada: " . $_REQUEST['operacao'];
echo "Erro ao executar, operação não encontarda!";
exit;
 ?>
