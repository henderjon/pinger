#!/usr/bin/env php
<?php

require "vendor/autoload.php";

use \Pinger\Pinger;

$api = "http://httpbin.org";

$dingle = new Pinger($api);

//$var = $Obj->HTTPMethod($endoint, $queryData);
$dingle->get("headers", array("test_key" => "test_value"));

print_r($dingle->getResponseMeta());

echo "\n\n" . str_repeat("***", 50) . "\n\n";

print_r($dingle->getResponse());

