<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'forma_pagto_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * 
            FROM forma_pagto";    
    if ($_POST['pesquisa'] != "") {
        $sql .= " WHERE idforma_pagto LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR forp_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idforma_pagto'] = "width='6%'";
    $dados['forp_nome'] = "";
    $dados['forp_tipo'] = "width='10%'";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("forma_pagto", "idforma_pagto");
    //
    $dados['id']              = $_POST['id_cadastro'];
  	$dados['forp_nome'] 			= $util->sgr($_POST['forp_nome']);
  	$dados['forp_tipo']       = $util->sgr($_POST['forp_tipo']);
    $db->gravarInserir($dados, true);
    //
  	if ($_POST['id_cadastro'] > 0) {
  		$id = $_POST['id_cadastro'];
    }else{
  		$id = $db->getUltimoID();
  }
    header('location: ../_Cadastros/' . $paginaRetorno . '?id_cadastro=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("forma_pagto", "idforma_pagto");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $util->mostraErro("Erro ao excluir forma de pagamento<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
