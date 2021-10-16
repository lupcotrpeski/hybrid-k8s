aws cloudformation update-stack --stack-name transitgateway-vpn \
--parameters \
ParameterKey=Owner,ParameterValue=aws \
ParameterKey=VPC,ParameterValue=vpc-id \
ParameterKey=CustomerGatewayCIDR,ParameterValue=192.168.1.0/24 \
ParameterKey=CustomerGatewayIp,ParameterValue=`curl checkip.amazonaws.com` \
ParameterKey=RouteTableSubnetA,ParameterValue=rtb-id \
ParameterKey=RouteTableSubnetB,ParameterValue=rtb-id \
ParameterKey=RouteTableSubnetC,ParameterValue=rtb-id \
ParameterKey=RouteTablePublic,ParameterValue=rtb-id \
ParameterKey=SubnetA,ParameterValue=subnet-id \
ParameterKey=SubnetB,ParameterValue=subnet-id \
ParameterKey=SubnetC,ParameterValue=subnet-id \
--template-body file://transitgateway.json \
  --capabilities CAPABILITY_NAMED_IAM;