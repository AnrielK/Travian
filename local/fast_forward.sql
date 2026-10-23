-- Test helper: finish all builds, research, training and troop/merchant movements now.
SET @now = UNIX_TIMESTAMP();
UPDATE queue      SET finish = @now        WHERE status = 0 AND finish > @now;
UPDATE movement   SET endtime = @now       WHERE proc = 0 AND endtime > @now;
UPDATE bdata      SET timestamp = @now     WHERE timestamp > @now;
UPDATE research   SET timestamp = @now     WHERE timestamp > @now;
UPDATE demolition SET timetofinish = @now  WHERE timetofinish > @now;
UPDATE training   SET lastupdate = lastupdate - CEIL(amt * eachtime) - 1, timestamp = @now WHERE amt > 0;
