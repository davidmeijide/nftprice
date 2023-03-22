<?php
include_once('Connection.php');

class WatchList{
    private $items;
    public function __construct($items){
        $this->items = $items;
    }

    public function activateAlert($id_alert){
        try{
            $conection = new Connection();
            $pdoStatement = $conection->prepare("UPDATE alerts SET active = 1 WHERE id_alert = :id_alert");
            $pdoStatement->bindParam(':id_alert',$id_alert);
            $pdoStatement->execute();
            return true;
        }
        catch(PDOException $e){
            die('Database error at alert activation: '.$e->getMessage());
        }
    }
    public function deactivateAlert($id_alert){
        try{
            $conection = new Connection();
            $pdoStatement = $conection->prepare("UPDATE alerts SET active = 0 WHERE id_alert = :id_alert");
            $pdoStatement->bindParam(':id_alert',$id_alert);
            $pdoStatement->execute();
            return true;
        }
        catch(PDOException $e){
            die('Database error at alert deactivation: '.$e->getMessage());
        }
    }

    public function addAlert($symbol, $compare, $price, $currency, $duration, $magnitude, $attributes){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("INSERT INTO alerts(symbol, fk_username, active, compare, floor_price, expiry_date, currency, token_traits) 
                                                VALUES(:symbol, :fk_username, :active, :compare, :floor_price, :expiry_date, :currency, :token_traits)");
            $expiry_date = time() + (($magnitude=="days"?24:1) * 60*60 * $duration);
            $expiry_date = date("Y-m-d H:i:s",$expiry_date);
            $active = 1;

            $pdoStatement->bindParam(':symbol',$symbol);
            $pdoStatement->bindParam(':fk_username',$_SESSION['username']);
            $pdoStatement->bindParam(':active',$active);
            $pdoStatement->bindParam(':compare',$compare);
            $price = $price * 1000000000;
            $pdoStatement->bindParam(':floor_price',$price);
            $pdoStatement->bindParam(':expiry_date',$expiry_date);
            $pdoStatement->bindParam(':currency',$currency);

            $attributes = implode(",",json_decode($attributes));
            $pdoStatement->bindParam(':token_traits',$attributes);
            
            $pdoStatement->execute();
            return true;
        }
        catch(PDOException $e){
            die('Database error at alert creation: '.$e->getMessage());
        }
    }
    
    public function setAlertPrice($id, $floor_price){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("UPDATE alerts SET floor_price = :floor_price WHERE id_alert = :id_alert");
            $pdoStatement->bindParam(':id_alert', $id);
            $floor_price = $floor_price * 1000000000;
            $pdoStatement->bindParam(':floor_price', $floor_price);
            $pdoStatement->execute();
        }
        catch(PDOException $e){
            die('Database error at alert update: '.$e->getMessage());
        }

    }

    public function removeAlert($id){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("DELETE FROM alerts WHERE id_alert = :id_alert");
            $pdoStatement->bindParam(':id_alert', $id);
            $pdoStatement->execute();
        }
        catch(PDOException $e){
            die('Database error at alert delete: '.$e->getMessage());
        }
    }

    public function getWatchlistFloorPrice($username){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("SELECT fk_symbol_listed as symbol, MIN(price) as floorPrice 
                                                FROM listed_tokens
                                                WHERE listed = 1 
                                                AND fk_symbol_listed IN (SELECT symbol FROM alerts 
                                                WHERE fk_username LIKE ?)
                                                GROUP BY fk_symbol_listed
            
            ");
            $pdoStatement->bindParam(1,$username);
            $pdoStatement->execute();
            return $pdoStatement->fetch(PDO::FETCH_ASSOC);
            
        }
        catch(PDOException $e){
            die('Error at attribute insertion: '.$e->getMessage());
        }
    }
}
/* $w = new WatchList("");
print_r($w->getWatchlistFloorPrice('david'));
 */
