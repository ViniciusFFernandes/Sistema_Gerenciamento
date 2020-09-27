<?php
  require_once("../_BD/conecta_login.php");
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $html->buscaHtml("lancamentos", $parametros);
  echo $html;
  exit;
?>
