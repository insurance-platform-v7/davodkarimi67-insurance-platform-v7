<?php

use Illuminate\Http\Request;

$request = Request::create('/api/quotes', 'POST');

$response = app()->handle($request);

echo 'Status: '.$response->getStatusCode().PHP_EOL;
echo $response->getContent().PHP_EOL;
