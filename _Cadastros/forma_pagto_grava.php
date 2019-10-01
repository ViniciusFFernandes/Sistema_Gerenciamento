<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
  if ($_POST['operacao'] == "buscaFormaPagto") {
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
    $tabelas->geraTabelaBusca($res, $db, $dados, "abreFormaPagto");
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/forma_pagto_edita.php');
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("forma_pagto", "idforma_pagto");
    //
    $dados['id']              = $_POST['idforma_pagto'];
  	$dados['forp_nome'] 			= $util->sgr($_POST['forp_nome']);
  	$dados['forp_tipo']       = $util->sgr($_POST['forp_tipo']);
    $db->gravarInserir($dados, true);
    //
  	if ($_POST['idforma_pagto'] > 0) {
  		$id = $_POST['idforma_pagto'];
    }else{
  		$id = $db->getUltimoID();
  }
    header('location: ../_Cadastros/forma_pagto_edita.php?idforma_pagto=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("forma_pagto", "idforma_pagto");
    $db->excluir($_POST['idforma_pagto'], "Excluir");
    if($db->erro()){
        $util->mostraErro("Erro ao excluir forma de pagamento<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/forma_pagto_edita.php');
    exit;
  }

 ?>
