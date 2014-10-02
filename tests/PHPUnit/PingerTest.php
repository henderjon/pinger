<?php

class PingerTest extends \PHPUnit_Framework_TestCase {


	function test_get(){

		$dingle = new \Pinger\Pinger("http://httpbin.org", ["get", "post", "headers"]);
		$response = $dingle->get("get", array("test_key" => "test_value"));

		$decoded = json_decode($response, true);

		$this->assertEquals($decoded["args"], ["test_key" => "test_value"], "get response 'form' property");
		$this->assertEquals($decoded["url"], "http://httpbin.org/get?test_key=test_value", "get response 'url' property");

	}

	function test_post(){

		$dingle = new \Pinger\Pinger("http://httpbin.org", ["get", "post", "headers"]);
		$response = $dingle->post("post", array("test_key" => "test_value"));

		$decoded = json_decode($response, true);

		$this->assertEquals($decoded["form"], ["test_key" => "test_value"], "post response 'form' property");
		$this->assertEquals($decoded["url"], "http://httpbin.org/post", "post response 'url' property");

	}

	function test_getInfo(){

		$dingle = new \Pinger\Pinger("http://httpbin.org", ["get", "post", "headers"]);
		$response = $dingle->post("post", array("test_key" => "test_value"));

		$info = $dingle->getInfo();

		$this->assertEquals($info["wrapper_type"], "http");

	}

	function test_setHeaders(){

		$dingle = new \Pinger\Pinger("http://httpbin.org", ["get", "post", "headers"]);

		$dingle->setHeaders([
			"User-Agent" => "PHP CLI - Pinger",
		]);

		$response = $dingle->post("post", array("test_key" => "test_value"));

		$decoded = json_decode($response, true);

		$this->assertEquals($decoded["headers"]["User-Agent"], "PHP CLI - Pinger");

	}

}