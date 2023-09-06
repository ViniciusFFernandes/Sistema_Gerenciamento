<?php
  require_once("../_BD/conecta_login.php");
  //
  //Operações do banco de dados
  if(!empty($_REQUEST['id_cadastro'])){
    $sql = "SELECT * 
            FROM cc 
            WHERE idcc = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
  }
  //
  //Monta variaveis de exibição
  //
  $sql = "SELECT * FROM bancos";
  $comboBoxBancos = $html->criaSelectSql("banc_nome", "idbancos", "cc_idbancos", $reg['cc_idbancos'], $sql, "form-control", "", true, "Selecione um Banco");
  //
  if(!empty($reg['idcc'])){ 
    //
    $btnExcluir = '<button type="button" onclick="excluiCadastro()" class="btn btn-danger">Excluir</button>';
  }
  //
  if (isset($_SESSION['mensagem'])) {
    $msg = $html->mostraMensagem($_SESSION['tipoMsg'], $_SESSION['mensagem']);
    unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
  }
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $html->buscaHtml(true);
  $html = str_replace("##Mensagem##", $msg, $html);
  $html = str_replace("##id_cadastro##", $reg['idcc'], $html);
  $html = str_replace("##cc_nome##", $reg['cc_nome'], $html);
  $html = str_replace("##cc_agencia##", $reg['cc_agencia'], $html);
  $html = str_replace("##cc_agencia_dg##", $reg['cc_agencia_dg'], $html);
  $html = str_replace("##cc_conta##", $reg['cc_conta'], $html);
  $html = str_replace("##cc_conta_dg##", $reg['cc_conta_dg'], $html);
  $html = str_replace("##comboBoxBancos##", $comboBoxBancos, $html);
  $html = str_replace("##btnExcluir##", $btnExcluir, $html);
  echo $html;
  exit;
?>