# Docker demo voting app

* Pull repository
* Install [composer](https://getcomposer.org/download/)
* cd to repository and:
```bash
composer.phar install
```
* Install [docker](https://docs.docker.com/engine/installation/) and [docker-compose](https://docs.docker.com/compose/install/)
* Make sure your ports 80 and 3306 are NOT bound.
* Run the infrastructure in background (may take a while)
```bash
sudo docker-compose up -d
```
* Import data structure to DB:
```bash
sudo docker ps | grep uxpindb | awk '{ print $1}' | sudo xargs docker inspect --format '{{ .NetworkSettings.IPAddress }}' | xargs -I %  sh -c 'mysql -uadmin --password=adminPass -h % app < structure.sql'
```
* Run command to get current haproxy IP:
```bash
sudo docker ps | grep haproxy | awk '{ print $1}' | sudo xargs docker inspect --format '{{ .NetworkSettings.IPAddress }}' 

```
* Go to IP in your browser
