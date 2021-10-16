# containers-aod
Repo for containers aod

2. Config static IP Address on Raspberry Pi
sudo vi /etc/netplan/50-cloud-init.yaml
```
# This file is generated from information provided by the datasource.  Changes
# to it will not persist across an instance reboot.  To disable cloud-init's
# network configuration capabilities, write a file
# /etc/cloud/cloud.cfg.d/99-disable-network-config.cfg with the following:
# network: {config: disabled}
network:
  version: 2
  renderer: networkd
  ethernets:
    eth0:
      addresses:
        - 192.168.1.103/24
      gateway4: 192.168.1.1
      nameservers:
          search: [trpeski.com.au]
          addresses: [192.168.1.1]
```

3. Patch you server
```
sudo apt update && sudo apt full-upgrade -y
sudo apt install -y rpi-eeprom
sudo shutdown -r now
```

4. Set the hostname

```
ubuntu@ubuntu:~$ sudo hostnamectl set-hostname k8s-worker-2.trpeski.com.au
==== AUTHENTICATING FOR org.freedesktop.hostname1.set-static-hostname ===
Authentication is required to set the statically configured local hostname, as well as the pretty hostname.
Authenticating as: Ubuntu (ubuntu)
Password:
==== AUTHENTICATION COMPLETE ===
ubuntu@ubuntu:~$ hostnamectl
   Static hostname: k8s-worker-2.trpeski.com.au
         Icon name: computer
        Machine ID: 7f260523a49e42a9960466af9b19ce9e
           Boot ID: 827b8cd4c71a46558dae89a81ed9c240
  Operating System: Ubuntu 21.04
            Kernel: Linux 5.11.0-1007-raspi
      Architecture: arm64
```

5. Set the timezone
```
ubuntu@k8s-worker-2:~$ timedatectl list-timezones | grep -i melbourne
Australia/Melbourne
ubuntu@k8s-worker-2:~$ timedatectl
               Local time: Tue 2021-09-21 11:24:31 UTC
           Universal time: Tue 2021-09-21 11:24:31 UTC
                 RTC time: n/a
                Time zone: Etc/UTC (UTC, +0000)
System clock synchronized: yes
              NTP service: active
          RTC in local TZ: no
ubuntu@k8s-worker-2:~$ sudo timedatectl set-timezone Australia/Melbourne
==== AUTHENTICATING FOR org.freedesktop.timedate1.set-timezone ===
Authentication is required to set the system timezone.
Authenticating as: Ubuntu (ubuntu)
Password:
==== AUTHENTICATION COMPLETE ===
ubuntu@k8s-worker-2:~$ timedatectl
               Local time: Tue 2021-09-21 21:24:54 AEST
           Universal time: Tue 2021-09-21 11:24:54 UTC
                 RTC time: n/a
                Time zone: Australia/Melbourne (AEST, +1000)
System clock synchronized: yes
              NTP service: active
          RTC in local TZ: no
```

6. Disable Wifi and Bluetoot
sudo vi /boot/firmware/config.txt
Add
```
dtoverlay=disable-wifi
dtoverlay=disable-bt
```

7. Update hosts DNS
sudo vi /etc/hosts
```
# Kubernetes Servers
192.168.1.101 k8s-master k8s-master.trpeski.com.au
192.168.1.102 k8s-worker-1 k8s-worker-1.trpeski.com.au
192.168.1.103 k8s-worker-2 k8s-worker-2.trpeski.com.au
```
8. Update the cname groups. Append the following to the end of the line at /boot/firmware/cmdline.txt 
```
sudo vi /boot/firmware/cmdline.txt 
cgroup_enable=memory swapaccount=1 cgroup_memory=1 cgroup_enable=cpuset
```

9. Run the following commands on all 3 EC2 Instances to install Docker and Kubernetes.
```
  sudo apt update
  sudo apt install apt-transport-https ca-certificates curl gnupg-agent software-properties-commonsoftware-properties-common
  curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
	sudo add-apt-repository "deb [arch=arm64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"
  curl -s https://packages.cloud.google.com/apt/doc/apt-key.gpg | sudo apt-key add -
  sudo awk 'BEGIN{ printf "deb https://apt.kubernetes.io/ kubernetes-xenial main" >> "/etc/apt/sources.list.d/kubernetes.list"  }'
  sudo apt update
  sudo apt install -y docker-ce-cli=5:20.10.9~3-0~ubuntu-focal docker-ce=5:20.10.9~3-0~ubuntu-focal containerd.io=1.4.11-1

  sudo usermod -aG docker $USER
  newgrp docker
```

