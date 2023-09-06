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
    $sql = "SELECT FORMAT(cole_qte, 2, 'de_DE') AS qte_coleta, 
                DATE_FORMAT(cole_data_entrada, '%d/%m/%Y') AS dataEntrada,
                prod_nome,
                cole_placa_veiculo,
                cole_qte
            FROM coleta 
                JOIN produtos ON (cole_idprodutos = idprodutos)
            WHERE cole_data_entrada >= '{$_REQUEST['data_inicio']}'
            AND cole_data_entrada <= '{$_REQUEST['data_fim']}'";
    //
    if(!empty($_REQUEST['idprodutos'])){
        $sql .= " AND cole_idprodutos = {$_REQUEST['idprodutos']}";
    }
    //
    $sql .= " ORDER BY cole_data_entrada";
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($colunas);
    $colunas['prod_nome'] = "";
    $colunas['dataEntrada'] = "width='15%'";
    $colunas['cole_placa_veiculo'] = "width='20%'";
    $colunas['qte_coleta'] = "width='15%' align='right'";
    //
    $cabecalho['Produto'] = '';
    $cabecalho['Data'] = '';
    $cabecalho['Placo Veiculo'] = '';
    $cabecalho['Quantidade'] = '';
    //
    if($_REQUEST['data_inicio'] != '' && $_REQUEST['data_fim'] != ''){
        $titulo = "Coletas no periodo de <br>" . $util->convertData($_REQUEST['data_inicio']) . " a " . $util->convertData($_REQUEST['data_fim']);
    }else{
        $titulo = "Relatório de Coletas";
    }
    //
    $tabelaResultado = $tabelas->geraTabelaPadrao($res, $db, $colunas, $cabecalho, "cole_qte", $util);
    //
    //Define se terá assinatura e qual será
    //
    $assinatura = '';
    //float-right
    if($parametros->buscaValor("sistema: relatorio coleta exibe assinatura") == 'SIM'){
        $assinatura .= '<div class="row">';
            $assinatura .= '<div class="col-md-8 col-sm-8 col-8 mt-2 p-2"></div>';
            $assinatura .= '<div class="col-md-4 col-sm-4 col-4 mt-2 p-2 text-center text-dark">';
                $imgAssinatura = $parametros->buscaValor("sistema: relatorio coleta assinatura digital");
                if(!empty($imgAssinatura)){
                    $assinatura .= '<img src="../imagens/' . $imgAssinatura . '" width="150px">';
                }else{
                    $assinatura .= '__________________________________';
                }
                $assinatura .= '<br>';
                //
                $textoAssinatura = $parametros->buscaValor("sistema: relatorio coleta texto para assinatura");
                if(empty($textoAssinatura)) $textoAssinatura = "Assinatura do Responsavel";
                //
                $assinatura .= '<b>' . $textoAssinatura . '</b>';
            $assinatura .= '</div>';
        $assinatura .= '</div>';
    }
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
