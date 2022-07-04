docker run -d -p 8000:80 -v ${PWD}:/var/www --network db-project-mysql_appnet db-project-php:v2 
