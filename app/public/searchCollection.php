<?php
include_once('../src/Connection.php');
include_once('../src/Collection.php');


$datos= json_decode(file_get_contents('php://input'),true);
$connection = new Connection();
$symbol = str_replace(" ","_",$datos['symbol']);
$pdoStatement = $connection->prepare("SELECT fk_symbol_listed AS symbol, name, description, image, listedCount, totalVolume, lastUpdate, floorPrice FROM collections 
                                    JOIN(
                                    SELECT fk_symbol_listed, MIN(price) AS floorPrice FROM listed_tokens
                                                WHERE listed = 1 AND fk_symbol_listed LIKE :symbol
                                                GROUP BY fk_symbol_listed) AS floorPriceCalc
                                    ON collections.symbol = floorPriceCalc.fk_symbol_listed");
$pdoStatement->bindParam(':symbol',$symbol);
$pdoStatement->execute();

// If collection in database
if($pdoStatement->rowCount()>0){
    $data = $pdoStatement->fetch(PDO::FETCH_ASSOC);
    $data['image']= "/public/img/collections/".$data['image'];
    echo json_encode($data);
}
// Collection not in database
else{
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
    //Store this info in the DB
    $collection = new Collection("");
    $collection->storeImage($rawData['image'],"img/collections/".$rawData['symbol'],250,250);


    $collection->insertCollection(
        $rawData['symbol'],
        $rawData['name'],
        $rawData['description'], 
        $rawData['symbol'].'.jpg',
        $rawData['listedCount'],
        array_key_exists('avgPrice24hr',$rawData)?$rawData['avgPrice24hr']:"",
        $rawData['volumeAll']);
        
    $rawData['justInserted'] = true;
    echo json_encode($rawData);

}






