<?php
  include_once '../_BD/conecta_login.php';
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $util->buscaHtml("inicio", $parametros);
  $html = str_replace("##Mensagem##", $msg, $html);
  echo $html;
  exit;
?>
