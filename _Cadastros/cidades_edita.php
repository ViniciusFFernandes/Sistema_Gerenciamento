﻿<?php
  include_once("../_BD/conecta_login.php");
  include_once("../Class/autoComplete.class.php");
  //
  //Operações do banco de dados
  if(!empty($_REQUEST['idcidades'])){
    $sql = "SELECT * 
            FROM cidades 
              LEFT JOIN estados ON (cid_idestados = idestados) 
            WHERE idcidades = {$_REQUEST['idcidades']}";
    $reg = $db->retornaUmReg($sql);
  }
  //
  //Gera o autoComplete 
  $autoComplete = new autoComplete();
  $codigo_js = $autoComplete->gerar("cid_estados", "cid_idestados", "estados", "est_nome", "idestados", "", "WHERE UPPER(est_nome) LIKE UPPER('##valor##%')");
 //echo $codigo_js;exit;
  //
  //Monta variaveis de exibição
  if(!empty($reg['idcidades'])){ 
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
  $html = str_replace("##autoComplete_Estados##", $codigo_js, $html);
  $html = str_replace("##idcidades##", $reg['idcidades'], $html);
  $html = str_replace("##cid_nome##", $reg['cid_nome'], $html);
  $html = str_replace("##cid_estados##", $reg['est_nome'], $html);
  $html = str_replace("##cid_idestados##", $reg['idestados'], $html);
  $html = str_replace("##btnExcluir##", $btnExcluir, $html);
  echo $html;
  exit;
?>