<?php
class Usuario{
   private $login;
   private $senha;



   function __construct($login, $senha){
       $this->login = $login;
       $this->senha = $senha;
   }
   public function conferirSenha($db){
        $resultado = false;
        $dados = $db->buscarUsuario($this->login);
        if($dados){
            if($this->senha == $dados['pess_senha'])
                $resultado['retorno'] = true;
                $resultado['idpessoas'] = $dados['idpessoas'];
        }
        return $resultado;
      }

   public function testaLogin($db, $util, $novoUser, $idpessoas){
      $db->setTabela("pessoas");
      $sql = "SELECT * FROM pessoas WHERE pess_usuario = " . $util->sgr($novoUser) . " AND idpessoas <> " . $idpessoas;
      $res = $db->consultar($sql);
      foreach ($res as $reg) {
        if($reg['idpessoas'] > 0){
          $existe = true;
        }
      }
      //
      if($existe){
        $dados['existe'] = "true";
      }else{
        $dados['existe'] = "false"; 
      }
      //
      header('Content-Type: application/json');
      echo json_encode($dados);
      exit;
   }
 }

?>
