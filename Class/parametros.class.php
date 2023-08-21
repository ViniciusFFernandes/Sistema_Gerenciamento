<?php
	require_once("util.class.php");

	class Parametros {
		private $db;
		private $util;

		function __construct($db){
			$this->db = $db;
			$this->util = new Util();
		}

		public function cadastraParametros($para_nome, $para_valor, $para_obs, $para_tipo = 'parametro', $para_nome_constante = ''){
			$this->db->setTabela("parametros", "idparametros");
			unset($dados);
			$dados['para_nome'] 			= $this->util->sgr($para_nome);
			$dados['para_valor'] 			= $this->util->sgr($para_valor);
			$dados['para_obs'] 				= $this->util->sgr($para_obs);
			$dados['para_tipo'] 			= $this->util->sgr($para_tipo);
			$dados['para_nome_constante'] 	= $this->util->sgr($para_nome_constante);
			$this->db->gravarInserir($dados);
			//
			if(!$this->db->erro() && ($para_tipo == "constante" || $para_tipo == "variavel")){
				$this->geraConstante();
			}
		}

		public function buscaValor($para_nome){
			$sql = "SELECT para_valor FROM parametros WHERE para_nome = '{$para_nome}'";
			$para_valor = $this->db->retornaUmCampoSql($sql, "para_valor");
			return $para_valor;
		}

		public function gravaValor($dados){
			if(is_numeric(str_replace("'", "", $dados['id']))){
				$campoID = "idparametros";
			}else{
				$campoID = "para_nome";
			}
			//
			$this->db->setTabela("parametros", $campoID);
			$this->db->gravarInserir($dados);
			echo $dados['para_tipo'];
			if(!$this->db->erro() && ($dados['para_tipo'] == "'constante'" || $dados['para_tipo'] == "'variavel'") ){
				$this->geraConstante();
			}
		}

		public function retornaDados($idparametros){
			$sql = "SELECT * FROM parametros WHERE idparametros = {$idparametros}";
			$res = $this->db->retornaUmReg($sql);
			return $res;
		}

		public function tabelaParametros($filtro, $util){
			$sql = "SELECT * FROM parametros";
			if(!empty($filtro)){
				$sql .= " WHERE para_nome LIKE " . $util->sgr("%" . $filtro ."%");
			}
			$sql .= " ORDER BY para_nome ASC";
			$res = $this->db->consultar($sql);
			$linhaColorida = false;
			foreach ($res as $reg) {
				//
				$ignoraTamanho = false;
				//
				$para_valor = $reg["para_valor"];
				if($para_valor == ''){
					$para_valor = '<span class="Obs_claro">*Em Branco*</span>';
					$ignoraTamanho = true;
				}
				//
				if(strlen($para_valor) > 25 && !$ignoraTamanho){
					$para_valor = substr($para_valor, 0, 25) . '<span class="Obs_claro">...</span>';
				}
				//
				$tabela .= "<div class='row border-top' data-toggle='modal' data-target='#editaParametros' onclick='editaParametros({$reg["idparametros"]})' style='cursor:pointer'>";
					$tabela .= "<div class='col-md-1 col-sm-1 d-none d-sm-block pb-2 pt-2'>{$reg["idparametros"]}</div>";
					$tabela .= "<div class='col-md-7 col-sm-7 col-12 pb-2 pt-2'><i class='fas fa-question d-none d-sm-block' id='help_{$reg["idparametros"]}' data-toggle='tooltip' data-placement='top' title='{$reg["para_obs"]}' style='cursor: help; float: left; font-size: 10px;'></i>&nbsp;{$reg["para_nome"]}</div>";
					$tabela .= "<div class='col-md-4 col-sm-4 col-12 pb-2 pt-2' align='right' id='para_valor_{$reg["idparametros"]}'>{$para_valor}</div>";
				$tabela .= "</div>";
			}
			//
		  	//escreve a tabela
		  	echo $tabela;
		}

		function geraConstante(){
			$sql = "SELECT * FROM parametros WHERE para_tipo = 'constante' OR para_tipo = 'variavel'";
			$res = $this->db->consultar($sql);
			//
			//Deleta o constante antigo
			unlink("../privado/constantes_old.vf");
			//
			//Renomeia o atual
			rename("../privado/constantes.vf", "../privado/constantes_old.vf");
			//
			//Cria o novo constante
			$constante = fopen("../privado/constantes.vf", "a");
			$textoConstante = "<?php \n";
			foreach ($res as $reg) {
				//prepara o valor
				if($reg['para_valor'] == 'SIM'){
					$valor = "true";
				}elseif($reg['para_valor'] == 'NAO'){
					$valor = "false";
				}elseif(empty($reg['para_valor'])){
					$valor = "''";
				}else{
					$valor = $this->util->sgr($reg['para_valor']);
				}
				if($reg['para_tipo'] == 'constante'){
					$textoConstante .= "define(" . $reg['para_nome_constante'] . ", " . $valor . ");\n";
				}elseif($reg['para_tipo'] == 'variavel'){
					$textoConstante .= "$" . $reg['para_nome_constante'] . " = " . $valor . ";\n";
				}
			}
			$textoConstante .= '?>';
			fwrite($constante, $textoConstante);
			fclose($constante);
		}
	}


?>
