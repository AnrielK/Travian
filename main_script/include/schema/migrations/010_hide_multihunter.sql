-- The Multihunter (user 2, the world's administrator account created by the installer) is not a player: hide it and
-- its village from the statistics (they list users / vdata with hidden = 0), like the installer does for new worlds.
UPDATE users SET hidden = 1 WHERE id = 2 AND name = 'Multihunter';
UPDATE vdata SET hidden = 1 WHERE owner = 2 AND (SELECT hidden FROM users WHERE id = 2) = 1;
