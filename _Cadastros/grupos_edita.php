﻿<?php
  include_once("../_BD/conecta_login.php");
  //
  //Operações do banco de dados
  if(!empty($_REQUEST['idgrupos'])){
    $sql = "SELECT * 
            FROM grupos 
            WHERE idgrupos = {$_REQUEST['idgrupos']}";
    $reg = $db->retornaUmReg($sql);
  }
  //
  //Monta variaveis de exibição
  if(!empty($reg['idgrupos'])){ 
    $btnExcluir = '<button type="button" onclick="excluiCadastro()" class="btn btn-danger">Excluir</button>';
  }
  //
  if (isset($_SESSION['mensagem'])) {
    $msg = $util->mostraMensagem($_SESSION['tipoMsg'], $_SESSION['mensagem']);
    unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
  }
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $util->buscaHtml("cadastros");
  $html = str_replace("##Mensagem##", $msg, $html);
  $html = str_replace("##idgrupos##", $reg['idgrupos'], $html);
  $html = str_replace("##grup_nome##", $reg['grup_nome'], $html);
  $html = str_replace("##btnExcluir##", $btnExcluir, $html);
  echo $html;
  exit;
?>