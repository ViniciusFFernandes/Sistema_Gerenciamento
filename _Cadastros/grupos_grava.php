<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
  if ($_POST['operacao'] == "buscaGrupos") {
    $sql = "SELECT * FROM grupos";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idgrupos LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR grup_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idgrupos'] = "width='6%'";
    $dados['grup_nome'] = "";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, "abreGrupos");
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/grupos_edita.php');
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("grupos", "idgrupos");

    unset($dados);
    $dados['id']            = $_POST['idgrupos'];
  	$dados['grup_nome'] 	  = $util->sgr($_POST['grup_nome']);
    $db->gravarInserir($dados, true);

  	if ($_POST['idgrupos'] > 0) {
  		$id = $_POST['idgrupos'];
    }else{
  		$id = $db->getUltimoID();
  }
  header('location:../_Cadastros/grupos_edita.php?idgrupos=' . $id);
  exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("grupos", "idgrupos");
    $db->excluir($_POST['idgrupos'], "Excluir");
    if($db->erro()){
        $util->mostraErro("Erro ao excluir produto<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/grupos_edita.php');
    exit;
  }

 ?>
