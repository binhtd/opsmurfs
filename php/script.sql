CREATE DATABASE IF NOT EXISTS opsmurfs;

USE opsmurfs;

CREATE TABLE account(
id int(10) NOT NULL AUTO_INCREMENT,
username varchar(255) NOT NULL,
password varchar(255) NOT NULL,
categoryid tinyint(1) NOT NULL,
sent tinyint(1),
inserted_date datetime,
PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `txnid` varchar(20) NOT NULL,
  `payment_amount` decimal(7,2) NOT NULL,
  `payment_status` varchar(25) NOT NULL,
  `itemid` varchar(25) NOT NULL,
  `createdtime` datetime NOT NULL,
  PRIMARY KEY (`id`)
);