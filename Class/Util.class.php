<?php

class Util{

	public function sgr($string){
		if ($string != "") {
          	return "'" . $string . "'";
        }else{
          	return "NULL";
        }
	}
	
	public function pgr($string){
		if ($string != "") {
          	return "(" . $string . ")";
        }else{
          	return "NULL";
        }
	}

	public function igr($num){
		if ($num != "") {
         	return intval($num);
        }else{
          	return "NULL";
        }
	}

	public function vgr($num){
		if ($num != "") {
         	return floatval($num);
        }else{
          	return "NULL";
        }
	}

	public function dgr($string, $incluiHora = ""){
		if ($string != "") {
					$string = explode(" ", $string);
					$data = explode("/", $string[0]);
					$dataFormatada = $data[2] . "-" . $data[1] . "-" . $data[0];
					if (!empty($incluiHora)) {
							$dataFormatada .= " " . $incluiHora;
					}elseif (!empty($string[1])) {
							$dataFormatada .= " " . $string[1];				
					}else{
						$dataFormatada .= " 00:00:00";		
					}

				return "'" . $dataFormatada . "'";
		}
	}

	public function convertData($string){
		if ($string != "") {
					$string = explode(" ", $string);
					$data = explode("-", $string[0]);
					$dataFormatada = $data[2] . "/" . $data[1] . "/" . $data[0];
					if (!empty($string[1])) {
						$dataFormatada .= " " . $string[1];
					}
				}else {
					return "NULL";
				}
				return $dataFormatada;
	}

	public function mostraErro($mensagem, $link = null){
		echo '
	    <link rel="stylesheet" href="../css/bootstrap.min.css">
		<div class="row">
        	<div class="col-md-4 col-sm-1"></div>
          	<div class="col-md-4 col-sm-10">
	    		<div class="panel panel-default">
	      			<div class="panel-heading">Ops! Tivemos algum probleminha.</div>
	      			<div class="panel-body">
	      	 			<span style="color: red;">Mas não se desespere!</span><br>
	        			ERRO: <br>' . $mensagem . '
	      			</div>
	      			<div class="panel-footer">' ;
	    if ($link != "") {
	    	echo '<button class="btn btn-danger btn-lg" onclick="window.location.replace(\'' . $link . '\');">Voltar</button>';
	    }else{
	     	echo '<button class="btn btn-danger btn-lg" onclick="window.history.back();">Voltar</button>';
	    }
	    echo	'</div>
		   		 	</div>
		   		</div>
		   		<div class="col-md-4 col-sm-1"></div>
		   	</div>
		';
	}

	public function mostraMensagem($tipo, $mensagem, $id = ''){
		$msg = '
		<div class="alert alert-' . $tipo . ' alert-dismissible" role="alert">
			<button type="button" id="botao_alerta" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<center>' . $mensagem;
		if (!empty($id)) {
			$msg .= '(' . $id . ')';
		}
		$msg .= '</center> </div>';
		return $msg;
	}

	public function defineChecked($string){
		if($string == "SIM"){
			return "checked";
		}else{
			return "";
		}
	}

	public function defineSelected($valorPadrao, $valor){
		if($valorPadrao == $valor){
			return 'selected="selected"';
		}else{
			return "";
		}
	}

	public function comboBoxSql($nomeInput, $campoMostra, $campoValue, $sql, $db){
		$res = $db->consultar($sql);
		$comboBox = '<select class="form-control" name="' . $nomeInput . '" id="' . $nomeInput . '" >';
		$comboBox .= '<option value="0" selected="selected">-----------</option>';
		foreach ($res as $reg) {
			$comboBox .= '<option value="' . $reg[$campoValue] . '">' . $reg[$campoMostra] . '</option>';
		}
		$comboBox .= '</select>';
		//
		return $comboBox;
	}

	public function buscaHtml($btnMenu = ''){
		$menu = file_get_contents('../menu.html');
		$opcoes_config = file_get_contents('../menu__opcoes_config.html');
		//
		if ($btnMenu == "inicio"){
			$busca = '##inicio##';
		}elseif($btnMenu == "cadastros"){
			$busca = '##cadastros##';
		}elseif($btnMenu == "lancamentos"){
			$busca = '##lancamentos##';
		}elseif($btnMenu == "relatorios"){
			$busca = '##relatorios##';
		}elseif($btnMenu == "quemsomos"){
			$busca = '##QuemSomos##';
		}
		$menu = str_replace($busca, 'class="active"', $menu);
		$menu = str_replace("##NomeUsuario##", $_SESSION['user'], $menu);
		$menu = str_replace("##opcoesConfig##", $opcoes_config, $menu);
		//
		$nome = explode(".", basename($_SERVER['PHP_SELF']));
		$nomeArquivo = $nome[0] . ".html";
		$html = file_get_contents("_HTML/" . $nomeArquivo);
		$html = str_replace("##Menu##", $menu, $html);
		//
		return $html;
	}
}
?>
