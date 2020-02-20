<?php
	class Parametros {
		private $db;
		private $util;

		function __construct($db, $util){
			$this->db = $db;
			$this->util = $util;
		}

		public function cadastraParametros($para_nome, $para_valor, $para_obs, $para_tipo = '', $para_nome_constante = ''){
			$this->db->setTabela("parametros", "idparametros");
			unset($dados);
			$dados['para_nome'] 			= $this->util->sgr($para_nome);
			$dados['para_valor'] 			= $this->util->sgr($para_valor);
			$dados['para_obs'] 				= $this->util->sgr($para_obs);
			$dados['para_tipo'] 			= $this->util->sgr($para_tipo);
			$dados['para_nome_constante'] 	= $this->util->sgr($para_nome_constante);
			$this->db->gravarInserir($dados);
			//
			if(!$this->db->erro() && $para_tipo != 'parametro'){
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

			if(!$this->db->erro() && $dados['para_tipo'] != 'parametro'){
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
		    $tabela .= "<table class='table' style='margin-top: 3px'>";
			foreach ($res as $reg) {
				//
				//Define se a linha vai ser de outra cor
				$class = '';
				if ($linhaColorida) {
					$class = "class='info'";
					$linhaColorida = false;
				}else{
					$linhaColorida = true;
				}
				//
				$para_valor = $reg["para_valor"];
				if($para_valor == ''){
					$para_valor = '<span class="Obs_claro">*Em Branco*</span>';
				}
				//
				$tabela .= "<tr {$class} data-toggle='modal' data-target='#editaParametros' onclick='editaParametros({$reg["idparametros"]})' style='cursor:pointer'>";
					$tabela .= "<td width='6%'>{$reg["idparametros"]}</td>";
					$tabela .= "<td><img src='../icones/duvida.png' width='12px' id='help_{$reg["idparametros"]}' data-toggle='tooltip' data-placement='top' title='{$reg["para_obs"]}' style='cursor: help; float: left;'>{$reg["para_nome"]}</td>";
					$tabela .= "<td align='right' width='20%' id='para_valor_{$reg["idparametros"]}'>{$para_valor}</td>";
				$tabela .- "</tr>";
			}
			$tabela .= "</table>";
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
			$textoConstante = '<?php ';
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
			$textoConstante .= ' ?>';
			fwrite($constante, $textoConstante);
			fclose($constante);
		}
	}


?>
