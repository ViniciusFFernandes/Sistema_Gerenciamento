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
				WHERE (pess_associado = 'SIM' 
					OR pess_funcionario = 'SIM')
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
		$sql = "SELECT * FROM salarios WHERE idsalarios = " . $idsalarios;
		$situacao = $this->db->retornaUmCampoSql($sql, "sala_situacao"); 
		$readonlye = '';
		if($situacao != 'Aberto'){
			$readonlye = ' readonly="true" ';
		}
		//
		$linhaFuncionarios = '<br>';
		$linhas = 1;
		//
		foreach($res AS $reg){
			//
			$linhaFuncionarios .= '<div class="row" padding-bottom: 5px;" id="linhaSalario_' . $reg['idsalarios_funcionarios'] . '">';
				$linhaFuncionarios .= '<div class="col-md-4 col-sm-4 col-12 pb-3">';
					$linhaFuncionarios .= '<b>' . $reg['pess_nome'] . '</b> <span id="retonoGravaFuncionario_' . $reg['idsalarios_funcionarios'] . '"></span>';
				$linhaFuncionarios .= '</div>';
				$linhaFuncionarios .= '<div class="col-md-3 col-sm-3 col-4 pb-3">';
					$linhaFuncionarios .= '<div class="input-group">';
						$linhaFuncionarios .= '<span class="input-group-addon">F</span>';
						$linhaFuncionarios .= '<input type="text" class="form-control" ' . $readonlye . ' onchange="gravaDadosSalarios(' . $reg['idsalarios_funcionarios'] . ')" id="safu_dias_' . $reg['idsalarios_funcionarios'] . '" name="safu_dias_' . $reg['idsalarios_funcionarios'] . '" placeholder="Faltas" value=' . $reg['safu_dias'] . '>';
					$linhaFuncionarios .= '</div>';
				$linhaFuncionarios .= '</div>';
				$linhaFuncionarios .= '<div class="col-md-3 col-sm-3 col-6 pb-3">';
					$linhaFuncionarios .= '<div class="input-group">';
						$linhaFuncionarios .= '<span class="input-group-addon">R$</span>';
						$linhaFuncionarios .= '<input type="text" class="form-control" ' . $readonlye . ' onchange="gravaDadosSalarios(' . $reg['idsalarios_funcionarios'] . ')" id="safu_total_' . $reg['idsalarios_funcionarios'] . '" name="safu_total_' . $reg['idsalarios_funcionarios'] . '" placeholder="Salario" value=' . $this->util->formataMoeda($reg['safu_total']) . '>';
					$linhaFuncionarios .= '</div>';
				$linhaFuncionarios .= '</div>';
				$linhaFuncionarios .= '<div class="col-md-2 col-sm-2 col-2 pb-3" style="padding-left: 0px;"> ';
					if(empty($readonlye)){
						$linhaFuncionarios .= '<button type="button" class="btn btn-light" id="btnExcluir_' . $reg['idsalarios_funcionarios'] . '" onclick="excluirFuncionario(' . $reg['idsalarios_funcionarios'] . ')"><i class="fas fa-trash text-danger"></i></button>';
						$linhaFuncionarios .= '<span style="margin-left: 5px;" id="spanAtt_' . $reg['idsalarios_funcionarios'] . '">&nbsp;</span>';
					}else{
						$linhaFuncionarios .= '<button type="button" class="btn btn-light" id="btnImprimir_' . $reg['idsalarios_funcionarios'] . '" onclick="imprimir(\'contapag_recibo_associado.php\', ' . $reg['idsalarios_funcionarios'] . ')"><i class="fas fa-print text-primary"></i></button>';
					}
				$linhaFuncionarios .= '</div>';
			$linhaFuncionarios .= '</div>';
			//
			$linhas++;
		}
		//
		return $linhaFuncionarios;
	}

	public function geraDescontoFaltas($salario, $faltas, $mes, $ano){
		$diasUteis = $this->util->retornaDiasUteisMes($mes, $ano);
		$valorDia = $salario / $diasUteis;
		$valorFaltas = $valorDia * $faltas;
		return $valorFaltas;
	}
}
?>
