<?php

$url = 'https://api-mainnet.magiceden.dev/v2/collections?offset=0&limit=20';
$httpOptions = [
    "http" => [
        "method" => "GET",
        "header" => 'Content-Type: application/json',
    ]
];

$streamContext = stream_context_create($httpOptions);
$jsonOutput = file_get_contents($url, false, $streamContext);
$rawData = json_decode($jsonOutput, true);
echo json_encode($rawData);


