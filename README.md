## Official Blockmove.io PHP SDK

Documentation is available at website https://docs.blockmove.io.

Examples are available in this package.

Install package via composer:
```bash
composer require blockmove.io/blockmove-api-php
```

Example
--------

```php
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

try {
	$result = APIClient::init($apiKey, $apiSecret)
						->getWalletBalance($walletId);
	
	var_dump($result);
}
catch (APIException $e) {
	echo 'API Error: ' . $e->getMessage();
}
catch (APIRequestException $e) {
	echo 'API Request failed: ' . $e->getMessage();
}
```
