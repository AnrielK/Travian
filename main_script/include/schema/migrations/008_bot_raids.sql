-- Computer-controlled players (users.access = 3, Model\FakeUserModel) also raid: when each of their villages
-- may send its next raid (unix time, 0 = not planned yet). See Model\BotRaidModel.
ALTER TABLE vdata
  ADD COLUMN IF NOT EXISTS botRaidTime INT(10) UNSIGNED NOT NULL DEFAULT 0;
