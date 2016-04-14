DROP TABLE IF EXISTS `#__helloworld`;

CREATE TABLE `#__helloworld` (
	`id`		INT(11)		NOT NULL AUTO_INCREMENT,
	`asset_id`	INT(10)		NOT NULL DEFAULT '0',
	`uid`		int(10)		UNSIGNED NOT NULL DEFAULT '0',
	`greeting`	VARCHAR(40)	NOT NULL,
	`published`	tinyint(4)	NOT NULL,
	`catid`		int(11)		NOT NULL DEFAULT '0',
	`params`	VARCHAR(1024)	NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;

INSERT INTO `#__helloworld` (`greeting`, `uid`) VALUES
('greeter A says: Hello World!', 1),
('greeter B says: Good bye World!', 2),
('greeter C says: See you later!', 3),
('greeter D says: We will meet again!', 4);
