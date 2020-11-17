<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'cc_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * FROM cc";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idcc LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR cc_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idcc'] = "width='6%'";
    $dados['cc_nome'] = "";
    //
    $cabecalho['Código'] = '';
    $cabecalho['Nome'] = '';
    //
    echo $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno, $cabecalho);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("cc", "idcc");

    unset($dados);
    $dados['id']                = $_POST['id_cadastro'];
  	$dados['cc_nome'] 	        = $util->sgr($_POST['cc_nome']);
  	$dados['cc_agencia'] 	      = $util->sgr($_POST['cc_agencia']);
  	$dados['cc_agencia_dg'] 	  = $util->sgr($_POST['cc_agencia_dg']);
  	$dados['cc_conta'] 	        = $util->sgr($_POST['cc_conta']);
  	$dados['cc_conta_dg'] 	    = $util->sgr($_POST['cc_conta_dg']);
  	$dados['cc_idbancos'] 	    = $util->igr($_POST['cc_idbancos']);
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
    $db->setTabela("cc", "idcc");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
