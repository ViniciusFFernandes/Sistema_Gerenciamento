<?php
require_once("../_BD/conecta_login.php");
require_once("Tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'grupos_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
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
    $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("grupos", "idgrupos");

    unset($dados);
    $dados['id']            = $_POST['id_cadastro'];
  	$dados['grup_nome'] 	  = $util->sgr($_POST['grup_nome']);
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
    $db->setTabela("grupos", "idgrupos");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
