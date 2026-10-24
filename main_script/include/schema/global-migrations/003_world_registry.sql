-- Several worlds side by side (scripts/native/runtime/worlds.php): a further world is a gameServers row with its own
-- game database and the settings it was created with. World 1 keeps gameDb empty (its settings come from the environment).
ALTER TABLE gameServers
  ADD COLUMN IF NOT EXISTS gameDb VARCHAR(64) NOT NULL DEFAULT '',
  ADD COLUMN IF NOT EXISTS gpack VARCHAR(32) NOT NULL DEFAULT '',
  ADD COLUMN IF NOT EXISTS mapSize INT(10) UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS heroSpeed INT(10) UNSIGNED NOT NULL DEFAULT 1;
