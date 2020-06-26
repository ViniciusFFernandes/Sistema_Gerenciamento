<?php
  require_once("../_BD/conecta_login.php");
  require_once("../Class/atualizacao.class.php");
  
  $atualizacao = new Atualizacao($db, $parametros, $util);
  $ultimaVersao = $atualizacao->getUltimaVersao();
  $versaoAtual = $parametros->buscaValor("sistema: versao do sistema");
  if($ultimaVersao > $versaoAtual){
    $btnAtualizar = '<button class="btn btn-success" onclick="atualizarSistema()"> Atualizar Sistema</button>';
  }else{
    $btnAtualizar = "Seu sistema já está totalmente atualizado!";
  }
  if($usuarios->usuario_pode_executar("versao_atualizacao.php-Permite gerar versoes de atualizacao")){
    $btnEnviarAtt = '<button class="btn btn-default" style="float: right; padding: 0px 3px; cursor: pointer;" title="Enviar atualizações" onclick="enviarAtualizacao(' . $_SESSION['idusuario'] . ')" data-toggle="modal" data-target="#atualizacaoSistema"><img src="../icones/enviar_atualizacao.png"></button>';
  }  
  unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $html->buscaHtml("", $parametros);
  $html = str_replace("##Mensagem##", $msg, $html);
  $html = str_replace("##versaoSistema##", $versaoAtual, $html);
  $html = str_replace("##ultimaVersaoSistema##", $ultimaVersao, $html);
  $html = str_replace("##btnAtualizar##", $btnAtualizar, $html);
  $html = str_replace("##btnEnviarAtt##", $btnEnviarAtt, $html);
  echo $html;
  exit;
?>