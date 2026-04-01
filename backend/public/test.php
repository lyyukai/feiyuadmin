<?php
require __DIR__ . '/../vendor/autoload.php';

$app = new think\App();
$http = $app->http;

// Dispatch and get the request inside the app
$response = $http->run();

// Get the actual request from the app container
$request = $app->make('request');

echo "Pathinfo: " . $request->pathinfo() . "\n";
echo "Path: " . $request->path() . "\n";
echo "Method: " . $request->method() . "\n";
