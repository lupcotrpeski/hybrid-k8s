1. Build the wordpress container for single architecture using the following command
```
docker build -t ltrpeski/wordpress:0.12 .
```

2. Build the wordpress container for multiple architectures using the following command
```
docker buildx build --platform linux/amd64,linux/arm64,linux/arm/v7 -t ltrpeski/wordpress:0.12 --push .
```

3. Create a secrets.yaml file for Kubernetes with your database username and password. Don't commit this to git.
```
apiVersion: v1
kind: Secret
metadata:
  name: rds
  namespace: dev
type: kubernetes.io/basic-auth
stringData:
  username: "wordpress"
  password: "password_lives_here"
```

3. Deploy to Kubernetes cluster using 
```
kubectl apply -f secrets.yaml
kubectl apply -f wordpress-service.yaml
kubectl apply -f wordpress.yaml
```