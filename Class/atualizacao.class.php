<?php
	class Atualizacao {
		private $ultimaVersao = 0.02;

		public function getUltimaVersao(){
			return $this->ultimaVersao;
		}

		public function atualizarSistema($versaoAtual){
			$dados['executaNovamente'] = true;
			$versaoAtual += 0.01;
			//
			$versao = '$this->versao_';
			$versao .= (str_pad(intval($versaoAtual), 2, "0", STR_PAD_LEFT));
			$versao .= "_";
			$versao .= (str_pad( round(($versaoAtual - intval($versaoAtual)), 2) * 100  , 2, "0", STR_PAD_LEFT));
			$versao .= "();";
			//echo $versao;
			$msg = eval("return " . $versao . ";");  // chama a funcao de atualizacao da versao.
			//
			if($versaoAtual >= $this->ultimaVersao) $dados['executaNovamente'] = false;
			$dados['novaVersao'] = $versaoAtual;
			$dados['msg'] = $msg;
			return $dados;
		}

		//////////////////////////////////////
		//Abaixo estão as versões do sistema//
		//////////////////////////////////////

		private function versao_00_02(){
			//
			// 19/08/2019 Vinicius
			//

			return "Teste de mensagem para ver se retorna";
		}

	}


?>
