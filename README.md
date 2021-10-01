# albelli_test
Albelli backend  (Laravel + swagger) and frontend (React+redux) test

# Run the containers

## One command to have the app ready
docker-compose up --build -d frontend

Will run all the required containers (mysql,php,nginx,react build..)

## Only backend

docker-compose up --build -d nginx

# Ports

## Backend api url
localhost:8000/api

localhost:8000/api/documentation  is the swagger UI.

## Frontend
localhost:8080

