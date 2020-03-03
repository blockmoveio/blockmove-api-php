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

// Additional message param for currencies like Ripple (Destination Tag), Stellar (Memo), etc.
$message = 'MESSAGE_TO_CHECK';


try {
	$result = APIClient::init($apiKey, $apiSecret)
						->getAddressInfo($address, $message);
	
	var_dump($result);
}
catch (APIException $e) {
	echo 'API Error: ' . $e->getMessage();
}
catch (APIRequestException $e) {
	echo 'API Request failed: ' . $e->getMessage();
}