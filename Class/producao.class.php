<?php

class producao{
	private $db;
	private $util;

	function __construct($db, $util){
		$this->db = $db;
		$this->util = $util;
	}

	public function getItensProducao($idproducao){
		$itensProducao = '<div class="row" id="formulaItens">
							<label style="margin-left: 15px;">Itens a serem consumidos</label>
				          <div class="col-12 col-md-12 col-sm-12 col-lg-12" id="tableitensProducao">';
	    $itensProducao .= $this->tabelaItens($idproducao);
	    $itensProducao .= '</div>';
	    $itensProducao .= '</div>';
	    $itensProducao .= '</div>';
	    //
	   	return $itensProducao;
    }
	
	public function tabelaItens($idproducao){
		$sqlFormulas = "SELECT * 
            FROM producao_itens
              LEFT JOIN produtos ON (pdci_idprodutos = idprodutos) 
            WHERE pdci_idproducao = {$idproducao}";
	    $resFormulas = $this->db->consultar($sqlFormulas);
	    $tabelaItens .= '<table class="table">';
		$tabelaItens .= '<tr>';
		$tabelaItens .= '<td width="10%">Cod.</td>';
		$tabelaItens .= '<td>Nome</td>';
		$tabelaItens .= '<td width="15%">Qte</td>';
		$tabelaItens .= '<td width="15%">Perca(%)</td>';
		$tabelaItens .= '<td width="15%">Perca</td>';
		$tabelaItens .= '</tr>';  
	    foreach ($resFormulas as $regFormulas) {
	        $tabelaItens .= "<tr id='itemFormula_" . $regFormulas["idprodutos_formulas"] . "'>";
		    $tabelaItens .= "<td>{$regFormulas["idprodutos"]}</td>";
		    $tabelaItens .= "<td>{$regFormulas["prod_nome"]}</td>";
		    $tabelaItens .= "<td>{$regFormulas["pdci_qte"]}</td>";
		    $tabelaItens .= "<td>{$regFormulas["pdci_perca"]}%</td>";
		    $tabelaItens .= "<td>{$regFormulas["pdci_qte_perca"]}</td>";
		    $tabelaItens .= "</tr>"; 
	    }
		$tabelaItens .= '</table>';
		return $tabelaItens;
	}
	
    public function insereItens($idproducao){
        // deleta os itens para inserir novamente
        $sql = "DELETE FROM producao_itens WHERE pdci_idproducao = {$idproducao}";
        $this->db->executaSQL($sql);
		// 
		$sql = "SELECT * FROM producao WHERE idproducao = {$idproducao}";
		$reg = $this->db->retornaUmReg($sql);
		//
		$sql = "SELECT  *
				FROM produtos_formulas 
				WHERE pfor_idproduto_final = {$reg['pdc_idprodutos']}";
		$res = $this->db->consultar($sql);
		foreach($res as $regForm){
			$qte = $regForm['pfor_qte'] * $reg['pdc_qte_produzida'];
			$perca_qte = ($regForm['pfor_qte'] * $regForm['pfor_porc_perca']) / 100;
			$sql = "INSERT INTO producao_itens (pdci_idproducao,
											pdci_idprodutos,
											pdci_qte,
											pdci_perca,
											pdci_qte_perca)
									VALUES( {$idproducao},
											{$regForm['pfor_idprodutos']},
											{$qte},
											{$regForm['pfor_porc_perca']},
											{$perca_qte})";
			$this->db->executaSQL($sql);
		}
    }
}
?>
