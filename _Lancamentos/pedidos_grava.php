<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
require_once("pedidos.class.php");
require_once("produtos.class.php");
require_once("contarec.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'pedidos_edita.php';
//
if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT *, 
               DATE_FORMAT(ped_abertura, '%d/%m/%Y') AS abertura
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
            $html->mostraErro("Este pedido não está aberto e não pode ser alterada!");
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
    $nomeCampo = "ped_idcc";
    if($_POST['tipo'] != ""){
        $nomeCampo = "pcon_idcc";
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
    if($reg['ped_com_entrada'] == 'SIM' || $reg['forp_tipo'] == 'Mensal com Entrada' || $reg['forp_tipo'] == 'A Vista'){
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
        if ($reg["forp_tipo"] == 'Dias informados') {
            $dias_prazo = trim($reg["forp_dias_prazo"]);
            //
            $diasPrazo = explode(',' , $dias_prazo);
            $diasSoma = $diasPrazo[$parcela - 1];
            $dados["pcon_vencto"] 		= dgr($util->manipulaData($data, $diasSoma, "dias"));
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
        $dados['pcon_parcela']          = $util->sgr($parcela . "/" . $reg['ped_qte_parcelas']);
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

if($_POST['operacao'] == 'excluirConta'){
    $db->setTabela("pedidos_contas", "idpedidos_contas");
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

if($_POST['operacao'] == 'gravarConta'){
    $db->setTabela("pedidos_contas", "idpedidos_contas");
    //
    unset($dados);
    $dados['id']                    = $_POST['id_cadastro'];
    $dados['pcon_idmeio_pagto']     = $util->igr($_POST['ped_idmeio_pagto']);
    $dados['pcon_idbancos']         = $util->igr($_POST['ped_idbancos']);
    $dados['pcon_idcc']             = $util->igr($_POST['ped_idcc']);
    $dados['pcon_idtipo_contas']    = $util->igr($_POST['ped_idtipo_contas']);
    $dados['pcon_vencimento_dias']  = $util->igr($_POST['pcon_vencimento_dias']);
    $dados['pcon_vencimento']       = $util->sgr($_POST['pcon_vencimento']);
    $dados['pcon_valor']            = $util->vgr($_POST['pcon_valor']);
    //    
    $db->gravarInserir($dados, false); 
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

if($_POST['operacao'] == 'editarContas'){
    $sql = "SELECT * 
            FROM pedidos_contas
                LEFT JOIN meio_pagto ON (idmeio_pagto = pcon_idmeio_pagto)
                LEFT JOIN cc ON (idcc = pcon_idcc)
                LEFT JOIN tipo_contas ON (idtipo_contas = pcon_idtipo_contas)
            WHERE idpedidos_contas = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
    //
    $sql = "SELECT * FROM meio_pagto";
    $ret["comboBoxMeioPagtoModal"] = $html->criaSelectSql("mpag_nome", "idmeio_pagto", "pcon_idmeio_pagto", $reg['pcon_idmeio_pagto'], $sql, "form-control", "", false, "Meio de Pagamento");
    //
    $sql = "SELECT * FROM bancos";
    $ret["comboBoxBancosModal"] = $html->criaSelectSql("banc_nome", "idbancos", "pcon_idbancos", $reg['pcon_idbancos'], $sql, "form-control", 'onchange="carregaComboBoxCC(\'Modal\')"', false, "Banco");
    //
    $sql = "SELECT * FROM cc WHERE cc_idbancos = " . $reg['pcon_idbancos'];
    $ret["comboBoxCCModal"] = $html->criaSelectSql("cc_nome", "idcc", "pcon_idcc", $reg['pcon_idcc'], $sql, "form-control", '', false, "Conta Corrente");
    //
    $sql = "SELECT * FROM tipo_contas";
    $ret["comboBoxTipoContaModal"] = $html->criaSelectSql("tico_nome", "idtipo_contas", "pcon_idtipo_contas", $reg['pcon_idtipo_contas'], $sql, "form-control", '', false, "Tipo da Conta");
    //
    $ret["pcon_vencimento"] = $reg['pcon_vencimento'];
    $ret["pcon_vencimento_dias"] = $reg['pcon_vencimento_dias'];
    $ret['pcon_valor'] = $util->formataMoeda($reg['pcon_valor']);
    $ret['idpedidos_contas'] = $reg['idpedidos_contas'];
    //
    echo json_encode($ret);
    exit;
}

if($_POST['operacao'] == 'fechar'){
    //
    $sql = "SELECT * 
        FROM pedidos 
        WHERE idpedidos = {$_REQUEST['id_cadastro']}";
    $regPedidos = $db->retornaUmReg($sql);
    //
    if($regPedidos['ped_situacao'] != 'Aberto'){
        $html->mostraErro("Este pedido não está aberto e não pode ser fechado!");
        exit; 
    }
    //
    $db->beginTransaction();
    //
    if($parametros->buscaValor("pedidos: baixa estoque no fechamento") == 'SIM'){
        $sql = "SELECT SUM(peit_qte) AS qte_pedido,
                    peit_idprodutos,
                    peit_idpedidos,
                    prod_nome,
                    prod_qte_estoque
                FROM pedidos_itens
                    JOIN produtos ON (peit_idprodutos = idprodutos)
                WHERE peit_idpedidos = {$regPedidos['idpedidos']}
                GROUP BY peit_idprodutos, prod_nome, prod_qte_estoque";
        $res = $db->consultar($sql);
        //
        $permiteEstoqueNegativo = $parametros->buscaValor("empresa: permite trabalhar com estoque negativo");
        //
        foreach ($res as $reg) {
            if($permiteEstoqueNegativo != 'SIM' && $reg['prod_qte_estoque'] < $reg['qte_pedido']){
                $db->rollBack();
                $html->mostraErro("Estoque insufiente do produto {$reg['prod_nome']}!");
                exit; 
            }
            //
            $estoque->geraMovimento($reg['peit_idprodutos'], "+", $reg['prod_qte_estoque'], basename($_SERVER['PHP_SELF']), $reg['peit_idpedidos']);
        }
    }
    //
    //Valida o parcelamento do pedido
    //
    $sql = "SELECT SUM(pcon_valor) AS total_contas
            FROM pedidos_contas
            WHERE pcon_idpedidos = {$regPedidos['idpedidos']}";
    $totalContas = $db->retornaUmCampoSql($sql, 'total_contas');
    //
    if($regPedidos['ped_total_pedido'] != $totalContas){
        $html->mostraErro("Parcelamento incompativel com valor do pedido!");
        exit; 
    }
    //
    //Faz a geração das parcelas do pedido
    //
    $sql = "SELECT * 
            FROM pedidos_contas
            WHERE pcon_idpedidos = {$regPedidos['idpedidos']}";
    $res = $db->consultar($sql);
    //
    $contarec = New Contarec($db);
    foreach ($res as $reg) {
        $db->setTabela("contarec", "idcontarec");
        //
        unset($dados);
        $dados['id']                    = 0;
        $dados['ctrc_idpedidos'] 	    = $util->igr($_POST['idpedidos']);
        $dados['ctrc_idcliente'] 	    = $util->igr($regPedidos["ped_idcliente"]);
        $dados['ctrc_inclusao']         = $util->dgr(date('d/m/Y H:i'));
        $dados['ctrc_situacao']         = $util->sgr("Pendente");
        $dados['ctrc_idmeio_pagto']     = $reg['pcon_idmeio_pagto'];
        $dados['ctrc_idbancos']         = $reg['pcon_idbancos'];
        $dados['ctrc_idcc']             = $reg['pcon_idcc'];
        $dados['ctrc_idtipo_contas']    = $reg['pcon_idtipo_contas'];
        $dados['ctrc_idempresas']        = $reg['pcon_idempresas'];
        $dados['ctrc_parcela']          = $util->sgr($reg['pcon_parcela']);
        $dados['ctrc_vencimento']       = $util->sgr($reg['pcon_vencimento']);
        $dados['ctrc_vlr_bruto']        = $util->vgr($reg['pcon_valor']);
        //    
        $db->gravarInserir($dados, false); 
        //
        $idContarec = $db->getUltimoID();
        $contarec->gerarHistorio($idContarec, "Inclusão", $reg['pcon_valor'], $_SESSION['idusuario'], '', $reg['pcon_idmeio_pagto'], $reg['pcon_idcc']);
        //
        $db->setTabela("pedidos_contas", "idpedidos_contas");
        //
        unset($dados);
        $dados['id']                    = $reg['idpedidos_contas'];
        $dados['pcon_idcontarec'] 	    = $idContarec;
        //
        $db->gravarInserir($dados, false); 
    }
    //
    $db->setTabela("pedidos", "idpedidos");
    //
    unset($dados);
    $dados['id']                    = $_POST['idpedidos'];
    $dados['ped_fechamento'] 	    = $util->dgr(date('d/m/Y H:i'));
    $dados['ped_situacao']          = $util->sgr("Fechado");
    //    
    $db->gravarInserir($dados, true, "Fechamento"); 
    //
    $db->commit();
    //
    header('location:../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $_POST['idpedidos']);
    exit;
}

if($_POST['operacao'] == 'reabrir'){
    //
    $db->beginTransaction();
    //
    $sql = "SELECT COUNT(1) AS total_quitadas 
            FROM contarec 
            WHERE (ctrc_situacao = 'Quitada'
                OR ctrc_situacao = 'QParcial')
            AND ctrc_idpedidos = " . $_POST['idpedidos'];
    //
    $qteQuitadas = $db->retornaUmCampoSql($sql, "total_quitadas");
    if($qteQuitadas > 0){
        $html->mostraErro("Este pedido possui contas pagas e não pode ser alterada!");
        exit;
    }
    //
    //Apaga as contas e limpa o pedidos_contas
    //
    $db->setTabela("contarec", "ctrc_idpedidos");
    $db->excluir($_POST['idpedidos']);
    //
    $sql = "UPDATE pedidos_contas
            SET pcon_idcontarec = NULL
            WHERE pcon_idpedidos = {$_POST['idpedidos']}";
    $db->executaSQL($sql);
    //
    $db->setTabela("pedidos", "idpedidos");
    //
    unset($dados);
    $dados['id']                    = $_POST['idpedidos'];
    $dados['ped_fechamento'] 	    = " NULL ";
    $dados['ped_situacao']          = $util->sgr("Aberto");
    //    
    $db->gravarInserir($dados, true, "reabertura"); 
    //
    $db->commit();
    //
    header('location:../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $_POST['idpedidos']);
    exit;
}

?>
