<?php
include_once('../view/footerView.php');
@session_start();
if(isset($_SESSION['username'])){
    header('Location: /home');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NFTprice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="d-flex justify-content-between p-1 align-items-center bg-light border border-dark">
        <a class="text-decoration-none text text-dark"  href="/index"><h1>NFTprice</h1></a>
        <div class="d-flex justify-content-around">
            <a class="m-4" href="login.php"><button class="btn btn-info">Login</button></a>
            <a class="m-4" href="register.php"><button class="btn btn-dark">Register</button></a>

        </div>
    </header>
    <div class="d-flex m-4  text">
        
        <div class="container w-50 text-algn-justify  p-4 ms-5 d-flex flex-column">
            <p class="fw-bolder fs-2">
                Never miss the lowest prices again!
            </p>
            <p class="fs-4">
            Receive alerts on your Telegram when an NFT reaches your desired price, and filter searches by specific attributes. 
            </p>
            <a  href="register.php" class=""><button class="btn btn-info">Get Started for FREE</button></a>
            <div class="mt-4 pt-5">
                <p class="fs-5">
                NFTPrice.app is a powerful tool that provides real-time NFT price data from 
                <a href="https://magiceden.io">MagicEden.io</a> and <a href="https://HowRare.is">HowRare.is</a>
                <br>
                Be the first to buy or sell with less than 5 seconds latency since a price change.
                </p>

            </div>
        </div>
        <div class="container w-50 text-algn-justify p-4 me-3">
            <img src="/public/img/interfaz_watchlist_web_tilt.png" alt="interface" class="mw-100">
        </div>
    </div>
    <div class="container w-50 text-algn-justify p-4 fs-4 text">
        
        </div>
    <?php
    showFooter();
    ?>
</body>
</html>