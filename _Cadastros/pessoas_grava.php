<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'pessoas_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * 
            FROM pessoas 
              LEFT JOIN cidades ON (pess_idcidades = idcidades) 
              LEFT JOIN estados ON (cid_idestados = idestados)";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idpessoas LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                OR pess_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                OR pess_idcidades LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    $tabelas->geraTabelaPes($res, $db);
    exit;
  }

  if ($_POST['operacao'] == "excluiTelefones") {
    $db->setTabela("pessoas_numeros", "idpessoas_numeros");
    $db->excluir($_POST['idtelefone']);
    exit;
  }

  if ($_POST['operacao'] == "gravaTelefones") {
    $db->setTabela("pessoas_numeros", "idpessoas_numeros");
    $dados['pnum_idpessoas']        = $util->sgr($_POST['idpessoas']);
    $dados['pnum_DDD']              = $util->sgr($_POST['pnum_DDD']);
    $dados['pnum_numero']           = $util->sgr($_POST['pnum_numero']);
    $db->gravarInserir($dados);
    exit;
  }

  if ($_POST['operacao'] == "buscaTelefones") {
    $sql = "SELECT * FROM pessoas_numeros WHERE pnum_idpessoas = " . $_POST['idpessoas'];
    $res = $db->consultar($sql);
    //
    $tabelas = new Tabelas();
    $tabelas->geraTabelaTel($res);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'validaUsuario'){
    $db->setTabela("pessoas");
    $sql = "SELECT * FROM pessoas WHERE pess_usuario = " . $util->sgr($_POST['pess_usuario']) . " AND idpessoas <> " . $_POST['idpessoas'];
    $reg = $db->retornaUmReg($sql);
    if($reg['idpessoas'] > 0){
      $dados['existe'] = "true";
    }else{
      $dados['existe'] = "false"; 
    }
    //
    echo json_encode($dados);
    exit;
  }

  if ($_POST['operacao'] == 'validaSenha'){
    $db->setTabela("pessoas");
    $sql = "SELECT * FROM pessoas WHERE idpessoas = " . $_POST['idpessoas'];
    $reg = $db->retornaUmReg($sql);
    if($reg['pess_senha'] != ''){
      $dados['existe'] = "true";
    }else{
      $dados['existe'] = "false"; 
    }
    //
    echo json_encode($dados);
    exit;
  }

  if ($_POST['operacao'] == 'gravaLogin'){
    $db->setTabela("pessoas", "idpessoas");

    $dados['id']                      = $_POST['idpessoas'];
    $dados['pess_usuario']            = $util->sgr($_POST['pess_usuario']);
    $dados['pess_idgrupos_acessos']   = $util->sgr($_POST['pess_idgrupos_acessos']);
    if(!empty($_POST['pess_senha'])){
      $dados['pess_senha']            = $util->sgr($_POST['pess_senha']);
    }

    $db->gravarInserir($dados);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("pessoas", "idpessoas");

    $dados['id']                     = $_POST['id_cadastro'];
  	$dados['pess_nome'] 			       = $util->sgr($_POST['pess_nome']);
  	$dados['pess_rg'] 				       = $util->sgr($_POST['pess_rg']);
  	$dados['pess_cpf'] 				       = $util->sgr($_POST['pess_cpf']);
  	$dados['pess_cnpj'] 			       = $util->sgr($_POST['pess_cnpj']);
  	$dados['pess_cep'] 				       = $util->sgr($_POST['pess_cep']);
  	$dados['pess_endereco'] 		     = $util->sgr($_POST['pess_endereco']);
  	$dados['pess_endereco_numero'] 	 = $util->sgr($_POST['pess_endereco_numero']);
    $dados['pess_idcidades']         = $util->igr($_POST['pess_idcidades']);
  	$dados['pess_bairro'] 			     = $util->sgr($_POST['pess_bairro']);
  	$dados['pess_cliente'] 			     = $util->sgr($_POST['pess_cliente']);
  	$dados['pess_fornecedor'] 		   = $util->sgr($_POST['pess_fornecedor']);
  	$dados['pess_funcionario'] 		   = $util->sgr($_POST['pess_funcionario']);

    $db->gravarInserir($dados, true);

  	if ($_POST['id_cadastro'] > 0) {
      $id = $_POST['id_cadastro'];
    }else{
  		$id = $db->getUltimoID();
    }
    header('location:../_Cadastros/' . $paginaRetorno . '?id_cadastro=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("pessoas", "idpessoas");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $util->mostraErro("Erro ao excluir pessoa<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
