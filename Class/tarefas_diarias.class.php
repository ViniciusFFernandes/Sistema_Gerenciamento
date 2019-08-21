<?php
	class Tarefas_Diarias {
		private $data_atual;
		private $parametros;
		private $ultima_execucao;
		private $db;
		private $util;

		function __construct($parametros, $db, $util){
			$this->data_atual = date('Y-m-d');
			$this->db = $db;
			$this->util = $util;
			$this->parametros = $parametros;
			$this->ultima_execucao = $this->parametros->buscaValor("sistema: data da ultima execucao de tarefas diarias");
		}

		public function executa_tarefas(){
	      	if ($this->ultima_execucao >= $this->data_atual) {
	        	return;
		  	}
		  	//Atualiza a data de execução.
		  	unset($dados);
		  	$dados['id'] 			= $this->util->sgr('sistema: data da ultima execucao de tarefas diarias');
		  	$dados['para_valor'] 	= $this->util->sgr($this->data_atual);
     		$this->parametros->gravaValor($dados);
     		//
     		//Executa tarefas
     		$this->apagaHistoricoAtualizacao();
		}

		private function apagaHistoricoAtualizacao(){
			$sql = "DELETE FROM versao_hist WHERE DATE_ADD(vhist_data, INTERVAL 60 DAY) <= NOW()";
			$this->db->executaSQL($sql);
		}
	}

?>
