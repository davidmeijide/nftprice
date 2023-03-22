<?php

class Connection extends PDO{
    
    private $dsn;
    public function __construct()
    {
        if(!defined('__ROOT__')) define('__ROOT__', dirname(dirname(__FILE__)));
        include(__ROOT__.'/private/config.php');

        $this->dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
        try{
            parent::__construct($this->dsn,$DB_USER,$DB_PASSWORD);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){
            die("Database connection error: ".$e->getMessage());
        }
    }
}

