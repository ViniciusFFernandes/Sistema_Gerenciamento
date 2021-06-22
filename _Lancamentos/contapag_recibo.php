<?php
    require_once("../_BD/conecta_login.php");
    //
    //Operações do banco de dados
    if(!empty($_REQUEST['id_cadastro'])){
        $sql = "SELECT *
                FROM contapag 
                    LEFT JOIN tipo_contas ON (ctpg_idtipo_contas = idtipo_contas)
                    JOIN pessoas ON (ctpg_idcliente = idpessoas) 
                WHERE idcontapag = {$_REQUEST['id_cadastro']}";
        $reg = $db->retornaUmReg($sql);
    }else{
        $html->mostraErro("Conta não encontrada!<br>Código não infomado!");
        exit;
    }
    if($reg['idcontapag'] <= 0){
        $html->mostraErro("Conta não encontrada!");
        exit;
    }
    if($reg['ctpg_situacao'] != 'Quitada' && $reg['ctpg_situacao'] != 'QParcial'){
        $html->mostraErro("Está conta não está paga!");
        exit;
    }
    if($reg['idpessoas'] <= 0){
        $html->mostraErro("Pessoa não encontrada!");
        exit;
    }
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
    $html = str_replace("##pess_nome##", $reg['pess_nome'], $html);
    $html = str_replace("##idconta##", $reg['idcontapag'], $html);
    $html = str_replace("##vlr_pago##", $util->formataMoeda($reg['ctpg_vlr_pago']), $html);
    $html = str_replace("##vlr_pago_extenso##", $util->valorPorExtenso($reg['ctpg_vlr_pago']), $html);
    $html = str_replace("##diaAtual##", date('d'), $html);
    $html = str_replace("##mesAtual##", $util->mesExtenso(date('m')), $html);
    $html = str_replace("##anoAtual##", date('Y'), $html);
    echo $html;
    exit;
?>