10. Configure the docker daemon
sudo vi /etc/docker/daemon.json
{
  "exec-opts": ["native.cgroupdriver=systemd"],
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "100m"
  },
  "storage-driver": "overlay2"
}

11. Disable IPv6
```
cat <<EOF | sudo tee /etc/sysctl.d/k8s.conf

net.bridge.bridge-nf-call-iptables = 1
EOF
sudo sysctl --system
```
12. Restart Docker
sudo systemctl enable docker
sudo systemctl daemon-reload
sudo systemctl restart docker

12. Install Kubernetes and hold the packages
```
sudo apt install -y kubelet=1.22.2-00 kubeadm=1.22.2-00 kubectl=1.22.2-00
sudo apt-mark hold kubelet kubeadm kubectl
```

13. Run the following command on the Kubernetes master. (Record the cluser join string from the end of this command)
```
ubuntu@k8s-master:~$ sudo kubeadm init --pod-network-cidr=10.244.0.0/16
[init] Using Kubernetes version: v1.22.2
[preflight] Running pre-flight checks
	[WARNING SystemVerification]: missing optional cgroups: hugetlb
[preflight] Pulling images required for setting up a Kubernetes cluster
[preflight] This might take a minute or two, depending on the speed of your internet connection
[preflight] You can also perform this action in beforehand using 'kubeadm config images pull'
[certs] Using certificateDir folder "/etc/kubernetes/pki"
[certs] Generating "ca" certificate and key
[certs] Generating "apiserver" certificate and key
[certs] apiserver serving cert is signed for DNS names [k8s-master.trpeski.com.au kubernetes kubernetes.default kubernetes.default.svc kubernetes.default.svc.cluster.local] and IPs [10.96.0.1 192.168.1.101]
[certs] Generating "apiserver-kubelet-client" certificate and key
[certs] Generating "front-proxy-ca" certificate and key
[certs] Generating "front-proxy-client" certificate and key
[certs] Generating "etcd/ca" certificate and key
[certs] Generating "etcd/server" certificate and key
[certs] etcd/server serving cert is signed for DNS names [k8s-master.trpeski.com.au localhost] and IPs [192.168.1.101 127.0.0.1 ::1]
[certs] Generating "etcd/peer" certificate and key
[certs] etcd/peer serving cert is signed for DNS names [k8s-master.trpeski.com.au localhost] and IPs [192.168.1.101 127.0.0.1 ::1]
[certs] Generating "etcd/healthcheck-client" certificate and key
[certs] Generating "apiserver-etcd-client" certificate and key
[certs] Generating "sa" key and public key
[kubeconfig] Using kubeconfig folder "/etc/kubernetes"
[kubeconfig] Writing "admin.conf" kubeconfig file
[kubeconfig] Writing "kubelet.conf" kubeconfig file
[kubeconfig] Writing "controller-manager.conf" kubeconfig file
[kubeconfig] Writing "scheduler.conf" kubeconfig file
[kubelet-start] Writing kubelet environment file with flags to file "/var/lib/kubelet/kubeadm-flags.env"
[kubelet-start] Writing kubelet configuration to file "/var/lib/kubelet/config.yaml"
[kubelet-start] Starting the kubelet
[control-plane] Using manifest folder "/etc/kubernetes/manifests"
[control-plane] Creating static Pod manifest for "kube-apiserver"
[control-plane] Creating static Pod manifest for "kube-controller-manager"
[control-plane] Creating static Pod manifest for "kube-scheduler"
[etcd] Creating static Pod manifest for local etcd in "/etc/kubernetes/manifests"
[wait-control-plane] Waiting for the kubelet to boot up the control plane as static Pods from directory "/etc/kubernetes/manifests". This can take up to 4m0s
[apiclient] All control plane components are healthy after 29.009889 seconds
[upload-config] Storing the configuration used in ConfigMap "kubeadm-config" in the "kube-system" Namespace
[kubelet] Creating a ConfigMap "kubelet-config-1.22" in namespace kube-system with the configuration for the kubelets in the cluster
[upload-certs] Skipping phase. Please see --upload-certs
[mark-control-plane] Marking the node k8s-master.trpeski.com.au as control-plane by adding the labels: [node-role.kubernetes.io/master(deprecated) node-role.kubernetes.io/control-plane node.kubernetes.io/exclude-from-external-load-balancers]
[mark-control-plane] Marking the node k8s-master.trpeski.com.au as control-plane by adding the taints [node-role.kubernetes.io/master:NoSchedule]
[bootstrap-token] Using token: i4zmdl.j468p07mxis4ye3g
[bootstrap-token] Configuring bootstrap tokens, cluster-info ConfigMap, RBAC Roles
[bootstrap-token] configured RBAC rules to allow Node Bootstrap tokens to get nodes
[bootstrap-token] configured RBAC rules to allow Node Bootstrap tokens to post CSRs in order for nodes to get long term certificate credentials
[bootstrap-token] configured RBAC rules to allow the csrapprover controller automatically approve CSRs from a Node Bootstrap Token
[bootstrap-token] configured RBAC rules to allow certificate rotation for all node client certificates in the cluster
[bootstrap-token] Creating the "cluster-info" ConfigMap in the "kube-public" namespace
[kubelet-finalize] Updating "/etc/kubernetes/kubelet.conf" to point to a rotatable kubelet client certificate and key
[addons] Applied essential addon: CoreDNS
[addons] Applied essential addon: kube-proxy

Your Kubernetes control-plane has initialized successfully!

To start using your cluster, you need to run the following as a regular user:

  mkdir -p $HOME/.kube
  sudo cp -i /etc/kubernetes/admin.conf $HOME/.kube/config
  sudo chown $(id -u):$(id -g) $HOME/.kube/config

Alternatively, if you are the root user, you can run:

  export KUBECONFIG=/etc/kubernetes/admin.conf

You should now deploy a pod network to the cluster.
Run "kubectl apply -f [podnetwork].yaml" with one of the options listed at:
  https://kubernetes.io/docs/concepts/cluster-administration/addons/

Then you can join any number of worker nodes by running the following on each as root:

kubeadm join 192.168.1.101:6443 --token REMOVED \
	--discovery-token-ca-cert-hash sha256:REMOVED
```
    
