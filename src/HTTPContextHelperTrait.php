<?php

namespace Pinger;

trait HTTPContextHelperTrait {

	/**
	 * attributes required by stream_context_create()
	 */
	protected $method, $user_agent;

	/**
	 * attribute to store the headers to be used for the request
	 */
	protected $headers = array();

	private $DELIM = "\r\n";


	/**
	 * method to set additional headers to be sent with the request
	 * @param array $headers An array of key => value pairs to use to create headers
	 * @return
	 */
	function setHeaders(array $headers){
		foreach($headers as $header => $value){
			$this->headers[strtolower($header)] = trim($value);
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
	protected function createHTTPContext($method, array $data = array()){

		$opts = array("http" => array("method" => strtoupper($method)));

		if($this->headers){
			$opts["http"]["header"] = $this->assembleHeaders();
		}

		if($this->user_agent){ // can be set as a header
			$opts["http"]["user_agent"] = $this->user_agent;
		}

		if($data){
			$opts["http"]["content"] = $this->toQueryString($data);
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
		return http_build_query($data, "no_", "&");
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