Please perform the following commands in order to get the project up and running:

1) git clone https://github.com/YegorChechurin/XM-TEST.git && cd XM-TEST
2) docker-compose up -d
3) docker-compose exec php composer install
4) docker-compose exec php php bin/console messenger:consume --no-interaction
5) In your browser type localhost:8080/index