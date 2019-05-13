<?php 
  //Inicia Sessão
  session_start(); 
  //
  //Desativa os erros e permite apenas avisos
  error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
  //
  if(empty($_POST['servidor'])){
    $_POST['servidor'] = 'localhost';
  }
  if(empty($_POST['porta'])){
    $_POST['porta'] =  '3306';
  }
  if(empty($_POST['usuario'])){
    $_POST['usuario'] =  'root';
  }
  if(empty($_POST['senha'])){
    $_POST['senha'] =  '';
  }
  if(empty($_POST['nomeBase'])){
    $_POST['nomeBase'] = 'DB_Base';
  }
  //
  $constante = fopen("../constantes.vf", "a");
  //
  $textoConstante = '<?php $SERVIDOR   = "' . $_POST['servidor'] . '";
  $PORTA    = "' . $_POST['porta'] . '";
  $USUARIO  = "' . $_POST['usuario'] . '";
  $SENHA    = "' . $_POST['senha'] . '";
  $DB_NAME  = "' . $_POST['nomeBase'] . '";
  ?>';
  //
  fwrite($constante, $textoConstante);
  fclose($constante);
  //
  $_SESSION['logado'] = false;
  $_SESSION['mensagem'] = "Constante criado! <br> Porfavor efetue o login novamente!";
  $_SESSION['tipoMsg'] = "info";
  header('Location: ../../index.php');
  exit;
?>