11. Run the following commands on the Kubernetes master.
```
sudo mkdir -p $HOME/.kube
sudo cp -i /etc/kubernetes/admin.conf $HOME/.kube/config
sudo chown $(id -u):$(id -g) $HOME/.kube/config
```

12. Install the Calico CNI
kubectl apply -f https://docs.projectcalico.org/v3.20/manifests/calico.yaml

14. Run the cluster join command as an addministrator by adding sudo to the cluster join command you go from step 11.
```
ubuntu@k8s-worker-1:~$ sudo kubeadm join 192.168.1.101:6443 --token REMOVED \
> --discovery-token-ca-cert-hash sha256:REMOVED
[preflight] Running pre-flight checks
	[WARNING SystemVerification]: missing optional cgroups: hugetlb
[preflight] Reading configuration from the cluster...
[preflight] FYI: You can look at this config file with 'kubectl -n kube-system get cm kubeadm-config -o yaml'
[kubelet-start] Writing kubelet configuration to file "/var/lib/kubelet/config.yaml"
[kubelet-start] Writing kubelet environment file with flags to file "/var/lib/kubelet/kubeadm-flags.env"
[kubelet-start] Starting the kubelet
[kubelet-start] Waiting for the kubelet to perform the TLS Bootstrap...

This node has joined the cluster:
* Certificate signing request was sent to apiserver and a response was received.
* The Kubelet was informed of the new secure connection details.

Run 'kubectl get nodes' on the control-plane to see this node join the cluster.
```

#############
Run the following commands on your Kubernetes Master.
#############

15. Run the following command on the master node to check the status of the cluster and the nodes. (This might take a few minutes)

