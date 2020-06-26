<?php
  require_once("../_BD/conecta_login.php");

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
  	$dados['id']         		 	= $util->sgr($_POST['idparametros']);
  	$dados['para_valor'] 			= $util->sgr($_POST['para_valor']);
  	$dados['para_obs'] 				= $util->sgr($_POST['para_obs']);
  	$dados['para_tipo'] 			= $util->sgr($_POST['para_tipo']);
  	$dados['para_nome_constante'] 	= $util->sgr($_POST['para_nome_constante']);
	$parametros->gravaValor($dados);
  	exit;
  } 
?>