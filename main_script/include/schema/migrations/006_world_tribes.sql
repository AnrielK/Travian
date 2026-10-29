-- The tribes of the world, chosen when the world is created and fixed afterwards (GAME_TRIBES):
-- 'classic' = Romans, Teutons and Gauls only; '' or 'all' = every tribe the world's look offers (Egyptians, Huns, ...).
ALTER TABLE config
  ADD COLUMN IF NOT EXISTS tribes VARCHAR(16) NOT NULL DEFAULT '';
