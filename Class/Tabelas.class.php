<?php

class Tabelas{

	public function geraTabelaTel($res){
    	$tabela = "<table class='table' width='100%' style='margin-top: 3px;'>";
	    foreach ($res as $reg) {
	     $tabela .= '<tr>';
	     $tabela .= '<td width="80px">(' . trim($reg['pnum_DDD']) . ')</td>
	        <td>' . $reg['pnum_numero'] . '</td>
	        <td width="20px;" align="left" id="btnExcluiTelefone_' . $reg['idpessoas_numeros'] . '"><img src="../icones/excluir.png" onclick="excluirTelefone(' . $reg['idpessoas_numeros'] . ')" style="cursor:pointer;"></td>
	      </tr>';
	  	}
	  $tabela .= "</table>";
	  //
	  //escreve a tabela
	  echo $tabela;
	}

	public function geraTabelaPes($res, $db){
		$linhaColorida = false;
	    $tabela = "<div class='tableBusca' style='max-height: 350px; overflow: auto;'>";
	    //
	    if(empty($res)){
	    	$tabela .= "Nenhum registro encontrado!";
	    	$escondeTabela = "display: none;";
	    }
	    //
		$tabela .= "<table class='table' style='margin-top: 3px;{$escondeTabela}'>";
		$tabela .= "<thead>";
			$tabela .= "<tr class='bg-primary'>";
				$tabela .= "<td class='d-none d-sm-table-cell'><b>Código<b></td>";
				$tabela .= "<td><b>Nome<b></td>";
				$tabela .= "<td class='d-none d-sm-table-cell'><b>Endereço<b></td>";
				$tabela .= "<td><b>Cidade<b></td>";
			$tabela .= "</tr>";
		$tabela .= "</thead>";
	    foreach ($res as $reg) {
	    	//
	    	//Monta o nome da cidade com a sigla do estado
	    	$cidadeEstado = '';
	    	if(!empty($reg['idcidades'])){
	    		$cidadeEstado = $reg['cid_nome'] . " - " . $reg['est_uf'];
	    	}
	    	//
	      $tabela .= '<tr';
				if ($linhaColorida) {$tabela .= " class='info'";}
				$tabela .= ' onclick="abreCadastro(' . $reg['idpessoas'] . ', \'pessoas_edita.php\')" style="cursor:pointer" id="linhasBusca">
	        <td width="6%" class="d-none d-sm-table-cell">' . $reg['idpessoas'] . '</td>
	        <td>' . $reg['pess_nome'] . ' <br> <i style="font-size: 13px;">' . $db->retornaUmTel($reg['idpessoas']) . '</i> </td>
	        <td class="d-none d-sm-table-cell">' . $reg['pess_endereco'] . '</td>
	        <td>' . $cidadeEstado . '</td>
	      </tr>';
			if ($linhaColorida) {
		    $linhaColorida = false;
		  }else {
		    $linhaColorida = true;
		  }
	  }
	  $tabela .= "</table></div>";
	  //
	  //escreve a tabela
	  return $tabela;
	}
	
	public function geraTabelaBusca($res, $db, $colunas, $link, $cabecalho = ''){
		$linhaColorida = false;
	    $tabela = "<div class='tableBusca' style='max-height: 250px; overflow: auto;'>";
	    if(empty($res)){
			$tabela .= "Nenhum registro encontrado!";
			$tabela .= "</div>";
			//
			//escreve a tabela
			return $tabela;
	    }
		$tabela .= "<table class='table' {$escondeTabela}' >";
		if(!empty($cabecalho)){
			$tabela .= "<thead>";
				$tabela .= "<tr class='bg-primary'>";
				foreach($cabecalho as $titulo => $config){
					$tabela .= "<td {$config}><b>{$titulo}<b></td>";
				}
				$tabela .= "</tr>";
			$tabela .= "</thead>";
		}
		foreach($res as $reg){
			//
			$primeiraLinha = true;
			foreach ($colunas as $coluna => $configuracoes) {
				if($primeiraLinha){
					$tabela .= "<tr onclick=\"abreCadastro({$reg[$coluna]}, '{$link}')\" style='cursor:pointer' id='linhasBusca'>";
					$primeiraLinha = false;
				}
				$tabela .= "<td {$configuracoes}>{$reg[$coluna]}</td>";
			}
			$tabela .- "</tr>";
		}
		$tabela .= "</table>";
		$tabela .= "</div>";
		//
	  	//escreve a tabela
		return $tabela;
	}

	public function geraTabelaPadrao($res, $db, $colunas, $cabecalho = '', $campoTotal = '', $util = ''){
		$linhaColorida = false;
	    if(empty($res)){
	    	$tabela .= "Nenhum registro encontrado!";
	    	$escondeTabela = "display: none;";
	    }
		$tabela .= "<table class='table text-dark border-dark' style='margin-top: 3px;{$escondeTabela}' >";
		if(!empty($cabecalho)){
			$tabela .= "<thead>";
				$tabela .= "<tr>";
				foreach($cabecalho as $titulo => $config){
					$tabela .= "<td class='bg-primary' {$config}><b>{$titulo}<b></td>";
				}
				$tabela .= "</tr>";
			$tabela .= "</thead>";
		}
		$total = 0;
		foreach ($res as $reg) {
		
			//
			$primeiraLinha = true;
			foreach ($colunas as $coluna => $tamanho) {
				if($primeiraLinha){
					$tabela .= "<tr>";
					$primeiraLinha = false;
				}
				$tabela .= "<td {$tamanho}>{$reg[$coluna]}</td>";
			}
			$tabela .- "</tr>";
			if(!empty($campoTotal)){
				$total += $reg[$campoTotal];
			}
		}
		if(!empty($campoTotal)){
			$tabela .= "<tr><td colspan='" . (count($colunas) - 1) . "' align='right'><b>Total...</b></td><td align='right'><b>" . $util->formataMoeda($total) . "</b></td></tr>";
		}
		$tabela .= "</table>";
		//
	  	//escreve a tabela
		return $tabela;
	}
}
?>
