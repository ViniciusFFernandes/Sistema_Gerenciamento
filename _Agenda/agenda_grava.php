<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'agenda.php';
//
if ($_POST['operacaoAjax'] == 'registrarEvento'){
    //
    //
  	$db->setTabela("agenda", "idagenda");

    $dados['id']                                = $_POST['id_cadastro'];
    $dados['agen_titulo'] 	                    = $util->sgr($_POST['agen_titulo']);
    $dados['agen_descricao'] 	                = $util->sgr($_POST['agen_descricao']);
    $dados['agen_inicio'] 	                    = $util->sgr($_POST['agen_inicio'], true);
    $dados['agen_fim'] 	                        = $util->sgr($_POST['agen_fim'], true);
    $dados['agen_cor'] 	                        = $util->sgr($_POST['agen_cor']);
    if($_POST['id_cadastro'] <= 0){
        $dados['agen_idusuario'] 	            = $util->igr($_SESSION['idusuario']);
        $dados['agen_data_registro'] 	        = $util->dgr(date('d/m/Y H:i'));
    }
    //
    $db->gravarInserir($dados, true);
    //
    if($db->erro()){
        $ret['retorno'] = "erro";
        $ret['msg'] = $db->getErro();
      }else{
        $ret['retorno'] = "ok";
        if ($_POST['id_cadastro'] > 0) {
            $ret["idagenda"] = $_POST['id_cadastro'];
            $ret["eventoAtt"] = true;
        }else{
            $ret["idagenda"] = $db->getUltimoID();
            $ret["eventoAtt"] = false;
        }
    }
    //
    echo json_encode($ret);
    exit;
}

if ($_POST['operacaoAjax'] == 'carregaEventos'){
    $sql = "SELECT * FROM agenda";
    $res = $db->consultar($sql);
    //
    $ret["result"] = $res;
    //
    echo json_encode($ret);
    exit;
}

if ($_POST['operacaoAjax'] == 'buscaEvento'){
    $sql = "SELECT * FROM agenda WHERE idagenda = " . $_POST["id_cadastro"];
    $reg = $db->retornaUmReg($sql);
    //
    echo json_encode($reg);
    exit;
}

if ($_POST['operacaoAjax'] == 'excluirEvento'){
    $db->setTabela("agenda", "idagenda");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $ret['retorno'] = "erro";
        $ret['msg'] = $db->getErro();
    }else{
        $ret['retorno'] = "ok";
    }
    //
    echo json_encode($ret);
    exit;
}
?>
