-- Local migrations: schema pieces referenced by the PHP code but missing from travian.sql.
-- Safe to run repeatedly (MariaDB IF NOT EXISTS syntax).

ALTER TABLE `activate` ADD COLUMN IF NOT EXISTS `country` varchar(64) NOT NULL DEFAULT '';
ALTER TABLE `users`    ADD COLUMN IF NOT EXISTS `type` tinyint(1) NOT NULL DEFAULT 0;

-- Cross-server gold bank (banksystem.php, Attacks.php)
CREATE TABLE IF NOT EXISTS `bank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `gold` int(11) NOT NULL DEFAULT 0,
  `nick` varchar(100) NOT NULL DEFAULT '',
  `code` varchar(64) NOT NULL DEFAULT '',
  `fail` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`), KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `operation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `code` varchar(64) NOT NULL DEFAULT '',
  `gold` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(64) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `server` varchar(100) NOT NULL DEFAULT '',
  `userid` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Admin panel logs
CREATE TABLE IF NOT EXISTS `login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(64) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `illegal_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT 0,
  `log` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Global medals / notifications (originally on a separate "servers" database)
CREATE TABLE IF NOT EXISTS `medalhas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL DEFAULT '',
  `descricao` text NOT NULL,
  `imagem` varchar(255) NOT NULL DEFAULT '',
  `categorie` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `notificacao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `autor` varchar(255) NOT NULL DEFAULT '',
  `tempo` int(11) NOT NULL DEFAULT 0,
  `tipo` int(11) NOT NULL DEFAULT 0,
  `mensagem` text NOT NULL,
  `servidor` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Local server: open "now", registration on, no e-mail activation needed
UPDATE `config` SET `OPENING` = UNIX_TIMESTAMP() - 60, `regstatus` = 1, `HOMEPAGE` = 'http://127.0.0.1:8080/' WHERE id = 1;

-- Local admin account for the admin panel (/panel): Multihunter / admin123
UPDATE `users` SET `password` = MD5(CONCAT('admin123', 'multihunter')) WHERE `username` = 'Multihunter';
