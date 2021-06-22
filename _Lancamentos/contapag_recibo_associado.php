<?php
    require_once("../_BD/conecta_login.php");
    //
    //Operações do banco de dados
    if(!empty($_REQUEST['id_cadastro'])){
        $sql = "SELECT *
                FROM salarios_funcionarios 
                    JOIN salarios ON (safu_idsalarios = idsalarios)
                    JOIN contapag ON (safu_idcontapag = idcontapag)
                    LEFT JOIN tipo_contas ON (ctpg_idtipo_contas = idtipo_contas)
                    JOIN pessoas ON (ctpg_idcliente = idpessoas) 
                    LEFT JOIN setores ON (idsetores = pess_idsetores)
                    LEFT JOIN funcoes ON (idfuncoes = pess_idfuncoes)
                    LEFT JOIN cidades ON (idcidades = pess_idcidades)
                    LEFT JOIN estados ON (idestados = cid_idestados)
                WHERE idsalarios_funcionarios = {$_REQUEST['id_cadastro']}";
        $reg = $db->retornaUmReg($sql);
    }else{
        $html->mostraErro("Salario não encontrada!<br>Código não infomado!");
        exit;
    }
    if($reg['idcontapag'] <= 0){
        $html->mostraErro("Conta não encontrada!");
        exit;
    }
    if($reg['tico_tipo_salario'] != 'SIM'){
        $html->mostraErro("Está conta não é do tipo salario!");
        exit;
    }
    if($reg['idpessoas'] <= 0){
        $html->mostraErro("Pessoa não encontrada!");
        exit;
    }
    //
    if(empty($reg['func_nome'])) $reg['func_nome'] = "Não Informado";
    if(empty($reg['set_nome'])) $reg['set_nome'] = "Não Informado";
    //
    $logoRelatorios = $parametros->buscaValor("sistema: nome da logo usada para relatorios");
    //
    $nomeEmpresa = $parametros->buscaValor("empresa: nome da empresa");
    $cnpjEmpresa = $parametros->buscaValor("empresa: cnpj da empresa");
    $enderecoEmpresa = $parametros->buscaValor("empresa: endereco da empresa");
    $cidadeEmpresa = $parametros->buscaValor("empresa: cidade da empresa");
    $ufEmpresa = $parametros->buscaValor("empresa: uf da empresa");
    $cepEmpresa = $parametros->buscaValor("empresa: CEP da empresa");
    $telefoneEmpresa = $parametros->buscaValor("empresa: telefone de contato da empresa");
    //
    $msgCesta = '';
    if($reg['safu_dias'] <= 0){
        $msgCesta = ' e uma cesta basica ';
    }
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
