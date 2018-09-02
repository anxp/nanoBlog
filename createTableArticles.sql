#/* Add table Articles for storing articles (id, title, body, flag is_published = 1 or 0 (published or draft) keywords and attached image if specified) */
CREATE TABLE essent_db.articles (
	art_ID INT UNSIGNED NOT NULL AUTO_INCREMENT,
	is_published TINYINT NOT NULL,
	title varchar(100) NOT NULL,
	content TEXT NOT NULL,
	category TINYINT NOT NULL,
	kwords VARCHAR(255) NULL,
	att_image VARCHAR(255) NULL,
	CONSTRAINT articles_PK PRIMARY KEY (art_ID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;
