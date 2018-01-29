<?php

namespace Pinger;

interface PingerInterface {
	/**
	 * set the encoding type for built queries
	 */
	public function setEncType($encType);

	/**
	 * set the separator for built queries
	 */
	public function setSeparator($separator);

	/**
	 * set the numeric prefix for built queries
	 */
	public function setNumPrefix($numPrefix);

	/**
	 * method to get the info of the last request
	 * @return array
	 */
	public function getResponseMeta();

	/**
	 * method to get the info of the last request
	 * @return array
	 */
	public function getResponse();

	/**
	 * Method to trigger a GET request to a given endpoint with the given data
	 * @param string $endpoint A valid endpoint
	 * @param array $data The data to send in the request
	 * @return string
	 */
	public function get($endpoint, array $data = array());

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
	public function post($endpoint, $data = "", $urlencoded = true);

	/**
	 * method to set additional headers to be sent with the request
	 * @param array $headers An array of key => value pairs to use to create headers
	 * @return
	 */
	public function setHeaders(array $headers);
}
