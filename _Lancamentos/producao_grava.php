<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
  if ($_POST['operacao'] == "buscaProducao") {
    $sql = "SELECT * 
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
    header('location:../_Cadastros/producao_edita.php');
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("producao", "idproducao");
    //
    $dados['id']                      = $_POST['idproducao'];
  	$dados['idproducao'] 			        = $util->igr($_POST['idproducao']);
  	$dados['pdc_data_abertura']       = $util->dgr($_POST['pdc_data_abertura']);
    $dados['pdc_data_fechamento']     = $util->dgr($_POST['pdc_data_fechamento']);
    $dados['pdc_idprodutos']          = $util->igr($_POST['pdc_idprodutos']);
    $dados['pdc_qte_produzida']       = $util->vgr($_POST['pdc_qte_produzida']);
    $dados['pdc_calcula_automatico']  = $util->sgr($_POST['pdc_calcula_automatico']);
    $db->gravarInserir($dados, true);
    //
  	if ($_POST['idcidades'] > 0) {
  		$id = $_POST['idcidades'];
    }else{
  		$id = $db->getUltimoID();
  }
    header('location: ../_Cadastros/cidades_edita.php?idcidades=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("cidades", "idcidades");
    $db->excluir($_POST['idcidades'], true);
    header('location:../_Cadastros/cidades_edita.php');
    exit;
  }

 ?>
