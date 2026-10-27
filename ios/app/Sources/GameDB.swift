import Foundation
import SQLite3

/// Direct access to the game's SQLite database for the test tools (the PHP side opens the same file).
enum GameDB {
    struct DBError: LocalizedError {
        let message: String
        var errorDescription: String? { message }
    }

    private static func open() throws -> OpaquePointer {
        var db: OpaquePointer?
        guard sqlite3_open_v2(Paths.database.path, &db, SQLITE_OPEN_READWRITE, nil) == SQLITE_OK, let db else {
            throw DBError(message: "Databázi nelze otevřít")
        }
        sqlite3_busy_timeout(db, 15_000)
        return db
    }

    /// Runs one or more statements.
    static func exec(_ sql: String) throws {
        let db = try open()
        defer { sqlite3_close(db) }
        var err: UnsafeMutablePointer<CChar>?
        if sqlite3_exec(db, sql, nil, nil, &err) != SQLITE_OK {
            let message = err.map { String(cString: $0) } ?? "SQL chyba"
            sqlite3_free(err)
            throw DBError(message: message)
        }
    }

    /// Runs a query and returns column names + rows as strings.
    static func query(_ sql: String) throws -> (columns: [String], rows: [[String]]) {
        let db = try open()
        defer { sqlite3_close(db) }
        var stmt: OpaquePointer?
        guard sqlite3_prepare_v2(db, sql, -1, &stmt, nil) == SQLITE_OK, let stmt else {
            throw DBError(message: String(cString: sqlite3_errmsg(db)))
        }
        defer { sqlite3_finalize(stmt) }
        let n = sqlite3_column_count(stmt)
        let columns = (0..<n).map { String(cString: sqlite3_column_name(stmt, $0)) }
        var rows: [[String]] = []
        while true {
            let rc = sqlite3_step(stmt)
            if rc == SQLITE_DONE { break }
            guard rc == SQLITE_ROW else { throw DBError(message: String(cString: sqlite3_errmsg(db))) }
            rows.append((0..<n).map { i in
                sqlite3_column_text(stmt, i).map { String(cString: $0) } ?? "NULL"
            })
            if rows.count >= 500 { break }
        }
        return (columns, rows)
    }

    // MARK: test tools

    private static let now = "CAST(strftime('%s','now') AS INTEGER)"

    static func fastForward() throws {
        try exec("""
        UPDATE queue      SET finish = \(now)       WHERE status = 0 AND finish > \(now);
        UPDATE movement   SET endtime = \(now)      WHERE proc = 0 AND endtime > \(now);
        UPDATE bdata      SET timestamp = \(now)    WHERE timestamp > \(now);
        UPDATE research   SET timestamp = \(now)    WHERE timestamp > \(now);
        UPDATE demolition SET timetofinish = \(now) WHERE timetofinish > \(now);
        UPDATE training   SET lastupdate = lastupdate - CAST(amt * eachtime AS INTEGER) - 2, timestamp = \(now) WHERE amt > 0;
        """)
        // The game processes its event queue at most once per second (mtime of application/queue.txt).
        try? FileManager.default.setAttributes([.modificationDate: Date().addingTimeInterval(-60)],
                                               ofItemAtPath: Paths.queueLock.path)
    }

    static func fillResources() throws {
        try exec("UPDATE vdata SET wood = maxstore, clay = maxstore, iron = maxstore, crop = maxcrop WHERE owner > 6;")
    }

    static func addGold(_ amount: Int = 1000) throws {
        try exec("UPDATE users SET gold = gold + \(amount) WHERE id > 6;")
    }

    static func removeProtection() throws {
        try exec("UPDATE users SET protect = 0, ptime = 0 WHERE id > 6;")
    }

    static func setSpeed(_ speed: Int) throws {
        try exec("UPDATE config SET SPEED = \(speed) WHERE id = 1;")
    }

    static func speed() -> Int? {
        guard let r = try? query("SELECT SPEED FROM config WHERE id = 1"), let v = r.rows.first?.first else { return nil }
        return Int(v)
    }

    static func players() -> [[String]] {
        let sql = """
        SELECT u.username,
               CASE u.tribe WHEN 1 THEN 'Římané' WHEN 2 THEN 'Germáni' WHEN 3 THEN 'Galové'
                            WHEN 6 THEN 'Egypťané' WHEN 7 THEN 'Hunové' ELSE u.tribe END,
               u.gold, (SELECT COUNT(*) FROM vdata v WHERE v.owner = u.id),
               (SELECT COALESCE(SUM(pop),0) FROM vdata v WHERE v.owner = u.id)
        FROM users u WHERE u.id > 6 ORDER BY u.id
        """
        return (try? query(sql).rows) ?? []
    }
}
