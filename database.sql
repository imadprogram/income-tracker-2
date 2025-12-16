-- Active: 1764672841665@@127.0.0.1@3306@smart_wallet_2
CREATE DATABASE smart_wallet;

use smart_wallet;

CREATE TABLE income(
    id INT PRIMARY KEY AUTO_INCREMENT,
    amount DECIMAL(4,2) NOT NULL,
    description VARCHAR(250) ,
    date DATE DEFAULT (CURRENT_DATE)
);
TRUNCATE income;

TRUNCATE expense;

SELECT sum(amount) FROM income;

CREATE TABLE expense(
    id INT PRIMARY KEY AUTO_INCREMENT,
    amount DECIMAL(6,2) NOT NULL,
    description VARCHAR(250) ,
    date DATE DEFAULT (CURRENT_DATE)
);

SELECT * FROM income;
SELECT * FROM expense;

ALTER TABLE income ADD user_id INT;
ALTER TABLE expense ADD user_id INT;


CREATE TABLE users(
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(250) NOT NULL UNIQUE,
    password VARCHAR(250) NOT NULL
)

SELECT * FROM users

TRUNCATE users

INSERT INTO users(name , email , password) VALUES('gumball', 'imad@gmail.com','12345')

show tables


CREATE DATABASE smart_wallet_2

use smart_wallet_2