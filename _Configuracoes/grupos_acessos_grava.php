<?php
  include_once("../_BD/conecta_login.php");
  include_once("../Class/Tabelas.class.php");
  //
  $paginaRetorno = 'grupos_acessos.php';
  //
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * 
			FROM grupos_acessos";
			
    if ($_POST['pesquisa'] != "") {
        $sql .= " WHERE idgrupos_acessos LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR grac_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idgrupos_acessos'] = "width='6%'";
    $dados['grac_nome'] = "";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno);
    exit;
  }

  if ($_POST['operacao'] == "ativarDesativarProgram") {
    $db->setTabela("grupos_acessos_programas", "idgrupos_acessos_programas");
    //
    unset($dados);
    $dados['id']            = $_POST['idgrupos_acessos_programas'];
  	$dados['gap_executa']   = $util->igr($_POST['gap_executa']);
    $db->gravarInserir($dados, true);
    //
    unset($dados);
    if($db->erro()){
      $dados['retorno'] = 'erro';
      $dados['msg'] = $db->getErro();
    }else{
      $dados['retorno'] = 'ok';
    }
    echo json_encode($dados);
     exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Configuracoes/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("grupos_acessos", "idgrupos_acessos");
    //
    unset($dados);
    $dados['id']              = $_POST['id_cadastro'];
  	$dados['grac_nome'] 			= $util->sgr($_POST['grac_nome']);
    $db->gravarInserir($dados, true);
    //
  	if ($_POST['id_cadastro'] > 0) {
  		$id = $_POST['id_cadastro'];
    }else{
      $id = $db->getUltimoID();
      //
      //Insere a permissão dos programas
      inserePermissoes($id);
  }
    header('location: ../_Configuracoes/' . $paginaRetorno . '?id_cadastro=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("grupos_acessos", "idgrupos_acessos");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $util->mostraErro("Erro ao excluir grupo<br>Operação cancelada!");
        exit;
    }
    header('location:../_Configuracoes/' . $paginaRetorno);
    exit;
  }


  function inserePermissoes($idGruposAcessos){
    global $db;
    
    $sql = "INSERT INTO grupos_acessos_programas (gap_idgrupos_acessos, gap_idprogramas, gap_executa)
              SELECT {$idGruposAcessos}, idprogramas, 0 FROM programas";
              echo $sql;
    $db->executaSQL($sql);
  }
?>