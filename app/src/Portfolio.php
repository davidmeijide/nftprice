<?php
include_once('Connection.php');

class Portfolio{
    private $items;
    public function __construct($items){
        $this->items = $items;
    }
    public function addItem($symbol, $purchase_price, $currency, $amount_owned, $dollars_per_coin){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("INSERT INTO portfolio(symbol, purchase_price, currency, amount_owned, dollars_per_coin, fk_username) 
                                            VALUES(:symbol, :purchase_price, :currency, :amount_owned, :dollars_per_coin, :fk_username)");
            $pdoStatement->bindParam(':symbol',$symbol);
            $pdoStatement->bindParam(':fk_username',$_SESSION['username']);
            $pdoStatement->bindParam(':purchase_price',$purchase_price);
            $pdoStatement->bindParam(':currency',$currency);
            $pdoStatement->bindParam(':amount_owned',$amount_owned);
            $pdoStatement->bindParam(':dollars_per_coin',$dollars_per_coin);
            $pdoStatement->execute();
            return true;
        }
        catch(PDOException $e){
            die('Database error at portfolio item insertion: '.$e->getMessage());
        }
    }

    public function removeItem($id_portfolio){
        try{
            $connection = new Connection();
            $pdoStatement = $connection->prepare("DELETE FROM portfolio WHERE id_portfolio = :id_portfolio");
            $pdoStatement->bindParam(':id_portfolio', $id_portfolio);
            $pdoStatement->execute();
            return true;
        }
        catch(PDOException $e){
            die('Database error at alert delete: '.$e->getMessage());
        }
        
    }

    public function getCoinUSD($currency){
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';
        $parameters = [
            'symbol' => $currency,
            'convert' => 'USD'
        ];

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: 425b0c4f-416d-40e0-afe3-cd3e989997f3'
        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL


        $curl = curl_init(); // Get cURL resource
        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers 
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response
        $response = (json_decode($response, true)); // print json decoded response
        /* print_r($response); */
        $price = reset($response['data'])['quote']['USD']['price'];
        curl_close($curl); // Close request
        return json_encode($price);

    }
}