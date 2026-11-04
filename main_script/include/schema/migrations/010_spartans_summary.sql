-- Number of Spartan players (tribe 8) for the statistics, like the other tribes' *_players_count.
ALTER TABLE summary
  ADD COLUMN IF NOT EXISTS spartans_players_count INT(11) NOT NULL DEFAULT 0 AFTER huns_players_count;
