<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/atualizacao.class.php");
// print_r($_POST);
// exit;
if($_POST['operacao'] == 'atualizarSistema'){
	$versaoAtual = $parametros->buscaValor("sistema: versao do sistema");
	$atualizacao = new Atualizacao();
	$dadosRetorno = $atualizacao->atualizarSistema($versaoAtual);
	//
	$dados['id']            = $util->sgr("sistema: versao do sistema");
  	$dados['para_valor'] 	= $util->sgr($dadosRetorno['novaVersao']);
  	$parametros->gravaValor($util->sgr("sistema: versao do sistema"), $dados, $db);
  	//
	echo json_encode($dadosRetorno);
	exit;
}
 ?>

