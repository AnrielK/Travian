#!/usr/bin/env python3
"""Converts the game's MariaDB database into an SQLite file with MySQL-like behaviour.

  - same tables, same column ORDER (the PHP code uses positional INSERTs)
  - text columns use COLLATE NOCASE (MySQL utf8_general_ci compares case-insensitively)
  - no NOT NULL constraints; every column gets a default (MySQL in non-strict mode
    silently fills missing values with 0 / '')
  - AUTO_INCREMENT primary keys become INTEGER PRIMARY KEY AUTOINCREMENT
  - indexes and unique keys are kept

Usage: mysql_to_sqlite.py <mysql database> <output.sqlite>   (connects to local MariaDB as root)
"""
import sqlite3, sys, os
import pymysql

db_name, out = sys.argv[1], sys.argv[2]
my = pymysql.connect(host='127.0.0.1', user='root', password='', database=db_name, charset='utf8',
                     unix_socket=os.environ.get('MYSQL_SOCKET', '/run/mysqld/mysqld.sock'))
cur = my.cursor()

def q(sql, *a):
    cur.execute(sql, a); return cur.fetchall()

tables = [r[0] for r in q("SELECT table_name FROM information_schema.tables WHERE table_schema=%s AND table_type='BASE TABLE' ORDER BY table_name", db_name)]

def sqlite_type(data_type):
    t = data_type.lower()
    if t in ('tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint', 'bit', 'year', 'timestamp'):
        return 'INTEGER'
    if t in ('float', 'double', 'decimal', 'real', 'numeric'):
        return 'REAL'
    if 'blob' in t or t in ('binary', 'varbinary'):
        return 'BLOB'
    return 'TEXT'

def default_sql(col_default, styp, data_type):
    if col_default is None or str(col_default).upper() == 'NULL':
        if data_type.lower() == 'timestamp':
            return "DEFAULT (strftime('%s','now'))"
        return {'INTEGER': 'DEFAULT 0', 'REAL': 'DEFAULT 0', 'TEXT': "DEFAULT ''", 'BLOB': "DEFAULT ''"}[styp]
    d = str(col_default)
    if d.upper() in ('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()', 'NOW()'):
        return "DEFAULT (strftime('%s','now'))"
    if len(d) >= 2 and d[0] == d[-1] == "'":   # MariaDB 10.2+ returns quoted literals
        d = d[1:-1]
    if styp in ('INTEGER', 'REAL'):
        try:
            float(d); return f"DEFAULT {d}"
        except ValueError:
            return 'DEFAULT 0'
    return "DEFAULT '" + d.replace("'", "''") + "'"

if os.path.exists(out): os.remove(out)
lite = sqlite3.connect(out)
lite.execute('PRAGMA journal_mode=DELETE')
lite.execute('PRAGMA page_size=4096')

for t in tables:
    cols = q("""SELECT column_name, data_type, column_default, extra, column_key FROM information_schema.columns
                WHERE table_schema=%s AND table_name=%s ORDER BY ordinal_position""", db_name, t)
    pk = [r[0] for r in q("""SELECT column_name FROM information_schema.statistics WHERE table_schema=%s AND table_name=%s
                             AND index_name='PRIMARY' ORDER BY seq_in_index""", db_name, t)]
    defs = []
    for name, dtype, cdef, extra, ckey in cols:
        styp = sqlite_type(dtype)
        if 'auto_increment' in (extra or '').lower():
            defs.append(f'"{name}" INTEGER PRIMARY KEY AUTOINCREMENT')
            pk = [p for p in pk if p != name] and None
            continue
        if pk == [name] and styp == 'INTEGER':
            defs.append(f'"{name}" INTEGER PRIMARY KEY')
            pk = None
            continue
        coll = ' COLLATE NOCASE' if styp == 'TEXT' else ''
        defs.append(f'"{name}" {styp}{coll} {default_sql(cdef, styp, dtype)}')
    if pk:
        defs.append('PRIMARY KEY (' + ', '.join(f'"{p}"' for p in pk) + ')')
    lite.execute(f'CREATE TABLE "{t}" (\n  ' + ',\n  '.join(defs) + '\n)')

    idx = q("""SELECT index_name, non_unique, GROUP_CONCAT(column_name ORDER BY seq_in_index) FROM information_schema.statistics
               WHERE table_schema=%s AND table_name=%s AND index_name<>'PRIMARY' GROUP BY index_name, non_unique""", db_name, t)
    for iname, non_unique, colnames in idx:
        u = '' if int(non_unique) else 'UNIQUE '
        cl = ', '.join(f'"{c}"' for c in colnames.split(','))
        lite.execute(f'CREATE {u}INDEX "{t}_{iname}" ON "{t}" ({cl})')

    cur.execute(f'SELECT * FROM `{t}`')
    rows = cur.fetchall()
    if rows:
        ph = ','.join('?' * len(rows[0]))
        conv = lambda v: (str(v) if hasattr(v, 'quantize') else (v.isoformat() if hasattr(v, 'isoformat') else v))
        lite.executemany(f'INSERT INTO "{t}" VALUES ({ph})', [tuple(conv(v) for v in r) for r in rows])
    print(f'{t:16s} {len(cols):3d} cols {len(rows):6d} rows')

lite.commit()
lite.execute('VACUUM')
lite.close()
print('->', out, os.path.getsize(out) // 1024, 'KiB')
