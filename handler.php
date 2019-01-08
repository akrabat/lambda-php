<?php
function hello($eventData) : array
{
    $response = ['msg' => 'hello from PHP '.PHP_VERSION];
    $response['eventData'] = $eventData;
    $data = json_decode($eventData);
    $response['data'] = $data;
    return $response;
}
