<?php
include_once(__DIR__.'/Bot.php');
include_once(__DIR__.'/WatchList.php');


function checkWatchList($watchList, $telegram_id){
    $bot = new Bot();
    $w = new WatchList("");
    /* print_r($watchList); */
    $desired_token = $bot->checkCollectionDB($watchList->symbol, $watchList->floor_price, $watchList->compare,$watchList->token_traits);
    $formated_attributes = str_replace($watchList->symbol."_", "\n\r        - ", $watchList->token_traits);
    
    $formated_attributes = str_replace("_", " -> ", $formated_attributes);
    $formated_attributes = str_replace(",", "", $formated_attributes);
    if($desired_token){
        // If conditions are met, set alert as inactive
        $w->deactivateAlert($watchList->id_alert);
        // Send the Telegram message
        $bot->sendMessage($telegram_id, "
        Your alert on <b>$watchList->symbol</b> has hit your desired floor price (". $watchList->floor_price/1000000000 ." $watchList->currency)
    • Current price: ". $desired_token->price/1000000000 ." $watchList->currency.
    • Attributes selected: $formated_attributes
    • <a href='https://magiceden.io/item-details/$desired_token->token_id'>$desired_token->name</a> is the cheapest NFT with the selected attributes");
    $time = date("Y-m-d h:i:s");
    echo "[$time] Sent message to $telegram_id - $desired_token->name | Price set:". $watchList->floor_price/1000000000 ." - token price: ". $desired_token->price/1000000000 ."\n";
    }
    
}
$bot = new Bot();

$watchLists = json_decode($bot->getActiveWatchLists());
foreach($watchLists as $watchList){
    // print_r($watchList);
    $username = $watchList->fk_username;
    $telegram_id = $bot->getTelegramId($username);
    // print_r($telegram_id);

    checkWatchList($watchList, $telegram_id);
}
// print_r($watchLists);