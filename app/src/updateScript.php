<?php

include_once('./Token.php');
include_once('./WatchList.php');
include_once('./Bot.php');
include_once('./Collection.php');
$times = array();
$time_start = microtime(true);

$bot = new Bot();
$watchLists = json_decode($bot->getAllDistinctWatchLists(),true);
$times['time_getAllDistinctWatchLists'] = microtime(true);

$token = new Token("","","","","");
$collection = new Collection("");

foreach($watchLists as $alert){
    $collection_info = $collection->getCollectionInfo($alert['symbol'])[0];
    $last_update = new DateTime($collection_info['lastUpdate']);
    $date_now = new DateTime();

    $date_difference = $last_update->diff($date_now);

    echo '<br>Last '.$alert['symbol'].' update was '.$date_difference->i.' minute(s) ago.<br>';
    if($date_difference->i<3){
        echo 'Not updating.'; 
        continue; //API CALL INTERVAL. IF >3 MIN -> UPDATE (IN MINUTES)
    } 
    echo 'Updating...<br>';
    $token->setToUnlistedAll($alert['symbol']);
    /* $time_setToUnlistedAll = microtime(true); */

    $listedTokens = $token->apiGetListedTokens($alert['symbol']);
    $times[$alert['symbol']." | time_apiGetListedTokens"] = microtime(true);

    $token->updateTokenInfo2($listedTokens);
    $times[$alert['symbol']." | updateTokenInfo2"] = microtime(true);

    $collection->updateCollectionInfo($alert['symbol'],$collection_info['listedCount'],$collection_info['totalVolume'],$collection_info['avgPrice24hr'],$last_update);
    $times[$alert['symbol']." | updateCollectionInfo"] = microtime(true);
} 
foreach($times as $name => $time){
    echo $name.": ".round($time-$time_start,3)." seconds.<br>";
}

echo "<br> End: ".round(microtime(true) - $time_start,2);

/* echo "<br> Loop 1: time_setToUnlistedAll: ". $time_setToUnlistedAll?$time_setToUnlistedAll:0 - $time_start; */
/* echo "<br> Loop 1: time_apiGetListedTokens: ". $time_apiGetListedTokens?$time_apiGetListedTokens:0 - $time_start;
echo "<br> Loop 2: time_updateTokenInfo2: ". $time_updateTokenInfo2?$time_updateTokenInfo2:0 - $time_start; */