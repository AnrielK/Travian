-- Hospital of Travian Legends (building 46): the troops of a village wounded in battle. Infantry and cavalry only
-- (u1 - u6); the amounts lose 10 % a day until they are healed (Model\WoundedModel applies it from `updated`).
CREATE TABLE IF NOT EXISTS wounded (
  kid INT(6) UNSIGNED NOT NULL,
  u1 DOUBLE NOT NULL DEFAULT 0,
  u2 DOUBLE NOT NULL DEFAULT 0,
  u3 DOUBLE NOT NULL DEFAULT 0,
  u4 DOUBLE NOT NULL DEFAULT 0,
  u5 DOUBLE NOT NULL DEFAULT 0,
  u6 DOUBLE NOT NULL DEFAULT 0,
  updated INT(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (kid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
