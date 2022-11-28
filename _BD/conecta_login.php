<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
date_default_timezone_set('America/Sao_Paulo');
if(!realpath("../privado/constantes.vf")){
	header("Location: ../privado/_Constante/criaConstante_login.html");
	exit;
}
// print_r($_REQUEST);exit;
require_once("../set_path.php");
require_once("DB.class.php");
require_once("util.class.php");
require_once("logs.class.php");
require_once("parametros.class.php");
require_once("atualizacao.class.php");
require_once("tarefas_diarias.class.php");
require_once("usuarios.class.php");
require_once("html.class.php");
require_once("constantes.vf");
//
//Se não existe define como null para evitar avisos de erro
if (!isset($_POST['operacao'])) {
	$_POST['operacao'] = null;
}
if (!isset($_SESSION['logado'])) {
	$_SESSION['logado'] = null;
}
//
//inicia as classes nescessarias
$util = new Util();
$db = new Db($SERVIDOR, $PORTA, $USUARIO, $SENHA, $DB_NAME);
$log = new log();
$parametros = new Parametros($db);
$html = new html($db);
$atualizacao = new Atualizacao($db);
//$chat = new Chat();
//
//Conecta com o banco de dados
$db->conectar();
//
//inicio das operações
//
//Efetua o login
if ($_POST['operacao'] == "logar") {
	$db->setTabela("pessoas", "idpessoas");
	$resultado = false; 
	$dados = $db->buscarUsuario($_POST['usuario']);
	if($dados['idpessoas'] > 0 && $_POST['senha'] == $dados['pess_senha']){
		//
		// 25/11/2022 Vinicius
		// Removido pois agora o tarefas diarias será executado todos os dias uma vez por dia por agendamento de tarefas no servidor
		//
		//Executa tarefas diarias no primeiro login bem sucedido do dia
		//$tarefasDiarias = new Tarefas_Diarias($parametros, $db, $util, $atualizacao);
		//$tarefasDiarias->executa_tarefas();
		//
		$_SESSION['logado'] 						= true;
		$_SESSION['user'] 							= $_POST['usuario'];
		$_SESSION['idusuario']				 	    = $dados['idpessoas'];
		$_SESSION['idgrupos_acessos']				= $dados['pess_idgrupos_acessos'];
	    $_SESSION['ultima_atividade'] 				= time();
	    $_SESSION['permanece_logado'] 				= $_POST['permanece_logado'];
		header('Location: ../_Inicio/inicio.php');
		exit;
	}else{
		$_SESSION['logado'] = false;
		$_SESSION['mensagem'] = "Usuario ou senha incorretos!!!<br>Tente novamente";
    	$_SESSION['tipoMsg'] = "danger";
		header('location:../index.php');
		exit;
	}
}

//
//Operação para deslogar do sistema
if ($_POST['operacao'] == "Sair") {
	session_destroy();
	header('Location: ../index.php');
	exit;
}

//
//Caso tente acessar as paginas pela url e nao esteja logado
if (!$_SESSION['logado']) {
	$html->mostraErro("Você não esta logado.<br>Para continuar é necessário que faça o login!", "../index.php");
	exit;
}

if (((time() - $_SESSION['ultima_atividade']) > 1800) && ($_SESSION['permanece_logado'] != 'SIM')) {
    // última atividade foi mais de 10 minutos atrás
    session_unset();     // unset $_SESSION
    session_destroy();   // destroindo session data
		//
    header('Location: ../index.php');
	exit;
}

$_SESSION['ultima_atividade'] = time(); // update ultima ativ.
//
//Verifica se o ususario pode acessar a pagina atual
$usuarios = new Usuarios($db, $util, $_SESSION['idusuario'], $_SESSION['idgrupos_acessos']);
if(!$usuarios->usuario_pode_executar()){
	$html->mostraErro("Você não tem permissão para executar este programa!<br>Consulte um administrador do sistema!<br> Programa: " . basename($_SERVER['PHP_SELF']));
	exit;
}
?>
