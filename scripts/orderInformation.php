<?php

function curlRequest(string $url, array $params = [])
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_COOKIE, 'PHPSESSID=' . $_COOKIE['PHPSESSID']);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Content-type: application/x-www-form-urlencoded",
    ],
    );
    $response = json_decode(curl_exec($curl));
    curl_close($curl);
    return $response;
}

function getOrderInformation(string $baseUrl): string
{
    $apiKey = readline('Enter your API key: ');
    $params = [
        'key' => $apiKey
    ];
    $url = $baseUrl . "/index.php?route=api/login";
    $loginResponse = curlRequest($url, $params);

    if (isset($loginResponse->token)) {
        $token = $loginResponse->token;
    } else {
        return "Login error\n";
    }

    $orderId = readline("Enter order id: ");
    $url = $baseUrl . "/index.php?route=api/order/info&token=$token&order_id=$orderId";
    $response = curlRequest($url);

    if (isset($response->order)) {
        return json_encode($response->order, JSON_PRETTY_PRINT) . "\n";
    } else {
        return $response->error;
    }
}

session_start();
$_COOKIE['PHPSESSID'] = session_id();
session_write_close();

$baseUrl = 'http://localhost:8008';

echo getOrderInformation($baseUrl);
