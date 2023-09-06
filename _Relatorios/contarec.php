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
    //
    $sql = "SELECT pess_nome, 
                ctrc_situacao,
                DATE_FORMAT(ctrc_vencimento, '%d/%m/%Y') AS dataVencto,
                FORMAT(ctrc_vlr_bruto, 2, 'de_DE') AS valorBruto,
                FORMAT(ctrc_vlr_desconto, 2, 'de_DE') AS valorDesconto,
                FORMAT(ctrc_vlr_juros, 2, 'de_DE') AS valorJuros,
                FORMAT(ctrc_vlr_liquido, 2, 'de_DE') AS valorLiquido,
                FORMAT(ctrc_vlr_pago, 2, 'de_DE') AS valorPago,
                FORMAT(ctrc_vlr_devedor, 2, 'de_DE') AS valorDevedor
            FROM contarec 
                JOIN pessoas ON (ctrc_idcliente = idpessoas)
            WHERE ctrc_idempresas = {$idempresa}";
    //
    if(!empty($_REQUEST['data_inicio'])){
        $sql .= " AND ctrc_vencimento >= '{$_REQUEST['data_inicio']}'";
    }
    if(!empty($_REQUEST['data_fim'])){
        $sql .= " AND ctrc_vencimento <= '{$_REQUEST['data_fim']}'";
    }
    //
    if(!empty($_REQUEST['idpessoas'])){
        $sql .= " AND idpessoas = {$_REQUEST['idpessoas']}";
    }
    //
    if($_REQUEST['modelo'] == 'PENDENTE'){
        $sql .= " AND (ctrc_situacao = 'Pendente' OR ctrc_situacao = 'QParcial')";
    }
    if($_REQUEST['modelo'] == 'PAGAS'){
        $sql .= " AND ctrc_situacao = 'Quitada'";
    }
    //
    if(!empty($_REQUEST['idtipo_contas'])){
        $sql .= " AND ctrc_idtipo_contas = {$_REQUEST['idtipo_contas']}";
        //
        $nomeTipoConta = $db->retornaUmCampoID("tico_nome", "tipo_contas", $_REQUEST['idtipo_contas']);
    }
    //
    $sql .= " ORDER BY dataVencto, pess_nome";
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($colunas);
    $colunas['pess_nome'] = "";
    $colunas['dataVencto'] = "width='10%'";
    if($_REQUEST['modelo'] == 'TUDO'){
        $colunas['ctrc_situacao'] = "width='10%'";
    }
    $colunas['valorBruto'] = " width='10%'align='right'";
    $colunas['valorDesconto'] = " width='10%'align='right'";
    $colunas['valorJuros'] = "width='10%' align='right'";
    $colunas['valorLiquido'] = "width='10%' align='right'";
    if($_REQUEST['modelo'] != 'PAGAS'){
        $colunas['valorDevedor'] = "width='10%' align='right'";
    }
    //
    $cabecalho['Nome'] = '';
    $cabecalho['Vencimento'] = '';
    if($_REQUEST['modelo'] == 'TUDO'){
        $cabecalho['Situação'] = '';
    }
    $cabecalho['Valor'] = "align='right'";
    $cabecalho['Descontos'] = "align='right'";
    $cabecalho['Juros'] = "align='right'";
    $cabecalho['Liquido'] = "align='right'";
    if($_REQUEST['modelo'] != 'PAGAS'){
        $cabecalho['Devedor'] = "align='right'";
    }
    //
    $campoTotal = "valorLiquido";
    if($_REQUEST['modelo'] != 'PAGAS'){
        $campoTotal = "valorDevedor";
    }
    //
    if($_REQUEST['data_inicio'] != '' && $_REQUEST['data_fim'] != ''){
        $titulo = "Contas no periodo de <br>" . $util->convertData($_REQUEST['data_inicio']) . " a " . $util->convertData($_REQUEST['data_fim']);
    }else{
        $titulo = "Relatório de Contas";
    }
    if(!empty($_REQUEST['idtipo_contas'])){
        $titulo .= "<br> Tipo da Conta: " . $nomeTipoConta; 
    }
    //
    $tabelaResultado = $tabelas->geraTabelaPadrao($res, $db, $colunas, $cabecalho, $campoTotal, $util);
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
    $html = str_replace("##tabelaResultado##", $tabelaResultado, $html);
    $html = str_replace("##tituloRelatorio##", $titulo, $html);
    $html = str_replace("##assinatura##", $assinatura, $html); 
    $html = str_replace("##tamanhoLogo##", $tamanhoLogo, $html); 
    echo $html;
    exit;
?>
