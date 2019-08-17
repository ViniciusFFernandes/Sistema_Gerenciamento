<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
  if ($_POST['operacao'] == "buscaCidades") {
    $sql = "SELECT * 
            FROM cidades 
              LEFT JOIN estados ON (cid_idestados = idestados)";
    
    if ($_POST['pesquisa'] != "") {
        $sql .= " WHERE idcidades LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR cid_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR est_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idcidades'] = "width='6%'";
    $dados['cid_nome'] = "";
    $dados['est_uf'] = "width='10%'";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, "abreCidades");
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/cidades_edita.php');
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("cidades", "idcidades");
    //
    $dados['id']            = $_POST['idcidades'];
  	$dados['cid_nome'] 			= $util->sgr($_POST['cid_nome']);
  	$dados['cid_idestados'] = $util->igr($_POST['cid_idestados']);
    $db->gravarInserir($dados);
    //
  	if ($_POST['idcidades'] > 0) {
  		$id = $_POST['idcidades'];
    }else{
  		$id = $db->getUltimoID();
  }
    header('location: ../_Cadastros/cidades_edita.php?idcidades=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("cidades", "idcidades");
    $db->excluir($_POST['idcidades']);
    header('location:../_Cadastros/cidades_edita.php');
    exit;
  }

 ?>
