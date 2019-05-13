<?php
	class log {

		//public function gravaLog($idusuario, $obs, $db, $util){

		//}

		public function gravaLogTempo($idsessao, $idusuario, $db, $util){
			//Testa se ja existe essa sessão registrada
			$db->setTabela("logs_acessos");
			$where = " log_idsessao = '{$idsessao}'";
			$res = $db->consultar($where, ' SUM(1) AS totalReg ');
			//
			unset($dados);
			$dados['log_idsessao'] 				= $util->sgr($idsessao);
			$dados['log_idusuario'] 			= $idusuario;
			$dados['log_ultimo_acesso'] 	    = time();
			$dados['log_ultimo_ip']             = $util->sgr($_SERVER['REMOTE_ADDR']);
			//
			foreach($res as $reg){
			    if ($reg['totalReg'] > 0) {
			    	//Caso exista atualiza o ultimo acesso
	  		     $db->alterar($where, $dados);
			    }else{
			    	//caso não exista insere essa sessão nos registros
			    	$db->gravar($dados);
			    }
	    	}
		    
		}

	}


?>
