<?php
  include_once("../_BD/conecta_login.php");
  include_once("../Class/autoComplete.class.php");
  
  if (isset($_SESSION['mensagem'])) {
    $msg = $util->mostraMensagem($_SESSION['tipoMsg'], $_SESSION['mensagem']);
    unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
  }
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $util->buscaHtml("");
  $html = str_replace("##Mensagem##", $msg, $html);
  $html = str_replace("##autoComplete_Estados##", $codigo_js, $html);
  $html = str_replace("##idunidades##", $reg['idunidades'], $html);
  $html = str_replace("##uni_nome##", $reg['uni_nome'], $html);
  $html = str_replace("##uni_sigla##", $reg['uni_sigla'], $html);
  $html = str_replace("##btnExcluir##", $btnExcluir, $html);
  echo $html;
  exit;
?>