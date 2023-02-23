<?php
class Connection extends PDO{
    private $host = "db-pdo";
    private $db = "mebot";
    private $user = "root";
    private $pass = "root";
    private $dsn;

    public function __construct()
    {
        $this->dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
        try{
            parent::__construct($this->dsn,$this->user,$this->pass);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){
            die("Erro na conexión: ".$e->getMessage());
        }
    }
}