```
ubuntu@k8s-master:~$ kubectl get nodes
NAME                          STATUS     ROLES                  AGE    VERSION
k8s-master.trpeski.com.au     NotReady   control-plane,master   2m1s   v1.22.2
k8s-worker-1.trpeski.com.au   NotReady   <none>                 5s     v1.22.2
ubuntu@k8s-master:~$ kubectl cluster-info
Kubernetes control plane is running at https://192.168.1.101:6443
CoreDNS is running at https://192.168.1.101:6443/api/v1/namespaces/kube-system/services/kube-dns:dns/proxy

To further debug and diagnose cluster problems, use 'kubectl cluster-info dump'.
ubuntu@k8s-master:~$ kubectl get nodes
NAME                          STATUS     ROLES                  AGE     VERSION
k8s-master.trpeski.com.au     NotReady   control-plane,master   2m54s   v1.22.2
k8s-worker-1.trpeski.com.au   NotReady   <none>                 58s     v1.22.2
k8s-worker-2.trpeski.com.au   NotReady   <none>                 34s     v1.22.2
```

11. Create a node.js file with the following contents

```
cat <<EOF > node.js
var http = require('http');

http.createServer(function (req, res) {
    res.writeHead(200, {'Content-Type': 'text/html'});
    res.end('I Am A Kubernetes Master!!!');
}).listen(8080);
EOF
```

12. Create a docker file with the following contents
```
cat <<EOF > Dockerfile
FROM node:15.5.1-alpine3.12

RUN \
apk update && \
apk upgrade && \
apk add bash iputils nodejs

COPY node.js /node.js

EXPOSE 8080

ENTRYPOINT [ "node", "/node.js" ]
EOF
```

13. Create an account at docker hub if you don't have one. https://www.docker.com/

14. Login to your docker command using the docker cli. Run the following command and insert your docker hub username and password.
    docker login

15. Build your docker container using your new or existing docker id.
    sudo docker build -t dockerid/containername:dockertag .
    

16. Push your docker image to docker hub.
    sudo docker push dockerid/containername:dockertag
    sudo docker push ltrpeski/nodesample:0.1

17. Create a kubernetes deployment file
```
cat <<EOF > deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: nodejs-deployment
  labels:
    app: nodejs
spec:
  replicas: 2
  selector:
    matchLabels:
      app: nodejs
  template:
    metadata:
      labels:
        app: nodejs
    spec:
      containers:
      - name: nodejs
        image: ltrpeski/nodesample:0.2
        ports:
        - containerPort: 8080
EOF
```

18. Apply your deployment file.
    kubectl apply -f deployment.yaml

19. Check your deployment and your pods.
    kubectl get deployment -o wide    
    kubectl get pods -o wide
    kubectl describe pod nodejs

20. Create a nodeport file to expose your service publically.
cat <<EOF > nodeport.yaml
apiVersion: v1
kind: Service
metadata:
  name: nodejs
spec:
  type: NodePort
  selector:
    app: nodejs
  ports:
    - protocol: TCP
      port: 8080
      targetPort: 8080
EOF

21. Apply your nodeport file.
    kubectl apply -f nodeport.yaml

22. Check your nodeport service. You should receipt a external cluster IP and Port.
    kubectl get service nodejs -o wide
    kubectl describe service nodejs
    $ kubectl get service nodejs -o wide
    NAME     TYPE       CLUSTER-IP       EXTERNAL-IP   PORT(S)          AGE     SELECTOR
    nodejs   NodePort   10.102.193.112   <none>        8080:31240/TCP   5m34s   app=nodejs

23. Test your service using the cluster IP Address and the external port you received in step 21.
    $ curl -I http://10.102.193.112:8080
    HTTP/1.1 200 OK
    Content-Type: text/html
    Date: Mon, 11 Jan 2021 04:32:51 GMT
    Connection: keep-alive
    Keep-Alive: timeout=5

    $ curl -l http://10.102.193.112:8080
    I Am A Kubernetes Master!!!

24. Modify the security group of your k8s-worker security group to allow http access using the external port from your cluster IP.
    Rules
        Type        Protocol    Port Range  Sources     Description
        Custom TCP  TCP         31240       0.0.0.0/0   Outbound http access

25. Insert the Public IP Address of your kubernetes working into a browser followed by the external service port. You should see your NodeJS Application

http://54.206.72.21:31240/
147dda4acc43:~ trpeski$ curl 54.206.72.21:31240/
I Am A Kubernetes Master!!!