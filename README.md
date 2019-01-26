# AWS Lambda PHP Hello World

The basics of using [Serverless Framework][1] for AWS Lambda PHP applications.

## Notes

1. Install Serverless Framework by following the [Quick Start][2]
2. Set up your [AWS credentials][3]
3. Create php binary layer and upload to AWS by following steps in [`php-runtime/README.md`][4]
4. Write your serverless application (!) - the default is in `hello-world/handler.php`
5. Run `sls deploy` to deploy to Lambda
6. Run `sls invoke -f hello -l` to invoke your function


As we've used Docker to create the runtime, you can test locally using the `Dockerfile` in `hello-world`:

    $ cd hello-world
    $ docker build -t lambda-php-test . && docker run lambda-php-test handler.hello '{"name": "world"}'


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
[2]: https://serverless.com/framework/docs/providers/aws/guide/quick-start/
[3]: https://serverless.com/framework/docs/providers/aws/guide/credentials/
[4]: php-runtime/README.md
