<?php
	require_once("Util.class.php");
	class html{
		private $db;
		private $util;

		function __construct($db){
			$this->db = $db;
			$this->util = new Util();
		}

		public function criaSelectSql($campoMostra, $campoValue, $nameInput, $valueReg, $sql, $class = "", $js = "", $geraZero = true){
			//
			$res = $this->db->consultar($sql);
			//
			$comboBox = "<select name='{$nameInput}' id='{$nameInput}' class='{$class}' {$js}>";
				if($geraZero) $comboBox .= "<option value=''>---</option>";
				foreach ($res as $reg) {
					$selected = "";
					if($reg[$campoValue] == $valueReg) $selected  = "selected='selected'";
					$comboBox .= "<option value='{$reg[$campoValue]}' {$selected}>{$reg[$campoMostra]}</option>";
				}
			$comboBox .= "</select>";
			//
			return $comboBox;
		}

		public function mostraErro($mensagem, $link = null){
			echo '
			<!DOCTYPE html>
			<html lang="pt">
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title>' . NOME_SISTEMA . '</title>
			</head>
			<body>
				<link rel="stylesheet" href="../css/bootstrap.min.css">
				<div class="row">
					<div class="col-md-4 col-sm-1 col-xs-1"></div>
					<div class="col-md-4 col-sm-10 col-xs-10">
						<div class="panel panel-default">
							<div class="panel-heading">Ops! Tivemos algum probleminha.</div>
							<div class="panel-body">
								<span style="color: red;">Mas não se desespere!</span><br>
								ERRO: ' . $mensagem . '
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
					<div class="col-md-4 col-sm-1 col-xs-1"></div>
				</div>
				</body>
			</html>
			';
		}

		public function mostraMensagem($tipo, $mensagem, $id = ''){
			$msg = '
			<div class="alert alert-' . $tipo . ' alert-dismissible" role="alert" style="margin: 0 auto; box-shadow: 1px 1px 5px black;">
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

		public function comboBoxSql($nomeInput, $campoMostra, $campoValue, $sql, $db, $valueReg = ''){
			$res = $db->consultar($sql);
			$comboBox = '<select class="form-control" name="' . $nomeInput . '" id="' . $nomeInput . '" >';
			$comboBox .= '<option value="" ' . $this->defineSelected('', $valueReg) . '>-----------</option>';
			foreach ($res as $reg) {
				$comboBox .= '<option value="' . $reg[$campoValue] . '" ' . $this->defineSelected($reg[$campoValue], $valueReg) . '>' . $reg[$campoMostra] . '</option>';
			}
			$comboBox .= '</select>';
			//
			return $comboBox;
		}

		public function buscaHtml($btnMenu = ''){
			$menu = file_get_contents('../menu.html');
			//
			$opcoes_config = file_get_contents('../menu__opcoes_config.html');
			$includes = file_get_contents('../includes.html');
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
			//
			$opcoesMenuConfig = $this->geraMenu($_SESSION['idgrupos_acessos'], 'configuracoes');
			//
			//Até o momento a pagina inicio não possui menu para gerar por tanto ignora
			if(!empty($btnMenu) && $btnMenu != 'inicio') $opcoesMenu = $this->geraMenu($_SESSION['idgrupos_acessos'], $btnMenu);
			//
			$menu = str_replace($busca, 'class="active"', $menu);
			$menu = str_replace("##NomeUsuario##", $_SESSION['user'], $menu);
			$menu = str_replace("##opcoesConfig##", $opcoes_config, $menu);
			$menu = str_replace("##imgLogo##", LOGO_EMPRESA, $menu);
			$menu = str_replace("##opcoesConfigGrupo##", $opcoesMenuConfig, $menu);
			//
			$nome = explode(".", basename($_SERVER['PHP_SELF']));
			$nomeArquivo = $nome[0] . ".html";
			$html = file_get_contents("_HTML/" . $nomeArquivo);
			$html = str_replace("##Menu##", $menu, $html);
			$html = str_replace("##opcoesMenuGrupo##", $opcoesMenu, $html);
			$html = str_replace("##includes##", $includes, $html);
			$html = str_replace("##nomeSistema##", NOME_SISTEMA, $html);
			//
			return $html;
		}

		public function criaMenu($idgrupos_acessos = '', $tipo_menu = ''){
			$sql = "SELECT * 
					FROM grupos_acessos_programas 
						JOIN programas ON (gap_idprogramas = idprogramas)
					WHERE prog_tipo = 'menu'";
			if(!empty($tipo_menu)){
				$sql .= " AND prog_tipo_menu = " . $this->util->sgr($tipo_menu);
			} 
			if(!empty($idgrupos_acessos)){
				$sql .= " AND gap_idgrupos_acessos = " . $this->util->igr($idgrupos_acessos);
			} 
			$sql .= " ORDER BY gap_idgrupos_acessos, prog_tipo_menu, idprogramas";
			$res = $this->db->consultar($sql);
			//
			$ultimoTipo = '###';
			$primeiro = true;
			$qteColunas = 0;
			foreach($res as $reg){
				if($ultimoTipo != $reg['prog_tipo_menu']){
					if(!$primeiro && $ultimoTipo != '###'){
						$sql = "UPDATE grupos_acessos SET grac_menu_{$ultimoTipo} = " . $this->util->sgr($menu) . " WHERE idgrupos_acessos = " . $idgrupos_acessos;
						$this->db->executaSQL($sql);
						if($this->db->erro()){
							return false;
						}
					}
					$menu = '';
					$ultimoTipo = $reg['prog_tipo_menu'];
					$idgrupos_acessos = $reg['gap_idgrupos_acessos'];
				}

				if($reg['gap_executa'] == 1 || $reg['gap_idgrupos_acessos'] == 1){
					if($reg['prog_tipo_menu'] != "configuracoes"){
						if($qteColunas == 4){
							$menu .= '<div class="clearfix"></div>';
							$qteColunas = 0;
						}
						$menu .= '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 " id="divLink"  onclick="direciona(\''. $reg['prog_file'] . '\')">
									<img src="../icones/' . $reg['prog_imagem'] . '"> &nbsp; ' . $reg['prog_nome'] . '
								</div>';
						$qteColunas ++;
					}else{
						$menu .= '<li><a href="../_Configuracoes/' . $reg['prog_file'] . '">' . $reg['prog_nome'] . '</a></li>';
					}			
				}
				$primeiro = false;
			}
			if($ultimoTipo != '###'){
				$sql = "UPDATE grupos_acessos SET grac_menu_{$ultimoTipo} = " . $this->util->sgr($menu) . " WHERE idgrupos_acessos = " . $idgrupos_acessos;
				$this->db->executaSQL($sql);
				if($this->db->erro()){
					return false;
				}
			}
			return true;
		}

		public function geraMenu($idgrupos_acessos, $tipo_menu = ''){
			$sql = "SELECT grac_menu_{$tipo_menu} AS menu FROM grupos_acessos WHERE idgrupos_acessos = " . $idgrupos_acessos;
			$menu = $this->db->retornaUmCampoSql($sql, "menu");
			if(is_null($menu)){
				if($this->criaMenu($idgrupos_acessos, $tipo_menu)){
					$this->geraMenu($idgrupos_acessos, $tipo_menu);
				}
			}else{
				if(empty($menu)){
					return "<center><b style='color: #ac2925;'>Permissão negada!</b></center>";
				}else{
					return $menu;
				}
				
			}	
		}
	}
?>
