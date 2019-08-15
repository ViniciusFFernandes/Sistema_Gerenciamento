<?php
  include_once("../_BD/conecta_login.php");
  
  if (isset($_SESSION['mensagem'])) {
    $msg = $util->mostraMensagem($_SESSION['tipoMsg'], $_SESSION['mensagem']);
    unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
  }
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $util->buscaHtml("");
  $html = str_replace("##Mensagem##", $msg, $html);
  echo $html;
  exit;
?>