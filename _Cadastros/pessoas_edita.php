<?php
  include_once("../_BD/conecta_login.php");
  include_once("../Class/autoComplete.class.php");
  include_once("../Class/html.class.php");
  //
  //Inicia classes nescessarias
  $html = new html($db, $util);
  //
  //Operações do banco de dados
  if(!empty($_REQUEST['id_cadastro'])){
    $sql = "SELECT * 
            FROM pessoas 
              LEFT JOIN cidades ON (pess_idcidades = idcidades) 
              LEFT JOIN estados ON (cid_idestados = idestados) 
            WHERE idpessoas = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
  }
  //
  //Gera o autoComplete 
  $autoComplete = new autoComplete();
  $codigo_js = $autoComplete->gerar("pess_cidades", "pess_idcidades", "cidades JOIN estados ON (cid_idestados = idestados)", "CONCAT(cid_nome, ' - ', est_uf)", "idcidades", "", "WHERE UPPER(cid_nome) LIKE UPPER('##valor##%')");
 //echo $codigo_js;exit;
  //
  //Monta variaveis de exibição
  $escondeDivTelefone = "style='display: none;'";
  //
  if(!empty($reg['idpessoas'])){ 
    $editaLogin = '<span align="right" data-toggle="modal" title="Cria/Edita login" data-target="#criaEditaLogin" style="cursor: pointer;">';
    $editaLogin .=  '&nbsp;<img src="../icones/cadeado.png">';
    $editaLogin .= '</span>';
    //
    $btnExcluir = '<button type="button" onclick="excluiCadastro()" class="btn btn-danger">Excluir</button>';
    //
    $checkCliente = $util->defineChecked($reg['pess_cliente']);
    $checkFornecedor = $util->defineChecked($reg['pess_fornecedor']);
    $checkFuncionario = $util->defineChecked($reg['pess_funcionario']);
    //
    if(!empty($reg['cid_nome'])){
      $cidade = $reg['cid_nome'] . " - " . $reg['est_uf'];
    }
    //
    $imgCarregandoTelefone = '<center><img src="../icones/carregando.gif" width="25px"></center>';
    $escondeDivTelefone = "";
    //
    $sql = "SELECT * FROM grupos_acessos WHERE IFNULL(grac_inativo, 0) <> 1";
    $comboGruposAcessos = $html->criaSelectSql("grac_nome", "idgrupos_acessos", "pess_idgrupos_acessos", $reg['pess_idgrupos_acessos'], $sql, "form-control");
  }
  //
  if (isset($_SESSION['mensagem'])) {
    $msg = $util->mostraMensagem($_SESSION['tipoMsg'], $_SESSION['mensagem']);
    unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
  }
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $util->buscaHtml("cadastros", $parametros);
  $html = str_replace("##Mensagem##", $msg, $html);
  $html = str_replace("##autoComplete_Cidades##", $codigo_js, $html);
  $html = str_replace("##grupos_acessos##", $comboGruposAcessos, $html);
  $html = str_replace("##caregando_telefone##", $imgCarregandoTelefone, $html);
  $html = str_replace("##esconde_div_telefone##", $escondeDivTelefone, $html);
  $html = str_replace("##EditaLogin##", $editaLogin, $html);
  $html = str_replace("##CheckCliente##", $checkCliente, $html);
  $html = str_replace("##CheckFornecedor##", $checkFornecedor, $html);
  $html = str_replace("##CheckFuncionario##", $checkFuncionario, $html);
  $html = str_replace("##id_cadastro##", $reg['idpessoas'], $html);
  $html = str_replace("##pess_nome##", $reg['pess_nome'], $html);
  $html = str_replace("##pess_cpf##", $reg['pess_cpf'], $html);
  $html = str_replace("##pess_cnpj##", $reg['pess_cnpj'], $html);
  $html = str_replace("##pess_rg##", $reg['pess_rg'], $html);
  $html = str_replace("##pess_endereco##", $reg['pess_endereco'], $html);
  $html = str_replace("##pess_endereco_numero##", $reg['pess_endereco_numero'], $html);
  $html = str_replace("##pess_cidades##", $cidade, $html);
  $html = str_replace("##pess_idcidades##", $reg['idcidades'], $html);
  $html = str_replace("##pess_bairro##", $reg['pess_bairro'], $html);
  $html = str_replace("##pess_cep##", $reg['pess_cep'], $html);
  $html = str_replace("##pess_usuario##", $reg['pess_usuario'], $html);
  $html = str_replace("##btnExcluir##", $btnExcluir, $html);
  echo $html;
  exit;
?>