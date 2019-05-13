<?php 
  //Inicia Sessão
  session_start(); 
  //
  //Desativa os erros e permite apenas avisos
  error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
  //
  //Verifica se já está logado
  if($_POST['senha'] != "vini1528"){
    $_SESSION['logado'] = false;
    $_SESSION['mensagem'] = "Senha incorreta! <br> Constante não foi criado!";
    $_SESSION['tipoMsg'] = "danger";
    header('Location: ../../index.php');
    exit;
  }
  if(realpath("../constantes.vf")){
    $_SESSION['logado'] = false;
    $_SESSION['mensagem'] = "Constante já existe, não é possivel criar novamente!";
    $_SESSION['tipoMsg'] = "danger";
    header('Location: ../../index.php');
    exit;
  }
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = file_get_contents('criaConstante.html');
  echo $html;
  exit;
?>
