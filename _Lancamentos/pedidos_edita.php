<?php
  require_once("../_BD/conecta_login.php");
  require_once("autoComplete.class.php");
  //
  //Operações do banco de dados
  if(!empty($_REQUEST['id_cadastro'])){
    $sql = "SELECT * 
            FROM pedidos 
              LEFT JOIN pessoas ON (ped_idcliente = idpessoas)
            WHERE idpedidos = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
  }
  //
  //Gera o autoComplete 
  $autoComplete = new autoComplete();
  $codigo_js = $autoComplete->gerar("ped_pessoa", "ped_idcliente", "pessoas LEFT JOIN cidades ON (pess_idcidades = idcidades) LEFT JOIN estados ON (cid_idestados = idestados)", "CONCAT(pess_nome, ', ', cid_nome, ' - ', est_uf)", "idpessoas", "", "WHERE UPPER(pess_nome) LIKE UPPER('##valor##%') AND (pess_cliente = 'SIM' OR pess_funcionario = 'SIM' OR pess_associado = 'SIM')");
  $codigo_campo = $autoComplete->criaCampos("ped_pessoa", "ped_idcliente", "Cliente");
  //
  //Monta variaveis de exibição
  //
  $sql = "SELECT * FROM forma_pagto";
  $comboBoxFormaPagto = $html->criaSelectSql("forp_nome", "idforma_pagto", "ped_idforma_pagto", $reg['ped_idforma_pagto'], $sql, "form-control", "", true, "Forma de Pagamento");
  //
  //
  $sql = "SELECT * FROM meio_pagto";
  $comboBoxMeioPagto = $html->criaSelectSql("mpag_nome", "idmeio_pagto", "ped_idmeio_pagto", $reg['ped_idmeio_pagto'], $sql, "form-control", "", true, "Meio de Pagamento");
  //
  $sql = "SELECT * FROM bancos";
  $comboBoxBancos = $html->criaSelectSql("banc_nome", "idbancos", "ped_idbancos", $reg['ped_idbancos'], $sql, "form-control", 'onchange="carregaComboBoxCC()"', true, "Banco");
  //
  $sql = "SELECT * FROM empresas";
  $comboEmpresas = $html->criaSelectSql("emp_nome", "idempresas", "ped_idempresa", $reg['ped_idempresa'], $sql, "form-control", '', true, "Empresa");
  //
  $comboBoxCC = "<font color='red'>*</font> Selecione o banco";
  //
  $btnGravarReabrir = '<button type="button" onclick="testaDados(\'gravar\')" class="btn btn-success">Gravar</button>';
  //
  if(!empty($reg['idpedidos'])){ 
    if($reg['ped_idbancos'] > 0){
      $sql = "SELECT * FROM cc WHERE cc_idbancos = " . $reg['ped_idbancos'];
      $comboBoxCC = $html->criaSelectSql("cc_nome", "idcc", "ped_idcc", $reg['ped_idcc'], $sql, "form-control");
    }
    //
    if($reg['ped_situacao'] == 'Fechado'){
      $ped_situacao = "<kbd class='bg-success'><b>{$reg['ped_situacao']}</b></kbd>";
      $btnGravarReabrir = '<button type="button" onclick="chamaGravar(\'reabrir\')" class="btn btn-warning">Reabrir</button>';
      $btnImprimir = '<button type="button" class="btn btn-info" data-target="#modelosImprimir" data-toggle="modal">Imprimir</button>';
    }else{
      $ped_situacao = "<kbd><b>{$reg['ped_situacao']}</b></kbd>";
    }
    if($reg['ped_situacao'] == 'Pendente'){
      $btnFechar = '<button type="button" class="btn btn-warning" onclick="chamaGravar(\'fechar\')" >Fechar</button>';
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
  $html = str_replace("##id_cadastro##", $reg['idpedidos'], $html);
  $html = str_replace("##idpedidos##", $reg['idpedidos'], $html);
  $html = str_replace("##ped_pessoa##", $reg['pess_nome'], $html);
  $html = str_replace("##ped_idcliente##", $reg['idpessoas'], $html);
  $html = str_replace("##ped_situacao##", $ped_situacao, $html);
  $html = str_replace("##ped_abertura##", str_replace(" ", "T", $reg['ped_abertura']), $html);
  $html = str_replace("##ped_fechamento##", str_replace(" ", "T", $reg['ped_fechamento']), $html);
  $html = str_replace("##comboBoxFormaPagto##", $comboBoxFormaPagto, $html);
  $html = str_replace("##comboBoxMeioPagto##", $comboBoxMeioPagto, $html);
  $html = str_replace("##comboBoxBancos##", $comboBoxBancos, $html);
  $html = str_replace("##comboEmpresas##", $comboEmpresas, $html);
  $html = str_replace("##comboBoxCC##", $comboBoxCC, $html);
  $html = str_replace("##ped_frete##", $util->formataMoeda($reg['ped_frete'], 2, true), $html);
  $html = str_replace("##ped_valor_desconto##", $util->formataMoeda($reg['ped_valor_desconto'], 2, true), $html);
  $html = str_replace("##ped_porc_desconto##", $util->formataMoeda($reg['ped_porc_desconto'], 2, true), $html);
  $html = str_replace("##ped_total_pedido##", $util->formataMoeda($reg['ped_total_pedido'], 2, true), $html);
  $html = str_replace("##btnExcluir##", $btnExcluir, $html);
  $html = str_replace("##btnFechar##", $btnFechar, $html);
  $html = str_replace("##btnGravarReabrir##", $btnGravarReabrir, $html);
  $html = str_replace("##btnImprimir##", $btnImprimir, $html);
  echo $html; 
  exit;
?>