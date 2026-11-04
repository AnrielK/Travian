-- Top 10 of Travian Legends: attack points won against Nature and the Natars this week ("PvE of the week"), kept apart
-- from week_attack_points, which only counts attacks on players ("PvP of the week").
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS week_pve_points BIGINT(20) NOT NULL DEFAULT 0 AFTER week_robber_points;
