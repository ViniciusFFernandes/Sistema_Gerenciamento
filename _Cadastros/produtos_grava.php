﻿<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
  if ($_POST['operacao'] == "buscaProdutos") {
    $sql = "SELECT * 
            FROM produtos 
              LEFT JOIN unidades ON (prod_idunidades = idunidades) 
              LEFT JOIN grupos ON (prod_idgrupos = idgrupos)";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idprodutos LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                OR prod_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                OR grup_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idprodutos'] = "width='6%'";
    $dados['prod_nome'] = "";
    $dados['uni_sigla'] = "width='10%'";
    $dados['grup_nome'] = "width='10%'";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, "abreProdutos");
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/produtos_edita.php');
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
    $db->setTabela("produtos", "idprodutos");

    unset($dados);
    $dados['id']                     = $_POST['idprodutos'];
    $dados['prod_nome']              = $util->sgr($_POST['prod_nome']);
    $dados['prod_idgrupos']          = $util->igr($_POST['prod_idgrupos']);
    $dados['prod_idsubgrupos']       = $util->igr($_POST['prod_idsubgrupos']);
    $dados['prod_idunidades']        = $util->igr($_POST['prod_idunidades']);
    $dados['prod_tipo_produto']      = $util->sgr($_POST['prod_tipo_produto']);
    $dados['prod_qte_estoque']       = $util->vgr($_POST['prod_qte_estoque']);
    $dados['prod_preco_tabela']      = $util->vgr($_POST['prod_preco_tabela']);

    $db->gravarInserir($dados);


    if ($_POST['idprodutos'] > 0) {
      $id = $_POST['idprodutos'];
    }else{
      $id = $db->getUltimoID();
    }
    header('location:../_Cadastros/produtos_edita.php?idprodutos=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("produtos", "idprodutos");
    $db->excluir($_POST['idprodutos']);
    header('location:../_Cadastros/produtos_edita.php');
    exit;
  }

 ?>