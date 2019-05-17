<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
  if ($_POST['operacao'] == "buscaUnidades") {
    $db->setTabela("unidades");
    //
    if ($_POST['pesquisa'] == "") {
       $res = $db->consultar();
    }else{
      $where = "  idunidades LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR uni_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR uni_sigla LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
       $res = $db->consultar($where);
    }
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idunidades'] = "width='6%'";
    $dados['uni_nome'] = "";
    $dados['uni_sigla'] = "width='10%'";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/cadastro_unidades.php');
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("unidades");

  	$dados['uni_nome'] 	 = $util->sgr($_POST['uni_nome']);
  	$dados['uni_sigla']  = $util->sgr($_POST['uni_sigla']);

  	if ($_POST['idunidades'] > 0) {
  		$where = " idunidades = " . $_POST['idunidades'];
  		$db->alterar($where, $dados);
  		$_SESSION['mensagem'] = "Alteração efetuado com sucesso!";
      $_SESSION['tipoMsg'] = "info";
      header('location: ../_Cadastros/cadastro_unidades.php?idunidades=' . $_POST['idunidades']);
  		exit;
    }else{
  		$db->gravar($dados);
  		$ultimoID = $db->getUltimoID();
  		$_SESSION['mensagem'] = "Cadastro efetuada com sucesso!";
      $_SESSION['tipoMsg'] = "info";
      header('location:../_Cadastros/cadastro_unidades.php?idunidades=' . $ultimoID);
  		exit;
  }
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("unidades");
    $where = "idunidades = " . $_POST['idunidades'];
    $db->excluir($where);
    $_SESSION['mensagem'] = "Cadastro excluido com sucesso!";
    $_SESSION['tipoMsg'] = "danger";
    header('location:../_Cadastros/cadastro_unidades.php');
    exit;
  }

 ?>
