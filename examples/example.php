#!/usr/bin/env php
<?php

require "vendor/autoload.php";

use \Pinger\Pinger;

$api = "http://httpbin.org";

$endpoints = array(
	"get",
	"post",
	"user-agent",
	"headers",
	"ip",
);

$dingle = new Pinger($api, $endpoints);

$dingle->setHeaders([
	"User-Agent" => "PHP CLI - Pinger",
]);

//$var = $Obj->HTTPMethod($endoint, $queryData);
$response = $dingle->post("post", array("test_key" => "test_value"));

print_r($response);

