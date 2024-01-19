DROP DATABASE IF EXISTS mafia;
CREATE DATABASE mafia ;
USE mafia;


DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `full_name` varchar(255) NOT NULL,
     `username` varchar(255) NOT NULL,
     `password` varchar(255),
     `is_bot` BOOLEAN DEFAULT 0,
     PRIMARY KEY (`id`),
     UNIQUE KEY `username` (`username`)
) ;

INSERT INTO players (full_name, username, is_bot) VALUES
      ('Luke Skywalker', 'lukeskywalker', 1),
      ('Princess Leia', 'princessleia', 1),
      ('Han Solo', 'hansolo', 1),
      ('Obi-Wan Kenobi', 'obiwankenobi', 1),
      ('Yoda', 'yodaami',  1),
      ('Dardh Vader', 'rankofmaster', 1),
      ('Chewbacca', 'chewbacca', 1),
      ('Lando Calrissian', 'landocalrissian', 1),
      ('Boba Fett', 'bobafett', 1);



DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `role_name` varchar(255) NOT NULL,
     `num_in_10_player_game` int(11) NOT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `role_name` (`role_name`)
);

INSERT INTO `mafia`.`roles` (`id`, `role_name`, `num_in_10_player_game`) VALUES (1, 'Mafia', 3);
INSERT INTO `mafia`.`roles` (`id`, `role_name`, `num_in_10_player_game`) VALUES (2, 'Villager', 5);
INSERT INTO `mafia`.`roles` (`id`, `role_name`, `num_in_10_player_game`) VALUES (3, 'Doctor', 1);
INSERT INTO `mafia`.`roles` (`id`, `role_name`, `num_in_10_player_game`) VALUES (4, 'Cop', 1);

DROP TABLE IF EXISTS `games`;
CREATE TABLE `games` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `start_date` datetime DEFAULT NULL,
     `end_date` datetime DEFAULT NULL,
     `winners` VARCHAR(64),
     PRIMARY KEY (`id`)
);


DROP TABLE IF EXISTS `participants`;
CREATE TABLE `participants` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `game_id` int(11) DEFAULT NULL,
    `player_id` int(11) DEFAULT NULL,
    `role_id` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `players_id` (`player_id`),
    KEY `role_id` (`role_id`),
    KEY `game_id` (`role_id`),
    CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`),
    CONSTRAINT `participants_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
    CONSTRAINT `participants_ibfk_3` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE
);