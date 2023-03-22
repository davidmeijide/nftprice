<?php

include_once(__DIR__.'/Token.php');
include_once(__DIR__.'/WatchList.php');
include_once(__DIR__.'/Bot.php');
include_once(__DIR__.'/Collection.php');
$times = array();
$time_start = microtime(true);

$bot = new Bot();
$watchLists = json_decode($bot->getAllDistinctWatchLists(),true);
// $times['time_getAllDistinctWatchLists'] = microtime(true);

$token = new Token("","","","","");
$collection = new Collection("");
$time = date("Y-m-d h:i:s");
$log = [];

foreach($watchLists as $alert){
    $collection_info = $collection->getCollectionInfo($alert['symbol'])[0];
    $last_update = new DateTime($collection_info['lastUpdate']);
    $date_now = new DateTime();

    $date_difference = $last_update->diff($date_now);

    array_push($log , '['.$time.'] Last '.$alert['symbol'].' update was '.$date_difference->i.' minute(s) ago.');
    if($date_difference->i<3){
        array_push($log, 'Not updating'); 
        continue; //API CALL INTERVAL. IF >3 MIN -> UPDATE (IN MINUTES)
    } 
    array_push($log, '['.date("Y-m-d h:i:s").'] Updating...');
    $token->setToUnlistedAll($alert['symbol']);

    $listedTokens = $token->apiGetListedTokens($alert['symbol']);
    array_push($log, '['.date("Y-m-d h:i:s").'] apiGetListedTokens');
    $token->updateTokenInfo($listedTokens);
    array_push($log, '['.date("Y-m-d h:i:s").'] updateTokenInfo');


    $collection->updateCollectionInfo($alert['symbol'],$collection_info['listedCount'],$collection_info['totalVolume'],$collection_info['avgPrice24hr'],$last_update);
    //$times[$alert['symbol']." | updateCollectionInfo"] = microtime(true);
    array_push($log, '['.date("Y-m-d h:i:s").'] updateCollectionInfo');
} 

array_push($log , "[".date("Y-m-d h:i:s")."] Finished updating. Seconds: ".round(microtime(true) - $time_start,2));

$watchLists? $log : die();

foreach($log as $line){
    echo $line ."\n";
}

