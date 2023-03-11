<?php

class Connection extends PDO{
    
    private $dsn;
    public function __construct()
    {
        define('__ROOT__', dirname(dirname(__FILE__)));
        require_once(__ROOT__.'/private/config.php');


        $this->dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
        try{
            parent::__construct($this->dsn,$DB_USER,$DB_PASSWORD);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){
            die("Erro na conexión: ".$e->getMessage());
        }
    }
}

