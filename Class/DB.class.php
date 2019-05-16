<?php
	class Db {
		private $host;
		private $porta;
		private $usudb;
		private $nomedb;
		private $senhadb;
		private $conexao;
		private $tabela;


/* não é seguro fazer desse jeito, sempre mudar  a senha e o usuario nos seus projetos empresariais */

		function __construct($host, $porta, $usudb, $senhadb, $nomedb){
			$this->host = $host;
			$this->porta = $porta;
			$this->nomedb = $nomedb;
			$this->usudb = $usudb;
			$this->senhadb = $senhadb;


		}

		public function conectar(){
			try{	
				$dados = "mysql:host=" . $this->host;
				$dados = $dados . ";port=" . $this->porta;
				$dados = $dados . ";dbname=" . $this->nomedb;
				$this->conexao = new PDO($dados, $this->usudb, $this->senhadb);   // pdo php data objectve, classe generica pra banco de dados
			}catch(PDOException $e){
				if(preg_match('/Unknown database/', $e->getMessage())){
					//Cria o banco e suas tabelas
					$this->criarBase();
				}else{
					echo 'ERROR: ' . $e->getMessage();
				}
			}
		}

		public function setTabela($tabela){
			$this->tabela = $tabela;
		}

		//função antiga para fazer login
		public function buscarUsuario($login){
			if (empty($login) && empty($id)) {
				return;
			}
			$sql = "SELECT * FROM " . $this->tabela . " WHERE ";
			//define se vai buscar pelo login, pelo id ou pelos dois
			if (!empty($login)) {
				$sql .= "pess_usuario = '" . $login . "' ";
			}
			if (!empty($login) && !empty($id)) {
				$sql .= " AND ";
			}
			if (!empty($id)) {
				$sql .= "idpessoas = '" . $id . "' ";
			}
			// hibrido da linguagem php com sql
			$query = $this->conexao->query($sql);
			$query->execute();
			$resultado = $query->fetch(PDO::FETCH_ASSOC);  // fetch = recuperação do resultado
			return $resultado;
		}

		public function getUltimoID(){
			$nomeID = 'id'. $this->tabela;
			$sql = "SELECT " . $nomeID . " FROM " . $this->tabela . " ORDER BY " . $nomeID . " DESC LIMIT 1";
			$query = $this->conexao->query($sql);
			$query->execute();
			$resultado = $query->fetch(PDO::FETCH_ASSOC);  // fetch = recuperação do resultado
			return $resultado[$nomeID];
		}

		public function consultar($where="", $campos="*"){
			$sql = "SELECT " . $campos . " FROM " . $this->tabela;
			if($where != ""){
				$sql = $sql . " WHERE " . $where;
			}
			//echo $sql;
			return $this->executaSQL($sql);
		}

		public function retornaUmReg($where="", $campos="*"){
			$sql = "SELECT " . $campos . " FROM " . $this->tabela;
			if($where != ""){
				$sql = $sql . " WHERE " . $where;
			}
			//echo $sql;
			$sql = trim($sql);
			//echo $sql;
			try{
				$this->conexao->beginTransaction();
				$resultado=$this->conexao->query($sql);
				$this->conexao->commit();
			}
			catch(PDOException $e) {
				$this->conexao->rollBack();
				$resultado = NULL;
				$mensagem  = $e->getMessage();
				file_put_contents("erro.log", $mensagem);
			}
			 if ($resultado){
			   	$dados = $resultado->fetch(PDO::FETCH_ASSOC);
			  }
			 return $dados;
		}

    public function executaSQL($sql){
		  $dados = array();
		  $sql = trim($sql);
		  //echo $sql;
		  try{
			$this->conexao->beginTransaction();
			$resultado=$this->conexao->query($sql);
			$this->conexao->commit();
			}
		  catch(PDOException $e) {
			$this->conexao->rollBack();
			$resultado = NULL;
			$mensagem  = $e->getMessage();
			file_put_contents("erro.log", $mensagem);
			}
		  if ($resultado){
		   while($row=$resultado->fetch(PDO::FETCH_ASSOC))
			{
			  $dados[] = $row;
			}
		  }
		  return $dados;

	  	}

		 public function gravar($dados = null){
			$campos   = implode(",",array_keys($dados));
			$valores  = implode(",",array_values($dados));
			$query = "INSERT INTO " . $this->tabela . " (" .
					  $campos." ) VALUES ( " . $valores . " ) ";
			//echo "$query<br>";
			//exit;
			return $this->executaSQL($query);
		 }

		 public function alterar($where = null,
								  $dados = null){
		  if(!is_null($where)){
			  $valores = array();
			  foreach($dados as $key=>$value){
				$valores[] = $key . " = " . $value;
			  }
			  $valores = implode(',',$valores);
			  $query = "UPDATE ".$this->tabela." SET ".
						$valores." WHERE ".$where;
		      //echo "$query<br>";
			  return $this->executaSQL($query);
		  }
		  else {
				 return false;
			   }
		}

		public function excluir($where = null){
				if(!is_null($where)){
					$query = "DELETE FROM " . $this->tabela . " WHERE " . $where;
					return $this->executaSQL($query);
				}
				else{
					return false;
				}
			}

		public function retornaUmTel($idpessoas){
			$sql = "SELECT * FROM pessoas_numeros WHERE pnum_idpessoas = " . $idpessoas . " LIMIT 1";
			$res = $this->conexao->query($sql);
			$res->execute();
			$reg = $res->fetch(PDO::FETCH_ASSOC);
			if ($reg['pnum_DDD'] != "") {
				$telefone = "(" . $reg['pnum_DDD'] . ") " .  $reg['pnum_numero'];
			}else{
				$telefone = $reg['pnum_numero'];
			}

			return $telefone;
		}

		private function criaBanco($nomeBD, $host, $user){
			$sql = "CREATE DATABASE IF NOT EXISTS `{$nomeBD}`
			DEFAULT character SET UTF8
			DEFAULT collate utf8_bin;
			GRANT ALL ON `{$nomeBD}`.* TO '{$user}'@'{$host}';
    		FLUSH PRIVILEGES;";
    		if(!$this->conexao->exec($sql)){
    			echo 'Falha ao criar banco de dados!';
    		}
		} 

		private function criaTabelas($nomeBD){
			//
			//Crias as tabelas do banco de dados
			$sql = file_get_contents("../_BD/tabelas_sistema.sql");
			$sql = utf8_decode($sql);
			if($this->conexao->exec($sql)){
    			echo 'Falha ao criar as tabelas!';
    			exit;
    		}
		}

		private function criarBase(){
			//Conecta ao servidor e cria a base
			$dados = "mysql:host=" . $this->host;
			$dados = $dados . ";port=" . $this->porta;
			$this->conexao = new PDO($dados, $this->usudb, $this->senhadb);   // pdo php data objectve, classe generica pra banco de dados
			$this->criaBanco($this->nomedb, $this->host, $this->usudb);
			//
			//conecta ao banco e cria as tabelas
			$dados = $dados . ";dbname=" . $this->nomedb;
			$this->conexao = new PDO($dados, $this->usudb, $this->senhadb);   // pdo php data objectve, classe generica pra banco de dados
			$this->criaTabelas($this->nomedb);
			//
			//Retorna mensagem e pede login novamente
			$_SESSION['logado'] = false;
			$_SESSION['mensagem'] = "Executamos uma atualização do sistema <br> Tente novamente";
    		$_SESSION['tipoMsg'] = "info";
			header('Location: ../index.php');
			exit;
		}
	}


?>
