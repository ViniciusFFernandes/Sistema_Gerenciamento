<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
require_once("pedcompras.class.php");
require_once("produtos.class.php");
require_once("contapag.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'pedcompras_edita.php';
//
if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT *, 
                DATE_FORMAT(pcom_abertura, '%d/%m/%Y') AS abertura
            FROM pedcompras 
            LEFT JOIN pessoas ON (pcom_idfornecedor = idpessoas)";
    //
    if ($_POST['pesquisa'] != "") {
    $sql .= " WHERE idpedcompras LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                OR pess_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idpedcompras'] = "width='6%' ";
    $dados['pess_nome'] = "";
    $dados['abertura'] = "class='d-none d-sm-table-cell'";
    $dados['pcom_situacao'] = "";
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
        $sql = "SELECT * FROM pedcompras WHERE idpedcompras = " . $_POST['id_cadastro'];
        $reg = $db->retornaUmReg($sql);
        //
        if($reg['pcom_situacao'] != "Aberto"){
            $html->mostraErro("Este pedido não está aberto e não pode ser alterada!");
            exit;
        }
    }
    //
    $db->beginTransaction();
    //
    $db->setTabela("pedcompras", "idpedcompras");
    //
    $pedcompras = New Pedcompras($db);
    $dadosDesconto = $pedcompras->retornaDadosDesconto($_POST['id_cadastro'], $_POST['pcom_frete'], $_POST['pcom_valor_desconto'], $_POST['pcom_porc_desconto']);
    //
    unset($dados);
    $dados['id']                        = $_POST['id_cadastro'];
    $dados['pcom_idfornecedor'] 	    = $util->igr($_POST['pcom_idfornecedor']);
    $dados['pcom_idtipo_contas']        = $util->igr($_POST['pcom_idtipo_contas']);
    $dados['pcom_idbancos']             = $util->igr($_POST['pcom_idbancos']);
    $dados['pcom_idcc']                 = $util->igr($_POST['pcom_idcc']);
    $dados['pcom_idmeio_pagto']         = $util->igr($_POST['pcom_idmeio_pagto']);
    $dados['pcom_idempresas']           = $util->igr($_POST['pcom_idempresas']);
    $dados['pcom_idforma_pagto']        = $util->igr($_POST['pcom_idforma_pagto']);
    $dados['pcom_obs'] 	                = $util->sgr($_POST['pcom_obs']);
    $dados['pcom_com_entrada'] 	        = $util->sgr($_POST['pcom_com_entrada']);
    $dados['pcom_qte_parcelas'] 	    = $util->igrNULL($_POST['pcom_qte_parcelas']);
    $dados['pcom_frete'] 	            = $util->vgr($_POST['pcom_frete']);
    $dados['pcom_porc_desconto'] 	    = $util->vgr($dadosDesconto['porcentagem']);
    $dados['pcom_valor_desconto'] 	    = $util->vgr($dadosDesconto['valor']);
    $dados['pcom_situacao']             = $util->sgr("Aberto");
    
    if($_POST['id_cadastro'] <= 0){
        $dados['pcom_abertura'] 	    = $util->dgr(date('d/m/Y H:i'));
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
    $db->setTabela("pedcompras_itens", "pcit_idpedcompras");
    $db->excluir($_POST['id_cadastro']);
    if($db->erro()){
        $db->rollBack();
        $html->mostraErro("Erro ao excluir os itens do pedido<br>Operação cancelada!");
        exit;
    }
    //
    $db->setTabela("pedcompras_contas", "pccon_idpedcompras");
    $db->excluir($_POST['id_cadastro']);
    if($db->erro()){
        $db->rollBack();
        $html->mostraErro("Erro ao excluir as contas do pedido<br>Operação cancelada!");
        exit;
    }
    //
    $db->setTabela("pedcompras", "idpedcompras");
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
    $nomeCampo = "pcom_idcc";
    if($_POST['tipo'] != ""){
        $nomeCampo = "pccon_idcc";
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
    $sql = "SELECT pcom_total_pedido 
            FROM pedcompras 
            WHERE idpedcompras = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
    //
    echo json_encode($reg);
    exit;
}

if($_POST['operacao'] == 'gravarProduto'){
    //
    $db->beginTransaction();
    //
    $db->setTabela("pedcompras_itens", "idpedcompras_itens");
    //
    unset($dados);
    $dados['id']                        = $_POST['id_cadastro'];
    $dados['pcit_idpedcompras'] 	    = $util->igr($_POST['idpedcompras']);
    $dados['pcit_idprodutos']           = $util->igr($_POST['idprodutos']);
    $dados['pcit_qte']                  = $util->vgr($_POST['pcit_qte']);
    $dados['pcit_vlr_unitario']         = $util->vgr($_POST['pcit_unitario']);
    $dados['pcit_porc_desconto']        = $util->vgr($_POST['pcit_desconto_porc']);
    $dados['pcit_valor_desconto']       = $util->vgr($_POST['pcit_desconto']);
    $dados['pcit_unidade_sigla']        = $util->sgr($_POST['pcit_sigla_unidade']);
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
            FROM pedcompras_itens
                JOIN produtos ON (idprodutos = pcit_idprodutos)
            WHERE idpedcompras_itens = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
    //
    $reg['pcit_vlr_unitario'] = $util->formataMoeda($reg['pcit_vlr_unitario']);
    $reg['pcit_qte'] = $util->formataMoeda($reg['pcit_qte']);
    $reg['pcit_valor_desconto'] = $util->formataMoeda($reg['pcit_valor_desconto']);
    $reg['pcit_porc_desconto'] = $util->formataMoeda($reg['pcit_porc_desconto']);
    $reg['pcit_total_item'] = $util->formataMoeda($reg['pcit_total_item']);
    //
    echo json_encode($reg);
    exit;
}

if($_POST['operacao'] == 'excluirProduto'){
    $db->setTabela("pedcompras_itens", "idpedcompras_itens");
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
    $pedcompras = new Pedcompras($db);
    $listaProdutos = $pedcompras->retornaItensPedido($_POST['idpedcompras']);
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
    $db->setTabela("pedcompras", "idpedcompras");
    //
    if($db->retornaUmCampoID("forp_tipo", "forma_pagto", $_POST['pcom_idforma_pagto']) == "A Vista"){
        $_POST['pcom_qte_parcelas'] = 1;
    }
    //
    unset($dados);
    $dados['id']                    = $_POST['idpedcompras'];
    $dados['pcom_idmeio_pagto'] 	    = $util->igr($_POST['pcom_idmeio_pagto']);
    $dados['pcom_idforma_pagto']     = $util->igr($_POST['pcom_idforma_pagto']);
    $dados['pcom_idbancos']          = $util->igr($_POST['pcom_idbancos']);
    $dados['pcom_idcc']              = $util->igr($_POST['pcom_idcc']);
    $dados['pcom_idtipo_contas']     = $util->igr($_POST['pcom_idtipo_contas']);
    $dados['pcom_qte_parcelas']      = $util->igr($_POST['pcom_qte_parcelas']);
    $dados['pcom_com_entrada']       = $util->sgr($_POST['pcom_com_entrada']);
    //
    $db->gravarInserir($dados);
    //
    $sql = "SELECT * FROM pedcompras LEFT JOIN forma_pagto ON (idforma_pagto = pcom_idforma_pagto) WHERE idpedcompras = " . $_POST['idpedcompras'];
    $reg = $db->retornaUmReg($sql);
    //
    $retorno['erro'] = 0;
    //
    if($reg['pcom_idmeio_pagto'] <= 0){
        $retorno['erro'] = 1;
        $retorno['msgErro'] = "Meio de pagamento não selecionado!";
        echo json_encode($retorno);
        exit;
    }
    if($reg['pcom_idforma_pagto'] <= 0){
        $retorno['erro'] = 1;
        $retorno['msgErro'] = "Forma de pagamento não selecionado!";
        echo json_encode($retorno);
        exit;
    }
    if($reg['forp_tipo'] == 'Parcelamento Livre' && $reg['pcom_qte_parcelas'] <= 0){
        $retorno['erro'] = 1;
        $retorno['msgErro'] = "Parcelas não informadas!";
        echo json_encode($retorno);
        exit;
    }
    if($reg['pcom_idbancos'] <= 0){
        $retorno['erro'] = 1;
        $retorno['msgErro'] = "Banco não selecionado!";
        echo json_encode($retorno);
        exit;
    }
    if($reg['pcom_idcc'] <= 0){
        $retorno['erro'] = 1;
        $retorno['msgErro'] = "Conta Bancaria não selecionado!";
        echo json_encode($retorno);
        exit;
    }
    
    //
    $db->setTabela("pedcompras_contas", "pccon_idpedcompras");
    $db->excluir($_POST['idpedcompras']);
    //
    $valorParcela = $reg['pcom_total_pedido'] / $reg['pcom_qte_parcelas'];
    if($reg['pcom_com_entrada'] == 'SIM' || $reg['forp_tipo'] == 'Mensal com Entrada' || $reg['forp_tipo'] == 'A Vista'){
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
    $db->setTabela("pedcompras_contas", "idpedcompras_contas");
    //
    while($parcela <= $reg['pcom_qte_parcelas']) {
        //
        $vencto = $util->manipulaData($data, $dias, "dias");
        //
        if ($reg["forp_tipo"] == 'Dias informados') {
            $dias_prazo = trim($reg["forp_dias_prazo"]);
            //
            $diasPrazo = explode(',' , $dias_prazo);
            $diasSoma = $diasPrazo[$parcela - 1];
            $dados["pccon_vencto"] 		= dgr($util->manipulaData($data, $diasSoma, "dias"));
            $dados["pccon_dias"]         = igr($diasPrazo[$i - 1]);
        }
        //
        unset($dados);
        $dados['id']                    = 0;
        $dados['pccon_idpedcompras'] 	    = $util->igr($_POST['idpedcompras']);
        $dados['pccon_idmeio_pagto']     = $util->igr($reg['pcom_idmeio_pagto']);
        $dados['pccon_idbancos']         = $util->igr($reg['pcom_idbancos']);
        $dados['pccon_idcc']             = $util->igr($reg['pcom_idcc']);
        $dados['pccon_idtipo_contas']    = $util->igr($reg['pcom_idtipo_contas']);
        $dados['pccon_idempresas']       = $util->igr($reg['pcom_idempresas']);
        $dados['pccon_vencimento_dias']  = $util->igr($dias);
        $dados['pccon_vencimento']       = $util->sgr($vencto);
        $dados['pccon_parcela']          = $util->sgr($parcela . "/" . $reg['pcom_qte_parcelas']);
        $dados['pccon_valor']            = $util->vgr($valorParcela);
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
    $pedcompras = new Pedcompras($db);
    $listaProdutos = $pedcompras->retornaContasPedido($_POST['idpedcompras']);
    //
    echo $listaProdutos;
    exit;
}

if($_POST['operacao'] == 'excluirConta'){
    $db->setTabela("pedcompras_contas", "idpedcompras_contas");
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
    $db->setTabela("pedcompras_contas", "idpedcompras_contas");
    //
    unset($dados);
    $dados['id']                    = $_POST['id_cadastro'];
    $dados['pccon_idmeio_pagto']     = $util->igr($_POST['pcom_idmeio_pagto']);
    $dados['pccon_idbancos']         = $util->igr($_POST['pcom_idbancos']);
    $dados['pccon_idcc']             = $util->igr($_POST['pcom_idcc']);
    $dados['pccon_idtipo_contas']    = $util->igr($_POST['pcom_idtipo_contas']);
    $dados['pccon_vencimento_dias']  = $util->igr($_POST['pccon_vencimento_dias']);
    $dados['pccon_vencimento']       = $util->sgr($_POST['pccon_vencimento']);
    $dados['pccon_valor']            = $util->vgr($_POST['pccon_valor']);
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
            FROM pedcompras_contas
                LEFT JOIN meio_pagto ON (idmeio_pagto = pccon_idmeio_pagto)
                LEFT JOIN cc ON (idcc = pccon_idcc)
                LEFT JOIN tipo_contas ON (idtipo_contas = pccon_idtipo_contas)
            WHERE idpedcompras_contas = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
    //
    $sql = "SELECT * FROM meio_pagto";
    $ret["comboBoxMeioPagtoModal"] = $html->criaSelectSql("mpag_nome", "idmeio_pagto", "pccon_idmeio_pagto", $reg['pccon_idmeio_pagto'], $sql, "form-control", "", false, "Meio de Pagamento");
    //
    $sql = "SELECT * FROM bancos";
    $ret["comboBoxBancosModal"] = $html->criaSelectSql("banc_nome", "idbancos", "pccon_idbancos", $reg['pccon_idbancos'], $sql, "form-control", 'onchange="carregaComboBoxCC(\'Modal\')"', false, "Banco");
    //
    $sql = "SELECT * FROM cc WHERE cc_idbancos = " . $reg['pccon_idbancos'];
    $ret["comboBoxCCModal"] = $html->criaSelectSql("cc_nome", "idcc", "pccon_idcc", $reg['pccon_idcc'], $sql, "form-control", '', false, "Conta Corrente");
    //
    $sql = "SELECT * FROM tipo_contas";
    $ret["comboBoxTipoContaModal"] = $html->criaSelectSql("tico_nome", "idtipo_contas", "pccon_idtipo_contas", $reg['pccon_idtipo_contas'], $sql, "form-control", '', false, "Tipo da Conta");
    //
    $ret["pccon_vencimento"] = $reg['pccon_vencimento'];
    $ret["pccon_vencimento_dias"] = $reg['pccon_vencimento_dias'];
    $ret['pccon_valor'] = $util->formataMoeda($reg['pccon_valor']);
    $ret['idpedcompras_contas'] = $reg['idpedcompras_contas'];
    //
    echo json_encode($ret);
    exit;
}

if($_POST['operacao'] == 'fechar'){
    //
    $sql = "SELECT * 
        FROM pedcompras 
        WHERE idpedcompras = {$_REQUEST['id_cadastro']}";
    $regPedcompras = $db->retornaUmReg($sql);
    //
    if($regPedcompras['pcom_situacao'] != 'Aberto'){
        $html->mostraErro("Este pedido não está aberto e não pode ser fechado!");
        exit; 
    }
    //
    $db->beginTransaction();
    //
    if($parametros->buscaValor("pedidos: baixa estoque no fechamento") == 'SIM'){
        $sql = "SELECT SUM(pcit_qte) AS qte_pedido,
                    pcit_idprodutos,
                    pcit_idpedcompra,
                    prod_nome,
                    prod_qte_estoque 
                FROM pedcompras_itens
                    JOIN produtos ON (pcit_idprodutos = idprodutos)
                WHERE pcit_idpedcompras = {$regPedcompras['idpedcompras']}
                GROUP BY pcit_idprodutos, prod_nome, prod_qte_estoque";
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
            $estoque->geraMovimento($reg['pcit_idprodutos'], "-", $reg['qte_pedido'], basename($_SERVER['PHP_SELF']), $reg['pcit_idpedcompra']);
        }
    }
    //
    //Valida o parcelamento do pedido
    //
    $sql = "SELECT SUM(pccon_valor) AS total_contas
            FROM pedcompras_contas
            WHERE pccon_idpedcompras = {$regPedcompras['idpedcompras']}";
    $totalContas = $db->retornaUmCampoSql($sql, 'total_contas');
    //
    if($regPedcompras['pcom_total_pedido'] != $totalContas){
        $html->mostraErro("Parcelamento incompativel com valor do pedido!");
        exit; 
    }
    //
    //Faz a geração das parcelas do pedido
    //
    $sql = "SELECT * 
            FROM pedcompras_contas
            WHERE pccon_idpedcompras = {$regPedcompras['idpedcompras']}";
    $res = $db->consultar($sql);
    //
    $contapag = New Contapag($db);
    foreach ($res as $reg) {
        $db->setTabela("contapag", "idcontapag");
        //
        unset($dados);
        $dados['id']                    = 0;
        $dados['ctpg_idpedcompras'] 	= $util->igr($_POST['idpedcompras']);
        $dados['ctpg_idcliente'] 	    = $util->igr($regPedcompras["pcom_idfornecedor"]);
        $dados['ctpg_inclusao']         = $util->dgr(date('d/m/Y H:i'));
        $dados['ctpg_situacao']         = $util->sgr("Pendente");
        $dados['ctpg_idmeio_pagto']     = $reg['pccon_idmeio_pagto'];
        $dados['ctpg_idbancos']         = $reg['pccon_idbancos'];
        $dados['ctpg_idcc']             = $reg['pccon_idcc'];
        $dados['ctpg_idtipo_contas']    = $reg['pccon_idtipo_contas'];
        $dados['ctpg_idempresas']        = $reg['pccon_idempresas'];
        $dados['ctpg_parcela']          = $util->sgr($reg['pccon_parcela']);
        $dados['ctpg_vencimento']       = $util->sgr($reg['pccon_vencimento']);
        $dados['ctpg_vlr_bruto']        = $util->vgr($reg['pccon_valor']);
        //    
        $db->gravarInserir($dados, false); 
        //
        $idContapag = $db->getUltimoID();
        $contapag->gerarHistorio($idContapag, "Inclusão", $reg['pccon_valor'], $_SESSION['idusuario'], '', $reg['pccon_idmeio_pagto'], $reg['pccon_idcc']);
        //
        $db->setTabela("pedcompras_contas", "idpedcompras_contas");
        //
        unset($dados);
        $dados['id']                    = $reg['idpedcompras_contas'];
        $dados['pccon_idcontapag'] 	    = $idContapag;
        //
        $db->gravarInserir($dados, false); 
    }
    //
    $db->setTabela("pedcompras", "idpedcompras");
    //
    unset($dados);
    $dados['id']                    = $_POST['idpedcompras'];
    $dados['pcom_fechamento'] 	    = $util->dgr(date('d/m/Y H:i'));
    $dados['pcom_situacao']          = $util->sgr("Fechado");
    //    
    $db->gravarInserir($dados, true, "Fechamento"); 
    //
    $db->commit();
    //
    header('location:../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $_POST['idpedcompras']);
    exit;
}

if($_POST['operacao'] == 'reabrir'){
    //
    $db->beginTransaction();
    //
    $sql = "SELECT COUNT(1) AS total_quitadas 
            FROM contapag 
            WHERE (ctpg_situacao = 'Quitada'
                OR ctpg_situacao = 'QParcial')
            AND ctpg_idpedcompras = " . $_POST['idpedcompras'];
    //
    $qteQuitadas = $db->retornaUmCampoSql($sql, "total_quitadas");
    if($qteQuitadas > 0){
        $html->mostraErro("Este pedido possui contas pagas e não pode ser alterada!");
        exit;
    }
    //
    //Apaga as contas e limpa o pedcompras_contas
    //
    $db->setTabela("contapag", "ctpg_idpedcompras");
    $db->excluir($_POST['idpedcompras']);
    //
    $sql = "UPDATE pedcompras_contas
            SET pccon_idcontapag = NULL
            WHERE pccon_idpedcompras = {$_POST['idpedcompras']}";
    $db->executaSQL($sql);
    //
    $db->setTabela("pedcompras", "idpedcompras");
    //
    unset($dados);
    $dados['id']                    = $_POST['idpedcompras'];
    $dados['pcom_fechamento'] 	    = " NULL ";
    $dados['pcom_situacao']          = $util->sgr("Aberto");
    //    
    $db->gravarInserir($dados, true, "reabertura"); 
    //
    $db->commit();
    //
    header('location:../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $_POST['idpedcompras']);
    exit;
}

?>
