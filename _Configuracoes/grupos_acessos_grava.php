<?php
  require_once("../_BD/conecta_login.php");
  require_once("tabelas.class.php");
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
    //
    // $db->beginTransaction();
    //
    $db->setTabela("grupos_acessos_programas", "idgrupos_acessos_programas");
    //
    unset($dados);
    $dados['id']            = $_POST['idgrupos_acessos_programas'];
  	$dados['gap_executa']   = $util->igr($_POST['gap_executa']);
    $db->gravarInserir($dados, true);
    //
    unset($dados);
    if($db->erro()){
      // $db->rollBack();
      $dados['retorno'] = 'erro';
      $dados['msg'] = $db->getErro();
    }else{
      $dados['retorno'] = 'ok';
      $sql = "SELECT * FROM grupos_acessos_programas JOIN programas ON (gap_idprogramas = idprogramas) WHERE idgrupos_acessos_programas = " . $_POST['idgrupos_acessos_programas'];
      $reg = $db->retornaUmReg($sql);
      if($reg['prog_tipo'] == 'menu'){
        $ret = $html->criaMenu($reg['gap_idgrupos_acessos'], $reg['prog_tipo_menu']);
        if(!$ret){
          // $db->rollBack();
          $dados['retorno'] = 'erro';
          $dados['msg'] = 'Erro ao recriar menu!<br>Operação cancelada!';
        }
      }
    }
    // $db->commit();
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

if($_POST['operacao'] == 'listarProgramas'){
  //
  $sql = "SELECT * 
            FROM grupos_acessos_programas 
              JOIN programas ON (gap_idprogramas = idprogramas)
            WHERE gap_idgrupos_acessos = " . $_POST['id_cadastro'];
    
    if ($_POST['pesquisa'] != "") {
        $sql .= " AND prog_file LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }

    $sql .= " ORDER BY prog_tipo, prog_tipo_origem, prog_nome";
    //
    $res = $db->consultar($sql);
    //
    if(empty($res)){
      $tabelaProgramas = "Nenhum registro encontrado!";
    }else{
      //Pega o primeiro tipo
      $tipoOrigem = $res[0]["prog_tipo_origem"];
      $tipoPrograma = $res[0]["prog_tipo"];
      $tabelaProgramas = "<table class='table' style='margin-top: 3px;' >";
      $tabelaProgramas .= "<caption class='captionTable' style='border-top-left-radius: 8px;border-top-right-radius: 8px;'>" . ucfirst($tipoPrograma) . "</caption>";
      $tabelaProgramas .= "<tr class='cabecalhoTable'>";
      $tabelaProgramas .= "<td colspan='2'><b>{$tipoOrigem}</b></td>";
      $tabelaProgramas .= "</tr>";
      foreach($res as $regProg){
        $class = '';
        if ($linhaColorida) {
          $class = "class='info'";
          $linhaColorida = false;
        }else{
          $linhaColorida = true;
        }
        if($tipoPrograma != $regProg["prog_tipo"]){
          $tabelaProgramas .= "</table>";
          $tabelaProgramas .= "<table class='table' style='margin-top: 3px;' cellpadding='3px' cellspacing='0' border='0'>";
          $tabelaProgramas .= "<caption class='captionTable' style='border-top-left-radius: 8px;border-top-right-radius: 8px;'>" . ucfirst($regProg["prog_tipo"]) . "</caption>";
          $tipoPrograma = $regProg["prog_tipo"];
          $tipoOrigem = "####";
        }
        if($tipoOrigem != $regProg["prog_tipo_origem"]){
          $tabelaProgramas .= "<tr class='cabecalhoTable'>";
          $tabelaProgramas .= "<td colspan='2'><b>{$regProg["prog_tipo_origem"]}</b></td>";
          $tabelaProgramas .= "</tr>";
          $tipoOrigem = $regProg["prog_tipo_origem"];
        }
        $tabelaProgramas .= "<tr {$class}>";
        $tabelaProgramas .= "<td>{$regProg['prog_nome']}</td>";
        if($regProg['gap_executa'] == 1){
          $btnAtivaDesativa = '<button type="button" onclick="ativarDesativar(\'Desativar\', ' . $regProg["idgrupos_acessos_programas"] . ')" class="btn btn-danger">Desativar</button>';
        }else{
          $btnAtivaDesativa = '<button type="button" onclick="ativarDesativar(\'Ativar\', ' . $regProg["idgrupos_acessos_programas"] . ')" class="btn btn-success">Ativar</button>';
        }
        $tabelaProgramas .= "<td align='right' id='btn_{$regProg["idgrupos_acessos_programas"]}' name='tdBtnAtivarDesativar'>{$btnAtivaDesativa}</td>";
        $tabelaProgramas .= "</tr>";
      }
      $tabelaProgramas .= "</table>";
    }
  //
  echo $tabelaProgramas;
  exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("grupos_acessos", "idgrupos_acessos");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir grupo<br>Operação cancelada!");
        exit;
    }
    header('location:../_Configuracoes/' . $paginaRetorno);
    exit;
  }

if($_POST['operacao'] == 'ativarDesativarTodos'){
  $sql = "UPDATE grupos_acessos_programas SET gap_executa = {$_POST['gap_executa']} WHERE gap_idgrupos_acessos = " . $_POST['gap_idgrupos_acessos'];
  $db->executaSQL($sql);
  //
  $sql = "SELECT * FROM grupos_acessos_programas WHERE gap_idgrupos_acessos = " . $_POST['gap_idgrupos_acessos'];
  $res = $db->consultar($sql);
  echo json_encode($res);
  exit;
}

if($_POST['operacao'] == "gerarMenu"){
  $resultMenu = $html->criaMenu($_POST['id_cadastro']);
  //
  if($resultMenu){
    echo "Ok";
  }else{
    echo "Erro";
  }
  //
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