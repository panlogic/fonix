<?php namespace Panlogic\Fonix;
/**
* Fonix helper package by Panlogic Ltd.
*
* NOTICE OF LICENSE
*
* Licensed under the terms from Panlogic Ltd.
*
* @package Fonix
* @version 1.0.5
* @author Panlogic Ltd
* @license MIT
* @copyright (c) 2015, Panlogic Ltd
* @link http://www.panlogic.co.uk
*/

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Fonix {

	/**
	 * The version of this class
	 *
	 * @var string
	 */
	private $version = "1.0.4";

	/**
	 * A Guzzle HTTP Client object
	 *
	 * @var \GuzzleHttp\Client
	 */
	private $client;

	/**
	 * The default HTTP method
	 *
	 * @var string
	 */
	private $method = "POST";

	/**
	 * The Fonix API key
	 *
	 * @var string
	 */
	private $apikey;

	/**
	 * The Fonix short code to use
	 *
	 * @var string
	 */
	private $originator;

	/**
	 * The response format expected
	 *
	 * @var string
	 */
	private $responseFormat = "json";

	/**
	 * Guzzle request options
	 *
	 * @var array
	 */
	private $requestOptions = [
		'headers' 			=> '',
		'body'				=> '',
		'allow_redirects' 	=> false,
		'timeout'			=> '5',
	];

	/**
	 * The base URL part for the request
	 *
	 * @var string
	 */
	private $base = "https://sonar.fonix.io/v2/";

	/**
	 * The end point part of the URL for the request
	 *
	 * @var string
	 */
	private $endpoint;

	/**
	 * Fonix operator codes
	 *
	 * @var array
	 */
	private $operator_codes = array(
		'three-uk' 		=> 'Three UK',
		'eeora-uk' 		=> 'EE (Orange) UK',
		'eetmo-uk' 		=> 'EE (T-Mobile) UK',
		'voda-uk' 		=> 'Vodafone UK',
		'o2-uk' 		=> 'O2 UK',
		'virgin-uk' 	=> 'Virgin Media UK',
		'unknown' 		=> 'Operator unknown',
	);

	/**
	 * Create a new Fonix instance
	 *
	 * @return void
	 */
	public function __construct($config = array())
	{
		$this->apikey = $config['platform'] == 'live' ? $config['live_apikey'] : $config['test_apikey'];
		$this->originator = $config['originator'];
		$this->requestOptions['headers'] = ['X-API-KEY' => $this->apikey];
		$this->client = new Client();
	}

	/**
	 * Return a Guzzle Object
	 *
	 * @return GuzzleHTTPClient
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * Convert a binary expression (e.g., "100111") into a binary-string
	 *
	 * @return String
	 */
	function bin2bstr($input)
	{
	  if (!is_string($input)) return null; // Sanity check

	  // Pack into a string
	  return pack('H*', base_convert($input, 2, 16));
	}

	/**
	 * Binary representation of a binary-string
	 *
	 * @return String
	 */
	function bstr2bin($input)
	{
	  if (!is_string($input)) return null; // Sanity check

	  // Unpack as a hexadecimal string
	  $value = unpack('H*', $input);

	  // Output binary representation
	  return base_convert($value[1], 16, 2);
	}

	/**
	 * Make a request
	 *
	 * @return Object
	 */
	private function call()
	{
		$this->requestOptions['body'] = array_merge($this->requestOptions['body'], ['ORIGINATOR' => $this->originator]);
		$requestOptions = [];
		foreach($this->requestOptions['body'] as $key=>$value)
		{
			$requestOptions[strtoupper($key)] = $value;
		}
		$this->requestOptions['body'] = $requestOptions;
		try
		{
			switch(strtolower($this->method))
			{
				case "get":
				try
				{
					$response = $this->getClient()->get($this->base . $this->endpoint, $this->requestOptions);
				}
				catch(RequestException $e)
				{
					return $e->getResponse();
				}
				break;

				case "post":
				try
				{
					$response = $this->getClient()->post($this->base . $this->endpoint, $this->requestOptions);
				}
				catch(RequestException $e)
				{
					return $e->getResponse();
				}
				break;
			}
			return $response;
		}
		catch(GuzzleHttp\Exception\BadResponseException $ex)
		{
			return $ex->getResponse()->getBody();
		}
	}

	/**
	 * Capture the response from a request
	 *
	 * @return Object
	 */
	private function response($response)
	{
		$result = new \stdClass();
		$body = $response->getBody();
		$result->statusCode = $response->getStatusCode();
		$result->reason = $response->getReasonPhrase();
		$result->json = '';
		if ($this->responseFormat == 'json')
		{
			$result->json = $response->json();
		}
		$result->xml = '';
		if ($this->responseFormat == 'xml')
		{
			$result->xml = $response->xml();
		}
		$result->body = $body;
		return $result;
	}

	/**
	 * Capture the usual response parameters from POST back
	 *
	 * @return Object
	 */
	private function basicMOResponse()
	{
		$return = new \stdClass();
		$return->ifversion = isset($_POST['IFVERSION']) ? $_POST['IFVERSION'] : '';
		$return->monumber = isset($_POST['MONUMBER']) ? $_POST['MONUMBER'] : '';
		$return->operator = isset($_POST['OPERATOR']) ? $_POST['OPERATOR'] : '';
		$return->destination = isset($_POST['DESTINATION']) ? $_POST['DESTINATION'] : '';
		$return->body = isset($_POST['BODY']) ? $_POST['BODY'] : '';
		$return->receievetime = isset($_POST['RECEIVETIME']) ? $_POST['RECEIVETIME'] : '';
		$return->guid = isset($_POST['GUID']) ? $_POST['GUID'] : '';
		return $return;
	}

	/**
	 * Send an SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function sendSMS($body = array(), $originator = null)
	{
		$this->requestOptions['body'] = $body;
		$this->endpoint = 'sendsms';
		$this->setOriginator($originator);
		return $this->response($this->call());
	}

	/**
	 * Send a Chargeable SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function chargeSMS($body = array(), $originator = null)
	{
		$this->requestOptions['body'] = $body;
		$this->endpoint = 'chargesms';
		$this->setOriginator($originator);
		return $this->response($this->call());
	}

	/**
	 * Send a Chargeable SMS message for Vodaphone
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function chargeMobile($body = array(), $originator = null)
	{
		$this->requestOptions['body'] = $body;
		$this->endpoint = 'chargemobile';
		$this->setOriginator($originator);
		return $this->response($this->call());
	}

	/**
	 * Send a Binary SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function sendBinarySMS($body = array(), $originator = null)
	{
		$this->requestOptions['body'] = array_merge($body, array('BINBODY' => isset($body['body']) ? $this->bstr2bin($body['body']) : '' ,'BINHEADER' => isset($body['header']) ? $this->bstr2bin($body['header']) : ''));
		unset($this->requestOptions['body']['body']);
		$this->endpoint = 'sendbinsms';
		$this->setOriginator($originator);
		return $this->response($this->call());
	}

	/**
	 * Send a WAP Push message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function sendWapPush($body = array(), $originator = null)
	{
		$this->requestOptions['body'] = array_merge($body, array('PUSHTITLE' => isset($body['title']) ? $body['title'] : '' ,'PUSHLINK' => isset($body['link']) ? $body['link'] : ''));
		unset($this->requestOptions['body']['body']);
		unset($this->requestOptions['body']['title']);
		unset($this->requestOptions['body']['link']);
		$this->endpoint = 'sendwappush';
		$this->setOriginator($originator);
		return $this->response($this->call());
	}

	/**
	 * Get the SMS MO return parameters
	 *
	 * @return Object
	 */
	public function getSMSMO()
	{
		$return = $this->basicMOResponse();
		$return->price = isset($_POST['PRICE']) ? $_POST['PRICE'] : '';
		return $return;
	}

	/**
	 * Get the SMS Stop return parameters
	 *
	 * @return Object
	 */
	public function getSMSStop()
	{
		$return = $this->basicMOResponse();
		return $return;
	}

	/**
	 * Get the SMS Binary return parameters
	 *
	 * @return Object
	 */
	public function getSMSBinary()
	{
		$return = $this->basicMOResponse();
		$return->binbody = isset($_POST['BINBODY']) ? $_POST['BINBODY'] : '';
		$return->binheader = isset($_POST['BINHEADER']) ? $_POST['BINHEADER'] : '';
		return $return;
	}

	/**
	 * Get the delivery receipt return parameters
	 *
	 * @return Object
	 */
	public function getDeliveryReceiptSMS()
	{
		$return = $this->basicMOResponse();
		$return->statuscode = isset($_POST['STATUSCODE']) ? $_POST['STATUSCODE'] : '';
		$return->statustext = isset($_POST['STATUSTEXT']) ? $_POST['STATUSTEXT'] : '';
		$return->statustime = isset($_POST['STATUSTIME']) ? $_POST['STATUSTIME'] : '';
		$return->price 		= isset($_POST['PRICE']) ? $_POST['PRICE'] : '';
		$return->request_id  = isset($_POST['REQUESTID']) ? $_POST['REQUESTID'] : '';
		return $return;
	}

	/**
	 * Get the delivery receipt return parameters from Billing API
	 *
	 * @return Object
	 */
	public function getChargeReport()
	{
		$return = new \stdClass();
		$return->ifversion = isset($_POST['IFVERSION']) ? $_POST['IFVERSION'] : '';
		$return->operator = isset($_POST['OPERATOR']) ? $_POST['OPERATOR'] : '';
		$return->monumber = isset($_POST['MONUMBER']) ? $_POST['MONUMBER'] : '';
		$return->statuscode = isset($_POST['STATUSCODE']) ? $_POST['STATUSCODE'] : '';
		$return->statustext = isset($_POST['STATUSTEXT']) ? $_POST['STATUSTEXT'] : '';
		$return->statustime = isset($_POST['STATUSTIME']) ? $_POST['STATUSTIME'] : '';
		$return->guid = isset($_POST['GUID']) ? $_POST['GUID'] : '';
		$return->requestid = isset($_POST['REQUESTID']) ? $_POST['REQUESTID'] : '';
		return $return;
	}

	/**
	 * Get the operator codes
	 *
	 * @return array
	 */
	public function getOperatorCodes()
	{
		return $this->operator_codes;
	}

	/**
	 * Get the class version
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Get the end point (REST path)
	 *
	 * @return string
	 */
	public function getEndPoint()
	{
		return $this->endpoint;
	}

	/**
	 * Get the api key
	 *
	 * @return string
	 */
	public function getAPIKey()
	{
		return $this->apikey;
	}

	/**
	 * Set the response format, default is JSON
	 *
	 * @return void
	 */
	public function setResponseFormat($format = "json")
	{
		$this->responseFormat = $format;
	}

	/**
	 * Set the originator
	 *
	 * @param string
	 * @return void
	 *
	 */
	public function setOriginator($originator = null)
	{
		if(!is_null($originator))
		{
			$this->originator = $originator;
		}
	}
}