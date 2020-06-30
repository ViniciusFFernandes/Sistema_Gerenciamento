<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.classs.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'meio_pagto_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * FROM meio_pagto";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idmeio_pagto LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR mpag_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idmeio_pagto'] = "width='6%'";
    $dados['mpag_nome'] = "";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("meio_pagto", "idmeio_pagto");

    unset($dados);
    $dados['id']            = $_POST['id_cadastro'];
  	$dados['mpag_nome'] 	  = $util->sgr($_POST['mpag_nome']);
    $db->gravarInserir($dados, true);

  	if ($_POST['id_cadastro'] > 0) {
  		$id = $_POST['id_cadastro'];
    }else{
  		$id = $db->getUltimoID();
  }
  header('location:../_Cadastros/' . $paginaRetorno . '?id_cadastro=' . $id);
  exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("meio_pagto", "idmeio_pagto");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
