<?php
include_once('src/Bot.php');

$bot = new Bot();
$watchList =  $bot->getWatchList($_POST['username']);
checkWatchList($watchList);

function checkWatchList($watchList){
    $bot = new Bot();
    foreach($watchList as $element){
        if($bot->checkCollection($element['symbol'], $element['floor_price'], $element['compare'])){
            $bot->sendMessage('1307787893',"Your alert on ".$element['symbol']." has hit your desired floor price (".$element['floor_price']." \$SOL).\n\r
            The current floor price is ".$bot->checkCollection($element['symbol'], $element['floor_price'],$element['compare']))." \$SOL";
        }
    }
}