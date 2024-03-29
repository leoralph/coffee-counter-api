SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS user_coffee_drinks;

CREATE TABLE users (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
    api_token VARCHAR(255) NOT NULL
);

CREATE TABLE user_coffee_drinks (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user_id INT NOT NULL,
	amount INT NOT NULL,
	dranked_at TIMESTAMP NOT NULL,
	FOREIGN KEY (user_id) REFERENCES users(id)
);
