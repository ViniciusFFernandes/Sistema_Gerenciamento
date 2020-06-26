<?php 
  //Inicia Sessão
  session_start(); 
  //
  //Desativa os erros e permite apenas avisos
  error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
  //
  //Verifica se já está logado
  if($_SESSION['logado']){
    header('Location: _Inicio/inicio.php');
    exit;
  }
  //
  require_once("privado/constantes.vf");
  //
  //Inclui classes
  require_once("Class/Util.class.php");
  require_once("Class/html.class.php");
  $util = new Util();
  $html = new html('', $util);
  //
  //Monta variaveis de exibição
  if (isset($_SESSION['mensagem'])) {
    $msg = $html->mostraMensagem($_SESSION['tipoMsg'], $_SESSION['mensagem']);
    unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
  }
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = file_get_contents('login.html');
  $html = str_replace("##Mensagem##", $msg, $html);
  $html = str_replace("##nomeSistema##", NOME_SISTEMA, $html);
  echo $html;
  exit;
?>
