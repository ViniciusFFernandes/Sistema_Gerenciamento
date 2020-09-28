<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
require_once("contarec.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'contarec_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT *, format(ctrc_vlr_bruto,2,'de_DE') ctrc_bruto, DATE_FORMAT(STR_TO_DATE(ctrc_vencimento, '%Y-%m-%d'), '%d/%m/%Y') as vencimento
            FROM contarec 
              LEFT JOIN pessoas ON (ctrc_idcliente = idpessoas)";
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
    $dados['vencimento'] = "align='right'";
    $dados['ctrc_bruto'] = "align='right'";
    $dados['ctrc_situacao'] = "";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Lancamentos/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
    $db->beginTransaction();
    //
    $db->setTabela("contarec", "idcontarec");
    //
    unset($dados);
    $dados['id']                  = $_POST['id_cadastro'];
    $dados['ctrc_idcliente'] 	    = $util->igr($_POST['ctrc_idcliente']);
    $dados['ctrc_idtipo_contas']  = $util->igr($_POST['ctrc_idtipo_contas']);
    $dados['ctrc_idbancos']       = $util->igr($_POST['ctrc_idbancos']);
    $dados['ctrc_idcc']           = $util->igr($_POST['ctrc_idcc']);
    $dados['ctrc_idmeio_pagto']   = $util->igr($_POST['ctrc_idmeio_pagto']);
    $dados['ctrc_vencimento'] 	  = $util->sgr($_POST['ctrc_vencimento']);
    $dados['ctrc_a_vista'] 	      = $util->sgr($_POST['ctrc_a_vista']);
    $dados['ctrc_inclusao'] 	    = $util->dgr(date('d/m/Y'));
    $dados['ctrc_vlr_bruto'] 	    = $util->vgr($_POST['ctrc_vlr_bruto']);
    $dados['ctrc_vlr_desconto'] 	= $util->vgr($_POST['ctrc_vlr_desconto']);
    $dados['ctrc_porc_desconto'] 	= $util->vgr($_POST['ctrc_porc_desconto']);
    $dados['ctrc_vlr_juros'] 	    = $util->vgr($_POST['ctrc_vlr_juros']);
    $dados['ctrc_porc_juros'] 	  = $util->vgr($_POST['ctrc_porc_juros']);
    $dados['ctrc_situacao']       = $util->sgr("Pendente");
    //
    $db->gravarInserir($dados, true);
    //
    if ($_POST['id_cadastro'] > 0) {
      $id = $_POST['id_cadastro'];
      $operacaoHistorioco = "Alteração";
    }else{
      $id = $db->getUltimoID();
      $operacaoHistorioco = "Inclusão";
    }
    //
    $contarec = New Contarec($db);
    $contarec->gerarHistorio($id, $operacaoHistorioco, $_POST['ctrc_vlr_bruto'], $_SESSION['idusuario'], '', $_POST['ctrc_idmeio_pagto'], $_POST['ctrc_idcc']);
    //
    if($_POST['ctrc_a_vista'] == 'SIM' && $parametros->buscaValor("sistema: incluir contas a vista já quitadas") == 'SIM'){
      if($_POST['ctrc_idcc'] <= 0){
        $html->mostraErro("Selecione a conta bancária!");
        $db->rollBack();
        exit;
      }
      if($_POST['ctrc_idmeio_pagto'] <= 0){
        $html->mostraErro("Selecione o meio de pagamento!");
        $db->rollBack();
        exit;
      }
      //
      $db->setTabela("contarec", "idcontarec");
      unset($dados);
      $dados['id']                  = $id;
      $dados['ctrc_vencimento'] 	  = $util->dgr(date("d/m/Y"));
      //
      $db->gravarInserir($dados, true);
      //
      $contarec->baixaConta($id, $_POST['ctrc_vlr_bruto'], 0, 0, $_POST['ctrc_idcc'], $_POST['ctrc_idmeio_pagto'], date("d/m/Y"), $_SESSION['idusuario']);
    }
    //
    $db->commit();
    //
    header('location:../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $id);
    exit;
  }

  if ($_POST['operacao'] == 'reabrir'){
    $contarec = New Contarec($db);
    $contarec->reabrirConta($_POST['id_cadastro'], $_SESSION['idusuario']);
    //
    header('location:../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $_POST['id_cadastro']);
    exit;
  }

  if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("contarec", "idcontarec");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Lancamentos/' . $paginaRetorno);
    exit;
  }

  if($_POST['operacao'] == 'geraComboBoxCC'){
    $sql = "SELECT * FROM cc WHERE cc_idbancos = " . $_POST['idbancos'];
    $comboBoxTipoConta = $html->criaSelectSql("cc_nome", "idcc", "ctrc_idcc", '', $sql, "form-control");
    echo $comboBoxTipoConta;
  }

 ?>
