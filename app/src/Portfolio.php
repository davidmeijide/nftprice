<?php
include_once('Connection.php');

class Portfolio{
    private $items;
    public function __construct($items){
        $this->items = $items;
    }
    public function addItem($symbol, $purchase_price, $currency, $amount_owned, $dollars_per_coin){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("INSERT INTO portfolio(symbol, purchase_price, currency, amount_owned, dollars_per_coin, fk_username) 
                                            VALUES(:symbol, :purchase_price, :currency, :amount_owned, :dollars_per_coin, :fk_username)");
            $pdoStatement->bindParam(':symbol',$symbol);
            $pdoStatement->bindParam(':fk_username',$_SESSION['username']);
            $pdoStatement->bindParam(':purchase_price',$purchase_price);
            $pdoStatement->bindParam(':currency',$currency);
            $pdoStatement->bindParam(':amount_owned',$amount_owned);
            $pdoStatement->bindParam(':dollars_per_coin',$dollars_per_coin);
            $pdoStatement->execute();
            return true;
        }
        catch(PDOException $e){
            die('Database error at portfolio item insertion: '.$e->getMessage());
        }
    }

    public function removeItem($id_portfolio){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("DELETE FROM portfolio WHERE id_portfolio = :id_portfolio");
            $pdoStatement->bindParam(':id_portfolio', $id_portfolio);
            $pdoStatement->execute();
            return true;
        }
        catch(PDOException $e){
            die('Database error at alert delete: '.$e->getMessage());
        }
        
    }
}