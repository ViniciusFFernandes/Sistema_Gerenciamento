<?php
	class Parametros {

		public function buscaValor($para_nome, $db){
			
		}

		public function gravaValor($id, $dados, $db){
			if(intval($id) > 0){
				$campoID = "idparametros";
			}else{
				$campoID = "para_nome";
			}
			//
			$db->setTabela("parametros", $campoID);
		    $db->gravarInserir($dados);
		}

		public function retornaDados($idparametros, $db){
			$sql = "SELECT * FROM parametros WHERE idparametros = {$idparametros}";
			$res = $db->retornaUmReg($sql);
			return $res;
		}

		public function tabelaParametros($filtro, $db, $util){
			$sql = "SELECT * FROM parametros";
			if(!empty($filtro)){
				$sql .= " WHERE para_nome LIKE " . $util->sgr("%" . $filtro ."%");
			}
			$sql .= " ORDER BY para_nome ASC";
			$res = $db->consultar($sql);
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
					$tabela .= "<td>{$reg["para_nome"]}</td>";
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
