<?php
require_once('../lib/sqlLib.php');
$db = new PDO('mysql:host=127.0.0.1;charset=utf8mb4', 'root', '');


$db->query("DROP SCHEMA IF EXISTS `liltvolontari`;");
echo "schema dropped...<br />";

$db->query("CREATE SCHEMA `liltvolontari`;");
echo "schema created...<br />";



$db = new DbConnection();
echo "db selected...<br />";


$db->query("
create table Users (
	id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	firstname VARCHAR(30) NOT NULL,
	lastname VARCHAR(30) NOT NULL,
	email VARCHAR(50),
	psw VARCHAR(150),
	phone VARCHAR(20),
	address VARCHAR(75),
	address2 VARCHAR(75),
	city VARCHAR(50),
	prov VARCHAR(15),
	cap VARCHAR(6),
	state VARCHAR(50),
	position INT(2),
	permessi INT(2)

) ENGINE=InnoDB;");
echo "users table created<br />";


$db->query("
	INSERT INTO `liltvolontari`.`Users` (`firstname`, `lastname`, `email`, `psw`, `position`, `permessi`) VALUES ('admin', 'admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', '1', '1');");
echo "added admin...<br />";


$db->query("
create table Calendar (
	id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	year INT(4) NOT NULL,
	month INT(2) NOT NULL,
	day INT(2) NOT NULL,
	maxVolunteerNumber INT(2),

	UNIQUE (year, month, day)
) ENGINE=InnoDB;");
echo "calendar table created<br />";


$db->query("
create table Turni (
	day INT(8) UNSIGNED NOT NULL,
	task VARCHAR(30) NOT NULL,
	position INT(2) NOT NULL,
	volunteer INT(8) UNSIGNED NOT NULL,

	PRIMARY KEY(day, task, position, volunteer),
	FOREIGN KEY(day) REFERENCES Calendar(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(volunteer) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE CASCADE

)ENGINE=InnoDB;");
echo "turn table created...<br />";


?>