<?php
  require_once("../_BD/conecta_login.php");
  require_once("autoComplete.class.php");
  //
  //Operações do banco de dados
  if(!empty($_REQUEST['id_cadastro'])){
    $sql = "SELECT * 
            FROM contapag 
              LEFT JOIN pessoas ON (ctpg_idcliente = idpessoas)
            WHERE idcontapag = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
  }
  //
  //Gera o autoComplete 
  $autoComplete = new autoComplete();
  $codigo_js = $autoComplete->gerar("ctpg_pessoa", "ctpg_idcliente", "pessoas LEFT JOIN cidades ON (pess_idcidades = idcidades) LEFT JOIN estados ON (cid_idestados = idestados)", "CONCAT(pess_nome, ', ', cid_nome, ' - ', est_uf)", "idpessoas", "", "WHERE UPPER(pess_nome) LIKE UPPER('##valor##%') AND (pess_cliente = 'SIM' OR pess_funcionario = 'SIM' OR pess_associado = 'SIM')");
  $codigo_campo = $autoComplete->criaCampos("ctpg_pessoa", "ctpg_idcliente", "Cliente");
  //
  //Monta variaveis de exibição
  //
  $sql = "SELECT * FROM tipo_contas";
  $comboBoxTipoConta = $html->criaSelectSql("tico_nome", "idtipo_contas", "ctpg_idtipo_contas", $reg['ctpg_idtipo_contas'], $sql, "form-control");
  //
  $sql = "SELECT * FROM meio_pagto";
  $comboBoxMeioPagto = $html->criaSelectSql("mpag_nome", "idmeio_pagto", "ctpg_idmeio_pagto", $reg['ctpg_idmeio_pagto'], $sql, "form-control");
  $comboBoxMeioPagtoModal = $html->criaSelectSql("mpag_nome", "idmeio_pagto", "ctpg_idmeio_pagtoModal", '', $sql, "form-control");
  //
  $sql = "SELECT * FROM bancos";
  $comboBoxBancos = $html->criaSelectSql("banc_nome", "idbancos", "ctpg_idbancos", $reg['ctpg_idbancos'], $sql, "form-control", 'onchange="carregaComboBoxCC()"');
  $comboBoxBancosModal = $html->criaSelectSql("banc_nome", "idbancos", "ctpg_idbancosModal", '', $sql, "form-control", 'onchange="carregaComboBoxCC(\'Modal\')"');
  //
  $comboBoxCC = "<br><font color='red'>*</font> Selecione o banco";
  //
  $btnGravarReabrir = '<button type="button" onclick="testaDados(\'gravar\')" class="btn btn-success">Gravar</button>';
  //
  if(!empty($reg['idcontapag'])){ 
    if($reg['ctpg_idbancos'] > 0){
      $sql = "SELECT * FROM cc WHERE cc_idbancos = " . $reg['ctpg_idbancos'];
      $comboBoxCC = $html->criaSelectSql("cc_nome", "idcc", "ctpg_idcc", $reg['ctpg_idcc'], $sql, "form-control");
    }
    //
    $checkAVista = $html->defineChecked($reg['ctpg_a_vista']);
    //
    if($reg['ctpg_situacao'] == 'Quitada' || $reg['ctpg_situacao'] == "QParcial"){
      $ctpg_situacao = "<kbd class='bg-success'><b>{$reg['ctpg_situacao']}</b></kbd>";
      $btnGravarReabrir = '<button type="button" onclick="chamaGravar(\'reabrir\')" class="btn btn-warning">Reabrir</button>';
      $btnImprimir = '<button type="button" class="btn btn-primary" data-target="#modelosImprimir"  data-toggle="modal">Imprimir</button>';
    }else{
      $ctpg_situacao = "<kbd><b>{$reg['ctpg_situacao']}</b></kbd>";
      $btnExcluir = '<button type="button" onclick="excluiCadastro()" class="btn btn-danger">Excluir</button>';
    }
    if($reg['ctpg_situacao'] == 'Pendente' || $reg['ctpg_situacao'] == "QParcial"){
      $btnPagar = '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#pagarConta">Pagar</button>';
    }
  }
  //
  if (isset($_SESSION['mensagem'])) {
    $msg = $html->mostraMensagem($_SESSION['tipoMsg'], $_SESSION['mensagem']);
    unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
  }
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $html->buscaHtml("lancamentos", $parametros);
  $html = str_replace("##Mensagem##", $msg, $html);
  $html = str_replace("##autoComplete_Pessoas##", $codigo_js, $html);
  $html = str_replace("##autoComplete_CampoPessoas##", $codigo_campo, $html);
  $html = str_replace("##id_cadastro##", $reg['idcontapag'], $html);
  $html = str_replace("##idcontapag##", $reg['idcontapag'], $html);
  $html = str_replace("##ctpg_pessoa##", $reg['pess_nome'], $html);
  $html = str_replace("##ctpg_idcliente##", $reg['idpessoas'], $html);
  $html = str_replace("##ctpg_situacao##", $ctpg_situacao, $html);
  $html = str_replace("##ctpg_inclusao##", $reg['ctpg_inclusao'], $html);
  $html = str_replace("##ctpg_vencimento##", $reg['ctpg_vencimento'], $html);
  $html = str_replace("##ctpg_vlr_bruto##", $reg['ctpg_vlr_bruto'], $html);
  $html = str_replace("##ctpg_vlr_juros##", $reg['ctpg_vlr_juros'], $html);
  $html = str_replace("##ctpg_porc_juros##", $reg['ctpg_porc_juros'], $html);
  $html = str_replace("##ctpg_vlr_desconto##", $reg['ctpg_vlr_desconto'], $html);
  $html = str_replace("##ctpg_porc_desconto##", $reg['ctpg_porc_desconto'], $html);
  $html = str_replace("##ctpg_vlr_liquido##", $reg['ctpg_vlr_liquido'], $html);
  $html = str_replace("##ctpg_vlr_pago##", $reg['ctpg_vlr_pago'], $html);
  $html = str_replace("##ctpg_vlr_devedor##", $reg['ctpg_vlr_devedor'], $html);
  $html = str_replace("##vlr_pagamento##", $reg['ctpg_vlr_devedor'], $html);
  $html = str_replace("##data_pagto##", date("Y-m-d"), $html);
  $html = str_replace("##comboBoxTipoConta##", $comboBoxTipoConta, $html);
  $html = str_replace("##comboBoxMeioPagto##", $comboBoxMeioPagto, $html);
  $html = str_replace("##comboBoxMeioPagtoModal##", $comboBoxMeioPagtoModal, $html);
  $html = str_replace("##comboBoxBancos##", $comboBoxBancos, $html);
  $html = str_replace("##comboBoxBancosModal##", $comboBoxBancosModal, $html);
  $html = str_replace("##checkAVista##", $checkAVista, $html);
  $html = str_replace("##comboBoxCC##", $comboBoxCC, $html);
  $html = str_replace("##btnExcluir##", $btnExcluir, $html);
  $html = str_replace("##btnPagar##", $btnPagar, $html);
  $html = str_replace("##btnGravarReabrir##", $btnGravarReabrir, $html);
  $html = str_replace("##btnImprimir##", $btnImprimir, $html);
  echo $html; 
  exit;
?>