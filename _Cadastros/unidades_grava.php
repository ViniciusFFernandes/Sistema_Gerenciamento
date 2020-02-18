<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'unidades_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
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
    $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("unidades", "idunidades");

    unset($dados);
    $dados['id']         = $_POST['id_cadastro'];
  	$dados['uni_nome'] 	 = $util->sgr($_POST['uni_nome']);
  	$dados['uni_sigla']  = $util->sgr($_POST['uni_sigla']);
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
    $db->setTabela("unidades", "idunidades");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $util->mostraErro("Erro ao excluir unidade<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
