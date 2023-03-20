<?php

ini_set('memory_limit', '256M');
include_once('./Token.php');
include_once('./WatchList.php');
include_once('./Bot.php');
include_once('./Collection.php');
$times = array();
$time_start = microtime(true);

$bot = new Bot();
$watchLists = json_decode($bot->getAllDistinctWatchLists(),true);
$time_getAllDistinctWatchLists = microtime(true);

$token = new Token("","","","","");
$collection = new Collection("");

$dict = $collection->apiGetAllMeAlias();
$time_apiGetAllMeAlias = microtime(true);

$collection->insertAllMeAlias($dict);
$time_insertAllMeAlias = microtime(true);

foreach($watchLists as $alert){
    $howrareAlias = $collection->getHowrareAlias($alert['symbol']);
    $allAtributes = $collection->apiGetAllAttributes($howrareAlias);
    $time_apiGetAllAttributes = microtime(true);

    $token->setToUnlistedAll($alert['symbol']);
    $time_setToUnlistedAll = microtime(true);
    
    if($allAtributes == false) continue;
    else{
        $collection->insertTypes($alert['symbol'],$allAtributes);
        $time_insertTypes = microtime(true);

        $collection->insertAllAttributes($alert['symbol'],$allAtributes);
        $time_insertAllAttributes = microtime(true);

    }

    $listedTokens = $token->apiGetListedTokens($alert['symbol']);
    $time_apiGetListedTokens = microtime(true);

    $token->updateTokenInfo($listedTokens);
    $time_updateTokenInfo = microtime(true);
    
} 




echo "<br> time_getAllDistinctWatchLists:". $time_getAllDistinctWatchLists - $time_start;
echo "<br> time_apiGetAllMeAlias:". $time_apiGetAllMeAlias - $time_start;
echo "<br> time_insertAllMeAlias:". $time_insertAllMeAlias - $time_start;
echo "<br> Loop 1: time_apiGetAllAttributes:". $time_apiGetAllAttributes - $time_start;
echo "<br> Loop 1: time_setToUnlistedAll:". $time_setToUnlistedAll - $time_start;
echo "<br> Loop 1: time_insertTypes:". $time_insertTypes - $time_start;
echo "<br> Loop 1: time_insertAllAttributes:". $time_insertAllAttributes - $time_start;
echo "<br> Loop 1: time_apiGetListedTokens:". $time_apiGetListedTokens - $time_start;
echo "<br> End: ". microtime(true) - $time_start;