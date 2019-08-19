<?php
  include_once("../_BD/conecta_login.php");

  if($_POST['operacao'] == "consultaAjax"){
  	$parametros->tabelaParametros($_POST['filtro'], $util);
  	exit;
  }

  if($_POST['operacao'] == "buscaDadosAjax"){
  	$dados = $parametros->retornaDados($_POST['idparametros']);
  	//print_r($res);
  	echo json_encode($dados);
  	exit;
  }  

  if($_POST['operacao'] == "gravar"){
  	unset($dados);
  	$dados['id']            = $_POST['idparametros'];
  	$dados['para_valor'] 	= $util->sgr($_POST['para_valor']);
  	$dados['para_obs'] 		= $util->sgr($_POST['para_obs']);
  	$parametros->gravaValor($_POST['idparametros'], $dados, $db);
  	exit;
  } 
?>