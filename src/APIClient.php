<?php

namespace BlockmoveAPI;

class APIClient {
	
	/**
	 * Transaction priority for fastest confirmation
	 */
	const PRIORITY_HIGH = 'high';
	
	/**
	 * Transaction priority for medium confirmation (used by default)
	 */
	const PRIORITY_MEDIUM = 'medium';
	
	/**
	 * Transaction priority for slow confirmation
	 */
	const PRIORITY_LOW = 'low';
	
	
	/**
	 * @var string Blockmove.io API Endpoint
	 */
	private $endpoint = 'https://api.blockmove.io/v1';

	/**
	 * @var string API Public Key
	 */
	private $apiKey;
	
	/**
	 * @var string API Secret Key
	 */
	private $apiSecret;
	
	/**
	 * @param string $apiKey API Public Key
	 * @param string $apiSecret API Secret Key
	 */
	public function __construct($apiKey, $apiSecret) {
		$this->setApiKey($apiKey);
		$this->setApiSecret($apiSecret);
	}
	
	/**
	 * @param string $apiKey API Public Key
	 * @param string $apiSecret API Secret Key
	 * @return APIClient
	 */
	public static function init($apiKey, $apiSecret) {
		return new APIClient($apiKey, $apiSecret);
	}

	/**
	 * @param string $endpoint Blockmove.io API Endpoint
	 * @return APIClient
	 */
	public function setEndpoint($endpoint) {
		$this->endpoint = trim($endpoint, '/');
		return $this;
	}
	
	/**
	 * @param string $apiKey API Public Key
	 * @return APIClient
	 */
	public function setApiKey($apiKey) {
		$this->apiKey = $apiKey;
		return $this;
	}
	
	/**
	 * @param string API Secret Key
	 * @return APIClient
	 */
	public function setApiSecret($apiSecret) {
		$this->apiSecret = $apiSecret;
		return $this;
	}
	
	/**
	 * @return string "OK"
	 * @throws APIException
	 */
	public function status() {
		$result = $this->request('status');
		
		if ($result['code'] != 200) {
			throw new APIException('Error: ' . $result['message']);
		}
		
		return 'OK';
	}
	
	/**
	 * @param string $walletId Wallet ID
	 * @param string $webhook Client endpoint url for receiving address balance change notifications
	 * @throws APIException
	 * @return array Generated address data
	 */
	public function generateAddress($walletId, $webhook = null) {
		$result = $this->request('generateaddress', [
			'wallet_id' => $walletId,
			'webhook' => $webhook
		]);
		
		if ($result['code'] != 200) {
			throw new APIException('Error: ' . $result['message']);
		}
		
		return $result['data'];
	}
	
	/**
	 * @param string $walletId Wallet ID
	 * @param string $txId Cryptocurrency Transaction ID Hash
	 * @throws APIException
	 * @return array Transaction data
	 */
	public function getTx($walletId, $txId) {
		$result = $this->request('tx', [
			'wallet_id' => $walletId,
			'tx_id' => $txId
		]);
		
		if ($result['code'] != 200) {
			throw new APIException('Error: ' . $result['message']);
		}
		
		return $result['data'];
	}
	
	/**
	 * @param string $walletId Wallet ID
	 * @throws APIException
	 * @return array Wallet balance info
	 */
	public function getWalletBalance($walletId) {
		$result = $this->request('walletbalance', [
			'wallet_id' => $walletId
		]);
		
		if ($result['code'] != 200) {
			throw new APIException('Error: ' . $result['message']);
		}
		
		return $result['data'];
	}
	
	/**
	 * @param string $address Cruptocurrency Address value
	 * @param string $message Additional address param, like Destination Tag for Ripple, Memo for Stellar, etc.
	 * @param string $token Token Symbol
	 * @throws APIException
	 * @return array Address info
	 */
	public function getAddressInfo($address, $message = null, $token = null) {
		$result = $this->request('addressinfo', [
			'address' => $address,
			'message' => $message,
			'token' => $token
		]);
		
		if ($result['code'] != 200) {
			throw new APIException('Error: ' . $result['message']);
		}
		
		return $result['data'];
	}

	/**
	 * @param string $walletId Wallet ID
	 * @param string $token Token Symbol
	 * @param array $params History Records Params (array format [limit, offset])
	 * @throws APIException
	 * @return array Wallet History
	 */
	public function getWalletHistory($walletId, $params = [], $token = null) {
		$result = $this->request('wallethistory', [
			'wallet_id' => $walletId,
			'token' => $token,
			'params' => $params
		]);

		if ($result['code'] != 200) {
			throw new APIException('Error: ' . $result['message']);
		}

		return $result['data'];
	}

	/**
	 * @param string $address Cruptocurrency Address value
	 * @param string $token Token Symbol
	 * @param array $params History Records Params (array format [limit, offset])
	 * @throws APIException
	 * @return array Address History
	 */
	public function getAddressHistory($address, $params = [], $token = null) {
		$result = $this->request('addresshistory', [
			'address' => $address,
			'token' => $token,
			'params' => $params
		]);

		if ($result['code'] != 200) {
			throw new APIException('Error: ' . $result['message']);
		}

		return $result['data'];
	}
	
	/**
	 * @param string $walletId Wallet ID
	 * @param string $walletPassword Wallet Password
	 * @param string|array $destinationAddress Cryptocurrency Addreess to send to. May contain additional message for currencies like Ripple, Monero, etc. (array fromat [address, message])
	 * @param float $amount sending amount
	 * @param string $priority transaction priority
	 * @param string $token Token Symbol
	 * @throws APIException
	 * @return array Transaction ID data
	 */
	public function send($walletId, $walletPassword, $destinationAddress, $amount, $priority = null, $token = null) {
		$result = $this->request('send', [
			'wallet_id' => $walletId,
			'password' => $this->encryptPassword($walletPassword),
			'destination' => $destinationAddress,
			'amount' => $amount,
			'priority' => $priority,
			'token' => $token
		]);
		
		if ($result['code'] != 200) {
			throw new APIException('Error: ' . $result['message']);
		}
		
		return $result['data'];
	}
	
	
	
	
	/**
	 * @param string $method
	 * @param array $params
	 * @throws APIRequestException
	 * @throws APIException
	 */
	private function request($method, $params = []) {
		if (empty($this->apiKey) || empty($this->apiSecret)) {
			throw new APIException('Error: API Key or API Secret Key are not set.');
		}

		$url = $this->endpoint . '/' . $method;

		$params['_api_key'] = $this->apiKey;
		$json = json_encode($params, 320);
		$sign = $this->createSign($json);

		$params['_api_sign'] = $sign;

		$data = json_encode($params, 320);

		$curl = curl_init();
		$options = [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING => 'UTF-8',
			CURLOPT_AUTOREFERER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_HTTPHEADER => ['Content-Type: application/json']
		];

		curl_setopt_array($curl, $options);
		
		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		if ($status != 200) {
			throw new APIRequestException('Error: call to ' . $url . ' failed with status ' . $status . ', response ' . $response . ', curl_error ' . curl_errno($curl) . ', curl_errno ' . curl_errno($curl));
		}
		
		curl_close($curl);
		
		return json_decode($response, true);
	}
	
	/**
	 * @param string $queryString
	 * @return string hash_hmac sha256
	 */
	private function createSign($queryString) {
		return hash_hmac('sha256', $queryString, $this->apiSecret);
	}
	
	/**
	 * @param string $password Wallet Password
	 * @return string Encrypted password
	 */
	private function encryptPassword($password) {
		return hash('sha512', $password);
	}
}