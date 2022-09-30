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
        if($reg['ped_situacao'] != "Pendente"){
            $util->mostraErro("Este pedido não está pendente e não pode ser alterada!");
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
    $dados['ped_qte_parcelas'] 	    = $util->igr($_POST['ped_qte_parcelas']);
    $dados['ped_frete'] 	        = $util->vgr($_POST['ped_frete']);
    $dados['ped_porc_desconto'] 	= $util->vgr($dadosDesconto['porcentagem']);
    $dados['ped_valor_desconto'] 	= $util->vgr($dadosDesconto['valor']);
    $dados['ped_situacao']         = $util->sgr("Pendente");
    
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
    $db->setTabela("contarec_hist", "crhi_idcontarec");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir o historico da conta<br>Operação cancelada!");
        exit;
    }
    //
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

?>
