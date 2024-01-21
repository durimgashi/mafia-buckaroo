## Project Information

In this project, I have employed a self-developed structure that incorporates a custom routing mechanism. Routes are defined in the `./routes/routes.php` file, where endpoints are specified as follows:

```php
$routes = [
    'test' => [
        'controller' => 'TestController',
        'method' => 'testMethod',
        'http_method' => 'GET'
    ],
    // Additional routes...
];
````
These routes are dispatched by the `./routes/Router.php` file.
The application entry point is the `index.php` file in the root of the project.

## Running the Application Using Docker

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

## Running the Application without Docker

If you would prefer to run the application without using docker, then you will need to follow a few steps:

- Manually create a database in you local machine
- Copy the structure and data found in `./database/database_dump.sql` file and run it in your newly created database
- Add the database credentials in the following file: `./utils/constants.php`
- Run `composer install` in the terminal
- Run the php application by executing the following command:

```bash
    php -S 127.0.0.1:5555 index.php
```

# Game Instructions

Upon launching the application, you will be prompted to provide user login information. If you don't have a user account, simply navigate to the Register link to create one. After successfully creating a user, you will be automatically be logged in with your new user. Next, you will be presented with a page featuring two buttons:

- Start Game: Initiates a new Mafia game.
- Change Player: Logs out the current user, redirecting to the login page.
## In-Game Dynamics
This game comprises 10 players, including the user and 9 bots. Roles are randomly assigned upon starting a new Mafia game:

- Villager (5)
- Mafia (3)
- Doctor (1)
- Cop (1)

## Day Cycle

During the Day cycle, players cast votes to identify potential Mafia members. If a clear quorum is reached for a certain player, they are jailed and removed from the game.

## Night Cycle

Different actions occur based on roles:

- Mafia: Votes to eliminate a player; if a quorum is reached, the player is removed.
- Doctor: Can save a player from Mafia elimination, including themselves.
- Cop: Investigates a player's Mafia affiliation.
- Villagers: Observe as other roles take action.

## Game Conclusion Criteria

The game reaches its conclusion under the following circumstances:

- The logged-in user is eliminated by the Mafia.
- The Mafia outnumbers the other roles.
- All Mafia members are eliminated.





