<?php 
    namespace App;
    use PDO;
    use PDOException;
    class Database{
        protected PDO $pdo;
        protected string $user = 'root';
        protected string $password = '';

        public function __construct() {
            try{
                $this->pdo=new PDO("mysql:host=127.0.0.1;dbname=pokedex",$this->user,$this->password,[
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                        PDO::ATTR_EMULATE_PREPARES => false
                ]);
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }

        public function getConnection(): PDO {
            return $this->pdo;
        }
    }
?>