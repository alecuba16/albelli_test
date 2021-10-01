#!/bin/sh
echo "Checking DB connection ..."

i=0
until [ $i -ge 50 ]; do
  nc -z albellimysql 3306 && break
  i=$(( i + 1 ))
  echo "$i: Waiting for DB 5 second ..."
  sleep 5
done
 
if [ $i -eq 10 ]; then
  echo "DB connection refused, terminating ..."
  exit 1
fi
 
echo "DB is up ..."
export ENV="production"
php artisan --env=production migrate:fresh --seed --force
echo "Migration finished ..."