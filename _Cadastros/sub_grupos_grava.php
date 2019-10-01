<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
  if ($_POST['operacao'] == "buscaSubGrupos") {
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
    $tabelas->geraTabelaBusca($res, $db, $dados, "abreSubGrupos");
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/sub_grupos_edita.php');
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("subgrupos", "idsubgrupos");

    unset($dados);
    $dados['id']            = $_POST['idsubgrupos'];
  	$dados['subg_nome'] 	  = $util->sgr($_POST['subg_nome']);
    $db->gravarInserir($dados, true);

  	if ($_POST['idgrupos'] > 0) {
  		$id = $_POST['idgrupos'];
    }else{
  		$id = $db->getUltimoID();
  }
  header('location:../_Cadastros/sub_grupos_edita.php?idsubgrupos=' . $id);
  exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("subgrupos", "idsubgrupos");
    $db->excluir($_POST['idsubgrupos'], "Excluir");
    if($db->erro()){
        $util->mostraErro("Erro ao excluir sub grupo<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/sub_grupos_edita.php');
    exit;
  }

 ?>
