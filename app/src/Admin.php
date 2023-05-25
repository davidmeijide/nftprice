<?php
include_once('Connection.php');

class Admin{
    
    static function getUsers(){
        $connection = new Connection();
        $pdoStatement = $connection->prepare("SELECT username, creation_date, last_login, telegram_id
                                                FROM users");
        $pdoStatement->execute();
        return json_encode($pdoStatement->fetchAll(PDO::FETCH_OBJ));
    }
    
}