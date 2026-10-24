-- The world's look (graphic pack, sections/gpack/gpack.php), chosen when the world is created and fixed afterwards.
-- Empty in worlds created before this column: the first request stores the look the world uses then.
ALTER TABLE config
  ADD COLUMN IF NOT EXISTS gpack VARCHAR(32) NOT NULL DEFAULT '';
