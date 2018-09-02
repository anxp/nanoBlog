CREATE TABLE essent_db.categories (
	cat_ID TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
	cat_name varchar(45) NOT NULL,
	CONSTRAINT categories_PK PRIMARY KEY (cat_ID),
	CONSTRAINT categories_UN UNIQUE KEY (cat_name)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;

#/* After table created, create manually for beginning some categories: Politics, Economics, Technologies, Sport etc... */