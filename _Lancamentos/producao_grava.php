<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
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
    header('location:../_Lancamentos/producao_edita.php');
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
    //
    $dados['id']                      = $_POST['idproducao'];
  	$dados['pdc_data_abertura']       = $dataAbertura;
    $dados['pdc_data_fechamento']     = $util->dgr($_POST['pdc_data_fechamento']);
    $dados['pdc_idprodutos']          = $util->igr($_POST['pdc_idprodutos']);
    $dados['pdc_qte_produzida']       = $util->vgr($_POST['pdc_qte_produzida']);
    $dados['pdc_calcula_automatico']  = $util->sgr($_POST['pdc_calcula_automatico']);
    $db->gravarInserir($dados, true);
    //
  	if ($_POST['idproducao'] > 0) {
  		$id = $_POST['idproducao'];
    }else{
  		$id = $db->getUltimoID();
  }
    header('location: ../_Lancamentos/producao_edita.php?idproducao=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("producao", "idproducao");
    $db->excluir($_POST['idproducao'], true);
    header('location:../_Lancamentos/producao_edita.php');
    exit;
  }

 ?>
