<?php

namespace Pinger;

class Pinger {

	/**
	 * We need a little extra help
	 */
	use HTTPContextHelperTrait;

	/**
	 * Explicitly define the base url/version
	 */
	protected $apiURL = "";

	/**
	 * Explicitly define the endpoints
	 */
	protected $endpoints = array();

	/**
	 * hold the cURL connection info
	 */
	protected $responseInfo = array();

	/**
	 *
	 */
	const METHOD_GET = "GET";

	/**
	 *
	 */
	const METHOD_POST = "POST";

	/**
	 * Description
	 * @param type $user
	 * @param type $pass
	 * @return type
	 */
	function __construct($api, array $endpoints){
		$this->apiURL    = $api;
		$this->endpoints = $endpoints;
	}

	/**
	 * method to get the info of the last request
	 * @return array
	 */
	function getInfo(){
		return $this->responseInfo;
	}

	/**
	 * Method to trigger a GET request to a given endpoint with the given data
	 * @param string $endpoint A valid endpoint
	 * @param array $data The data to send in the request
	 * @return string
	 */
	function get($endpoint, array $data = array()){
		$url = $this->normalizeEndpoint($endpoint, $data);

		$context = $this->createHTTPContext(static::METHOD_GET);

		return $this->ping($url, $context);
	}

	/**
	 * Method to trigger a POST request to a given endpoint with the given data
	 * @param string $endpoint A valid endpoint
	 * @param array $data The data to send in the request
	 * @return string
	 */
	function post($endpoint, array $data = array()){
		$url = $this->normalizeEndpoint($endpoint);

		$this->setHeaders(["Content-type" => "application/x-www-form-urlencoded"]);

		$context = $this->createHTTPContext(static::METHOD_POST, $data);

		return $this->ping($url, $context);
	}

	/**
	 * method to validate and normalize the URL/endpoint of the request. GET
	 * requests send their data here
	 *
	 * @param string $endpoint The endpoint to access
	 * @param array $data The content of the request
	 * @return string
	 */
	protected function normalizeEndpoint($endpoint, array $data = array()){
		$url = "";

		if(in_array($endpoint, $this->endpoints)){
			$url = rtrim($this->apiURL, " /") ."/". rtrim($endpoint, "/");
			if($data){
				$url = sprintf("%s?%s", rtrim($url, " ?"), $this->toQueryString($data));
			}
		}else{
			throw new InvalidEndpointException("That endpoint doesn't exist");
		}

		return $url;
	}

	/**
	 * method to execute the request.
	 * @param string $url
	 * @param resource $context
	 * @return string
	 */
	protected function ping($url, $context){
		$stream = fopen($url, 'r', false, $context);
		$this->responseInfo = stream_get_meta_data($stream);
		$response = stream_get_contents($stream);
		fclose($stream);
		return $response;
	}
}

