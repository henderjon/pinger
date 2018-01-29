<?php

class PingerTest extends \PHPUnit_Framework_TestCase {


	function test_get(){

		$dingle = new \Pinger\Pinger("http://httpbin.org");
		$response = $dingle->get("get", array("test_key" => "test_value"));

		$decoded = json_decode($response, true);

		$this->assertInstanceOf("\\Pinger\\PingerInterface", $dingle);
		$this->assertEquals($decoded["args"], ["test_key" => "test_value"]);
		$this->assertEquals($decoded["url"], "http://httpbin.org/get?test_key=test_value");

	}

	function test_post(){

		$dingle = new \Pinger\Pinger("http://httpbin.org/");
		// should strip "/"
		$response = $dingle->post("/post", array("test_key" => "test_value"));

		$decoded = json_decode($response, true);

		$this->assertEquals($decoded["form"], ["test_key" => "test_value"]);
		$this->assertEquals($decoded["url"], "http://httpbin.org/post");

	}

	function test_post_body(){

		$dingle = new \Pinger\Pinger("http://httpbin.org/");
		// should strip "/"
		$dingle->setHeaders(["Content-type" => "text/plain"]);
		$response = $dingle->post("/post", "Post Tenebras Lux", false);

		$decoded = json_decode($response, true);

		$this->assertEquals($decoded["form"], []);
		$this->assertEquals($decoded["data"], "Post Tenebras Lux");
		$this->assertEquals($decoded["url"], "http://httpbin.org/post");

	}

	function test_getResponseMeta(){

		$dingle = new \Pinger\Pinger("http://httpbin.org");
		$response = $dingle->post("post", array("test_key" => "test_value"));

		$info = $dingle->getResponseMeta();

		$this->assertEquals($info["wrapper_type"], "http");

	}

	function test_getResponse(){

		$dingle = new \Pinger\Pinger("http://httpbin.org");
		$response = $dingle->get("headers", array("test_key" => "test_value"));

		$expected = '';
		$expected .= "{\n";
		$expected .= "  \"headers\": {\n";
		$expected .= "    \"Connection\": \"close\", \n";
		$expected .= "    \"Host\": \"httpbin.org\", \n";
		$expected .= "    \"User-Agent\": \"PHP stream_context_create()\"\n";
		$expected .= "  }\n";
		$expected .= "}\n";

		$info = $dingle->getResponse();

		$this->assertEquals($expected, $info);

	}

	function test_setHeaders(){

		$dingle = new \Pinger\Pinger("http://httpbin.org");

		$dingle->setHeaders([
			"User-Agent" => "PHP CLI - Pinger",
		]);

		$response = $dingle->post("post", array("test_key" => "test_value"));

		$decoded = json_decode($response, true);

		$this->assertEquals($decoded["headers"]["User-Agent"], "PHP CLI - Pinger");

	}

}

