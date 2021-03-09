The source code for crumble-online.com.


I use UserSpice for user management and a lot of the php functionality like database queries and accessing 
the GET and POST parameters. (https://userspice.com/)



Instructions for running this code locally:

I use xampp because it's convenient but you just need some way to run an apache server and a SQL database.

- Clone the repo into the directory where your public html files go. (For xampp: xampp/htdocs)
- Open up the site in your browser (probably http://localhost)
- Go through the steps to setup UserSpice (if using xampp: Database Host is localhost)
- Once UserSpice is setup, delete the install directory. You can also delte the if statement at the start of index.php
- The default login info for UserSpice should be username: admin, password: password
- To setup the database run the queries shown below. They will create the tables that the site uses to keep
track of the games and challenges. If you named your database something other than crumble, you will need
to change the name in the first line of each query.

```
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


