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
$time = date("Y-m-d h:i:sa");
$logScript = "";

foreach($watchLists as $alert){
    $collection_info = $collection->getCollectionInfo($alert['symbol'])[0];
    $last_update = new DateTime($collection_info['lastUpdate']);
    $date_now = new DateTime();

    $date_difference = $last_update->diff($date_now);

    $logScript .= '['.$time.'] Last '.$alert['symbol'].' update was '.$date_difference->i.' minute(s) ago. \n';
    if($date_difference->i<3){
        $logScript .= 'Not updating.\n'; 
        continue; //API CALL INTERVAL. IF >3 MIN -> UPDATE (IN MINUTES)
    } 
    $logScript .= 'Updating...\n';
    $token->setToUnlistedAll($alert['symbol']);
    /* $time_setToUnlistedAll = microtime(true); */

    $listedTokens = $token->apiGetListedTokens($alert['symbol']);
    // $times[$alert['symbol']." | time_apiGetListedTokens"] = microtime(true);
    $logScript .= '['.$time.'] apiGetListedTokens\n';
    $token->updateTokenInfo($listedTokens);
    //$times[$alert['symbol']." | updateTokenInfo"] = microtime(true);
    $logScript .= '['.$time.'] updateTokenInfo\n';


    $collection->updateCollectionInfo($alert['symbol'],$collection_info['listedCount'],$collection_info['totalVolume'],$collection_info['avgPrice24hr'],$last_update);
    //$times[$alert['symbol']." | updateCollectionInfo"] = microtime(true);
    $logScript .= '['.$time.'] updateCollectionInfo\n';
} 
/* foreach($times as $name => $time){
    echo $name.": ".round($time-$time_start,3)." seconds.<br>";
} */

$logScript .= "Finished updating. Seconds: ".round(microtime(true) - $time_start,2). "\n";

echo $logScript;

/* echo "<br> Loop 1: time_setToUnlistedAll: ". $time_setToUnlistedAll?$time_setToUnlistedAll:0 - $time_start; */
/* echo "<br> Loop 1: time_apiGetListedTokens: ". $time_apiGetListedTokens?$time_apiGetListedTokens:0 - $time_start;
echo "<br> Loop 2: time_updateTokenInfo: ". $time_updateTokenInfo?$time_updateTokenInfo:0 - $time_start; */