<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
  if ($_POST['operacao'] == "buscaUnidades") {
    $sql = "SELECT * FROM unidades";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idunidades LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR uni_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR uni_sigla LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idunidades'] = "width='6%'";
    $dados['uni_nome'] = "";
    $dados['uni_sigla'] = "width='10%'";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, "abreUnidades");
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/cadastro_unidades.php');
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("unidades", "idunidades");

    $dados['id']         = $_POST['idunidades'];
  	$dados['uni_nome'] 	 = $util->sgr($_POST['uni_nome']);
  	$dados['uni_sigla']  = $util->sgr($_POST['uni_sigla']);
    $db->gravarInserir($dados);

  	if ($_POST['idunidades'] > 0) {
  		$id = $_POST['idunidades'];
    }else{
  		$id = $db->getUltimoID();
  }
  header('location:../_Cadastros/cadastro_unidades.php?idunidades=' . $id);
  exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("unidades", "idunidades");
    $db->excluir($_POST['idunidades']);
    header('location:../_Cadastros/cadastro_unidades.php');
    exit;
  }

 ?>
