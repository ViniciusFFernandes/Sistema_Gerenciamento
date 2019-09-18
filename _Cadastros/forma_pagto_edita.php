<?php
  include_once("../_BD/conecta_login.php");
  include_once("../Class/autoComplete.class.php");
  //
  //Operações do banco de dados
  if(!empty($_REQUEST['idforma_pagto'])){
    $sql = "SELECT * 
            FROM forma_pagto 
            WHERE idforma_pagto = {$_REQUEST['idforma_pagto']}";
    $reg = $db->retornaUmReg($sql);
  }
  //
  //Monta variaveis de exibição
  if(!empty($reg['idforma_pagto'])){ 
    $comboBoxTipo     = $util->defineSelected("", $reg['forp_tipo']);
    $comboBoxTipoAV   = $util->defineSelected("A Vista", $reg['forp_tipo']);
    $comboBoxTipoMCE  = $util->defineSelected("Mensal com Entrada", $reg['forp_tipo']);
    $comboBoxTipoMSE  = $util->defineSelected("Mensal sem Entrada", $reg['forp_tipo']);
    $comboBoxTipoDU   = $util->defineSelected("Dias Uteis", $reg['forp_tipo']);
    $comboBoxTipoDDL  = $util->defineSelected("DDL", $reg['forp_tipo']);
    $comboBoxTipoDI   = $util->defineSelected("Dias informados", $reg['forp_tipo']);
    $comboBoxTipoPL   = $util->defineSelected("Parcelamento Livre", $reg['forp_tipo']);
    $comboBoxTipoG    = $util->defineSelected("Garantia", $reg['forp_tipo']);
    $comboBoxTipoD    = $util->defineSelected("Devolucao", $reg['forp_tipo']);
    $comboBoxTipoT    = $util->defineSelected("Troca", $reg['forp_tipo']);
    $comboBoxTipoR    = $util->defineSelected("Retorno", $reg['forp_tipo']);
    $comboBoxTipoB    = $util->defineSelected("Bonificacao", $reg['forp_tipo']);
    $comboBoxTipoCD   = $util->defineSelected("Condicional", $reg['forp_tipo']);
    $comboBoxTipoCS   = $util->defineSelected("Consignacao", $reg['forp_tipo']);
    $comboBoxTipoFM   = $util->defineSelected("Faturado", $reg['forp_tipo']);

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
  $html = str_replace("##idforma_pagto##", $reg['idforma_pagto'], $html);
  $html = str_replace("##forp_nome##", $reg['forp_nome'], $html);
  $html = str_replace("##comboBoxTipo##", $comboBoxTipo, $html);
  $html = str_replace("##comboBoxTipoAV##", $comboBoxTipoAV, $html);
  $html = str_replace("##comboBoxTipoMCE##", $comboBoxTipoMCE, $html);
  $html = str_replace("##comboBoxTipoMSE##", $comboBoxTipoMSE, $html);
  $html = str_replace("##comboBoxTipoDU##", $comboBoxTipoDU, $html);
  $html = str_replace("##comboBoxTipoDDL##", $comboBoxTipoDDL, $html);
  $html = str_replace("##comboBoxTipoDI##", $comboBoxTipoDI, $html);
  $html = str_replace("##comboBoxTipoPL##", $comboBoxTipoPL, $html);
  $html = str_replace("##comboBoxTipoG##", $comboBoxTipoG, $html);
  $html = str_replace("##comboBoxTipoD##", $comboBoxTipoD, $html);
  $html = str_replace("##comboBoxTipoT##", $comboBoxTipoT, $html);
  $html = str_replace("##comboBoxTipoR##", $comboBoxTipoR, $html);
  $html = str_replace("##comboBoxTipoB##", $comboBoxTipoB, $html);
  $html = str_replace("##comboBoxTipoCD##", $comboBoxTipoCD, $html);
  $html = str_replace("##comboBoxTipoCS##", $comboBoxTipoCS, $html);
  $html = str_replace("##comboBoxTipoFM##", $comboBoxTipoFM, $html);
  $html = str_replace("##btnExcluir##", $btnExcluir, $html);
  echo $html;
  exit;
?>