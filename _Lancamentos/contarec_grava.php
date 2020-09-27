<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
require_once("contarec.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'contarec_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * FROM contarec LEFT JOIN pessoas ON (ctrc_idcliente = idpessoas)";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idcontarec LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR pess_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idcontarec'] = "width='6%'";
    $dados['pess_nome'] = "";
    $dados['ctrc_vencimento'] = "";
    $dados['ctrc_vlr_bruto'] = "";
    $dados['ctrc_situacao'] = "";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
    $db->setTabela("contarec", "idcontarec");

    unset($dados);
    $dados['id']                  = $_POST['id_cadastro'];
    $dados['ctrc_idcliente'] 	    = $util->igr($_POST['ctrc_idcliente']);
    $dados['ctrc_idtipo_contas']  = $util->sgr($_POST['ctrc_idtipo_contas']);
    $dados['ctrc_idbancos']       = $util->sgr($_POST['ctrc_idbancos']);
    $dados['ctrc_idcc']           = $util->sgr($_POST['ctrc_idcc']);
    $dados['ctrc_idforma_pagto']  = $util->sgr($_POST['ctrc_idforma_pagto']);
    $dados['ctrc_vencimento'] 	  = $util->dgr($_POST['ctrc_vencimento']);
    $dados['ctrc_vencimento'] 	  = $util->dgr($_POST['ctrc_vencimento']);
    $dados['ctrc_vlr_bruto'] 	    = $util->vgr($_POST['ctrc_vlr_bruto']);
    $dados['ctrc_vlr_desconto'] 	= $util->vgr($_POST['ctrc_vlr_desconto']);
    $dados['ctrc_vlr_juros'] 	    = $util->vgr($_POST['ctrc_vlr_juros']);

    $db->gravarInserir($dados, true);

    if ($_POST['id_cadastro'] > 0) {
      $id = $_POST['id_cadastro'];
    }else{
      $id = $db->getUltimoID();
      //
      $contarec = New Contarec();
      $contarec->geraHistorio($id, "Inclusão", '', $_POST['ctrc_vlr_bruto'], $_SESSION['idusuario']);
    }
    header('location:../_Cadastros/' . $paginaRetorno . '?id_cadastro=' . $id);
    exit;
  }

  if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("contarec", "idcontarec");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

  if($_POST['operacao'] == 'geraComboBoxCC'){
    $sql = "SELECT * FROM cc WHERE cc_idbancos = " . $_POST['idbancos'];
    $comboBoxTipoConta = $html->criaSelectSql("cc_nome", "idcc", "ctrc_idcc", '', $sql, "form-control");
    echo $comboBoxTipoConta;
  }

 ?>
