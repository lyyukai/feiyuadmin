<?php
namespace think {
    require __DIR__ . '/../vendor/autoload.php';
    
    // Get the app
    $app = new App();
    
    // Create a request
    $request = \think\Request::create('/testapi', 'GET');
    
    // Run the app
    $http = $app->http;
    $response = $http->run($request);
    
    echo "Response: " . $response->getContent();
}
