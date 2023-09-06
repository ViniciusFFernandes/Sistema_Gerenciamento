<?php
    require_once("../_BD/conecta_login.php");
    //
    //Operações do banco de dados
    if(!empty($_REQUEST['id_cadastro'])){
        $sql = "SELECT *
                FROM contarec
                    LEFT JOIN tipo_contas ON (ctrc_idtipo_contas = idtipo_contas)
                    JOIN pessoas ON (ctrc_idcliente = idpessoas) 
                WHERE idcontarec = {$_REQUEST['id_cadastro']}";
        $reg = $db->retornaUmReg($sql);
    }else{
        $html->mostraErro("Conta não encontrada!<br>Código não infomado!");
        exit;
    }
    if($reg['idcontarec'] <= 0){
        $html->mostraErro("Conta não encontrada!");
        exit;
    }
    if($reg['ctrc_situacao'] != 'Quitada' && $reg['ctrc_situacao'] != 'QParcial'){
        $html->mostraErro("Está conta não está paga!");
        exit;
    }
    if($reg['idpessoas'] <= 0){
        $html->mostraErro("Pessoa não encontrada!");
        exit;
    }
    //
    $sqlEmpresas = "SELECT *
                    FROM empresas 
                        LEFT JOIN cidades ON (idcidades = emp_idcidades) 
                        LEFT JOIN estados ON (cid_idestados = idestados)
                    WHERE idempresas = {$reg['ctrc_idempresas']}";
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
    $html = str_replace("##nomeOperador##", $nomeOperador, $html);
    $html = str_replace("##idconta##", $reg['idcontarec'], $html);
    $html = str_replace("##vlr_pago##", $util->formataMoeda($reg['ctrc_vlr_pago']), $html);
    $html = str_replace("##vlr_pago_extenso##", $util->valorPorExtenso($reg['ctrc_vlr_pago']), $html);
    $html = str_replace("##diaAtual##", date('d'), $html);
    $html = str_replace("##mesAtual##", $util->mesExtenso(date('m')), $html);
    $html = str_replace("##anoAtual##", date('Y'), $html);
    $html = str_replace("##tamanhoLogo##", $tamanhoLogo, $html); 
    echo $html;
    exit;
?>
