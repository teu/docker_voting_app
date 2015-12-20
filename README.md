# Docker demo voting app

1. Install docker and docker-compose
2. Run the infrastructure (may take a while)
```bash
sudo docker-compose up
```
3. Import data structure to DB:
```bash
sudo docker ps | grep uxpindockerapp_db | awk '{ print $1}' | sudo xargs docker inspect --format '{{ .NetworkSettings.IPAddress }}' | xargs -I %  sh -c 'mysql -uadmin --password=adminPass -h % app < structure.sql'
```
4. Run command to get current haproxy IP:
```bash
sudo docker ps | grep haproxy | awk '{ print $1}' | sudo xargs docker inspect --format '{{ .NetworkSettings.IPAddress }}' 

```
5. Go to IP in your browser
