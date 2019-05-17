<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
  if ($_POST['operacao'] == "buscaCidades") {
    $db->setTabela("cidades LEFT JOIN estados ON (cid_idestados = idestados)");
    //
    if ($_POST['pesquisa'] == "") {
       $res = $db->consultar();
    }else{
      $where = "  idcidades LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR cid_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR est_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
       $res = $db->consultar($where);
    }
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idcidades'] = "width='6%'";
    $dados['cid_nome'] = "";
    $dados['est_uf'] = "width='10%'";
    //
    $tabelas->geraTabelaCid($res, $db);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/cadastro_cidades.php');
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("cidades");

  	$dados['cid_nome'] 			= $util->sgr($_POST['cid_nome']);
  	$dados['cid_idestados'] = $util->igr($_POST['cid_idestados']);

  	if ($_POST['idcidades'] > 0) {
  		$where = " idcidades = " . $_POST['idcidades'];
  		$db->alterar($where, $dados);
  		$_SESSION['mensagem'] = "Alteração efetuado com sucesso!";
      $_SESSION['tipoMsg'] = "info";
      header('location: ../_Cadastros/cadastro_cidades.php?idcidades=' . $_POST['idcidades']);
  		exit;
    }else{
  		$db->gravar($dados);
  		$ultimoID = $db->getUltimoID();
  		$_SESSION['mensagem'] = "Cadastro efetuada com sucesso!";
      $_SESSION['tipoMsg'] = "info";
      header('location:../_Cadastros/cadastro_cidades.php?idcidades=' . $ultimoID);
  		exit;
  }
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("cidades");
    $where = "idcidades = " . $_POST['idcidades'];
    $db->excluir($where);
    $_SESSION['mensagem'] = "Cadastro excluido com sucesso!";
    $_SESSION['tipoMsg'] = "danger";
    header('location:../_Cadastros/cadastro_cidades.php');
    exit;
  }

 ?>
