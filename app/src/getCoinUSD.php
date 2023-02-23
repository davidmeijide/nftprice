<?php
$currency = $_POST['currency'];

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
$response = (json_decode($response,true)); // print json decoded response
/* print_r($response); */
$price = reset($response['data'])['quote']['USD']['price'];
echo json_encode($price);

curl_close($curl); // Close request
?>