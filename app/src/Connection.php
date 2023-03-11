<?php

class Connection extends PDO{

    private $db, $host, $user, $pass, $dsn;
    
    public function __construct()
    {
        include_once('../private/config.php');
        $this->db = $DB_NAME;
        $this->host = $DB_HOST;
        $this->user = $DB_USER;
        $this->pass = $DB_PASSWORD;


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

