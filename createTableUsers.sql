#/* After table creation, create first user: login = admin, pass = 73254769800, role = admin, is_enabled = 1 */
#/* 73254769800 is a hash of 123 made by simple hash function in admin/index.php */
#/* So, first user will have loginname: admin and password: 123 */

CREATE TABLE essent_db.users (
	user_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
	login varchar(45) NOT NULL,
	pass varchar(45) NOT NULL,
	`role` varchar(45) NOT NULL,
	is_enabled TINYINT NOT NULL,
	name varchar(100) NULL,
	familyname varchar(100) NULL,
	CONSTRAINT users_PK PRIMARY KEY (user_ID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;

#/* Make user column UNIQUE: */
ALTER TABLE essent_db.users ADD CONSTRAINT users_UN UNIQUE KEY (login)