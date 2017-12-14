
DROP SCHEMA IF EXISTS `liltvolontari`;
CREATE SCHEMA `liltvolontari` ;

create table users (
	id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	firstname VARCHAR(30) NOT NULL,
	lastname VARCHAR(30) NOT NULL,
	email VARCHAR(50),
	CF varchar(15),
	birthdate DATE,
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

) ENGINE=InnoDB;


INSERT INTO `liltvolontari`.`users` (`firstname`, `lastname`, `email`, `psw`, `position`, `permessi`) VALUES ('admin', 'admin', 'admin@gmail.com', '21232f297a57a5a743894a0e4a801fc3', '1', '1');



create table calendar (
	id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	year INT(4) NOT NULL,
	month INT(2) NOT NULL,
	day INT(2) NOT NULL,
	maxVolunteerNumber INT(2),

	UNIQUE (year, month, day)
) ENGINE=InnoDB;


create table turni (
	day INT(8) UNSIGNED NOT NULL,
	task VARCHAR(30) NOT NULL,
	position INT(2) NOT NULL,
	volunteer INT(8) UNSIGNED NOT NULL,

	PRIMARY KEY(day, task, position, volunteer),
	FOREIGN KEY(day) REFERENCES calendar(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(volunteer) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE

)ENGINE=InnoDB;




create table events (
	id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	type VARCHAR(15) NOT NULL,
	title VARCHAR(100) NOT NULL,
	date DATE,
	timeStart TIME NOT NULL,
	timeEnd TIME NOT NULL,
	location VARCHAR(300),
	description LONGTEXT,
	requirements LONGTEXT,
	minAttendants INT(6),
	maxAttendants INT(6),
	who VARCHAR(256)
)ENGINE=InnoDB;



CREATE TABLE eventsattendants (
	event INT(8) UNSIGNED NOT NULL,
	volunteer INT(8) UNSIGNED NOT NULL,
	note varchar(100),
  timestamp int(11),
  multiplicity int(11),

	FOREIGN KEY(event) REFERENCES events(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(volunteer) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
	PRIMARY KEY (event, volunteer)
)ENGINE = InnoDB;

















