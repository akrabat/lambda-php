<?php
/**
 *
 * Modified from: https://github.com/pagnihotry/PHP-Lambda-Runtime/blob/master/runtime/runtime.php
 * Copyright (c) 2018 Parikshit Agnihotry
 *
 * RKA Changes:
 *     - JSON encode result of handler function
 *     - Catch any Throwables and write to error log
 */

/**
 * PHP class to interact with AWS Runtime API
 */
class LambdaRuntime
{
    const POST = "POST";
    const GET = "GET";

    private $url;
    private $functionCodePath;
    private $requestId;
    private $response;
    private $rawEventData;
    private $eventPayload;
    private $handler;

    /**
     * Constructor to initialize the class
     */
    function __construct()
    {
        $this->url = "http://".getenv("AWS_LAMBDA_RUNTIME_API");
        $this->functionCodePath = getenv("LAMBDA_TASK_ROOT");
        $this->handler = getenv("_HANDLER");
    }

    /**
     * Get the current request Id being serviced by the runtime
     */
    public function getRequestId() {
        return $this->requestId;
    }

    /**
     * Get the handler setting defined in AWS Lambda configuration
     */
    public function getHandler() {
        return $this->handler;
    }

    /**
     * Get the current event payload
     */
    public function getEventPayload() {
        return $this->eventPayload;
    }

    /**
     * Get the buffered response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Reset the response buffer
     */
    public function resetResponse() {
        $this->response = "";
    }

    /**
     * Add string to the response buffer. This is printed out on success.
     */
    public function addToResponse($str) {
        $this->response = $this->response.$str;
    }

    public function flushResponse() {
        $result = $this->curl(
            "/2018-06-01/runtime/invocation/".$this->getRequestId()."/response",
            LambdaRuntime::POST,
            $this->getResponse()
        );
        $this->resetResponse();
    }

    /**
     * Get the Next event data
     */
    public function getNextEventData() {
        $this->rawEventData = $this->curl("/2018-06-01/runtime/invocation/next", LambdaRuntime::GET);

        if(!isset($this->rawEventData["headers"]["lambda-runtime-aws-request-id"][0])) {
            //Handle error
            $this->reportError(
                "MissingEventData",
                "Event data is absent. EventData:".var_export($this->rawEventData, true)
            );
            //setting up response so the while loop does not try to invoke the handler with unexpected data
            return array("error"=>true);
        }

        $this->requestId = $this->rawEventData["headers"]["lambda-runtime-aws-request-id"][0];

        $this->eventPayload = $this->rawEventData["body"];

        return $this->rawEventData;
    }

    /**
     * Report error to Lambda runtime
     */
    public function reportError($errorType, $errorMessage) {
        $errorArray = array("errorType"=>$errorType, "errorMessage"=>$errorMessage);
        $errorPayload = json_encode($errorArray);
        $result = $this->curl(
            "/2018-06-01/runtime/invocation/".$this->getRequestId()."/error",
            LambdaRuntime::POST,
            $errorPayload
        );
    }

    /**
     * Report initialization error with runtime
     */
    public function reportInitError($errorType, $errorMessage) {
        $errorArray = array("errorType"=>$errorType, "errorMessage"=>$errorMessage);
        $errorPayload = json_encode($errorArray);
        $result = $this->curl(
            "/2018-06-01/runtime/init/error",
            LambdaRuntime::POST,
            $errorPayload
        );
    }

    /**
     * Internal function to make curl requests to the runtime API
     */
    private function curl($urlPath, $method, $payload="") {

        $fullURL = $this->url . $urlPath;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $fullURL);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $headers = [];

        // Parse curl headers
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
          function($curl, $header) use (&$headers)
          {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) // ignore invalid headers
              return $len;

            $name = strtolower(trim($header[0]));
            if (!array_key_exists($name, $headers))
              $headers[$name] = [trim($header[1])];
            else
              $headers[$name][] = trim($header[1]);

            return $len;
          }
        );

        //handle post request
        if($method == LambdaRuntime::POST) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            // Set HTTP Header for POST request
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Length: ' . strlen($payload)
                )
            );
        }

        $response = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array("headers"=>$headers, "body"=>$response, "httpCode" => $httpCode);
    }
}

$lambdaRuntime = new LambdaRuntime();
$handler =  $lambdaRuntime->getHandler();

//Extract file name and function
list($handlerFile , $handlerFunction) = explode(".", $handler);

//Include the handler file
require_once($handlerFile.".php");

//Poll for the next event to be processed

while (true) {

    //Get next event
    $data = $lambdaRuntime->getNextEventData();

    //Check if there was an error that runtime detected with the next event data
    if(isset($data["error"]) && $data["error"]) {
        continue;
    }

    //Process the events
    $eventPayload = $lambdaRuntime->getEventPayload();

    try {
        //Handler is of format Filename.function
        //Execute handler
        $functionReturn = $handlerFunction($eventPayload);
        $json = json_encode($functionReturn, true);
        $lambdaRuntime->addToResponse($json);
    } catch (\Throwable $e) {
        error_log((string)$e);
    }

    //Report result
    $lambdaRuntime->flushResponse();
}
