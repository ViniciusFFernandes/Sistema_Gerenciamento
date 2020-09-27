<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'sub_grupos_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * FROM subgrupos";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idsubgrupos LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR subg_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idsubgrupos'] = "width='6%'";
    $dados['subg_nome'] = "";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("subgrupos", "idsubgrupos");

    unset($dados);
    $dados['id']            = $_POST['id_cadastro'];
  	$dados['subg_nome'] 	  = $util->sgr($_POST['subg_nome']);
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
    $db->setTabela("subgrupos", "idsubgrupos");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
