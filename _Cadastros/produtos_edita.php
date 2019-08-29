﻿<?php
  include_once("../_BD/conecta_login.php");
  require_once("../Class/html.class.php");
  //
  //Inicia classes nescessarias
  $html = new html($db, $util);
  //
  //Operações do banco de dados
  if(!empty($_REQUEST['idprodutos'])){
    $sql = "SELECT * 
            FROM produtos 
              LEFT JOIN unidades ON (prod_idunidades = idunidades) 
              LEFT JOIN grupos ON (prod_idgrupos = idgrupos) 
              LEFT JOIN subgrupos ON (prod_idsubgrupos = idsubgrupos) 
            WHERE idprodutos = {$_REQUEST['idprodutos']}";
    $reg = $db->retornaUmReg($sql);
  }
  //
  //
  //Monta variaveis de exibição
  $sql = "SELECT * FROM unidades";
  $comboBoxUnidades = $html->criaSelectSql("uni_sigla", "idunidades", "prod_idunidades", $reg['prod_idunidades'], $sql, "form-control");
  //
  $sql = "SELECT * FROM grupos";
  $comboBoxGrupos = $html->criaSelectSql("grup_nome", "idgrupos", "prod_idgrupos", $reg['prod_idgrupos'], $sql, "form-control");
  //
  $sql = "SELECT * FROM subgrupos";
  $comboBoxSubGrupos = $html->criaSelectSql("subg_nome", "idsubgrupos", "prod_idsubgrupos", $reg['prod_idsubgrupos'], $sql, "form-control");
  //
  if(!empty($reg['idprodutos'])){ 
    //
    $btnExcluir = '<button type="button" onclick="excluiCadastro()" class="btn btn-danger">Excluir</button>';
    //
    $comboBoxTipo     = $util->defineSelected("", $reg['prod_tipo_produto']);
    $comboBoxTipoPR   = $util->defineSelected("Produto Revenda", $reg['prod_tipo_produto']);
    $comboBoxTipoMP   = $util->defineSelected("Materia Prima", $reg['prod_tipo_produto']);
    $comboBoxTipoPC   = $util->defineSelected("Produto para Consumo", $reg['prod_tipo_produto']);
    $comboBoxTipoPP   = $util->defineSelected("Producao Propria", $reg['prod_tipo_produto']);
    $comboBoxTipoPV   = $util->defineSelected("Producao para Venda", $reg['prod_tipo_produto']);
    $comboBoxTipoV    = $util->defineSelected("Veiculo", $reg['prod_tipo_produto']);
    $comboBoxTipoBT   = $util->defineSelected("Brindes de Terceiros", $reg['prod_tipo_produto']);
    $comboBoxTipoBP   = $util->defineSelected("Brindes Próprios", $reg['prod_tipo_produto']);
    $comboBoxTipoE    = $util->defineSelected("Embalagem", $reg['prod_tipo_produto']);
    $comboBoxTipoIP   = $util->defineSelected("Imobilizado Proprio", $reg['prod_tipo_produto']);
    $comboBoxTipoPAT  = $util->defineSelected("Terceiros", $reg['prod_tipo_produto']);
    $comboBoxTipoO    = $util->defineSelected("Outros", $reg['prod_tipo_produto']);
    //
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
  $html = str_replace("##idprodutos##", $reg['idprodutos'], $html);
  $html = str_replace("##comboBoxTipo##", $comboBoxTipo, $html);
  $html = str_replace("##comboBoxTipoPR##", $comboBoxTipoPR, $html);
  $html = str_replace("##comboBoxTipoMP##", $comboBoxTipoMP, $html);
  $html = str_replace("##comboBoxTipoPC##", $comboBoxTipoPC, $html);
  $html = str_replace("##comboBoxTipoPP##", $comboBoxTipoPP, $html);
  $html = str_replace("##comboBoxTipoPV##", $comboBoxTipoPV, $html);
  $html = str_replace("##comboBoxTipoV##", $comboBoxTipoV, $html);
  $html = str_replace("##comboBoxTipoBT##", $comboBoxTipoBT, $html);
  $html = str_replace("##comboBoxTipoBP##", $comboBoxTipoBP, $html);
  $html = str_replace("##comboBoxTipoE##", $comboBoxTipoE, $html);
  $html = str_replace("##comboBoxTipoIP##", $comboBoxTipoIP, $html);
  $html = str_replace("##comboBoxTipoPAT##", $comboBoxTipoPAT, $html);
  $html = str_replace("##comboBoxTipoO##", $comboBoxTipoO, $html);
  $html = str_replace("##prod_nome##", $reg['prod_nome'], $html);
  $html = str_replace("##prod_qte_estoque##", $reg['prod_qte_estoque'], $html);
  $html = str_replace("##prod_preco_tabela##", $reg['prod_preco_tabela'], $html);
  $html = str_replace("##comboBoxGrupos##", $comboBoxGrupos, $html);
  $html = str_replace("##comboBoxSubGrupos##", $comboBoxSubGrupos, $html);
  $html = str_replace("##comboBoxUnidades##", $comboBoxUnidades, $html);
  $html = str_replace("##btnExcluir##", $btnExcluir, $html);
  echo $html;
  exit;
?>