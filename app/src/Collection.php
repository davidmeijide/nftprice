<?php
ini_set('memory_limit', '256M');

include_once('Connection.php');
include_once('Token.php');
class Collection{
    private $symbol;
    public function __construct($symbol){
        $this->symbol = $symbol;
    }

    public function getCollectionInfo($symbol){
        try{            
            $connection = new Connection();
            $pdoStatement = $connection->prepare("SELECT * FROM collections WHERE symbol LIKE :symbol");
            $pdoStatement->bindParam(':symbol', $symbol);
   
            $pdoStatement->execute();
            return $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e){
            die('Error at token selection: '.$e->getMessage());
        }
    }
    public function insertCollection($symbol, $name, $description, $image, $listed_count, $avg_price, $total_volume){
        try{            
            $connection = new Connection();
            $pdoStatement = $connection->prepare("INSERT IGNORE INTO collections (symbol, name, description, image, listedCount, avgPrice24hr, totalVolume, lastUpdate)
            VALUES(:symbol, :name, :description, :image, :listed_count, :avg_price, :total_volume, :last_update)");
            $pdoStatement->bindParam(':symbol', $symbol);
            $pdoStatement->bindParam(':name', $name);
            $pdoStatement->bindParam(':description', $description);
            $pdoStatement->bindParam(':image', $image);
            $pdoStatement->bindParam(':listed_count', $listed_count);
            $pdoStatement->bindParam(':avg_price', $avg_price);
            $pdoStatement->bindParam(':total_volume', $total_volume);
            $last_update = date('Y-m-d H:i:s');
            $pdoStatement->bindParam(':last_update', $last_update);
            
   
            $pdoStatement->execute();
            return $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e){
            die('Error at token selection: '.$e->getMessage());
        }
        
    }

    public function updateCollectionInfo($symbol, $listed_count, $total_volume, $avg_price, $last_update){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("UPDATE collections 
            SET listedCount = :listed_count, totalVolume = :total_volume, avgPrice24hr = :avg_price, lastUpdate = :lastUpdate
            WHERE symbol LIKE :symbol");

            $pdoStatement->bindParam(':listed_count', $listed_count);
            $pdoStatement->bindParam(':total_volume', $total_volume);
            $pdoStatement->bindParam(':avg_price', $avg_price);
            $lastUpdate = date('Y-m-d H:i:s');
            $pdoStatement->bindParam(':lastUpdate', $lastUpdate);
            $pdoStatement->bindParam(':symbol', $symbol);
            $pdoStatement->execute();

        }
        catch(PDOException $e){
            die('Error at collection update: '.$e->getMessage());
        }
    }

    public function updateLastUpdate($symbol){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("UPDATE collections SET lastUpdate = :lastUpdate WHERE symbol LIKE :symbol");

            $lastUpdate = date('Y-m-d H:i:s');
            $pdoStatement->bindParam(':lastUpdate', $lastUpdate);
            $pdoStatement->bindParam(':symbol', $symbol);
            $pdoStatement->execute();

        }
        catch(PDOException $e){
            die('Error at date update: '.$e->getMessage());
        }
    }

    public function apiSearchCollection($symbol){
        $url = "https://api-mainnet.magiceden.dev/v2/collections/$symbol";
        $httpOptions = [
        "http" => [
        "method" => "GET",
        "header" => 'Content-Type: application/json',
            ],
        ];

        $streamContext = stream_context_create($httpOptions);
        $jsonOutput = file_get_contents($url, false, $streamContext);
        $rawData = json_decode($jsonOutput, true);
        return json_encode($rawData);

    }

