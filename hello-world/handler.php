<?php
function hello($eventData) : array
{
    echo "We can send info the to log. This is \$eventData:\n";
    print_r($eventData);

    $response = [
        'msg' => 'hello from PHP '.PHP_VERSION,
        'eventData' => $eventData,
    ];

    return $response;
}
