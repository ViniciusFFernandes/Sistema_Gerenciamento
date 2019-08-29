<?php
	class Parametros {
		private $db;
		private $util;

		function __construct($db, $util){
			$this->db = $db;
			$this->util = $util;
		}

		public function cadastraParametros($para_nome, $para_valor, $para_obs){
			$this->db->setTabela("parametros", "idparametros");
			unset($dados);
			$dados['para_nome'] 	= $this->util->sgr($para_nome);
			$dados['para_valor'] 	= $this->util->sgr($para_valor);
			$dados['para_obs'] 		= $this->util->sgr($para_obs);
		    $this->db->gravarInserir($dados);
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
				$tabela .= "<tr {$class} data-toggle='modal' data-target='#editaParametros' onclick='editaParametros({$reg["idparametros"]})' style='cursor:pointer'>";
					$tabela .= "<td width='6%'>{$reg["idparametros"]}</td>";
					$tabela .= "<td><img src='../icones/duvida.png' width='12px' id='help_{$reg["idparametros"]}' data-toggle='tooltip' data-placement='top' title='{$reg["para_obs"]}' style='cursor: help; float: left;'>{$reg["para_nome"]}</td>";
					$tabela .= "<td align='right' width='20%' id='para_valor_{$reg["idparametros"]}'>{$reg["para_valor"]}</td>";
				$tabela .- "</tr>";
			}
			$tabela .= "</table>";
			//
		  	//escreve a tabela
		  	echo $tabela;
		}

	}


?>
