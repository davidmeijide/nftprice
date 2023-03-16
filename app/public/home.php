<?php
include_once('../src/Bot.php');
include_once('../src/WatchList.php');
include_once('../src/Portfolio.php');
include_once('../src/Collection.php');
include_once('../view/homeView.php');

@session_start();



if(!isset($_SESSION['username'])){
    header('Location: /login');    
}
elseif(isset($_POST['active'])){
    $bot = new Bot();
    $watchlist = new WatchList($bot->getWatchList($_SESSION['username']));
    if($_POST['active']==1){
        $watchlist->deactivateAlert($_POST['id_alert']);
    }
    elseif($_POST['active']==0){
        $watchlist->activateAlert($_POST['id_alert']);

    }
}
elseif(isset($_POST['getWatchList'])){
    $bot = new Bot();
    $watchlist = $bot->getWatchList($_SESSION['username']);
    header('Content-Type: application/json');
    echo $watchlist;

}

elseif(isset($_POST['addAlert'])){
    $watchlist = new WatchList("");
    $watchlist->addAlert($_POST['symbol'],$_POST['compare'],$_POST['price'],$_POST['currency'],$_POST['duration'],$_POST['magnitude'],$_POST['attributes']);
    echo true;

}

elseif(isset($_POST['setAlertPrice'])){
    $watchlist = new WatchList("");
    $watchlist->setAlertPrice($_POST['id_alert'],$_POST['alert_price']);
}

elseif(isset($_POST['removeAlert'])){
    $watchlist = new WatchList("");
    $watchlist->removeAlert($_POST['id_alert']);
}

elseif(isset($_POST['getPortfolio'])){
    $bot = new Bot("");
    header('Content-Type: application/json');
    $portfolio = $bot->getPortfolio($_SESSION['username']);
    echo $portfolio;

}

elseif(isset($_POST['addToPortFolio'])){
    $portfolio = new Portfolio("");
    header('Content-Type: application/json');
    $portfolio->addItem($_POST['symbol'], $_POST['purchase-price'], $_POST['currency'], $_POST['amount-owned'], $_POST['currency-price']);
}

/* elseif(isset($_POST['showPortfolioForm'])){
    $portfolio = new Portfolio("");

    echo $bot->showPortfolioForm();

} */

elseif(isset($_POST['removeItem'])){
    $portfolio = new Portfolio("");
    $portfolio->removeItem($_POST['id_portfolio']);

}

elseif(isset($_POST['getTopCollections'])){
    $collection = new Collection("");
    echo $collection->getTopCollections();

}
elseif(isset($_POST['getAttributeTypes'])){
    $collection = new Collection("");
    echo $collection->getAttributeTypes($_POST['symbol']);

}

elseif(isset($_POST['getAttributeValues'])){
    $collection = new Collection("");
    echo $collection->getAttributeValues($_POST['id_trait']);

}

elseif(isset($_POST['processUpdates'])){
    $bot = new Bot();
    $updates = $bot->getUpdates();
    echo $bot->processUpdates($updates);
}

elseif(isset($_POST['linkTelegram'])){
    $bot = new Bot();
    echo $bot->linkTelegram($_SESSION['username'],$_POST['telegram_id']);
    
}

elseif(isset($_POST['isTelegramLinked'])){
    $bot = new Bot();
    echo $bot->isTelegramLinked($_SESSION['username']);
}

elseif(isset($_POST['sendTestAlert'])){
    $bot = new Bot();
    echo $bot->sendTestAlert($_SESSION['username']);
}
elseif(isset($_POST['insertCollection'])){
    $collection = new Collection("");
    echo $collection->insertScript($_POST['symbol']);
}

else{
    $bot = new Bot();
    $watchlist = $bot->getWatchList($_SESSION['username']);
    showHead();
    showSearch();
    showTemplates();

}
?>




