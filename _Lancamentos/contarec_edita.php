<?php
  require_once("../_BD/conecta_login.php");
  require_once("autoComplete.class.php");
  //
  //Operações do banco de dados
  if(!empty($_REQUEST['id_cadastro'])){
    $sql = "SELECT * 
            FROM contarec 
              LEFT JOIN pessoas ON (ctrc_idcliente = idpessoas)
            WHERE idcontarec = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
  }
  //
  //Gera o autoComplete 
  $autoComplete = new autoComplete();
  $codigo_js = $autoComplete->gerar("ctrc_pessoa", "ctrc_idcliente", "pessoas LEFT JOIN cidades ON (pess_idcidades = idcidades) LEFT JOIN estados ON (cid_idestados = idestados)", "CONCAT(pess_nome, ', ', cid_nome, ' - ', est_uf)", "idpessoas", "", "WHERE UPPER(pess_nome) LIKE UPPER('##valor##%') AND (pess_cliente = 'SIM' OR pess_funcionario = 'SIM' OR pess_associado = 'SIM')");
  $codigo_campo = $autoComplete->criaCampos("ctrc_pessoa", "ctrc_idcliente", "Cliente");
  //
  //Monta variaveis de exibição
  //
  $sql = "SELECT * FROM tipo_contas";
  $comboBoxTipoConta = $html->criaSelectSql("tico_nome", "idtipo_contas", "ctrc_idtipo_contas", $reg['ctrc_idtipo_contas'], $sql, "form-control");
  //
  $sql = "SELECT * FROM meio_pagto";
  $comboBoxMeioPagto = $html->criaSelectSql("mpag_nome", "idmeio_pagto", "ctrc_idmeio_pagto", $reg['ctrc_idmeio_pagto'], $sql, "form-control");
  //
  $sql = "SELECT * FROM bancos";
  $comboBoxBancos = $html->criaSelectSql("banc_nome", "idbancos", "ctrc_idbancos", $reg['ctrc_idbancos'], $sql, "form-control", 'onchange="carregaComboBoxCC()"');
  //
  $comboBoxCC = "<br><font color='red'>*</font> Selecione o banco";
  //
  $btnGravarReabrir = '<button type="button" onclick="testaDados(\'gravar\')" class="btn btn-success">Gravar</button>';
  //
  if(!empty($reg['idcontarec'])){ 
    if($reg['ctrc_idbancos'] > 0){
      $sql = "SELECT * FROM cc WHERE cc_idbancos = " . $reg['ctrc_idbancos'];
      $comboBoxCC = $html->criaSelectSql("cc_nome", "idcc", "ctrc_idcc", $reg['ctrc_idcc'], $sql, "form-control");
    }
    //
    $checkAVista = $html->defineChecked($reg['ctrc_a_vista']);
    //
    if($reg['ctrc_situacao'] == 'Quitada' || $reg['ctrc_situacao'] == "QParcial"){
      $ctrc_situacao = "<kbd class='bg-success'><b>{$reg['ctrc_situacao']}</b></kbd>";
      $btnGravarReabrir = '<button type="button" onclick="chamaGravar(\'reabrir\')" class="btn btn-warning">Reabrir</button>';
    }else{
      $ctrc_situacao = "<kbd><b>{$reg['ctrc_situacao']}</b></kbd>";
      $btnExcluir = '<button type="button" onclick="excluiCadastro()" class="btn btn-danger">Excluir</button>';
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
  $html = str_replace("##id_cadastro##", $reg['idcontarec'], $html);
  $html = str_replace("##idcontarec##", $reg['idcontarec'], $html);
  $html = str_replace("##ctrc_pessoa##", $reg['pess_nome'], $html);
  $html = str_replace("##ctrc_idcliente##", $reg['idpessoas'], $html);
  $html = str_replace("##ctrc_situacao##", $ctrc_situacao, $html);
  $html = str_replace("##ctrc_inclusao##", $reg['ctrc_inclusao'], $html);
  $html = str_replace("##ctrc_vencimento##", $reg['ctrc_vencimento'], $html);
  $html = str_replace("##ctrc_vlr_bruto##", $reg['ctrc_vlr_bruto'], $html);
  $html = str_replace("##ctrc_vlr_juros##", $reg['ctrc_vlr_juros'], $html);
  $html = str_replace("##ctrc_porc_juros##", $reg['ctrc_porc_juros'], $html);
  $html = str_replace("##ctrc_vlr_desconto##", $reg['ctrc_vlr_desconto'], $html);
  $html = str_replace("##ctrc_porc_desconto##", $reg['ctrc_porc_desconto'], $html);
  $html = str_replace("##ctrc_vlr_liquido##", $reg['ctrc_vlr_liquido'], $html);
  $html = str_replace("##ctrc_vlr_pago##", $reg['ctrc_vlr_pago'], $html);
  $html = str_replace("##ctrc_vlr_devedor##", $reg['ctrc_vlr_devedor'], $html);
  $html = str_replace("##comboBoxTipoConta##", $comboBoxTipoConta, $html);
  $html = str_replace("##comboBoxMeioPagto##", $comboBoxMeioPagto, $html);
  $html = str_replace("##comboBoxBancos##", $comboBoxBancos, $html);
  $html = str_replace("##checkAVista##", $checkAVista, $html);
  $html = str_replace("##comboBoxCC##", $comboBoxCC, $html);
  $html = str_replace("##btnExcluir##", $btnExcluir, $html);
  $html = str_replace("##btnGravarReabrir##", $btnGravarReabrir, $html);
  echo $html;
  exit;
?>