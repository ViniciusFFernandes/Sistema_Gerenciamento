<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
require_once("salarios.class.php");
require_once("contapag.class.php");
// print_r($_REQUEST);
// exit;
$paginaRetorno = 'salarios_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT *, CONCAT(lpad(sala_mes,2,0), '/', sala_ano) AS sala_referente,  format(sala_vlr_total,2,'de_DE') sala_vlr_total, DATE_FORMAT(STR_TO_DATE(sala_data, '%Y-%m-%d'), '%d/%m/%Y') as sala_data
            FROM salarios";
    //
    if ($_POST['pesquisa'] != "") {
        $sql .= " WHERE idsalarios LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR sala_data LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idsalarios'] = "width='6%'";
    $dados['sala_data'] = "";
    $dados['sala_referente'] = "";
    //
    $cabecalho['Código'] = '';
    $cabecalho['Data'] = '';
    $cabecalho['Referente'] = '';
    //
    echo $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno, $cabecalho);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location: ../_Lancamentos/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
    if($_POST['sala_mes'] <= 0 ){
      $html->mostraErro("Informe o mês referente aos salarios<br>Operação cancelada!");
      exit;
    }
    if($_POST['sala_ano'] <= 0){
      $html->mostraErro("Informe o ano referente aos salarios<br>Operação cancelada!");
      exit;
    }
    //
  	$db->setTabela("salarios", "idsalarios");
    //
    if(empty($_POST['sala_data'])){
      $dataAbertura = " NOW() ";
    }else{
      $dataAbertura = $util->dgr($_POST['sala_data']);
    }
    //
    unset($dados);
    $dados['id']                    = $_POST['id_cadastro'];
  	$dados['sala_data']             = $dataAbertura;
    $dados['sala_mes']        = $util->igr($_POST['sala_mes']);
    $dados['sala_ano']        = $util->igr($_POST['sala_ano']);
    if($_POST['id_cadastro'] <= 0){
      $dados['sala_situacao']        = $util->sgr("Aberto");
    }
    //
    $db->gravarInserir($dados, true);
    //
  	if ($_POST['id_cadastro'] > 0) {
      $id = $_POST['id_cadastro'];
    }else{
      $id = $db->getUltimoID();
      //
      $salarios = new salarios($db);
      $salarios->insereFuncionarios($id);
    }
    header('location: ../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
  $db->setTabela("salarios_funcionarios", "safu_idsalarios");
  $db->excluir($_POST['id_cadastro'], "Excluir");
  if($db->erro()){
      $html->mostraErro("Erro ao excluir salarios de funcionarios<br>Operação cancelada!");
      exit;
  }
  //
  $db->setTabela("salarios", "idsalarios");
  $db->excluir($_POST['id_cadastro'], "Excluir");
  if($db->erro()){
      $html->mostraErro("Erro ao excluir salarios<br>Operação cancelada!");
      exit;
  }
  header('location:../_Lancamentos/' . $paginaRetorno);
  exit;
}

if($_POST['operacao'] == "excluiFunc"){
  $db->setTabela("salarios_funcionarios", "idsalarios_funcionarios");
  $db->excluir($_POST['idsalarios_funcionarios']);
  //
  if($db->erro()){
      echo "Erro ao excluir funcionarios\nOperação cancelada!";
      exit;
  }
  echo "Ok";
  exit;
}

if($_POST['operacao'] == "gravaDadosSalario"){
  $db->setTabela("salarios_funcionarios", "idsalarios_funcionarios");
  //
  unset($dados);
  $dados['id']                    = $_POST['idsalarios_funcionarios'];
  $dados['safu_dias']             = $util->igr($_POST['safu_dias']);
  $dados['safu_total']        = $util->vgr($_POST['safu_total']);
  //
  $db->gravarInserir($dados, true);
  //
  if($db->erro()){
      echo "Erro ao gravar informações de salario\nOperação cancelada!";
      exit;
  }
  echo "Ok";
  exit;
}

