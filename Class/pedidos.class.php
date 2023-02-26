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
            //
            $sql = "SELECT * 
                    FROM pedidos_itens 
                        LEFT JOIN produtos ON (idprodutos = peit_idprodutos)
                    WHERE peit_idpedidos = " . $idpedidos;
            $res = $this->db->consultar($sql);
            //
            if(empty($res)) return; //Retorna em branco caso nao tenha itens
            //
            $div = '<div class="row pb-3">';
                $div .= '<div class="col-md-12 col-sm-12 col-12">';
                //
                    $div .= '<div class="row linhaPedidoItens font-weight-bold">';
                        $div .= '<div class="col-md-3 col-sm-3 d-none d-sm-block">';
                            $div .= 'Produto';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block text-right">';
                            $div .= 'Qte';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block text-right">';
                            $div .= 'Unitário';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block text-right">';
                            $div .= 'Desconto';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block text-right">';
                            $div .= 'Total';
                        $div .= '</div>';
                        $div .= '<div class="col-md-1 col-sm-1 d-none d-sm-block text-right">';
                        $div .= '</div>';
                    $div .= '</div>';
                    //
                    foreach($res AS $reg){
                        $div .= '<div class="row border-prod">';
                            $div .= '<div class="col-md-3 col-sm-3 col-12 pt-2">';
                                $div .= $reg['idprodutos'] . ' - ' . $reg['prod_nome'];
                                $div .= '<i style="cursor: pointer; float: right;" class="fas fa-trash-alt pl-1 d-block d-sm-none"  onclick="excluiItem(' . $reg['idpedidos_itens'] . ')"></i>';
                                $div .= '<i style="cursor: pointer; float: right;" class="far fa-edit pr-1 d-block d-sm-none" onclick="editarItem(' . $reg['idpedidos_itens'] . ')"></i>';
                                $div .= '<div class="row">';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosProdPed">';
                                        $div .= 'Qte: ' . $this->util->formataNumero($reg['peit_qte']);
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosProdPed">';
                                        $div .= 'Unitário: ' . $this->util->formataMoeda($reg['peit_vlr_unitario']);
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosProdPed">';
                                        $div .= 'Desconto: ' . $this->util->formataMoeda($reg['peit_valor_desconto']);
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosProdPed">';
                                        $div .= 'Total: ' . $this->util->formataMoeda($reg['peit_total_item']);
                                    $div .= '</div>';
                                $div .= '</div>';
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block text-right">';
                                $div .= $this->util->formataNumero($reg['peit_qte']);
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block text-right">';
                                $div .= $this->util->formataMoeda($reg['peit_vlr_unitario']);
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block text-right">';
                                $div .= $this->util->formataMoeda($reg['peit_valor_desconto']);
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block text-right">';
                                $div .= $this->util->formataMoeda($reg['peit_total_item']);
                            $div .= '</div>';
                            $div .= '<div class="col-md-1 col-sm-1 pt-2 d-none d-sm-block text-right">';
                                $div .= '<i style="cursor: pointer;" class="far fa-edit mr-2" onclick="editarItem(' . $reg['idpedidos_itens'] . ')"></i>';
                                $div .= '<i style="cursor: pointer;" class="fas fa-trash-alt ml-2"  onclick="excluiItem(' . $reg['idpedidos_itens'] . ')"></i>';
                            $div .= '</div>';
                        $div .= '</div>';
                    }
                //
                $div .= '</div>';
            $div .= '</div>';
            //
            return $div;
        }

        public function retornaContasPedido($idpedidos){
            //
            $sql = "SELECT * 
                    FROM pedidos_contas 
                        LEFT JOIN meio_pagto ON (idmeio_pagto = pcon_idmeio_pagto)
                        LEFT JOIN cc ON (idcc = pcon_idcc)
                        LEFT JOIN tipo_contas ON (idtipo_contas = pcon_idtipo_contas)
                    WHERE pcon_idpedidos = " . $idpedidos;
            $res = $this->db->consultar($sql);
            //
            if(empty($res)) return ""; //Retorna em branco caso nao tenha itens
            //
            $div = '<div class="row pb-3">';
                $div .= '<div class="col-md-12 col-sm-12 col-12">';
                //
                    $div .= '<div class="row linhaPedidoContas font-weight-bold">';
                        $div .= '<div class="col-md-1 col-sm-1 d-none d-sm-block">';
                            $div .= 'Dias';
                        $div .= '</div>';
                        $div .= '<div class="col-md-1 col-sm-1 d-none d-sm-block">';
                            $div .= 'Vencimento';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block">';
                            $div .= 'Conta Bancaria';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block">';
                            $div .= 'Meio de Pagamento';
                        $div .= '</div>';
                        $div .= '<div class="col-md-1 col-sm-1 d-none d-sm-block">';
                            $div .= 'Conta';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block">';
                            $div .= 'Tipo';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block text-right">';
                            $div .= 'Valor';
                        $div .= '</div>';
                        $div .= '<div class="col-md-1 col-sm-1 d-none d-sm-block text-right">';
                        $div .= '</div>';
                    $div .= '</div>';
                    //
                    foreach($res AS $reg){
                        $div .= '<div class="row border-prod">';
                            $div .= '<div class="col-md-1 col-sm-1 pt-2 d-none d-sm-block">';
                                $div .= $reg['pcon_vencimento_dias'];
                            $div .= '</div>';
                            $div .= '<div class="col-md-1 col-sm-1 col-12 pt-2">';
                                $div .=  '<span class="d-block d-sm-none">' . $reg['pcon_vencimento_dias'] . ' - </span>' . $this->util->convertData($reg['pcon_vencimento']);
                                $div .= '<i style="cursor: pointer; float: right;" class="fas fa-trash-alt pl-1 d-block d-sm-none"  onclick="excluiConta(' . $reg['idpedidos_contas'] . ')"></i>';
                                $div .= '<i style="cursor: pointer; float: right;" class="far fa-edit pr-1 d-block d-sm-none" onclick="editarConta(' . $reg['idpedidos_contas'] . ')"></i>';
                                $div .= '<div class="row">';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosContasPed">';
                                        $div .= 'Conta Bancaria: ' . $reg['cc_nome'];
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosContasPed">';
                                        $div .= 'Meio de Pagamento: ' . $reg['mpag_nome'];
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosContasPed">';
                                        $div .= 'Conta a Receber: ' . $reg['pcon_idcontarec'];
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosContasPed">';
                                        $div .= 'Valor: ' . $this->util->formataMoeda($reg['pcon_valor']);
                                    $div .= '</div>';
                                $div .= '</div>';
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block">';
                                $div .= $reg['cc_nome'];
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block">';
                                $div .= $reg['mpag_nome'];
                            $div .= '</div>';
                            $div .= '<div class="col-md-1 col-sm-1 pt-2 d-none d-sm-block">';
                                $div .= $reg['pcon_idcontarec'];
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block">';
                                $div .= $reg['tico_nome'];
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block text-right">';
                                $div .= $this->util->formataMoeda($reg['pcon_valor']);
                            $div .= '</div>';
                            $div .= '<div class="col-md-1 col-sm-1 pt-2 d-none d-sm-block text-right">';
                                $div .= '<i style="cursor: pointer;" class="far fa-edit mr-2" onclick="editarConta(' . $reg['idpedidos_contas'] . ')"></i>';
                                $div .= '<i style="cursor: pointer;" class="fas fa-trash-alt ml-2"  onclick="excluiConta(' . $reg['idpedidos_contas'] . ')"></i>';
                            $div .= '</div>';
                        $div .= '</div>';
                    }
                //
                $div .= '</div>';
            $div .= '</div>';
            //
            return $div;
        }

    }
?>
