The source code for crumble-online.com.


I use UserSpice for user management and a lot of the php functionality like database queries and accessing 
the GET and POST parameters. (https://userspice.com/)



Instructions for running this code locally:

I use xampp because it's convenient but you just need some way to run an apache server and a SQL database.

- To setup the database, run the queries shown below. They will create the database, a user for UserSpice
  to use, and the tables that the site uses to keep track of the games and challenges.
- Clone the repo into the directory where your public html files go. (For xampp: xampp/htdocs)
- Open up the site in your browser (probably http://localhost)
- Go through the steps to setup UserSpice (Database Host is probably localhost, username is username and password is password)
- Once UserSpice is setup, either delete the install directory or delete the if statement at the start of index.php that checks if install/index.php exists
- The default login info for UserSpice should be username: admin, password: password

```
CREATE DATABASE `crumble`;

CREATE USER 'username'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON `crumble`.* TO 'username'@'localhost';

CREATE TABLE `crumble`.`games_completed` (
  `id` int(11) NOT NULL,
  `user_id_black` int(11) NOT NULL,
  `user_id_white` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `moves` text NOT NULL,
  `extra` char(1) NOT NULL DEFAULT 'n',
  `winner` varchar(1) NOT NULL,
  `end_time` int(10) UNSIGNED NOT NULL DEFAULT current_timestamp(),
  `time_black` mediumint(9) DEFAULT NULL,
  `time_white` mediumint(9) DEFAULT NULL,
  PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `crumble`.`games_current` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id_black` int(11) NOT NULL,
  `user_id_white` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `moves` text NOT NULL,
  `extra` char(1) NOT NULL DEFAULT 'n',
  `time` int(10) UNSIGNED DEFAULT current_timestamp(),
  `time_black` mediumint(9) DEFAULT NULL,
  `time_white` mediumint(9) DEFAULT NULL,
  PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `crumble`.`game_challenges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plays` varchar(1) NOT NULL,
  `extra` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
```