if ($_POST['operacao'] == 'fechar'){
  $db->beginTransaction();
  //
  $salarios = new salarios($db);
  $contapag = New Contapag($db);
  //  
  $idtipo_conta = $parametros->buscaValor("sistema: codigo do tipo da conta padrao para salarios");
  $idbancos = $parametros->buscaValor("sistema: codigo do banco padrao para contas");
  $idcc = $parametros->buscaValor("sistema: codigo da conta bancaria padrao para contas");
  $idmeio_pagto = $parametros->buscaValor("sistema: codigo do meio de pagamento padrao para contas");
  $recalculaSalario = $parametros->buscaValor("sistema: recalcula contas tipo salario");
  $incluiContaQuitada = $parametros->buscaValor("sistema: incluir contas do tipo salario já quitadas");
  //
  if($idtipo_conta <= 0){
    $html->mostraErro("Tipo da conta padrão não definido<br>Operação cancelada!");
    exit;
  }
  if($idbancos <= 0){
    $html->mostraErro("Banco padrão não definido<br>Operação cancelada!");
    exit;
  }
  if($idcc <= 0){
    $html->mostraErro("Conta corrente padrão não definida<br>Operação cancelada!");
    exit;
  }
  if($idmeio_pagto <= 0){
    $html->mostraErro("Meio de pagamento padrão não definido<br>Operação cancelada!");
    exit;
  }
  //
  //
  $db->setTabela("salarios", "idsalarios");
  //
  unset($dados);
  $dados['id']                    = $_POST['id_cadastro'];
  $dados['sala_situacao']         = $util->sgr("Fechado");
  $dados['sala_data_fechamento']  = " NOW() ";
  //
  $db->gravarInserir($dados, true);
  //
  if($db->erro()){
      $html->mostraErro("Não foi possivel fechar os salarios de funcionarios<br>Operação cancelada!");
      $db->rollBack();
      exit;
  }
  //
  $sql = "SELECT * 
          FROM salarios 
            JOIN salarios_funcionarios ON (safu_idsalarios = idsalarios)
            JOIN pessoas ON (idpessoas = safu_idpessoas)
          WHERE idsalarios = " . $_POST['id_cadastro'];
  $res = $db->consultar($sql);
  //
  foreach($res AS $reg){
    $db->setTabela("contapag", "idcontapag");
    //
    unset($dados);
    $dados['ctpg_idcliente'] 	    = $util->igr($reg['idpessoas']);
    $dados['ctpg_idtipo_contas']  = $util->igr($idtipo_conta);
    $dados['ctpg_idbancos']       = $util->igr($idbancos);
    $dados['ctpg_idcc']           = $util->igr($idcc);
    $dados['ctpg_idmeio_pagto']   = $util->igr($idmeio_pagto);
    $dados['ctpg_vencimento'] 	  = $util->dgr(date('d/m/Y'));
    $dados['ctpg_inclusao'] 	    = $util->dgr(date('d/m/Y'));
    $dados['ctpg_vlr_bruto'] 	    = $reg['safu_total'];
    //
    $descontoPorFalta = $salarios->geraDescontoFaltas($reg['safu_total'], $reg['safu_dias'], $reg['sala_mes'], $reg['sala_ano']);
    $dados['ctpg_vlr_desconto'] 	= $descontoPorFalta;
    //
    $dados['ctpg_situacao']       = $util->sgr("Pendente");
    //
    $db->gravarInserir($dados, false);
    if($db->erro()){
      $html->mostraErro("Não foi possivel criar a conta de salario do funcionario<br>Operação cancelada!");
      $db->rollBack();
      exit;
    }
    //
    $idcontapag = $db->getUltimoID();
    $contapag->gerarHistorio($idcontapag, "Inclusão", $reg['safu_total'], $_SESSION['idusuario'], '', $idmeio_pagto, $idcc);
    //
    $db->setTabela("salarios_funcionarios", "idsalarios_funcionarios");
    //
    unset($dados);
    $dados['id']                        = $reg['idsalarios_funcionarios'];
    $dados['safu_vlr_desconto_faltas']  = $descontoPorFalta;
    $dados['safu_idcontapag']           = $idcontapag;
    //
    $db->gravarInserir($dados, false);
    //
    if($db->erro()){
        $html->mostraErro("Não foi possivel vincular a conta aos registros de salario<br>Operação cancelada!");
        $db->rollBack();
        exit;
    }
    //
    if($recalculaSalario == 'SIM'){
      $contapag->attContaSalario($idcontapag, $reg['idpessoas'], $$idmeio_pagto, $idcc, $reg['sala_mes'], $reg['sala_ano']);
    }
    //
    if($incluiContaQuitada == 'SIM'){
      //
      $valorPago = $db->retornaUmCampoID("(ctpg_vlr_bruto + ctpg_vlr_juros) - ctpg_vlr_desconto", "contapag", $idcontapag);
      //
      $contapag->baixaConta($idcontapag, $valorPago, 0, 0, $_POST['ctpg_idcc'], $_POST['ctpg_idmeio_pagto'], date("d/m/Y"), $_SESSION['idusuario']);
    }

  }
  //
  $db->commit();
  //
  header('location: ../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $_POST['id_cadastro']);
  exit;
}

if ($_POST['operacao'] == 'reabrir'){
  $db->beginTransaction();
  //
  $contapag = New Contapag($db);
  //
  $recalculou = $parametros->buscaValor("sistema: recalcula contas tipo salario");
  //
  $sql = "SELECT * 
          FROM salarios 
            JOIN salarios_funcionarios ON (safu_idsalarios = idsalarios)
            JOIN pessoas ON (idpessoas = safu_idpessoas)
          WHERE idsalarios = " . $_POST['id_cadastro'];
  $res = $db->consultar($sql);
  //
  foreach($res AS $reg){
    if($recalculou == 'SIM'){
      //
      // Reverte as att de contas salarios
      //
      $sql = "SELECT * 
              FROM contapag 
                JOIN contapag_hist ON (idcontapag = cphi_idcontapag)
              WHERE ctpg_situacao = 'BaixaSistema'
                AND ctpg_processou = 'SIM'
                AND cphi_operacao = 'BaixaSistema'
                AND ctpg_idconta_salario = " . $reg['safu_idcontapag'];
      $res2 = $db->consultar($sql);
      foreach($res2 AS $reg2){
        $valor = $reg2['cphi_valor'];
        //
        $db->setTabela("contapag_hist", "idcontapag_hist");
        $db->excluir($reg2['idcontapag_hist'], "Excluir");
        //
        $db->setTabela("contapag", "idcontapag");
        //
        unset($dados);
        $dados['id']                      = $reg2['idcontapag'];
        $dados['ctpg_recalculou']  	      = $util->sgr("NAO");
        $dados['ctpg_vlr_pago']  	        = "ctpg_vlr_pago - " . $util->vgr($valor);
        $dados['ctpg_idconta_salario']  	= " NULL ";
        //
        $db->gravarInserir($dados, false);
      }
      //
      $db->setTabela("contapag", "ctpg_idconta_salario");
      unset($dados);
      $dados['id']                      = $reg['safu_idcontapag'];
      $dados['ctpg_processou']          = $util->sgr("NAO");
      $dados['ctpg_idconta_salario']  	= " NULL ";
      //
      $db->gravarInserir($dados, false);
      //
    }
    //
    $situacao = $db->retornaUmCampoID('ctpg_stituacao', 'contapag', $reg['safu_idcontapag']);
    if($situacao == 'Quitada' || $situacao == 'QParcial'){
      $html->mostraErro("Você não pode rabrir com contas quitadas!<br>Operação cancelada!");
      $db->rollBack();
      exit;
    }
    //$contapag->reabrirConta($reg['safu_idcontapag'], $_SESSION['idusuario']);
    //
    $db->setTabela("contapag_hist", "cphi_idcontapag");
    $db->excluir($reg['safu_idcontapag'], "Excluir");
    if($db->erro()){
      $html->mostraErro("Erro ao excluir o historico da conta<br>Operação cancelada!");
      $db->rollBack();
      exit;
    }
    //
    $db->setTabela("contapag", "idcontapag");
    $db->excluir($reg['safu_idcontapag'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir a conta<br>Operação cancelada!");
        $db->rollBack();
        exit;
    }
    //
    $db->setTabela("salarios_funcionarios", "idsalarios_funcionarios");
    unset($dados);
    $dados['id']               = $reg['idsalarios_funcionarios'];
    $dados['safu_idcontapag']  = " NULL ";
    //
    $db->gravarInserir($dados, false);
    //
    if($db->erro()){
        $html->mostraErro("Não foi possivel desvincular a conta dos registros de salario<br>Operação cancelada!");
        $db->rollBack();
        exit;
    }
  }
  $db->setTabela("salarios", "idsalarios");
  unset($dados);
  $dados['id']                    = $_POST['id_cadastro'];
  $dados['sala_situacao']         = $util->sgr("Aberto");
  $dados['sala_data_fechamento']  = " NULL ";
  //
  $db->gravarInserir($dados, true, "Reabertura");
  //
  if($db->erro()){
      $html->mostraErro("Erro ao reabrir salarios de funcionarios<br>Operação cancelada!");
      $db->rollBack();
      exit;
  }
  //
  $db->commit();
  //
  header('location: ../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $_POST['id_cadastro']);
  exit;
}

echo "Operação enviada: " . $_REQUEST['operacao'];
echo "Erro ao executar, operação não encontarda!";
exit;
 ?>
