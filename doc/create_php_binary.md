# Creating PHP binary 

1. Create a EC2 large instance
2. Copy compile_php.sh to instance

    From this project's root directory:
    
        $ export AWS_IP=ec2-user@{ipaddress}
        $ export SSH_KEY_FILE=~/.ssh/aws-key.rsa
        $ scp -i $SSH_KEY_FILE doc/compile_php.sh $AWS_IP:compile_php.sh
        $ ssh -i $SSH_KEY_FILE -t $AWS_IP "chmod a+x compile_php.sh && ./compile_php.sh 7.3.0"
        $ scp -i $SSH_KEY_FILE $AWS_IP:php-7-bin/bin/php layer/php/php

    (Replace `{ipaddress}` with the IP address of your EC2 instance)

3. Shutdown the EC2 instance


(Full details in [AWS Lambda Custom Runtime for PHP: A Practical Example][1])

[1]: https://aws.amazon.com/blogs/apn/aws-lambda-custom-runtime-for-php-a-practical-example/
