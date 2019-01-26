<?php declare(strict_types=1);

class Context
{
    /**
     * The name of the Lambda function.
     *
     * @var string
     */
    public $functionName;

    /**
     * The version of the function.
     *
     * @var string
     */
    public $functionVersion;

    /**
     * The Amazon Resource Name (ARN) used to invoke the function. Indicates if the invoker specified a version number or alias.
     *
     * @var string
     */
    public $invokedFunctionArn;

    /**
     * The amount of memory configured on the function.
     *
     * @var string
     */
    public $memoryLimitInMb;

    /**
     * The identifier of the invocation request.
     *
     * @var string
     */
    public $awsRequestID;

    /**
     * The log group for the function.
     *
     * @var string
     */
    public $logGroupName;

    /**
     * The log stream for the function instance.
     *
     * @var string
     */
    public $logStreamName;

    /**
     * The date that the execution times out, in Unix time milliseconds.
     *
     * @var string
     */
    public $deadlineMs;

    /**
     * (mobile apps) Information about the Amazon Cognito identity that authorized the request. (JSON?)
     *
     * @var string
     */
    public $identity;

    /**
     * (mobile apps) Client context provided to the Lambda invoker by the client application.  (JSON?)
     *
     * @var string
     */
    public $clientContext;

    /**
     * The name of the AWS region where the Lambda function is executed
     *
     * @var string
     */
    public $awsRegion; // The name of the AWS region where the Lambda function is executed

    /**
     * The name of the AWS X-Ray tracing header.
     *
     * @var string
     */
    public $awsTraceId;

    /**
     * Returns the number of milliseconds left before the execution times out.
     * @return float
     */
    public function getRemainingTimeInMillis()
    {
        return ((float)$this->deadlineMs) - (microtime(true)*1000.0);
    }
}
