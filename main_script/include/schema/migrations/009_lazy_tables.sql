-- Tables that the game used to create on first use (CREATE TABLE IF NOT EXISTS in the middle of a request). That
-- statement commits the open transaction implicitly (e.g. registering a player inside a scheduled task or test
-- fixture), so they are created here; the classes only create them outside a transaction as a fallback.
CREATE TABLE IF NOT EXISTS `ov_legends_guide` (
  `uid` INT UNSIGNED NOT NULL PRIMARY KEY,
  `state` VARCHAR(255) NOT NULL DEFAULT '{}'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ov_hero_appearance` (
  `uid` INT UNSIGNED NOT NULL PRIMARY KEY,
  `gender` VARCHAR(6) NOT NULL DEFAULT 'male',
  `config` TEXT NOT NULL,
  `lastupdate` INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ov_quest40` (
  `uid` INT UNSIGNED NOT NULL,
  `state` SMALLINT NOT NULL DEFAULT 0,
  `optional` INT UNSIGNED NOT NULL DEFAULT 0,
  `data` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ov_task_progress` (
  `uid` INT UNSIGNED NOT NULL,
  `kid` INT UNSIGNED NOT NULL,
  `task` VARCHAR(48) NOT NULL,
  `steps` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`, `kid`, `task`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ov_unit_favorites` (
  `uid` INT UNSIGNED NOT NULL,
  `unit_id` SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (`uid`, `unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ov_village_groups` (
  `uid` INT UNSIGNED NOT NULL,
  `gid` INT UNSIGNED NOT NULL,
  `name` VARCHAR(40) NOT NULL DEFAULT '',
  `color` VARCHAR(12) NOT NULL DEFAULT 'grey',
  `sort` INT NOT NULL DEFAULT 0,
  `collapsed` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`, `gid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ov_village_group_members` (
  `uid` INT UNSIGNED NOT NULL,
  `kid` INT UNSIGNED NOT NULL,
  `gid` INT UNSIGNED NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`uid`, `kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ov_quick_links` (
  `uid` INT UNSIGNED NOT NULL,
  `kid` INT UNSIGNED NOT NULL,
  `links` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`uid`, `kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