    public function storeImage($src, $target, $newwidth, $newheight){
        try{
            $img = file_get_contents($src);
            $im = imagecreatefromstring($img);
            $width = imagesx($im);
            $height = imagesy($im);
            $thumb = imagecreatetruecolor($newwidth, $newheight);
    
            imagecopyresized($thumb, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    
            imagejpeg($thumb, $target.'.jpg'); //save image as jpg
    
            imagedestroy($thumb); 
    
            imagedestroy($im);
            return true;
        }
        catch(Exception $e){
            echo ("Image storage failed for $src: ".$e);
            
        }
        return false;
    }
    
    public function apiGetAllAttributes($symbol){
        $url = "https://api.howrare.is/v0.1/collections/$symbol";
        $httpOptions = [
            "http" => [
            "method" => "GET",
            "header" => 'Content-Type: application/json',
                ],
            ];
    
        $streamContext = stream_context_create($httpOptions);
        $jsonOutput = @file_get_contents($url, false, $streamContext);
        return $jsonOutput;
    }

    public function insertTypes($symbol, $json){
       try{

           $data =  json_decode($json, true);  
           $data = ($data['result']['data']['items'][0]['attributes']);
           $connection = new Connection();
           $sqlInsertTypes = "INSERT IGNORE INTO trait_types (id_trait, trait_type, fk_symbol)
           VALUES(?, ?, ?)";
           $stmt = $connection->prepare($sqlInsertTypes);
           foreach($data as $attribute){
                if($attribute['name']=='Attribute count') continue;
                $id_trait = $symbol . '_' . $attribute['name'];
                $row = array($id_trait, $attribute['name'],$symbol);
                $stmt->execute($row);
           }
       }
       catch(PDOException $e){
        die('Error at attribute type insertion: '.$e->getMessage());
    }
    }


    public function insertAllAttributes($symbol, $json){
        try{          
            $data =  json_decode($json, true);  
            $data = ($data['result']['data']['items']);
            $connection = new Connection();

            $sqlInsertTokenInfo = "INSERT IGNORE INTO listed_tokens(token_id, fk_symbol_listed, name, rarity, token_traits, listed)
            VALUES ";
            
            $sqlInsertValues = "INSERT IGNORE INTO trait_values (value_id, fk_trait_type, value)
            VALUES ";

            $sqlArrayTokenInfo = array();
            $paramArrayTokenInfo = array();

            $sqlArrayValues = array();
            $paramArrayValues = array();

            foreach($data as $token){
                //Token info 
                $sqlArrayTokenInfo[] = '(' . implode(',', array_fill(0, 5, '?')) . ',1)';

                $attr_values = []; // This array is filled with the pk from trait_values of a given token. Then inserted in listed_tokens
                foreach($token['attributes'] as $attribute){
                    if($attribute['name']=='Attribute count') continue;
                    //values table
                    $sqlArrayValues[] = '(' . implode(',', array_fill(0, 3, '?')) . ')';
                    $id_trait = $symbol . '_' . $attribute['name'];
                    $value_id = $id_trait . '_' . $attribute['value'];
                    array_push($paramArrayValues, $value_id, $id_trait,$attribute['value']);
                    array_push($attr_values, $value_id);
                }
                array_push($paramArrayTokenInfo, $token['mint'], $symbol, $token['name'],$token['rank'],(implode(",",$attr_values)));
                
            }

            //token info
            $sqlInsertTokenInfo .= implode(',', $sqlArrayTokenInfo);
            $stmt = $connection->prepare($sqlInsertTokenInfo);
            $stmt->execute($paramArrayTokenInfo);
            //values
            $sqlInsertValues .= implode(',', $sqlArrayValues);
            $stmt = $connection->prepare($sqlInsertValues);
            $stmt->execute($paramArrayValues);
                        
            return true;
        }
        catch(PDOException $e){
            die('Error at attribute insertion: '.$e->getMessage());
        }
    }

        
    public function apiGetAllMeAlias(){
        $url = "https://api.howrare.is/v0.1/collections";
        $httpOptions = [
            "http" => [
            "method" => "GET",
            "header" => 'Content-Type: application/json',
                ],
            ];
    
        $streamContext = stream_context_create($httpOptions);
        $jsonOutput = file_get_contents($url, false, $streamContext);

        $data = json_decode($jsonOutput,true)['result']['data'];
        $dict = array();
        foreach($data as $item){
            $howRareSymbol = substr($item['url'],1);
            $dict[$howRareSymbol] = $item['me_key'];
        }
        return $dict;
    }

    public function insertAllMeAlias($dict){
        try{
            $connection = new Connection();
            $sqlInsertValues = "UPDATE collections
            SET howrare_symbol = ? WHERE symbol LIKE ?";
            $stmt = $connection->prepare($sqlInsertValues);
            /* print_r($dict); */
            foreach($dict as $key => $value){
                $row = array($key,$value);
                $stmt->execute($row);
            }
            
        }
        catch(PDOException $e){
            die('Error at attribute insertion: '.$e->getMessage());
        }
    }
    public function insertMeAlias($symbol, $alias){
        try{
            $connection = new Connection();
            $sqlInsertValues = "UPDATE collections
            SET howrare_symbol = ? WHERE symbol LIKE ?";
            $stmt = $connection->prepare($sqlInsertValues);
            /* print_r($dict); */
            $stmt->bindParam(1,$alias);
            $stmt->bindParam(2,$symbol);
            $stmt->execute();
            }
        catch(PDOException $e){
            die('Error at attribute insertion: '.$e->getMessage());
        }
    }
    public function getHowrareAlias($symbol){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("SELECT howrare_symbol FROM collections WHERE symbol LIKE ?");
            $pdoStatement->bindParam(1,$symbol);
            $pdoStatement->execute();
            return $pdoStatement->fetch(PDO::FETCH_ASSOC)['howrare_symbol'];
            
        }
        catch(PDOException $e){
            die('Error at attribute insertion: '.$e->getMessage());
        }
    }

