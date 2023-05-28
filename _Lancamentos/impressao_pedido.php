<?php
    require_once("../_BD/conecta_login.php");
    require_once("../Class/tabelas.class.php");
    //
    //
    if(!empty($_REQUEST['idempresas'])){
        $idempresa = $_REQUEST['idempresas'];
    }else{
        $idempresa = CODIGO_EMPRESA;
    }
    //
    $sqlEmpresas = "SELECT *
                    FROM empresas 
                        LEFT JOIN cidades ON (idcidades = emp_idcidades) 
                        LEFT JOIN estados ON (cid_idestados = idestados)
                    WHERE idempresas = {$idempresa}";
    $regEmpresas = $db->retornaUmReg($sqlEmpresas);
    //
    $logoRelatorios = $regEmpresas['emp_logo'];
    //
    $nomeEmpresa = $regEmpresas['emp_nome'];
    $cnpjEmpresa = $regEmpresas['emp_cnpj'];
    $enderecoEmpresa = $regEmpresas['emp_endereco'];
    $cidadeEmpresa = $regEmpresas['cid_nome'];
    $ufEmpresa = $regEmpresas['est_uf'];
    $cepEmpresa = $regEmpresas['emp_cep'];
    $telefoneEmpresa = $regEmpresas['emp_telefone'];
    //
    if($regEmpresas['emp_logo_relatorio'] == 'SIM'){
        $tamanhoLogo = "150px;";
        $nomeEmpresa = "";
    }else{
        $tamanhoLogo = "85px;";
    }
    //
    $sql = "SELECT *
            FROM pedidos 
                JOIN pessoas ON (ped_idcliente = idpessoas)
                LEFT JOIN cidades ON (pess_idcidades = idcidades)
                LEFT JOIN estados ON (cid_idestados = idestados)
            WHERE idpedidos = {$_REQUEST['id_cadastro']}";
    //
    $regPedido = $db->retornaUmReg($sql);
    //
    if($regPedido['pess_cpf'] == ""){
       $mostraCPF = "d-none"; 
    }
    if($regPedido['pess_cnpj'] == ""){
        $mostraCNPJ = "d-none"; 
    }
    //
    $enderecoCliente = $regPedido['pess_endereco'] . ", " . $regPedido['pess_endereco_numero'] . ", " . $regPedido['cid_nome'] . " - " . $regPedido['est_uf'] . ", " . $regPedido["pess_cep"];
    //
    //
    $sql = "SELECT CONCAT(FORMAT(peit_qte, 2, 'de_DE'), peit_unidade_sigla) AS qte_sigla, 
                FORMAT(peit_vlr_unitario, 2, 'de_DE') AS vlr_unitario, 
                FORMAT(peit_valor_desconto, 2, 'de_DE') AS vlr_desconto, 
                FORMAT(peit_total_item, 2, 'de_DE') AS vlr_total, 
                prod_nome
            FROM pedidos_itens
                LEFT JOIN produtos ON (peit_idprodutos = idprodutos)
            WHERE peit_idpedidos = {$_REQUEST['id_cadastro']}
            ORDER BY prod_nome";
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($colunas);
    $colunas['prod_nome'] = "";
    $colunas['qte_sigla'] = "width='15%' align='right'";
    $colunas['vlr_unitario'] = "width='15%' align='right'";
    $colunas['vlr_desconto'] = "width='15%' align='right'";
    $colunas['vlr_total'] = "width='15%' align='right'";
    //
    unset($cabecalho);
    $cabecalho['Produto'] = '';
    $cabecalho['Quantidade'] = "align='right'";
    $cabecalho['Valor Unitario'] = "align='right'";
    $cabecalho['Valor Desconto'] = "align='right'";
    $cabecalho['Valor Total'] = "align='right'";
    //
    $tabelaProdutos = $tabelas->geraTabelaPadrao($res, $db, $colunas, $cabecalho, "vlr_total", $util);
    //
    //
    $sql = "SELECT DATE_FORMAT(STR_TO_DATE(pcon_vencimento, '%Y-%m-%d'), '%d/%m/%Y') as vencto,
            FORMAT(pcon_valor, 2, 'de_DE') AS valor, 
            pcon_parcela
            FROM pedidos_contas
            WHERE pcon_idpedidos = {$_REQUEST['id_cadastro']}";
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($colunas);
    $colunas['vencto'] = "width='15%'";
    $colunas['pcon_parcela'] = "align='right'";
    $colunas['valor'] = "width='15%' align='right'";
    //
    unset($cabecalho);
    $cabecalho['Vencimento'] = '';
    $cabecalho['Parcela'] = "align='right'";
    $cabecalho['Valor'] = "align='right'";
    //
    $tabelaContas = $tabelas->geraTabelaPadrao($res, $db, $colunas, $cabecalho, "valor", $util);
    //
    //
    //Abre o arquivo html e Inclui mensagens e trechos php
    $html = $html->buscaHtml("", $parametros);
    $html = str_replace("##logoRelatorios##", $logoRelatorios, $html);
    $html = str_replace("##nomeEmpresa##", $nomeEmpresa, $html);
    $html = str_replace("##cnpjEmpresa##", $cnpjEmpresa, $html);
    $html = str_replace("##enderecoEmpresa##", $enderecoEmpresa, $html);
    $html = str_replace("##cidadeEmpresa##", $cidadeEmpresa, $html);
    $html = str_replace("##ufEmpresa##", $ufEmpresa, $html);
    $html = str_replace("##cepEmpresa##", $cepEmpresa, $html);
    $html = str_replace("##telefoneEmpresa##", $telefoneEmpresa, $html);
    $html = str_replace("##tamanhoLogo##", $tamanhoLogo, $html); 
    $html = str_replace("##idpedidos##", $regPedido["idpedidos"], $html); 
    $html = str_replace("##clienteNome##", $regPedido["pess_nome"], $html); 
    $html = str_replace("##clienteCPF##", $regPedido["pess_cpf"], $html); 
    $html = str_replace("##clienteCNPJ##", $regPedido["pess_cnpj"], $html); 
    $html = str_replace("##clienteEndereco##", $enderecoCliente, $html); 
    $html = str_replace("##mostraCPF##", $mostraCPF, $html); 
    $html = str_replace("##mostraCNPJ##", $mostraCNPJ, $html); 
    $html = str_replace("##tabelaProdutos##", $tabelaProdutos, $html); 
    $html = str_replace("##tabelaContas##", $tabelaContas, $html);
    echo $html;
    exit;
?>
