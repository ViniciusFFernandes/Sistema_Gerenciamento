<?php
    require_once("util.class.php");
    require_once("parametros.class.php");
    require_once("util.class.php");
    require_once("html.class.php");
    require_once("cc.class.php");

    class cc{
        private $db;
		private $util;
		private $parametros;
		private $html;

		function __construct($db){
			$this->db = $db;
			$this->util = new Util();
			$this->parametros = new Parametros($db);
			$this->html = new html($db);
		}

        public function gerarLancamento($idconta, $operacao, $valor, $idoperador, $data, $idmeio_pagto, $idcc, $tipoConta, $obs){
            $this->db->setTabela("cc_lanctos", "idcc_lanctos");
            //
            unset($dados);
            $dados['id']                    = 0;
            $dados['ccla_idcc'] 	        = $this->util->igr($idcc);
            $dados['ccla_data']             = $this->util->dgr($data);
            $dados['ccla_inclusao']         = $this->util->dgr(date('d/m/Y H:i'));
            $dados['ccla_valor']            = $this->util->vgr($valor);
            $dados['ccla_obs']              = $this->util->sgr($obs);
            $dados['ccla_idmeio_pagto'] 	= $this->util->igr($idmeio_pagto);
            $dados['ccla_idoperador'] 	    = $this->util->igr($idoperador);
            $dados['ccla_tipo'] 	        = $this->util->sgr($operacao);
            $dados['ccla_id' . $tipoConta] 	= $this->util->igr($idconta);
            //
            $this->db->gravarInserir($dados, true);
        }

    }
?>
