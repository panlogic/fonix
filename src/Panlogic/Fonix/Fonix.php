<?php namespace Panlogic\Fonix;
/**
* Fonix helper package by Panlogic Ltd.
*
* NOTICE OF LICENSE
*
* Licensed under the terms from Panlogic Ltd.
*
* @package Fonix
* @version 1.0.0
* @author Panlogic Ltd
* @license GPL3
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
	private $version = "1.0";

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
	public function __construct($config)
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
	 * Make a request
	 *
	 * @return Object
	 */
	private function call()
	{
		$this->requestOptions['body'] = array_merge($this->requestOptions['body'], ['ORIGINATOR' => $this->originator]);
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
		$result = new stdObject();
		$body = $response->getBody();
		$result->statusCode = $response->getStatusCode();
		$result->reason = $response->getReasonPhrase();
		$result->json = isset($body->json()) ? $body->json : '';
		$result->xml = isset($body->xml()) ? $body->xml : '';
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
		$return = new StdObject();
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
	public function sendSMS($body = array())
	{
		$this->requestOptions['body'] = array_merge($this->requestOptions['body'], $body);
		$this->endpoint = 'sendsms';
		return $this->response($this->call());
	}

	/**
	 * Send a Chargeable SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function chargeSMS($body = array())
	{
		$this->requestOptions['body'] = array_merge($this->requestOptions['body'], $body);
		$this->endpoint = 'sendsms';
		return $this->response($this->call());
	}

	/**
	 * Send a Binary SMS message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function sendBinarySMS($body = array())
	{
		$this->requestOptions['binbody'] = array_merge($this->requestOptions['binbody'], $body);
		$this->endpoint = 'sendbinsms';
		return $this->response($this->call());
	}

	/**
	 * Send a WAP Push message
	 *
	 * @param  array  $body
	 * @return Object
	 */
	public function sendWapPush($body = array())
	{
		$this->requestOptions['pushtitle'] = isset($body['title']) ? $body['title'] : '';
		$this->requestOptions['pushlink'] = isset($body['link']) ? $body['link'] : '';
		$this->endpoint = 'sendwappush';
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
		$return->price = isset($_POST['PRICE']) ? $_POST['PRICE'] : '';
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
}