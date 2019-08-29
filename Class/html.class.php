<?php

class html{
	private $db;
	private $util;

	function __construct($db, $util){
		$this->db = $db;
		$this->util = $util;
	}

	public function criaSelectSql($campoMostra, $campoValue, $nameInput, $valueReg, $sql, $class = "", $js = "", $geraZero = true){
		//
		$res = $this->db->consultar($sql);
		//
		$comboBox = "<select name='{$nameInput}' id='{$nameInput}' class='{$class}' {$js}>";
			if($geraZero) $comboBox .= "<option value=''>---</option>";
			foreach ($res as $reg) {
				$selected = "";
				if($reg[$campoValue] == $valueReg) $selected  = "selected='selected'";
				$comboBox .= "<option value='{$reg[$campoValue]}' {$selected}>{$reg[$campoMostra]}</option>";
			}
		$comboBox .= "</select>";
		//
		return $comboBox;
	}
}
?>
