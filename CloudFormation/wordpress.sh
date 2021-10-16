owner=aws
environment=dev

aws cloudformation update-stack --stack-name ${owner}-${environment}-wordpress \
--parameters \
ParameterKey=Owner,ParameterValue=${owner} \
ParameterKey=Environment,ParameterValue=${environment} \
ParameterKey=VpcId,ParameterValue=vpc-id \
ParameterKey=CustomerGatewayCIDR,ParameterValue=192.168.1.0/24 \
ParameterKey=Target1,ParameterValue=192.168.1.101 \
ParameterKey=Target2,ParameterValue=192.168.1.102 \
ParameterKey=Target3,ParameterValue=192.168.1.103 \
ParameterKey=PublicSubnets,ParameterValue="subnet-id\,subnet-id\,subnet-id" \
ParameterKey=PrivateSubnets,ParameterValue="subnet-id\,subnet-id\,subnet-id" \
ParameterKey=DBName,ParameterValue=wordpress \
ParameterKey=DBUser,ParameterValue=wordpress \
ParameterKey=DBPassword,ParameterValue=password-lives-here \
--template-body file://wordpress.json \
  --capabilities CAPABILITY_NAMED_IAM;