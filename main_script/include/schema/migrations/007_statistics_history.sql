-- Daily history of every player for the Statistics → General page (Travian Legends "Všeobecné" view):
-- population rank in the world and in the alliance, culture points, production, and the resources won / lost by
-- raids and sent / received by merchants that day. One row per player and day of the round (day 0 = start day);
-- Model\StatisticsHistoryModel keeps today's row up to date, battles and merchant deliveries add to it.
CREATE TABLE IF NOT EXISTS stats_history (
  uid INT(11) UNSIGNED NOT NULL,
  day SMALLINT(5) UNSIGNED NOT NULL,
  pop INT(11) NOT NULL DEFAULT 0,
  pop_rank INT(11) UNSIGNED NOT NULL DEFAULT 0,
  ally_pop_rank INT(11) UNSIGNED NOT NULL DEFAULT 0,
  aid INT(11) UNSIGNED NOT NULL DEFAULT 0,
  cp BIGINT(20) NOT NULL DEFAULT 0,
  cp_prod INT(11) NOT NULL DEFAULT 0,
  prod_r1 BIGINT(20) NOT NULL DEFAULT 0,
  prod_r2 BIGINT(20) NOT NULL DEFAULT 0,
  prod_r3 BIGINT(20) NOT NULL DEFAULT 0,
  prod_r4 BIGINT(20) NOT NULL DEFAULT 0,
  raid_in BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  raid_out BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  trade_in BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  trade_out BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  updated INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (uid, day),
  KEY day (day)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
