<?php
  require_once("../_BD/conecta_login.php");
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $html->buscaHtml("inicio");
  $html = str_replace("##Mensagem##", $msg, $html);
  echo $html;
  exit;
?>
