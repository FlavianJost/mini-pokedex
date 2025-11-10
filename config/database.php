<?php 
    namespace App;
    use PDO;
    use PDOException;
    class Database{
        protected PDO $pdo;
        protected string $dbname = 'pokedex';
        protected string $user = 'root';
        protected string $password = '';

        public function __construct() {
            try{
                $this->pdo=new PDO("mysql:host=127.0.0.1;dbname=$this->dbname",$this->user,$this->password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }

        public function getConnection(): PDO {
            return $this->pdo;
        }
    }
?>