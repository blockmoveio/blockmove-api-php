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

// Generated address
$address = 'ADDRESS_TO_CHECK';

// Token symbol for currencies like Ethereum, for example: USDC, TUSD, etc.
$token = 'TOKEN_SYMBOL';


try {
	$result = APIClient::init($apiKey, $apiSecret)
						->getAddressInfo($address, null, $token);
	
	var_dump($result);
}
catch (APIException $e) {
	echo 'API Error: ' . $e->getMessage();
}
catch (APIRequestException $e) {
	echo 'API Request failed: ' . $e->getMessage();
}