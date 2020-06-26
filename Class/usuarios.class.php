<?php
   Class Usuarios{
       private $id;
       private $idgrupos_acessos;
       private $db;
       private $util;

    function __construct($db, $util, $id, $idgrupos_acessos){
        $this->id = $id;
        $this->idgrupos_acessos = $idgrupos_acessos;
        $this->db = $db;
        $this->util = $util;
    }

    public function usuario_pode_executar($prog_file = ''){
        //Grupo um sempre retorna true
        if($this->idgrupos_acessos == 1){
            return true;
        }
        
        if(empty($prog_file)){
            $prog_file = basename($_SERVER['PHP_SELF']);
        }

        $sql = "SELECT gap_executa 
                FROM grupos_acessos_programas 
                    JOIN programas ON (gap_idprogramas = idprogramas)
                WHERE gap_idgrupos_acessos = " . $this->idgrupos_acessos . " 
                AND prog_file = " . $this->util->sgr($prog_file);
        $reg = $this->db->retornaUmReg($sql);
        if($reg['gap_executa'] == 1){
            return true;
        }else{
            return false;
        }
    }
   }
?>