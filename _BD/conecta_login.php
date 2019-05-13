<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
if(!realpath("../privado/constantes.vf")){
	header("Location: ../privado/_Constante/criaConstante_login.html");
	exit;
}
require_once("../Class/DB.class.php");
require_once("../Class/Util.class.php");
require_once("../Class/usuario.class.php");
require_once("../Class/logs.class.php");
require_once("../privado/constantes.vf");
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
//$chat = new Chat();
//
//Conecta com o banco de dados
$db->conectar();
//
//inicio das operações
//
//
//Efetua o login
if ($_POST['operacao'] == "logar") {
 	$db->setTabela("pessoas");
 	$user = new Usuario($_POST['usuario'], $_POST['senha']);
	$resultado = $user->conferirSenha($db);
	if ($resultado['retorno']){
		$_SESSION['logado'] 						= true;
		$_SESSION['user'] 							= $_POST['usuario'];
		$_SESSION['senha'] 							= $_POST['senha'];
		$_SESSION['idusuario']				 	    = $resultado['idpessoas'];
		$_SESSION['mensagem'] 					    = "Logado com sucesso!<br>Bem vindo!!!";
    $_SESSION['tipoMsg'] 						    = "success";
    $_SESSION['ultima_atividade'] 	= time();
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
	$util->mostraErro("Você não esta logado.<br>Para continuar é necessário que faça o login!", "../index.php");
	exit;
}

if ((time() - $_SESSION['ultima_atividade']) > 600) {
    // última atividade foi mais de 10 minutos atrás
    session_unset();     // unset $_SESSION
    session_destroy();   // destroindo session data
		//
    header('Location: ../index.php');
		exit;
}

$_SESSION['ultima_atividade'] = time(); // update ultima ativ.
?>
