<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/atualizacao.class.php");
//
$atualizacao = new Atualizacao($db, $parametros, $util);
// print_r($_POST);
// exit;
if($_POST['operacao'] == 'atualizarSistema'){
	$versaoAtual = $parametros->buscaValor("sistema: versao do sistema");
	$dadosRetorno = $atualizacao->atualizarSistema($versaoAtual);
	//
	if($dadosRetorno['executado']){
		//
		//Atualiza a versão dos parametros
		$dados['id']            = $util->sgr("sistema: versao do sistema");
		$dados['para_valor'] 	= $util->sgr($dadosRetorno['novaVersao']);
		$parametros->gravaValor($dados);
		//
		//Grava um historico das atualizações
		$db->setTabela("versao_hist", "idversao_hist");
		unset($dados);
		$dados['vhist_versao'] 		= $util->sgr($dadosRetorno['novaVersao']);
		$dados['vhist_mensagem'] 	= $util->sgr($dadosRetorno['msg']);
		$dados['vhist_data'] 		= " NOW() ";
		$db->gravarInserir($dados);
	}
  	//
	echo json_encode($dadosRetorno);
	exit;
}

if($_POST['operacao'] == 'buscarHistorico'){
  	//
	echo $atualizacao->geraHistorico();
	exit;
}

if($_POST['operacao'] == 'baixarAtualizacoes'){
	//
  echo $atualizacao->baixaAtualizacao(true);
  exit;
}
 ?>

