<?php
    require_once("../_BD/conecta_login.php");
    //
    //Operações do banco de dados
    if(!empty($_REQUEST['id_cadastro'])){
        $sql = "SELECT *
                FROM pessoas 
                WHERE idpessoas = {$_REQUEST['id_cadastro']}";
        $reg = $db->retornaUmReg($sql);
    }else{
        $html->mostraErro("Pessoa não encontrada!<br>Código não infomado!", '', true);
        exit;
    }
    //
    //Valida se é um associado 
    if($reg['pess_associado'] != 'SIM' && $reg['pess_funcionario'] != 'SIM'){
        $html->mostraErro("Esta pessoa não é um funcionario ou um associado!<br>Impressão de recibo não permitida!", '', true);
        exit;
    }
    //
    //Valida a empresa do funcionario
    if($reg['pess_idempresas'] == '' ){
        $html->mostraErro("Este funcionario não está vinculado a uma empresa!<br>Impressão de recibo não permitida!", '', true);
        exit;
    }
    //
    //
    $sqlEmpresas = "SELECT *
                    FROM empresas 
                        LEFT JOIN cidades ON (idcidades = emp_idcidades) 
                        LEFT JOIN estados ON (cid_idestados = idestados)
                    WHERE idempresas = " . $reg['pess_idempresas'];
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
    $mensagemMes = '';
    if($_REQUEST['mesano'] != ''){
        $mesAno = explode("-", $_REQUEST['mesano']);
        $mensagemMes = ' referente ao mes de ' . $util->mesExtenso($mesAno[0]) . ' de ' . $mesAno[1];
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
    $html = str_replace("##pess_nome##", $reg['pess_nome'], $html);
    $html = str_replace("##tamanhoLogo##", $tamanhoLogo, $html); 
    $html = str_replace("##diaAtual##", date('d'), $html);
    $html = str_replace("##mesAtual##", $util->mesExtenso(date('m')), $html);
    $html = str_replace("##anoAtual##", date('Y'), $html);
    $html = str_replace("##mensagemMes##", $mensagemMes, $html);
    echo $html;
    exit;
?>