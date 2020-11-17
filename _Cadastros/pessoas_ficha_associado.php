<?php
    require_once("../_BD/conecta_login.php");
    //
    //Operações do banco de dados
    if(!empty($_REQUEST['id_cadastro'])){
        $sql = "SELECT *
                FROM pessoas 
                    LEFT JOIN setores ON (idsetores = pess_idsetores)
                    LEFT JOIN funcoes ON (idfuncoes = pess_idfuncoes)
                    LEFT JOIN cidades ON (idcidades = pess_idcidades)
                    LEFT JOIN estados ON (idestados = cid_idestados)
                WHERE idpessoas = {$_REQUEST['id_cadastro']}";
        $reg = $db->retornaUmReg($sql);
    }else{
        $html->mostraErro("Pessoa não encontrada!<br>Código não infomado!");
        exit;
    }
    if($reg['idpessoas'] > 0){
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
    $html = str_replace("##pess_nome##", $reg['pess_nome'], $html);
    $html = str_replace("##pess_endereco##", $reg['pess_endereco'], $html);
    $html = str_replace("##pess_cidade##", $reg['cid_nome'], $html);
    $html = str_replace("##pess_uf##", $reg['est_uf'], $html);
    $html = str_replace("##pess_cep##", $reg['pess_cep'], $html);
    $html = str_replace("##pess_setor##", $reg['set_nome'], $html);
    $html = str_replace("##pess_funcao##", $reg['func_nome'], $html);
    $html = str_replace("##pess_cpf##", $reg['pess_cpf'], $html);
    $html = str_replace("##pess_rg##", $reg['pess_rg'], $html);
    echo $html;
    exit;
?>