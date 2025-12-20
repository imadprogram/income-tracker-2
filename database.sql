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

TRUNCATE users;
DELETE FROM users;

INSERT INTO users(name , email , password) VALUES('gumball', 'imad@gmail.com','12345')

show tables


CREATE DATABASE smart_wallet_2

use smart_wallet_2

ALTER TABLE users ADD otp_code VARCHAR(6);

CREATE TABLE cards(
    id INT PRIMARY KEY AUTO_INCREMENT,
    card_number VARCHAR(19) NOT NULL,
    ex_date VARCHAR(6) NOT null,
    CVC VARCHAR(3) NOT NULL,
    balance DECIMAL(10 , 2) DEFAULT 0.00,
    user_id INT,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
)

ALTER TABLE cards ADD COLUMN card_index INT NOT NULL;

ALTER TABLE cards ADD COLUMN card_holder VARCHAR(250) NOT NULL;

ALTER TABLE cards DROP card_name;

ALTER TABLE expense ADD COLUMN card_id INT;
ALTER TABLE expense ADD FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE CASCADE;

SELECT * FROM cards

DELETE FROM users

CREATE TABLE transactions(
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    card_id INT,
    description VARCHAR(250),
    amount DECIMAL(9,2),
    date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE CASCADE
)

ALTER TABLE transactions ADD COLUMN type ENUM('income' , 'expense') NOT NULL

SELECT * FROM transactions 

DELETE FROM transactions