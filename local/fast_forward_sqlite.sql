UPDATE queue      SET finish = CAST(strftime('%s','now') AS INTEGER)       WHERE status = 0 AND finish > CAST(strftime('%s','now') AS INTEGER);
UPDATE movement   SET endtime = CAST(strftime('%s','now') AS INTEGER)      WHERE proc = 0 AND endtime > CAST(strftime('%s','now') AS INTEGER);
UPDATE bdata      SET timestamp = CAST(strftime('%s','now') AS INTEGER)    WHERE timestamp > CAST(strftime('%s','now') AS INTEGER);
UPDATE research   SET timestamp = CAST(strftime('%s','now') AS INTEGER)    WHERE timestamp > CAST(strftime('%s','now') AS INTEGER);
UPDATE demolition SET timetofinish = CAST(strftime('%s','now') AS INTEGER) WHERE timetofinish > CAST(strftime('%s','now') AS INTEGER);
UPDATE training   SET lastupdate = lastupdate - CAST(amt * eachtime AS INTEGER) - 2, timestamp = CAST(strftime('%s','now') AS INTEGER) WHERE amt > 0;
