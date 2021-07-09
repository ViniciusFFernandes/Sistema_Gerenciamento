<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
require_once("contapag.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'contapag_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT *, format(ctpg_vlr_bruto,2,'de_DE') ctpg_bruto, DATE_FORMAT(STR_TO_DATE(ctpg_vencimento, '%Y-%m-%d'), '%d/%m/%Y') as vencimento
            FROM contapag 
              LEFT JOIN pessoas ON (ctpg_idcliente = idpessoas)";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idcontapag LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR pess_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idcontapag'] = "width='6%'";
    $dados['pess_nome'] = "";
    $dados['vencimento'] = "align='right'";
    $dados['ctpg_bruto'] = "align='right'";
    $dados['ctpg_situacao'] = "";
    //
    $cabecalho["Código"] = "";
    $cabecalho["Cliente"] = "";
    $cabecalho["Vencimento"] = "align='right'";
    $cabecalho["Valor"] = "align='right'";
    $cabecalho["Situação"] = "";
    //
    echo $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno, $cabecalho);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Lancamentos/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
    if($_POST['id_cadastro'] > 0){
      $sql = "SELECT * FROM contapag WHERE idcontapag = " . $_POST['id_cadastro'];
      $reg = $db->retornaUmReg($sql);
      //
      if($reg['ctpg_situacao'] != "Pendente"){
        $util->mostraErro("Está conta não está pendente e não pode ser alterada!");
        exit;
      }
    }
    //
    $db->beginTransaction();
    //
    $db->setTabela("contapag", "idcontapag");
    //
    if($_POST['ctpg_idempresas'] <= 0){
      $_POST['ctpg_idempresas'] = CODIGO_EMPRESA;
    }
    //
    unset($dados);
    $dados['id']                  = $_POST['id_cadastro'];
    $dados['ctpg_idcliente'] 	    = $util->igr($_POST['ctpg_idcliente']);
    $dados['ctpg_idtipo_contas']  = $util->igr($_POST['ctpg_idtipo_contas']);
    $dados['ctpg_idbancos']       = $util->igr($_POST['ctpg_idbancos']);
    $dados['ctpg_idcc']           = $util->igr($_POST['ctpg_idcc']);
    $dados['ctpg_idmeio_pagto']   = $util->igr($_POST['ctpg_idmeio_pagto']);
    $dados['ctpg_idempresa']      = $util->igr($_POST['ctpg_idempresa']);
    $dados['ctpg_vencimento'] 	  = $util->sgr($_POST['ctpg_vencimento']);
    $dados['ctpg_a_vista'] 	      = $util->sgr($_POST['ctpg_a_vista']);
    $dados['ctpg_inclusao'] 	    = $util->dgr(date('d/m/Y'));
    $dados['ctpg_vlr_bruto'] 	    = $util->vgr($_POST['ctpg_vlr_bruto']);
    $dados['ctpg_vlr_desconto'] 	= $util->vgr($_POST['ctpg_vlr_desconto']);
    $dados['ctpg_porc_desconto'] 	= $util->vgr($_POST['ctpg_porc_desconto']);
    $dados['ctpg_vlr_juros'] 	    = $util->vgr($_POST['ctpg_vlr_juros']);
    $dados['ctpg_porc_juros'] 	  = $util->vgr($_POST['ctpg_porc_juros']);
    $dados['ctpg_situacao']       = $util->sgr("Pendente");
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
    $contapag = New Contapag($db);
    $contapag->gerarHistorio($id, $operacaoHistorioco, $_POST['ctpg_vlr_bruto'], $_SESSION['idusuario'], '', $_POST['ctpg_idmeio_pagto'], $_POST['ctpg_idcc']);
    //
    //
    $contaQuitada = false;
    if($db->retornaUmCampoID("tico_tipo_salario", "tipo_contas", $_POST['ctpg_idtipo_contas']) == 'SIM'  && $parametros->buscaValor("sistema: recalcula contas tipo salario") == 'SIM' && $db->retornaUmCampoID("ctpg_recalculou", "contapag", $id) != 'SIM'){
      $contapag->attContaSalario($id, $_POST['ctpg_idcliente'], $_POST['ctpg_idmeio_pagto'], $_POST['ctpg_idcc']);
    }
    //
    if($_POST['ctpg_a_vista'] == 'SIM' && $parametros->buscaValor("sistema: incluir contas a vista já quitadas") == 'SIM'){
      if($_POST['ctpg_idcc'] <= 0){
        $html->mostraErro("Selecione a conta bancária!");
        $db->rollBack();
        exit;
      }
      if($_POST['ctpg_idmeio_pagto'] <= 0){
        $html->mostraErro("Selecione o meio de pagamento!");
        $db->rollBack();
        exit;
      }
      //
      $db->setTabela("contapag", "idcontapag");
      unset($dados);
      $dados['id']                  = $id;
      $dados['ctpg_vencimento'] 	= $util->dgr(date("d/m/Y"));
      //
      $db->gravarInserir($dados, false);
      //
      $valorPago = $db->retornaUmCampoID("(ctpg_vlr_bruto + ctpg_vlr_juros) - ctpg_vlr_desconto", "contapag", $id);
      //
      $contapag->baixaConta($id, $valorPago, 0, 0, $_POST['ctpg_idcc'], $_POST['ctpg_idmeio_pagto'], date("d/m/Y"), $_SESSION['idusuario']);
      //
      $contaQuitada = true;
    }

    if($parametros->buscaValor("sistema: incluir contas do tipo salario já quitadas") == 'SIM' && !$contaQuitada && $db->retornaUmCampoID("tico_tipo_salario", "tipo_contas", $_POST['ctpg_idtipo_contas']) == 'SIM'){
      //
      $valorPago = $db->retornaUmCampoID("(ctpg_vlr_bruto + ctpg_vlr_juros) - ctpg_vlr_desconto", "contapag", $id);
      //
      $contapag->baixaConta($id, $valorPago, 0, 0, $_POST['ctpg_idcc'], $_POST['ctpg_idmeio_pagto'], date("d/m/Y"), $_SESSION['idusuario']);
      //
      $contaQuitada = true;
    }
    //
    $db->commit();
    //
    header('location:../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $id);
    exit;
  }

  if ($_POST['operacao'] == 'reabrir'){
    //
    if($db->retornaUmCampoID("ctpg_processou", "contapag", $_POST['id_cadastro']) == 'SIM'){
      $html->mostraErro("Está conta não pode ser reaberta, está ligada a uma conta salario<br>Operação cancelada!");
      exit;
    }
    //
    $contapag = New Contapag($db);
    $contapag->reabrirConta($_POST['id_cadastro'], $_SESSION['idusuario']);
    //
    header('location:../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $_POST['id_cadastro']);
    exit;
  }

  if ($_POST['operacao'] == "excluiCad") {
    //
    $situacao = $db->retornaUmCampoID('ctpg_stituacao', 'contapag', $_POST['id_cadastro']);
    if($situacao == 'Quitada' || $situacao == 'QParcial'){
      $html->mostraErro("Está conta já esta paga e não pode ser excluida!<br>Operação cancelada!");
      $db->rollBack();
      exit;
    }
    //
    $db->setTabela("contapag_hist", "cphi_idcontapag");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
      $html->mostraErro("Erro ao excluir o historico da conta<br>Operação cancelada!");
      $db->rollBack();
      exit;
    }
    //
    $db->setTabela("contapag", "idcontapag");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        $db->rollBack();
        exit;
    }
    header('location:../_Lancamentos/' . $paginaRetorno);
    exit;
  }

  if($_POST['operacao'] == 'geraComboBoxCC'){
    $nomeCampo = "ctpg_idcc";
    if($_POST['tipo'] != ""){
      $nomeCampo = "idcc_pagamento";
    }
    $sql = "SELECT * FROM cc WHERE cc_idbancos = " . $_POST['idbancos'];
    $comboBoxTipoConta = $html->criaSelectSql("cc_nome", "idcc", $nomeCampo, '', $sql, "form-control");
    echo $comboBoxTipoConta;
  }

  if($_POST['operacao'] == 'buscarHistorico'){
    //
    $contapag = New Contapag($db);
    echo $contapag->geraHistorico($_POST['id_cadastro']);
    exit;
  }

  if($_POST['operacao'] == 'efetuarPagamento'){
    //
    $db->beginTransaction();
    //
    $sql = "SELECT * FROM contapag WHERE idcontapag = " . $_POST['id_cadastro'];
    $reg = $db->retornaUmReg($sql);
    //
    if(empty($_POST['idcc_pagamento'])) $_POST['idcc_pagamento'] = $reg['ctpg_idcc'];
    if(empty($_POST['ctpg_idmeio_pagtoModal'])) $_POST['ctpg_idmeio_pagtoModal'] = $reg['ctpg_idmeio_pagto'];
    //
    $contapag = New Contapag($db);
    $contapag->baixaConta($_POST['id_cadastro'], $_POST['vlr_pagamento'] , $_POST['vlr_multa'], $_POST['vlr_desconto'], $_POST['idcc_pagamento'], $_POST['ctpg_idmeio_pagtoModal'], $util->convertData($_POST['data_pagto']), $_SESSION['idusuario']);
    //
    $db->commit();
    //
    header('location:../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $_POST['id_cadastro']);
    exit;
  }

 ?>
