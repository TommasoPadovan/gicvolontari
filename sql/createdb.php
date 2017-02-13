<?php
require_once('../lib/sqlLib.php');
$conn=connect();


queryThis("DROP SCHEMA IF EXISTS `liltvolontari`;", $conn);
echo "schema dropped...<br />";

queryThis("CREATE SCHEMA `liltvolontari`;", $conn);
echo "schema created...<br />";


mysql_select_db('liltvolontari');
echo "db selected...<br />";



queryThis("
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

) ENGINE=InnoDB;", $conn);
echo "users table created<br />";


queryThis("
	INSERT INTO `liltvolontari`.`Users` (`firstname`, `lastname`, `email`, `psw`, `position`, `permessi`) VALUES ('admin', 'admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', '1', '1');", $conn);
echo "added admin...<br />";


queryThis("
create table Calendar (
	id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	year INT(4) NOT NULL,
	month INT(2) NOT NULL,
	day INT(2) NOT NULL,
	maxVolunteerNumber INT(2),

	UNIQUE (year, month, day)
) ENGINE=InnoDB;", $conn);
echo "calendar table created<br />";


queryThis("
create table Turni (
	day INT(8) UNSIGNED NOT NULL,
	task VARCHAR(30) NOT NULL,
	position INT(2) NOT NULL,
	volunteer INT(8) UNSIGNED NOT NULL,

	PRIMARY KEY(day, task, position, volunteer),
	FOREIGN KEY(day) REFERENCES Calendar(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(volunteer) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE CASCADE

)ENGINE=InnoDB;",
$conn);
echo "turn table created...<br />";


?>