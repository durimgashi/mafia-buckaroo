## Application Setup (Using Docker)

As a prerequisite to running the application this way, you will need to have `docker` and `docker-compose` installed.

This repository contains a Dockerized Setup of the Mafia game. The application consists of two services: MySQL and PHP. Docker Compose is used to orchestrate these services.

Build and run the Docker containers by running the following command in the terminal:

```bash
    docker compose up -d --build
```

This will start the MySQL and PHP containers.

- The PHP application should be accessible on `localhost:5555` (Port `5555` is mapped to port `80` inside the php-container, which runs an Apache Web Server)
- MySQL should be accessible on `localhost:3307` (Port `3307` is mapped to port `3306` inside the mysql-container. You can use the credentials inside the `mysql.env` file).


To stop the application and remove the containers, run:

```bash
    docker compose down
```

---

## Application Setup (Without Docker)

If you would prefer to run the application without using docker, then you will need to follow a few steps:

- Manually create a database in you local machine
- Copy the structure and data found in `./database/database_dump.sql` file and run it in your newly created database
- Add the database credentials in the following file: `./utils/constants.php`
- Run `composer install` in the terminal
- Run the php application by executing the following command:

```bash
    php -S 127.0.0.1:5555 index.php
```




