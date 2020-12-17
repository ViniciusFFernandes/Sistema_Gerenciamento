<?php
require_once("util.class.php");

class Salarios{
	private $db;
	private $util;

	function __construct($db){
		$this->db = $db;
		$this->util = new Util();
	}
	public function insereFuncionarios($idsalarios){
		$sql = "SELECT idpessoas 
				FROM pessoas 
				WHERE pess_associado = 'SIM' 
					AND pess_inativo <> 'S'";
		$res = $this->db->consultar($sql);
		//
		$this->db->setTabela("salarios_funcionarios", "idsalarios_funcionarios");
		//
		foreach($res AS $reg){
			unset($dados);
			$dados['id']                    = 0;
			$dados['safu_idsalarios']       = $idsalarios;
			$dados['safu_idpessoas']   		= $this->util->igr($reg['idpessoas']);
			$this->db->gravarInserir($dados, true);	
		}
	}

	public function getFuncionarios($idsalarios){
		$sql = "SELECT * 
				FROM salarios_funcionarios
					JOIN pessoas ON (idpessoas = safu_idpessoas) 
				WHERE safu_idsalarios = " . $idsalarios;
		$res = $this->db->consultar($sql);
		//
		$linhaFuncionarios = '<br>';
		$linhas = 1;
		//
		foreach($res AS $reg){
			$corLinha = "bg-light";
			if($linhas % 2) $corLinha = "bg-primary";
			//
			$linhaFuncionarios .= '<div class="row ' . $corLinha . '" style="padding-top: 5px; padding-bottom: 5px;" id="linhaSalario_' . $reg['idsalarios_funcionarios'] . '">';
				$linhaFuncionarios .= '<div class="col-md-4 col-sm-4 col-xs-12" style="padding-top: 5px;">';
					$linhaFuncionarios .= '<b>' . $reg['pess_nome'] . '</b> <span id="retonoGravaFuncionario_' . $reg['idsalarios_funcionarios'] . '"></span>';
				$linhaFuncionarios .= '</div>';
				$linhaFuncionarios .= '<div class="col-md-3 col-sm-3 col-xs-4">';
					$linhaFuncionarios .= '<div class="input-group">';
						$linhaFuncionarios .= '<span class="input-group-addon">F</span>';
						$linhaFuncionarios .= '<input type="text" class="form-control" id="safu_dias_' . $reg['idsalarios_funcionarios'] . '" name="safu_dias_' . $reg['idsalarios_funcionarios'] . '" placeholder="Faltas" value=' . $reg['safu_dias'] . '>';
					$linhaFuncionarios .= '</div>';
				$linhaFuncionarios .= '</div>';
				$linhaFuncionarios .= '<div class="col-md-3 col-sm-3 col-xs-6">';
					$linhaFuncionarios .= '<div class="input-group">';
						$linhaFuncionarios .= '<span class="input-group-addon">R$</span>';
						$linhaFuncionarios .= '<input type="text" class="form-control" id="safu_total_' . $reg['idsalarios_funcionarios'] . '" name="safu_total_' . $reg['idsalarios_funcionarios'] . '" placeholder="Salario" value=' . $reg['safu_total'] . '>';
					$linhaFuncionarios .= '</div>';
				$linhaFuncionarios .= '</div>';
				$linhaFuncionarios .= '<div class="col-md-2 col-sm-2 col-xs-2" style="padding-left: 0px;">';
					$linhaFuncionarios .= '<button type="button" class="btn btn-light" id="btnExcluir_' . $reg['idsalarios_funcionarios'] . '" onclick="excluirFuncionario(' . $reg['idsalarios_funcionarios'] . ')"><img src="../icones/excluir2.png" width="15px;"></button>';
				$linhaFuncionarios .= '</div>';
			$linhaFuncionarios .= '</div>';
			//
			$linhas++;
		}
		//
		return $linhaFuncionarios;
	}
}
?>
