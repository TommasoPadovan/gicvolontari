create table Users (
	id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	firstname VARCHAR(30) NOT NULL,
	lastname VARCHAR(30) NOT NULL,
	email VARCHAR(50),
	psw VARCHAR(150),
	position INT(2),
	permessi INT(2)

) ENGINE=InnoDB;


INSERT INTO `liltvolontari`.`users` (`firstname`, `lastname`, `email`, `psw`, `position`, `permessi`) VALUES ('admin', 'admin', 'admin@gmail.com', '21232f297a57a5a743894a0e4a801fc3', '1', '1');



create table Calendar (
	id INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	year INT(4) NOT NULL,
	month INT(2) NOT NULL,
	day INT(2) NOT NULL,

	UNIQUE (year, month, day)
) ENGINE=InnoDB;


create table Turni (
	day INT(8) UNSIGNED NOT NULL,
	task VARCHAR(30) NOT NULL,
	position INT(2) NOT NULL,
	volunteer INT(8) UNSIGNED NOT NULL,

	PRIMARY KEY(day, task, position, volunteer),
	FOREIGN KEY(day) REFERENCES Calendar(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY(volunteer) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE CASCADE

)ENGINE=InnoDB;