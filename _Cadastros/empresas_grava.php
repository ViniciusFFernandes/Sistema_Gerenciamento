<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'empresas_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * FROM empresas";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idempresas LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR emp_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%"). "
                  OR emp_cnpj LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idempresas'] = "width='6%'";
    $dados['emp_nome'] = "";
    //
    $cabecalho['Código'] = '';
    $cabecalho['Nome'] = '';
    //
    echo $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno, $cabecalho);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("empresas", "idempresas");

    unset($dados);
    $dados['id']                  = $_POST['id_cadastro'];
    $dados['emp_nome'] 	          = $util->sgr($_POST['emp_nome']);
    $dados['emp_cnpj'] 	          = $util->sgr($_POST['emp_cnpj']);
    $dados['emp_endereco'] 	      = $util->sgr($_POST['emp_endereco']);
    $dados['emp_cep'] 	          = $util->sgr($_POST['emp_cep']);
    $dados['emp_telefone'] 	      = $util->sgr($_POST['emp_telefone']);
    $dados['emp_idcidades'] 	    = $util->sgr($_POST['emp_idcidades']);
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
    $db->setTabela("empresas", "idempresas");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

  if($_POST['operacaoAjax'] == "enviarLogo"){
    //
    // Captura o arquivo selecionado
    $arquivo = $_FILES['arquivo'];
    //
    //Define os tipos de arquivos válidos (No nosso caso, só imagens)
    $tipos = array('jpg', 'png', 'gif', 'psd', 'bmp');
    //
    // Chama a função para enviar o arquivo 
    $retornoEnvio = $util->uploadFile($arquivo, '../uploads/', $tipos, "empresa_{$_REQUEST['id_cadastro']}_logo_" . rand());
    //
    $data['sucesso'] = false;
    if(isset($retornoEnvio['erro'])){    
        $data['msg'] = $retornoEnvio['erro'];
    }
    else{
      $data['sucesso'] = true;
      //
      // Caminho do arquivo 
      $data['msg'] = $retornoEnvio['caminho'];
      //
      //Grava na tabela o nome da imagem
      $db->setTabela("empresas", "idempresas");

      unset($dados);
      $dados['id']                  = $_REQUEST['id_cadastro'];
      $dados['emp_logo'] 	          = $util->sgr($retornoEnvio['nomeArquivo']);
      $db->gravarInserir($dados);
    }
    //
    // Codifica a variável array $data para o formato JSON 
    echo json_encode($data);
  }


 ?>
