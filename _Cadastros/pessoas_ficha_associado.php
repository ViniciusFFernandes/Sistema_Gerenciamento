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
        $html->mostraErro("Pessoa não encontrada!<br>Código não infomado!", '', true);
        exit;
    }
    //
    //Valida se é um associado 
    if($reg['pess_associado'] != 'SIM'){
        $html->mostraErro("Esta pessoa não é um associad!<br>Impressão de ficha não permitida!", '', true);
        exit;
    }
    //
    if($reg['idpessoas'] > 0){
        //
        if(empty($reg['func_nome'])) $reg['func_nome'] = "Não Informado";
        if(empty($reg['set_nome'])) $reg['set_nome'] = "Não Informado";
        //
        if(!empty($reg['pess_idempresas'])){
            $idempresa = $reg['pess_idempresas'];
        }else{
            $idempresa = CODIGO_EMPRESA;
        }
        //
        $sqlEmpresas = "SELECT *
                        FROM empresas 
                            LEFT JOIN cidades ON (idcidades = emp_idcidades) 
                            LEFT JOIN estados ON (cid_idestados = idestados)
                        WHERE idempresas = " . $idempresa;
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
    $html = str_replace("##tamanhoLogo##", $tamanhoLogo, $html); 
    echo $html;
    exit;
?>