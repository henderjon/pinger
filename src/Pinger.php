<?php

namespace Pinger;

class Pinger implements PingerInterface {

	/**
	 * Explicitly define the base url/version
	 */
	protected $apiURL;

	/**
	 * hold the cURL connection info
	 */
	protected $responseMeta = array();

	/**
	 * hold the cURL connection info
	 */
	protected $response;

	/**
	 * hold the cURL connection info
	 */
	protected $encType = PHP_QUERY_RFC1738;

	/**
	 * hold the cURL connection info
	 */
	protected $separator = null;

	/**
	 * hold the cURL connection info
	 */
	protected $numPrefix = "no_";

	/**
	 *
	 */
	const METHOD_GET = "GET";

	/**
	 *
	 */
	const METHOD_POST = "POST";

	/**
	 * attribute(s) required by stream_context_create()
	 */
	protected $user_agent = "PHP stream_context_create()";

	/**
	 * attribute to store the headers to be used for the request
	 */
	protected $headers = array();

	/**
	 * header delimiter
	 */
	private $DELIM = "\r\n";

	/**
	 * Description
	 * @param type $user
	 * @param type $pass
	 * @return type
	 */
	public function __construct($api){
		$this->apiURL    = $api;
		$this->separator = ini_get("arg_separator.output");
	}

	/**
	 * set the encoding type for built queries
	 */
	public function setEncType($encType){
		$this->encType = $encType;
	}

	/**
	 * set the separator for built queries
	 */
	public function setSeparator($separator){
		$this->separator = $separator;
	}

	/**
	 * set the numeric prefix for built queries
	 */
	public function setNumPrefix($numPrefix){
		$this->numPrefix = $numPrefix;
	}

	/**
	 * method to get the info of the last request
	 * @return array
	 */
	public function getResponseMeta(){
		return $this->responseMeta;
	}

	/**
	 * method to get the info of the last request
	 * @return array
	 */
	public function getResponse(){
		return $this->response;
	}

	/**
	 * Method to trigger a GET request to a given endpoint with the given data
	 * @param string $endpoint A valid endpoint
	 * @param array $data The data to send in the request
	 * @return string
	 */
	public function get($endpoint, array $data = array()){
		$url     = $this->normalizeEndpoint($endpoint, $data);
		$context = $this->createHTTPContext(static::METHOD_GET);
		return $this->ping($url, $context);
	}

	/**
	 * Method to trigger a POST request to a given endpoint with the given data.
	 * By default, data is urlencoded, switching this off allows the posting of
	 * a raw body.
	 *
	 * @param string $endpoint A valid endpoint
	 * @param array $data The data to send in the request
	 * @param  bool $urlencoded Add the x-www-form-urlencoded header
	 * @return string
	 */
	public function post($endpoint, $data = "", $urlencoded = true){
		$url = $this->normalizeEndpoint($endpoint);

		if($urlencoded){
			$data = $this->toQueryString($data);
			$this->setHeaders(["Content-type" => "application/x-www-form-urlencoded"]);
		}

		$context = $this->createHTTPContext(static::METHOD_POST, $data);
		return $this->ping($url, $context);
	}

	/**
	 * method to set additional headers to be sent with the request
	 * @param array $headers An array of key => value pairs to use to create headers
	 * @return
	 */
	public function setHeaders(array $headers){
		foreach($headers as $header => $value){
			$this->headers[strtolower($header)] = trim($value);
		}
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
		$url = rtrim($this->apiURL, " /") ."/". ltrim($endpoint, "/");

		if($data){
			$url = sprintf("%s?%s", rtrim($url, " ?"), $this->toQueryString($data));
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
		$stream = @fopen($url, 'r', false, $context);
		if($stream){
			$this->responseMeta = stream_get_meta_data($stream);
			$this->response     = stream_get_contents($stream);
			fclose($stream);
			return $this->response;
		}else{
			throw new PingerException("Failed to open {$url}.");
		}
	}

	/**
	 * Method to combine the various information necessary to create a stream
	 * context. POST requests send data here
	 *
	 * @param string $method The HTTP method
	 * @param array $data The content of the request
	 * @return resource
	 */
	protected function createHTTPContext($method, $data = null){

		$opts = array("http" => array("method" => strtoupper($method)));

		if($this->headers){
			$opts["http"]["header"] = $this->assembleHeaders();
		}

		if($this->user_agent){ // can be set as a header
			$opts["http"]["user_agent"] = $this->user_agent;
		}

		if($data){
			$opts["http"]["content"] = $data;
		}

		return stream_context_create($opts);
	}

	/**
	 * method to take an array of data and create a valid query string to send as
	 * part of the URL or as the body of the request
	 * @param array $data The data to encode
	 * @return string
	 */
	protected function toQueryString(array $data = array()){
		if(!$data){ return ""; }
		return http_build_query($data, $this->numPrefix, $this->separator, $this->encType);
	}

	/**
	 * method to normalize the headers
	 * @return string
	 */
	protected function assembleHeaders(){
		$finalHeader = array();
		foreach($this->headers as $header => $value){
			$finalHeader[] = sprintf("%s: %s", ucwords($header), trim($value));
		}
		return implode($this->DELIM, $finalHeader);
	}

}

