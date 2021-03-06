# albelli_test
Albelli backend  (Laravel + swagger) and frontend (React+redux) test

# Relationship between advertisements and offers.
That relation is many-to-many that can only be broken by using a pivot table, which maps each individual advertisement id with each offer id. In Laravel, it can be done quickly by following the conventions (first advertisements_offers table, etc), then you have to manage the relationship in the store, delete and update methods in order not to have consistency errors.

![](/screenshots/many-to-many.png?raw=true )

# Run the containers

## One command to have the app ready
```
docker-compose up --build -d frontend
```
Will run all the required containers (mysql,php,nginx,react build..)

## Only backend
```
docker-compose up --build -d nginx
```
# Ports

## Backend api url
```
localhost:8000/api
```

Swagger UI url
```
localhost:8000/api/documentation
```
## Frontend
React application
```
localhost:8080
```
# Screenshots
## Backend
![](/screenshots/microservices.png?raw=true )
![](/screenshots/swaggerAPIgeneral.png?raw=true )
![](/screenshots/swaggerDetail1.png?raw=true )
![](/screenshots/swaggerDetail2.png?raw=true )
## Frontend
![](/screenshots/uilogin.png?raw=true )
![](/screenshots/uisignup.png?raw=true )
![](/screenshots/uislogged.png?raw=true )
![](/screenshots/uioffersmain.png?raw=true )
![](/screenshots/uioffersaddnew.png?raw=true )
![](/screenshots/uiofferspickdate.png?raw=true )
![](/screenshots/uioffersremoverelated.png?raw=true )
