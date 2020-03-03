<?php

require_once '../src/APIClient.php';
require_once '../src/APIException.php';
require_once '../src/APIRequestException.php';

use BlockmoveAPI\APIClient;
use BlockmoveAPI\APIException;
use BlockmoveAPI\APIRequestException;

// Generated API Key from your Blockmove.io Wallet
$apiKey = 'YOUR_API_KEY';

// Generated API Secret Key from your Blockmove.io Wallet
$apiSecret = 'YOUR_API_SECRET';

// Wallet ID
$walletId = 'WALLET_ID';

// Your Wallet Password
$password = 'WALLET_PASSWORD';

// Address to send coins
$address = ['address' => 'DESTINATION_ADDRESS', 'message' => 'DESTINATION_TAG'];

// Amount of coins in FLOAT format
$amount = 0.1;


try {
	$result = APIClient::init($apiKey, $apiSecret)
						->send($walletId, $password, $address, $amount, APIClient::PRIORITY_MEDIUM);
	
	var_dump($result);
}
catch (APIException $e) {
	echo 'API Error: ' . $e->getMessage();
}
catch (APIRequestException $e) {
	echo 'API Request failed: ' . $e->getMessage();
}