<?php
  include_once("../_BD/conecta_login.php");
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $util->buscaHtml("cadastros");
  echo $html;
  exit;
?>
