<?php


include_once('Connection.php');
class Bot{
    function __construct(){
        
    }
    
    function sendMessage($chatId,$message){
        include('../private/API_KEYS.php');
        $url = "https://api.telegram.org/bot$TELEGRAM_API_KEY";
        $params=[
            'chat_id'=>$chatId, 
            'text'=>$message,
            'parse_mode'=>'HTML',
        ];
        $ch = curl_init($url . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_close($ch);
        $result = curl_exec($ch);
        return $result;
}
       
    function getUpdates(){
        include('../private/API_KEYS.php');
        $lastUpdateId = file_get_contents('../last_update.txt');
        $url = "https://api.telegram.org/bot$TELEGRAM_API_KEY/getUpdates?offset=$lastUpdateId";
        $httpOptions = [
            "http" => [
                "method" => "GET",
                "header" => 'Content-Type: application/json',
            ],
        ];
        $streamContext = stream_context_create($httpOptions);
        $jsonOutput = file_get_contents($url, false, $streamContext);
        $rawData = json_decode($jsonOutput, true);
        if (count($rawData['result'])>0){
            $lastUpdateId = $rawData['result'][count($rawData['result'])-1]['update_id'] +1;
            file_put_contents('../last_update.txt', $lastUpdateId);
        }
        return $rawData;
    }
    public function processUpdates($update){
        if ($update["ok"] != 1 || count($update['result']) == 0) return; //End function if update failed or there are no messages
        foreach($update['result'] as $update){ //Each update may have multiple messages
            $chatId = $update['message']['from']['id'];
            $text = $update['message']['text'];
            switch($text){
                case "/start":
                    $this->sendMessage($chatId, "Your code is: <b>$chatId</b>"); // Sends user's telegram id for account linking

            }

        }
        return json_encode(["response"=>true]);

        
    }
    public function linkTelegram($username, $telegram_id){
        $connection = new Connection();
        $pdoStatement = $connection->prepare("UPDATE users SET telegram_id = :telegram_id
                                                WHERE username LIKE :username");
        $pdoStatement->bindParam(':telegram_id',$telegram_id);
        $pdoStatement->bindParam(':username',$username);
        $pdoStatement->execute();
        return true;
    }
    
    /* Returns the collection's floor price if the alert price is  */
    public function checkCollection($symbol, $alertPrice, $compare){
        $symbol = str_replace(" ","_",$symbol);
        $url = "https://api-mainnet.magiceden.dev/v2/collections/$symbol/stats";
        $httpOptions = [
            "http" => [
                "method" => "GET",
                "header" => 'Content-Type: application/json',
            ],
        ];
    
        $streamContext = stream_context_create($httpOptions);
        $jsonOutput = file_get_contents($url, false, $streamContext);
        $data = json_decode($jsonOutput, true);
        $floorPrice = round($data['floorPrice']/1000000000,2);
        if($compare == 'lower' && $floorPrice<=$alertPrice){
            return $floorPrice;
        } 
        elseif($compare == 'greater' && $floorPrice>=$alertPrice) return $floorPrice;
        else return false;
    
    }
  
    function getWatchList($username){
        $conection = new Connection();
        $pdoStatement = $conection->prepare("SELECT * FROM alerts 
        JOIN(
        SELECT fk_symbol_listed, MIN(price) AS floorPrice FROM listed_tokens WHERE listed = 1
                    GROUP BY fk_symbol_listed) AS floorPriceCalc
                    ON alerts.symbol = floorPriceCalc.fk_symbol_listed
                    WHERE fk_username LIKE ?");

        $pdoStatement->bindParam(1,$username);
        $pdoStatement->execute();
        return json_encode($pdoStatement->fetchAll(PDO::FETCH_ASSOC));
    
    }
    function getAllWatchLists(){
        $conection = new Connection();
        $pdoStatement = $conection->prepare("SELECT * FROM alerts");
        $pdoStatement->execute();
        return json_encode($pdoStatement->fetchAll(PDO::FETCH_ASSOC));
    
    }
    //Gets all the watchlists that have a different symbol and that are active.
    //This way we know which collections to update
    function getAllDistinctWatchLists(){
        $conection = new Connection();
        $pdoStatement = $conection->prepare("SELECT * FROM (
            SELECT b.*,
            ROW_NUMBER() OVER (PARTITION BY symbol) as num
            FROM alerts b
            WHERE active=1
            
         ) tbl
         WHERE num = 1");
        $pdoStatement->execute();
        return json_encode($pdoStatement->fetchAll(PDO::FETCH_ASSOC));
    
    }

    function getFloorPrice($symbol){
        $symbol = str_replace(" ","_",$symbol);
        $url = "https://api-mainnet.magiceden.dev/v2/collections/$symbol/stats";
        $httpOptions = [
            "http" => [
                "method" => "GET",
                "header" => 'Content-Type: application/json',
            ],
        ];
    
        $streamContext = stream_context_create($httpOptions);
        $jsonOutput = file_get_contents($url, false, $streamContext);
        $rawData = json_decode($jsonOutput, true);
        $floorPrice = round($rawData['floorPrice']/1000000000,2);
        return $floorPrice;
    }

    function getPortfolio($username){
        $connection = new Connection();
        $pdoStatement = $connection->prepare("SELECT * FROM portfolio
                                    WHERE fk_username LIKE :username");
        $pdoStatement->bindParam(':username',$username);
        $pdoStatement->execute();
        return json_encode($pdoStatement->fetchAll(PDO::FETCH_ASSOC));
    
    }

    function isTelegramLinked($username){
        $connection = new Connection();
        $pdoStatement = $connection->prepare("SELECT telegram_id FROM users WHERE username LIKE :username AND NOT(telegram_id IS NULL OR telegram_id = '')");
        $pdoStatement->bindParam(":username",$username);
        $pdoStatement->execute();
        return $pdoStatement->rowCount();
    }

    function sendTestAlert($username){
        $connection = new Connection();
        $pdoStatement = $connection->prepare("SELECT telegram_id FROM users WHERE username LIKE :username");
        $pdoStatement->bindParam(":username",$username);
        $pdoStatement->execute();

        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        $this->sendMessage($result['telegram_id'], "This is a test message. Your Telegram is correctly linked!");
    }

    public function checkCollection2($symbol, $alertPrice, $compare, $attributes){
        $symbol = str_replace(" ","_",$symbol);
        $connection = new Connection();
        $compareSign = $compare=="lower"?"<=":">=";
        $minmax = $compare=="lower"?"MIN":"MAX";
        $query = "SELECT :minmax(price) FROM listed_tokens
                    WHERE fk_symbol_listed LIKE :symbol
                    AND listed = 1
                    AND :alert_price :compare price
                    AND token_traits LIKE :attributes";
                    
        $pdoStatement = $connection->prepare($query);
        $pdoStatement->bindParam(":minmax",$minmax);
        $pdoStatement->bindParam(":symbol",$symbol);
        $pdoStatement->bindParam(":alert_price",$alertPrice);
        $pdoStatement->bindParam(":compare",$compareSign);
        $attributes = "%.$attributes.%";
        $pdoStatement->bindParam(":attributes",$attributes);
        $pdoStatement->execute();
        $floorPrice = $pdoStatement->fetchAll(PDO::FETCH_OBJ);
        


        
        if($compare == 'lower' && $floorPrice<=$alertPrice){
            return $floorPrice;
        } 
        elseif($compare == 'greater' && $floorPrice>=$alertPrice) return $floorPrice;
        else return false;
    
    }



}

//$bot = new Bot();
//echo $bot->checkCollection2("degods",150,"lower",'');


/* $updates = $bot->getUpdates();
echo "<pre>";
print_r($updates);
echo "</pre>";
$bot->processUpdates($updates); */