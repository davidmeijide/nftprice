<?php


include_once('Connection.php');

class Token{
    private $token_id;
    private $symbol;
    private $name;
    private $owner;
    private $price;

    public function __construct($token_id, $symbol, $name, $owner, $price){
        $this->token_id = $token_id;
        $this->symbol = $symbol;
        $this->name = $name;
        $this->owner = $owner;
        $this->price = $price;
    }
    public function getTokenInfo($address){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("SELECT * FROM listed_tokens WHERE listed = 1 AND token_id LIKE :address");
            $pdoStatement->bindParam(':address',$address);
            $pdoStatement->execute();
            return $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e){
            die('Error at token select query: '.$e->getMessage());
        }
    }

    public function updateTokenInfo($data){
        try{            
            $connection = new Connection();
            $pdoStatement = $connection->prepare("UPDATE listed_tokens SET
                                                owner = null, price = :price, listed = 1
                                                WHERE token_id LIKE :token_id");
            foreach($data as $row){
                $pdoStatement->bindParam(':token_id', $row['tokenMint']);
                $price = $row['price']*1000000000;
                $pdoStatement->bindParam(':price', $price);
                $pdoStatement->execute();
            }
            return true;
        }
        catch(PDOException $e){
            die('Error at token update: '.$e->getMessage());
        }  
    }

    public function insertTokenInfo($address, $symbol, $name, $rarity=null, $owner=null, $price=null){
        try{            
            $connection = new Connection();
            $pdoStatement = $connection->prepare("INSERT INTO listed_tokens(token_id, fk_symbol_listed, name, owner, price, rarity, listed)
                                                VALUES(:token_id, :fk_symbol_listed, :name, :owner, :price, :rarity, 1)
                                                ON DUPLICATE KEY UPDATE
                                                owner = :owner, price = :price, rarity = :rarity, listed = 1");
            $pdoStatement->bindParam(':token_id', $address);
            $pdoStatement->bindParam(':fk_symbol_listed', $symbol);
            $pdoStatement->bindParam(':name', $name);
            $pdoStatement->bindParam(':owner', $owner);
            $pdoStatement->bindParam(':price', $price);
            $pdoStatement->bindParam(':rarity', $rarity);

            $pdoStatement->execute();
            return true;
        }
        catch(PDOException $e){
            die('Error at token insertion: '.$e->getMessage());
        }
    }

    public function setToUnlistedAll($symbol){
        $connection = new Connection();
        $preQuery = $connection->prepare("SELECT * FROM listed_tokens WHERE fk_symbol_listed LIKE :symbol");
        $preQuery->bindParam(':symbol',$symbol);
        $preQuery->execute();
        if($preQuery->rowCount()==0) return false;

        $pdoStatement = $connection->prepare("UPDATE listed_tokens SET listed = 0 WHERE fk_symbol_listed LIKE :symbol");
        $pdoStatement->bindParam(':symbol',$symbol);
        $pdoStatement->execute();
        return true;
    }

    public function getListedTokens($symbol){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("SELECT * FROM listed_tokens WHERE (listed = 1 AND fk_symbol_listed LIKE :symbol)");
            $pdoStatement->bindParam(':symbol',$symbol);
            $pdoStatement->execute();
            return $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e){
            die('Error at selecting tokens: '.$e->getMessage());
        }
    }
    /* Returns all the NFT's listed of the given collection */
    public function apiGetListedTokens($symbol){
        $tokens = []; 
        $offset = 0;
        $lastCount = 20;
        /* while(count($rawData)==20); */
        while($lastCount==20){
            $url = "https://api-mainnet.magiceden.dev/v2/collections/$symbol/listings?offset=$offset&limit=20";
            $httpOptions = [
                "http" => [
                    "method" => "GET",
                    "header" => 'Content-Type: application/json',
                ],
            ];
        
            $streamContext = stream_context_create($httpOptions);
            $jsonOutput = file_get_contents($url, false, $streamContext);
            $rawData = json_decode($jsonOutput, true);

            $tokens = array_merge($tokens,$rawData);
            $offset+=20;
            $lastCount = count($rawData);
            // Wait 0.55 seconds
            usleep(550000);
        }

        return $tokens;
    }

    public function apiGetTokenInfo($address){
        $url = "https://api-mainnet.magiceden.dev/v2/tokens/$address";
        $httpOptions = [
            "http" => [
                "method" => "GET",
                "header" => 'Content-Type: application/json',
            ],
        ];
    
        $streamContext = stream_context_create($httpOptions);
        $jsonOutput = file_get_contents($url, false, $streamContext);
        $rawData = json_decode($jsonOutput, true);
        return $rawData;
    }

    
    
}





