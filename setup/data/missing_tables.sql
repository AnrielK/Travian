-- Tables used by the code but missing from the original install script (sql.sql).
-- Safe to run on an existing server: every table is created only if it does not exist.

CREATE TABLE IF NOT EXISTS `kasalar` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kasadi` varchar(100) NOT NULL DEFAULT '',
  `envanter` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `odun` int(11) NOT NULL DEFAULT 0,
  `tugla` int(11) NOT NULL DEFAULT 0,
  `demir` int(11) NOT NULL DEFAULT 0,
  `tahil` int(11) NOT NULL DEFAULT 0,
  `hayvan` int(11) NOT NULL DEFAULT 0,
  `altinver` int(11) NOT NULL DEFAULT 0,
  `goldclub` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `envanter` (`envanter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bank` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL DEFAULT '',
  `gold` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `operation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL DEFAULT '',
  `code` varchar(40) NOT NULL DEFAULT '',
  `gold` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(45) NOT NULL DEFAULT '',
  `time` int(11) unsigned NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `server` varchar(50) NOT NULL DEFAULT '',
  `userid` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `reward_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL DEFAULT '',
  `reward_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `login_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT 0,
  `ip` varchar(45) NOT NULL DEFAULT '',
  `time` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `illegal_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) unsigned NOT NULL DEFAULT 0,
  `log` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- users.god / users.favor are written by gods.php but were never created.
-- (MySQL- and MariaDB-compatible "add column if missing")
SET @s = (SELECT IF(COUNT(*) = 0, 'ALTER TABLE `users` ADD `god` tinyint(1) unsigned NOT NULL DEFAULT 0', 'SELECT 1')
          FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'god');
PREPARE php8_stmt FROM @s; EXECUTE php8_stmt; DEALLOCATE PREPARE php8_stmt;
SET @s = (SELECT IF(COUNT(*) = 0, 'ALTER TABLE `users` ADD `favor` int(11) unsigned NOT NULL DEFAULT 0', 'SELECT 1')
          FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'favor');
PREPARE php8_stmt FROM @s; EXECUTE php8_stmt; DEALLOCATE PREPARE php8_stmt;
