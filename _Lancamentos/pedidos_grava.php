<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
require_once("pedidos.class.php");
require_once("produtos.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'pedidos_edita.php';
//
if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT *, 
                DATE_FORMAT(STR_TO_DATE(ped_abertura, '%Y-%m-%d'), '%d/%m/%Y') as abertura
            FROM pedidos 
            LEFT JOIN pessoas ON (ped_idcliente = idpessoas)";
    //
    if ($_POST['pesquisa'] != "") {
    $sql .= " WHERE idpedidos LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                OR pess_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idpedidos'] = "width='6%' ";
    $dados['pess_nome'] = "";
    $dados['abertura'] = "class='d-none d-sm-table-cell'";
    $dados['ped_situacao'] = "";
    //
    $cabecalho["Código"] = "";
    $cabecalho["Cliente"] = "";
    $cabecalho["Abertura"] = "class='d-none d-sm-table-cell'";
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
        $sql = "SELECT * FROM pedidos WHERE idpedidos = " . $_POST['id_cadastro'];
        $reg = $db->retornaUmReg($sql);
        //
        if($reg['ped_situacao'] != "Aberto"){
            $util->mostraErro("Este pedido não está aberto e não pode ser alterada!");
            exit;
        }
    }
    //
    $db->beginTransaction();
    //
    $db->setTabela("pedidos", "idpedidos");
    //
    $pedidos = New Pedidos($db);
    $dadosDesconto = $pedidos->retornaDadosDesconto($_POST['id_cadastro'], $_POST['ped_frete'], $_POST['ped_valor_desconto'], $_POST['ped_porc_desconto']);
    //
    unset($dados);
    $dados['id']                    = $_POST['id_cadastro'];
    $dados['ped_idcliente'] 	    = $util->igr($_POST['ped_idcliente']);
    $dados['ped_idtipo_contas']     = $util->igr($_POST['ped_idtipo_contas']);
    $dados['ped_idbancos']          = $util->igr($_POST['ped_idbancos']);
    $dados['ped_idcc']              = $util->igr($_POST['ped_idcc']);
    $dados['ped_idmeio_pagto']      = $util->igr($_POST['ped_idmeio_pagto']);
    $dados['ped_idempresas']        = $util->igr($_POST['ped_idempresas']);
    $dados['ped_idforma_pagto']     = $util->igr($_POST['ped_idforma_pagto']);
    $dados['ped_obs'] 	            = $util->sgr($_POST['ped_obs']);
    $dados['ped_com_entrada'] 	    = $util->sgr($_POST['ped_com_entrada']);
    $dados['ped_qte_parcelas'] 	    = $util->igrNULL($_POST['ped_qte_parcelas']);
    $dados['ped_frete'] 	        = $util->vgr($_POST['ped_frete']);
    $dados['ped_porc_desconto'] 	= $util->vgr($dadosDesconto['porcentagem']);
    $dados['ped_valor_desconto'] 	= $util->vgr($dadosDesconto['valor']);
    $dados['ped_situacao']         = $util->sgr("Aberto");
    
    if($_POST['id_cadastro'] <= 0){
        $dados['ped_abertura'] 	        = $util->dgr(date('d/m/Y H:i'));
    }
    //
    $db->gravarInserir($dados, true);
    //
    if ($_POST['id_cadastro'] > 0) {
        $id = $_POST['id_cadastro'];
    }else{
        $id = $db->getUltimoID();
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
    //
    $db->beginTransaction();
    //
    $db->setTabela("pedidos_itens", "peit_idpedidos");
    $db->excluir($_POST['id_cadastro']);
    if($db->erro()){
        $db->rollBack();
        $html->mostraErro("Erro ao excluir os itens do pedido<br>Operação cancelada!");
        exit;
    }
    //
    $db->setTabela("pedidos_contas", "pcon_idpedidos");
    $db->excluir($_POST['id_cadastro']);
    if($db->erro()){
        $db->rollBack();
        $html->mostraErro("Erro ao excluir as contas do pedido<br>Operação cancelada!");
        exit;
    }
    //
    $db->setTabela("pedidos", "idpedidos");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $db->rollBack();
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    //
    $db->commit();
    //
    header('location:../_Lancamentos/' . $paginaRetorno);
    exit;
}

if($_POST['operacao'] == 'geraComboBoxCC'){
    $nomeCampo = "ctrc_idcc";
    if($_POST['tipo'] != ""){
        $nomeCampo = "idcc_pagamento";
    }
    $sql = "SELECT * FROM cc WHERE cc_idbancos = " . $_POST['idbancos'];
    $comboBoxTipoConta = $html->criaSelectSql("cc_nome", "idcc", $nomeCampo, '', $sql, "form-control");
    echo $comboBoxTipoConta;
}

if($_POST['operacao'] == 'buscaDadosProduto'){
    $produtos = new produtos($db);
    //
    $dados = $produtos->getDadosProdutos($_POST['idprodutos']); 
    //
    $ret = array();
    $ret['prod_preco'] = $util->formataMoeda($dados['prod_preco_tabela']);
    $ret['prod_unidade'] = $dados['uni_sigla'];
    //
    echo json_encode($ret);
    exit;
}

if($_POST['operacao'] == 'atzTotalPedido'){
    $sql = "SELECT ped_total_pedido 
            FROM pedidos 
            WHERE idpedidos = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
    //
    echo json_encode($reg);
    exit;
}

if($_POST['operacao'] == 'gravarProduto'){
    //
    $db->beginTransaction();
    //
    $db->setTabela("pedidos_itens", "idpedidos_itens");
    //
    unset($dados);
    $dados['id']                    = $_POST['id_cadastro'];
    $dados['peit_idpedidos'] 	    = $util->igr($_POST['idpedidos']);
    $dados['peit_idprodutos']       = $util->igr($_POST['idprodutos']);
    $dados['peit_qte']              = $util->vgr($_POST['peit_qte']);
    $dados['peit_vlr_unitario']     = $util->vgr($_POST['peit_unitario']);
    $dados['peit_porc_desconto']    = $util->vgr($_POST['peit_desconto_porc']);
    $dados['peit_valor_desconto']   = $util->vgr($_POST['peit_desconto']);
    $dados['peit_unidade_sigla']    = $util->sgr($_POST['peit_sigla_unidade']);
    //    
    $db->gravarInserir($dados, false);
    //
    $ret = array();
    //
    if($db->erro()){
        $ret['retorno'] = "erro";
        $ret['msg'] = $db->getErro();
    }else{
        $ret['retorno'] = "ok";
        $db->commit();
    }
    //
    echo json_encode($ret);
    exit;
}

if($_POST['operacao'] == 'editarProduto'){
    $sql = "SELECT * 
            FROM pedidos_itens
                JOIN produtos ON (idprodutos = peit_idprodutos)
            WHERE idpedidos_itens = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
    //
    $reg['peit_vlr_unitario'] = $util->formataMoeda($reg['peit_vlr_unitario']);
    $reg['peit_qte'] = $util->formataMoeda($reg['peit_qte']);
    $reg['peit_valor_desconto'] = $util->formataMoeda($reg['peit_valor_desconto']);
    $reg['peit_porc_desconto'] = $util->formataMoeda($reg['peit_porc_desconto']);
    $reg['peit_total_item'] = $util->formataMoeda($reg['peit_total_item']);
    //
    echo json_encode($reg);
    exit;
}

if($_POST['operacao'] == 'excluirProduto'){
    $db->setTabela("pedidos_itens", "idpedidos_itens");
    $db->excluir($_POST['id_cadastro']);
    //
    if($db->erro()){
        $ret['retorno'] = "erro";
        $ret['msg'] = $db->getErro();
    }else{
        $ret['retorno'] = "ok";
    }
    //
    echo json_encode($ret);
    exit;
}

if($_POST['operacao'] == 'attlistaProdutos'){
    $pedidos = new Pedidos($db);
    $listaProdutos = $pedidos->retornaItensPedido($_POST['idpedidos']);
    //
    echo $listaProdutos;
    exit;
}

if($_POST['operacao'] == 'retornaTipoFormaPagto'){
    $sql = "SELECT * FROM forma_pagto WHERE idforma_pagto = " . $_POST['idforma_pagto'];
    $reg = $db->retornaUmReg($sql);
    //
    $ret['retorno'] = $reg['forp_tipo'];
    //
    echo json_encode($ret);
    exit;
}

if($_POST['operacao'] == 'gerarParcelas'){
    //
    $db->setTabela("pedidos", "idpedidos");
    //
    unset($dados);
    $dados['id']                    = $_POST['idpedidos'];
    $dados['ped_idmeio_pagto'] 	    = $util->igr($_POST['ped_idmeio_pagto']);
    $dados['ped_idforma_pagto']     = $util->igr($_POST['ped_idforma_pagto']);
    $dados['ped_idbancos']          = $util->igr($_POST['ped_idbancos']);
    $dados['ped_idcc']              = $util->igr($_POST['ped_idcc']);
    $dados['ped_idtipo_contas']     = $util->igr($_POST['ped_idtipo_contas']);
    $dados['ped_qte_parcelas']      = $util->igr($_POST['ped_qte_parcelas']);
    $dados['ped_com_entrada']       = $util->sgr($_POST['ped_com_entrada']);
    //
    $db->gravarInserir($dados);
    //
    $sql = "SELECT * FROM pedidos LEFT JOIN forma_pagto ON (idforma_pagto = ped_idforma_pagto) WHERE idpedidos = " . $_POST['idpedidos'];
    $reg = $db->retornaUmReg($sql);
    //
    $retorno['erro'] = 0;
    //
    if($reg['ped_idmeio_pagto'] <= 0){
        $retorno['erro'] = 1;
        $retorno['msgErro'] = "Meio de pagamento não selecionado!";
        echo json_encode($retorno);
        exit;
    }
    if($reg['ped_idforma_pagto'] <= 0){
        $retorno['erro'] = 1;
        $retorno['msgErro'] = "Forma de pagamento não selecionado!";
        echo json_encode($retorno);
        exit;
    }
    if($reg['forp_tipo'] == 'Parcelamento Livre' && $reg['ped_qte_parcelas'] <= 0){
        $retorno['erro'] = 1;
        $retorno['msgErro'] = "Parcelas não informadas!";
        echo json_encode($retorno);
        exit;
    }
    if($reg['ped_idbancos'] <= 0){
        $retorno['erro'] = 1;
        $retorno['msgErro'] = "Banco não selecionado!";
        echo json_encode($retorno);
        exit;
    }
    if($reg['ped_idcc'] <= 0){
        $retorno['erro'] = 1;
        $retorno['msgErro'] = "Conta Bancaria não selecionado!";
        echo json_encode($retorno);
        exit;
    }
    //
    $db->setTabela("pedidos_contas", "pcon_idpedidos");
    $db->excluir($_POST['idpedidos']);
    //
    $valorParcela = $reg['ped_total_pedido'] / $reg['ped_qte_parcelas'];
    if($reg['ped_com_entrada'] == 'SIM' || $reg['forp_tipo'] == 'Mensal com Entrada'){
        $dias = 0;
    }else{
        if($reg['forp_dias'] > 0){
            $dias = $reg['forp_dias'];
        }else{
            $dias = 30;
        }
    }
    //
    $parcela = 1;
    $data = date("Y-m-d");
    $db->setTabela("pedidos_contas", "idpedidos_contas");
    //
    while($parcela <= $reg['ped_qte_parcelas']) {
        //
        $vencto = $util->manipulaData($data, $dias, "dias");
        //
        if ($regNota["foRP_tipo"] == 'Dias informados') {
            $dias_prazo = trim($regNota["forp_dias_prazo"]);
            //
            $diasPrazo = explode(',' , $dias_prazo);
            $diasSoma = $diasPrazo[$i - 1];
            $dados["pcon_vencto"] 		= dgr(m_datas::somarData($data, $diasSoma));
            $dados["pcon_dias"]         = igr($diasPrazo[$i - 1]);
        }
        //
        unset($dados);
        $dados['id']                    = 0;
        $dados['pcon_idpedidos'] 	    = $util->igr($_POST['idpedidos']);
        $dados['pcon_idmeio_pagto']     = $util->igr($reg['ped_idmeio_pagto']);
        $dados['pcon_idbancos']         = $util->igr($reg['ped_idbancos']);
        $dados['pcon_idcc']             = $util->igr($reg['ped_idcc']);
        $dados['pcon_idtipo_contas']    = $util->igr($reg['ped_idtipo_contas']);
        $dados['pcon_idempresas']       = $util->igr($reg['ped_idempresas']);
        $dados['pcon_vencimento_dias']  = $util->igr($dias);
        $dados['pcon_vencimento']       = $util->sgr($vencto);
        $dados['pcon_valor']            = $util->vgr($valorParcela);
        //    
        $db->gravarInserir($dados, false);
        //
        $dias += 30;
        $parcela ++;
    }
    $retorno['erro'] = 0;
    $retorno['msgErro'] = "";
    echo json_encode($retorno);
    exit;
}

if($_POST['operacao'] == 'attParcelas'){
    $pedidos = new Pedidos($db);
    $listaProdutos = $pedidos->retornaContasPedido($_POST['idpedidos']);
    //
    echo $listaProdutos;
    exit;
}

?>
