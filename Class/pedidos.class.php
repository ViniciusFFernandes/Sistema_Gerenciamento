<?php
    require_once("util.class.php");
    require_once("parametros.class.php");
    require_once("util.class.php");
    require_once("html.class.php");
    require_once("cc.class.php");

    class Pedidos{
        private $db;
		private $util;
		private $parametros;
		private $html;
		private $cc;

		function __construct($db){
			$this->db = $db;
			$this->util = new Util();
			$this->parametros = new Parametros($db);
			$this->html = new html($db);
			$this->cc = new cc($db);
		}

        public function retornaDadosDesconto($idpedidos, $valorPedido, $valorDesconto, $porcDesconto){
            if($idpedidos > 0){
                $totalPedidos = $this->db->retornaUmCampoID('ped_total_produtos + ped_frete', 'pedidos', $idpedidos);
            }else{
                $totalPedidos = $valorPedido;
            }
            //
            $dados = array();
            //
            if($valorDesconto > 0){
                $dados['valor'] = $valorDesconto;
                $dados['porcentagem'] = ($valorDesconto * 100) / $totalPedidos;
            }elseif($porcDesconto > 0){
                $dados['porcentagem'] = $porcDesconto;
                $dados['valor'] = ($totalPedidos * $porcDesconto) / 100;
            }else{
                $dados['valor'] = 0;
                $dados['porcentagem'] = 0; 
            }
            //
            return $dados;
        }

        public function retornaItensPedido($idpedidos){
            $sql = "SELECT * 
                    FROM pedidos_itens 
                        LEFT JOIN produtos ON (idprodutos = peit_idprodutos)
                    WHERE peit_idpedidos = " . $idpedidos;
            //
            $div = '<div class="row">';
                $div = '<div class="col-md-12 col-sm-12 col-12">';

                $div = '</div>';
            $div = '</div>';
        }

    }
?>
