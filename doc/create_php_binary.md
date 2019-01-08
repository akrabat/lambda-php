# Creating PHP binary 

1. Create a EC2 large instance based on 
[the current supported version AMI](https://docs.aws.amazon.com/lambda/latest/dg/current-supported-versions.html).
2. Connect to the instance after it boots to accept it to your known hosts:

        $ export AWS_IP=ec2-user@{ipaddress}
        $ export SSH_KEY_FILE=~/.ssh/aws-key.rsa
        $ ssh -i $SSH_KEY_FILE $AWS_IP
        $ exit

    (Replace `{ipaddress}` with the IP address of your EC2 instance)

3. Copy compile_php.sh to instance

    From this project's root directory:
    
        $ scp -i $SSH_KEY_FILE doc/compile_php.sh $AWS_IP:compile_php.sh
        $ ssh -i $SSH_KEY_FILE -t $AWS_IP "chmod a+x compile_php.sh && ./compile_php.sh 7.3.0"
        $ scp -i $SSH_KEY_FILE $AWS_IP:php-7-bin/bin/php layer/php/php

     (If you don't need/want to compile PHP from source, you can save the 
      [version directly from GitHub](https://github.com/araines/serverless-php/raw/master/php) 
      to layer/php/php)

4. Shutdown the EC2 instance


(Full details in [AWS Lambda Custom Runtime for PHP: A Practical Example][1])

[1]: https://aws.amazon.com/blogs/apn/aws-lambda-custom-runtime-for-php-a-practical-example/