    public function insertTokenTraits($tokenMint,$traitValue){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("INSERT IGNORE INTO token_traits(fk_token_id, fk_trait_value)
                                                VALUES (:fk_token_id, :fk_trait_value)");
            $pdoStatement->bindParam(':fk_token_id', $tokenMint);
            $pdoStatement->bindParam(':fk_trait_value', $traitValue);
            $pdoStatement->execute();
        }
        catch(PDOException $e){
            die('Error at token_trait insertion: '.$e->getMessage());
        }
    }

    public function getTopCollections(){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("SELECT name,symbol FROM collections ORDER BY totalVolume LIMIT 200");
            $pdoStatement->execute();
            return json_encode($pdoStatement->fetchAll(PDO::FETCH_NUM));
        }
        catch(PDOException $e){
            die('Error getting top collections: '.$e->getMessage());
        }
    }

    public function insertScript($symbol){
        $token = new Token("","","","","");
        $collection = new Collection("");

        $howRareAlias = array_keys($collection->apiGetAllMeAlias(),$symbol)[0];
        $collection->insertMeAlias($symbol,$howRareAlias);
        $allAtributes = $collection->apiGetAllAttributes($howRareAlias);
        $token->setToUnlistedAll($symbol);
        
        if($allAtributes == false) return false;
        else{
            $collection->insertTypes($symbol,$allAtributes);
            $collection->insertAllAttributes($symbol,$allAtributes);
        }
        $listedTokens = $token->apiGetListedTokens($symbol);
        $token->updateTokenInfo($listedTokens);
        return true;
        
    }

    public function getFloorPrice($symbol){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("SELECT fk_symbol_listed, MIN(price) FROM listed_tokens 
            WHERE listed = 1 AND fk_symbol_listed LIKE ?
            GROUP BY fk_symbol_listed
            
            ");
            $pdoStatement->bindParam(1,$symbol);
            $pdoStatement->execute();
            return $pdoStatement->fetch(PDO::FETCH_ASSOC);
            
        }
        catch(PDOException $e){
            die("Error at fetching floor price for $symbol: ".$e->getMessage());
        }
    }

    public function getAttributeTypes($symbol){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("SELECT id_trait, trait_type FROM trait_types
            WHERE fk_symbol LIKE ?");
            $pdoStatement->bindParam(1,$symbol);
            $pdoStatement->execute();
            return json_encode($pdoStatement->fetchAll(PDO::FETCH_ASSOC));
            
        }
        catch(PDOException $e){
            die('Error at attribute type selection: '.$e->getMessage());
        }
    }

    public function getAttributeValues($attribute_id){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("SELECT value_id, fk_trait_type, value FROM trait_values
            WHERE fk_trait_type LIKE ?");
            $pdoStatement->bindParam(1,$attribute_id);
            $pdoStatement->execute();
            return json_encode($pdoStatement->fetchAll(PDO::FETCH_ASSOC));
            
        }
        catch(PDOException $e){
            die('Error at attribute value selection: '.$e->getMessage());
        }
    }

    
}



