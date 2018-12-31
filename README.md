# AWS Lambda PHP Hello World

The basics of using [Serverless Framework][1] for AWS Lambda PHP applications.

## Notes

1. Create php binary by following steps in `doc/create_php_binary.md`
2. Write your serverless application (!)
2. sls deploy
3. sls invoke -f hello -l

## PHP handler function signature

The signature for the PHP function is:

    function main($eventData) : array

Hello world looks like:

    <?php
    function hello($eventData) : array
    {
        return ["msg" => "Hello from PHP " . PHP_VERSION];
    }


[1]: https://serverless.com
