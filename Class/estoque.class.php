<?php

class estoque{
	private $db;
	private $util;

	function __construct($db, $util){
		$this->db = $db;
		$this->util = $util;
	}

	public function geraMovimento($idprodutos, $maismMenos, $qte, $origem, $idorigem){
		$this->db->setTabela("produtos_movto", "idprodutos_movto");
	    //
	    unset($dados);
	  	$dados['prmv_idprodutos'] 	= $this->util->igr($idprodutos);
	  	$dados['prmv_data'] 		= " NOW() ";
	  	$dados['prmv_idoperador'] 	= $this->util->igr($_SESSION['idusuario']);
	  	$dados['prmv_idorigem'] 	= $this->util->igr($idorigem);
	  	$dados['prmv_origem'] 		= $this->util->sgr($origem);
	  	$dados['prmv_qte'] 			= $this->util->vgr($qte);
	  	$dados['prmv_maismenos'] 	= $this->util->sgr($maismMenos);
	    //
	    $this->db->gravarInserir($dados);
	    if($this->db->erro()){
	      $this->db->rollBack();
	      $this->util->mostraErro($this->db->getErro() . " <br><br>Operação cancelada!");
	      exit;
	    }
    }
	
}
?>
