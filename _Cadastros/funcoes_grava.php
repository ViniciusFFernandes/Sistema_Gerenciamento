<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.classs.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'funcoes_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * FROM funcoes";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idfuncoes LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR func_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idfuncoes'] = "width='6%'";
    $dados['func_nome'] = "";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("funcoes", "idfuncoes");

    unset($dados);
    $dados['id']            = $_POST['id_cadastro'];
  	$dados['func_nome'] 	= $util->sgr($_POST['func_nome']);
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
    $db->setTabela("funcoes", "idfuncoes");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
