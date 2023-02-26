<?php
	class log {
		private $db;
		private $util;

		function __construct($db){
			$this->db = $db;
			$this->util = new Util();
		}

		public function gravaLog(){
			// //
			// $db->setTabela("logs_acessos");
			// //
			// unset($dados);
			// $dados['log_idsessao'] 				= $util->sgr($idsessao);
			// $dados['log_idusuario'] 			= $idusuario;
			// $dados['log_ultimo_acesso'] 	    = time();
			// $dados['log_ultimo_ip']             = $util->sgr($_SERVER['REMOTE_ADDR']);
			// //
			// $this->db->gravarInserir($dados, false);
		}

	}


?>
