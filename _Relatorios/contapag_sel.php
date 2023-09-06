<?php
    require_once("../_BD/conecta_login.php");
    require_once("tabelas.class.php");
    require_once("autoComplete.class.php");
    //
    //Gera o autoComplete 
    $autoComplete = new autoComplete();
    //
    $codigo_js = $autoComplete->gerar("pessoas", "idpessoas", "pessoas LEFT JOIN cidades ON (pess_idcidades = idcidades) LEFT JOIN estados ON (cid_idestados = idestados)", "CONCAT(pess_nome, ', ', cid_nome, ' - ', est_uf)", "idpessoas", "", "WHERE UPPER(pess_nome) LIKE UPPER('##valor##%')");
    $codigo_campo = $autoComplete->criaCampos("pessoas", "idpessoas", "Filtrar Pessoa");
    //
    $sql = "SELECT * FROM tipo_contas";
    $comboBoxTipoConta = $html->criaSelectSql("tico_nome", "idtipo_contas", "idtipo_contas", "", $sql, "form-control", "", true, "Selecione o Tipo da Conta", false);
    //
    if (isset($_SESSION['mensagem'])) {
        $msg = $html->mostraMensagem($_SESSION['tipoMsg'], $_SESSION['mensagem']);
        unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
    }
    //
    //
    //Monta variaveis de exibição
    $sql = "SELECT * FROM empresas";
    $comboBoxEmpresas = $html->criaSelectSql("emp_nome", "idempresas", "idempresas", '', $sql, "form-control", "", true, "Selecione a Empresa", false);
    //
    //Abre o arquivo html e Inclui mensagens e trechos php
    $html = $html->buscaHtml(true);
    $html = str_replace("##Mensagem##", $msg, $html);
    $html = str_replace("##autoComplete_Pessoas##", $codigo_js, $html);
    $html = str_replace("##autoComplete_CampoPessoas##", $codigo_campo, $html);
    $html = str_replace("##comboBoxEmpresas##", $comboBoxEmpresas, $html);
    $html = str_replace("##comboBoxTipoConta##", $comboBoxTipoConta, $html);
    $html = str_replace("##pessoas##", "", $html);
    $html = str_replace("##idpessoas##", "", $html);  
    echo $html;
    exit;
?>