<?php
    require_once("../_BD/conecta_login.php");
    require_once("../Class/tabelas.class.php");
    //
    //
    $sql = "SELECT *
            FROM pedcompras 
                JOIN pessoas ON (pcom_idfornecedor = idpessoas)
                LEFT JOIN cidades ON (pess_idcidades = idcidades)
                LEFT JOIN estados ON (cid_idestados = idestados)
            WHERE idpedcompras = {$_REQUEST['id_cadastro']}";
    //
    $regPedido = $db->retornaUmReg($sql);
    //
    if(!empty($regPedido['pcom_idempresas'])){
        $idempresa = $regPedido['pcom_idempresas'];
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
    $tabelas = new Tabelas();
    //
    unset($valores);
    $valores[0]["item"] = "Total dos Produtos";
    $valores[0]["valor"] = $util->formataMoeda($regPedido['pcom_total_produtos']);
    $valores[1]["item"] = "Frete";
    $valores[1]["valor"] = $util->formataMoeda($regPedido['pcom_frete']);
    if($regPedido['pcom_valor_desconto'] > 0){
        $valores[2]["item"] = "Desconto no Pedido";
        $valores[2]["valor"] = $util->formataMoeda($regPedido['pcom_valor_desconto'] * -1);
    }
    //
    unset($colunas);
    $colunas['item'] = "";
    $colunas['valor'] = "width='15%' align='right'";
    //
    unset($cabecalho);
    $cabecalho[' '] = '';
    $cabecalho['Valor'] = "align='right'";
    //
    $tabelaValores = $tabelas->geraTabelaPadrao($valores, $db, $colunas, $cabecalho, "valor", $util, "table-primary", "Totais");
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
    $sql = "SELECT CONCAT(FORMAT(pcit_qte, 2, 'de_DE'), pcit_unidade_sigla) AS qte_sigla, 
                FORMAT(pcit_vlr_unitario, 2, 'de_DE') AS vlr_unitario, 
                FORMAT(pcit_valor_desconto, 2, 'de_DE') AS vlr_desconto, 
                FORMAT((pcit_qte * pcit_vlr_unitario), 2, 'de_DE') AS vlr_sem_desconto, 
                FORMAT(pcit_total_item, 2, 'de_DE') AS vlr_total, 
                prod_nome
            FROM pedcompras_itens
                LEFT JOIN produtos ON (pcit_idprodutos = idprodutos)
            WHERE pcit_idpedcompras = {$_REQUEST['id_cadastro']}
            ORDER BY prod_nome";
    //
    $res = $db->consultar($sql);
    //
    unset($colunas);
    $colunas['prod_nome'] = "";
    $colunas['qte_sigla'] = "width='12%' align='right'";
    $colunas['vlr_unitario'] = "width='12%' align='right'";
    $colunas['vlr_sem_desconto'] = "width='12%' align='right'";
    $colunas['vlr_desconto'] = "width='12%' align='right'";
    $colunas['vlr_total'] = "width='12%' align='right'";
    //
    unset($cabecalho);
    $cabecalho['Produto'] = '';
    $cabecalho['Quantidade'] = "align='right'";
    $cabecalho['Unitário'] = "align='right'";
    $cabecalho['Valor'] = "align='right'";
    $cabecalho['Desconto'] = "align='right'";
    $cabecalho['Total'] = "align='right'";
    //
    $tabelaProdutos .= $tabelas->geraTabelaPadrao($res, $db, $colunas, $cabecalho, "vlr_total", $util, "table-primary", "Lista de Produtos");
    //
    //
    $sql = "SELECT DATE_FORMAT(pccon_vencimento, '%d/%m/%Y') AS vencto,
            FORMAT(pccon_valor, 2, 'de_DE') AS valor, 
            pccon_parcela
            FROM pedcompras_contas
            WHERE pccon_idpedcompras = {$_REQUEST['id_cadastro']}";
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($colunas);
    $colunas['vencto'] = "width='15%'";
    $colunas['pccon_parcela'] = "align='right'";
    $colunas['valor'] = "width='15%' align='right'";
    //
    unset($cabecalho);
    $cabecalho['Vencimento'] = '';
    $cabecalho['Parcela'] = "align='right'";
    $cabecalho['Valor'] = "align='right'";
    //
    $tabelaContas = $tabelas->geraTabelaPadrao($res, $db, $colunas, $cabecalho, "valor", $util, "table-primary", "Parcelamento");
    //
    //
    //Abre o arquivo html e Inclui mensagens e trechos php
    $html = $html->buscaHtml(false);
    $html = str_replace("##logoRelatorios##", $logoRelatorios, $html);
    $html = str_replace("##nomeEmpresa##", $nomeEmpresa, $html);
    $html = str_replace("##cnpjEmpresa##", $cnpjEmpresa, $html);
    $html = str_replace("##enderecoEmpresa##", $enderecoEmpresa, $html);
    $html = str_replace("##cidadeEmpresa##", $cidadeEmpresa, $html);
    $html = str_replace("##ufEmpresa##", $ufEmpresa, $html);
    $html = str_replace("##cepEmpresa##", $cepEmpresa, $html);
    $html = str_replace("##telefoneEmpresa##", $telefoneEmpresa, $html);
    $html = str_replace("##tamanhoLogo##", $tamanhoLogo, $html); 
    $html = str_replace("##idpedcompras##", $regPedido["idpedcompras"], $html); 
    $html = str_replace("##clienteNome##", $regPedido["pess_nome"], $html); 
    $html = str_replace("##clienteCPF##", $regPedido["pess_cpf"], $html); 
    $html = str_replace("##clienteCNPJ##", $regPedido["pess_cnpj"], $html); 
    $html = str_replace("##clienteEndereco##", $enderecoCliente, $html); 
    $html = str_replace("##mostraCPF##", $mostraCPF, $html); 
    $html = str_replace("##mostraCNPJ##", $mostraCNPJ, $html); 
    $html = str_replace("##tabelaProdutos##", $tabelaProdutos, $html); 
    $html = str_replace("##tabelaValores##", $tabelaValores, $html); 
    $html = str_replace("##tabelaContas##", $tabelaContas, $html);
    echo $html;
    exit;
?>
