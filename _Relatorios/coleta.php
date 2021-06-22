<?php
    require_once("../_BD/conecta_login.php");
    //
    //
    $sqlEmpresas = "SELECT *
                    FROM empresas 
                    WHERE idempresas = {$_REQUEST['idempresas']}";
    $regEmpresas = $db->retornaUmReg($sqlEmpresas);
    //
    $logoRelatorios = ;
    //
    $nomeEmpresa = ;
    $cnpjEmpresa = ;
    $enderecoEmpresa = ;
    $cidadeEmpresa = ;
    $ufEmpresa = ;
    $cepEmpresa = ;
    $telefoneEmpresa = ;
    //
    $sql = "SELECT *
            FROM coletas 
            WHERE cole_data_entrada >= '{$_REQUEST['data_inicio']}'
            AND cole_data_entrada <= '{$_REQUEST['data_fim']}'";
    //
    if(!empty($_REQUEST['idprodutos'])){
        $sql .= " AND cole_idprodutos = {$_REQUEST['idprodutos']}";
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idempresas'] = "width='6%'";
    $dados['emp_nome'] = "";
    //
    $cabecalho['Código'] = '';
    $cabecalho['Nome'] = '';
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno, $cabecalho);
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
    $html = str_replace("##mesExtenso##", $util->mesExtenso($reg['sala_mes']), $html);
    $html = str_replace("##ano##", $reg['sala_ano'], $html);
    $html = str_replace("##pess_nome##", $reg['pess_nome'], $html);
    $html = str_replace("##vlr_bruto##", $util->formataMoeda($reg['ctpg_vlr_bruto']), $html);
    $html = str_replace("##vlr_juros##", $util->formataMoeda($reg['ctpg_vlr_juros']), $html);
    $html = str_replace("##receita_total##", $util->formataMoeda($reg['ctpg_vlr_bruto'] + $reg['ctpg_vlr_juros']), $html);
    $html = str_replace("##falta_dias##", $reg['safu_dias'], $html);
    $html = str_replace("##desconto_faltas##", $util->formataMoeda($reg['safu_vlr_desconto_faltas']), $html);
    $html = str_replace("##vlr_adiantamento##", $util->formataMoeda($reg['ctpg_vlr_desconto'] - $reg['safu_vlr_desconto_faltas']), $html);
    $html = str_replace("##vlr_desconto##", $util->formataMoeda($reg['ctpg_vlr_desconto']), $html);
    $html = str_replace("##vlr_liquido##", $util->formataMoeda($reg['ctpg_vlr_liquido']), $html);
    $html = str_replace("##vlr_liquito_extenso##", $util->valorPorExtenso($reg['ctpg_vlr_liquido']), $html);
    $html = str_replace("##diaAtual##", date('d'), $html);
    $html = str_replace("##mesAtual##", $util->mesExtenso(date('m')), $html);
    $html = str_replace("##anoAtual##", date('Y'), $html);
    $html = str_replace("##msg_cesta_basica##", $msgCesta, $html);
    echo $html;
    exit;
?>
