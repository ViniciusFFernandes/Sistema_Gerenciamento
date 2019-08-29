<?php
  include_once("../_BD/conecta_login.php");
  include_once("../Class/atualizacao.class.php");
  
  $atualizacao = new Atualizacao($db, $parametros, $util);
  $ultimaVersao = $atualizacao->getUltimaVersao();
  $versaoAtual = $parametros->buscaValor("sistema: versao do sistema");
  if($ultimaVersao > $versaoAtual){
    $btnAtualizar = '<button class="btn btn-success" onclick="atualizarSistema()"> Atualizar Sistema</button>';
  }else{
    $btnAtualizar = "Seu sistema já está totalmente atualizado!";
  }
  
  unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $util->buscaHtml("");
  $html = str_replace("##Mensagem##", $msg, $html);
  $html = str_replace("##versaoSistema##", $versaoAtual, $html);
  $html = str_replace("##ultimaVersaoSistema##", $ultimaVersao, $html);
  $html = str_replace("##btnAtualizar##", $btnAtualizar, $html);
  echo $html;
  exit;
?>