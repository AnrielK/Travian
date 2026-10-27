#!/usr/bin/env python3
"""End-to-end smoke test of the game served by the iOS app (simulator) or any server.

usage: smoke.py BASE_URL DATA_DIR
  DATA_DIR = app's Application Support/Travian folder (www/, data/travian.sqlite, logs/)
Exit code 1 if any page returns HTTP 5xx, PHP logs a fatal error, or SQL errors are logged.
"""
import os, re, sys, sqlite3, time, collections, urllib.parse as up
import requests
from bs4 import BeautifulSoup

B = sys.argv[1].rstrip('/')
DATA = sys.argv[2]
DB = os.path.join(DATA, 'data', 'travian.sqlite')
LOCK = os.path.join(DATA, 'www', 'application', 'queue.txt')
problems = []

def sql(q, *a):
    con = sqlite3.connect(DB, timeout=15)
    try:
        cur = con.execute(q, a); rows = cur.fetchall(); con.commit(); return rows
    finally:
        con.close()

def check(r, what):
    if r.status_code >= 500:
        problems.append(f'{what}: HTTP {r.status_code}')
    return r

def register(name, tribe, sector):
    s = requests.Session()
    check(s.post(B + '/anmelden.php', data=dict(ft='a1', referal='', name=name, email=name + '@example.com',
                 pw='secret123', country='1', agb='1', vid='0', kid='0')), 'register')
    check(s.post(B + '/activate.php?page=vid', data={'vid': tribe}), 'tribe')
    check(s.post(B + '/activate.php?page=sector', data={'sector': sector}), 'sector')
    r = check(s.post(B + '/activate.php?page=confirmation', data={'confirm': '1'}), 'confirm')
    print(f'register {name}: {r.url}')
    return s

def login(name, pw='secret123'):
    s = requests.Session()
    r = check(s.post(B + '/login.php', data=dict(ft='a4', user=name, pw=pw, lowRes='')), 'login')
    print(f'login {name}: {r.url}')
    return s

def fast_forward():
    now = "CAST(strftime('%s','now') AS INTEGER)"
    for q in [f"UPDATE queue SET finish={now} WHERE status=0 AND finish>{now}",
              f"UPDATE movement SET endtime={now} WHERE proc=0 AND endtime>{now}",
              f"UPDATE bdata SET timestamp={now} WHERE timestamp>{now}",
              f"UPDATE training SET lastupdate=lastupdate-CAST(amt*eachtime AS INTEGER)-2, timestamp={now} WHERE amt>0"]:
        sql(q)
    t = time.time() - 60
    os.utime(LOCK, (t, t))

def hidden_form(html, must_have):
    for f in BeautifulSoup(html, 'html.parser').find_all('form'):
        if f.find('input', attrs={'name': must_have}):
            return f, {i.get('name'): i.get('value', '') for i in f.find_all('input') if i.get('name')}
    return None, {}

# ---------------------------------------------------------------- scenario
t0 = time.time()
r = check(requests.get(B + '/login.php'), 'login page'); print('login page', r.status_code, len(r.text))
a = register('alfa', 1, 'nw')
register('beta', 2, 'so')
uid_a = sql("SELECT id FROM users WHERE username='alfa'")[0][0]
va = sql("SELECT wref FROM vdata WHERE owner=?", uid_a)[0][0]
vb = sql("SELECT wref FROM vdata v JOIN users u ON u.id=v.owner WHERE u.username='beta'")[0][0]
bx, by = sql("SELECT x, y FROM wdata WHERE id=?", vb)[0]
sql(f"UPDATE fdata SET f19=5,f19t=19,f39=5,f39t=16,f20=5,f20t=17,f21=5,f21t=22,f22=3,f22t=18,f23=20,f23t=10,f24=20,f24t=11,f26=5 WHERE vref={va}")
sql(f"UPDATE vdata SET wood=50000,clay=50000,iron=50000,crop=50000,maxstore=100000,maxcrop=100000 WHERE wref={va}")
sql(f"UPDATE units SET u1=200,u2=50 WHERE vref={va}")
sql("UPDATE users SET protect=0, ptime=0 WHERE id>6")
a = login('alfa')

# build a resource field
link = re.search(r"dorf1\.php\?[^']*", check(a.get(B + '/build.php?id=1'), 'field').text)
if link: check(a.get(B + '/' + link.group(0)), 'upgrade')
print('build queue:', sql("SELECT count(*) FROM bdata")[0][0])
# train
check(a.post(B + '/build.php', data=dict(id='19', ft='t1', t1='10')), 'train')
print('training rows:', sql("SELECT count(*) FROM training")[0][0])
# message
r = a.get(B + '/nachrichten.php?t=1'); f, d = hidden_form(r.text, 'key')
d.update(an='beta', be='Ahoj', message='Test z iOS', s1='send'); check(a.post(B + '/nachrichten.php?t=1', data=d), 'message')
# alliance
check(a.post(B + '/build.php', data=dict(id='22', ft='ali1', ally1='IOS', ally2='iOS testers', s1='ok')), 'alliance')
# market
r = check(a.post(B + '/build.php?id=20&t=5', data=dict(ft='check', id='20', r1='100', r2='100', r3='0', r4='0', x=bx, y=by)), 'market')
f, d = hidden_form(r.text, 'ft')
if f is not None and d.get('ft') != 'check':
    check(a.post(B + '/' + (f.get('action') or 'build.php').lstrip('/'), data=d), 'market send')
# attack
r = check(a.post(B + '/build.php?t=2&id=39', data={'t1': '20', 't2': '5', 'x': bx, 'y': by, 'c': '3', 's1': 'ok'}), 'attack')
f, d = hidden_form(r.text, 'ckey')
if f is not None: check(a.post(B + '/' + f.get('action').lstrip('/'), data=d), 'attack confirm')
print('movements:', sql("SELECT sort_type, proc FROM movement"))
fast_forward(); check(a.get(B + '/dorf1.php'), 'process'); time.sleep(1.5)
fast_forward(); check(a.get(B + '/dorf1.php'), 'process 2')

state = {
    'alliances': sql("SELECT count(*) FROM alidata")[0][0],
    'messages': sql("SELECT count(*) FROM mdata")[0][0],
    'reports': sql("SELECT count(*) FROM ndata")[0][0],
    'pending_builds': sql("SELECT count(*) FROM bdata")[0][0],
    'pending_training': sql("SELECT count(*) FROM training")[0][0],
    'units_alfa': sql("SELECT u1, u2 FROM units WHERE vref=?", va)[0],
}
print('state:', state)
for k, ok in [('alliances', state['alliances'] >= 1), ('messages', state['messages'] >= 1),
              ('reports', state['reports'] >= 2), ('pending_builds', state['pending_builds'] == 0),
              ('pending_training', state['pending_training'] == 0)]:
    if not ok: problems.append(f'scenario check failed: {k}={state[k]}')

# ---------------------------------------------------------------- crawl
def crawl(s, start, limit, prefix='/'):
    seen = set(); q = collections.deque(start); bad = re.compile(r'logout|del|abort|cancel|delete|vacation|ban|sitter|a=\d|c=[0-9a-f]{3}|give|add|edit|reset|massmessage', re.I)
    while q and len(seen) < limit:
        u = q.popleft()
        if u in seen: continue
        seen.add(u)
        try: r = s.get(B + u, timeout=60)
        except Exception as e: problems.append(f'{u}: {e}'); continue
        check(r, u)
        if 'text/html' not in r.headers.get('content-type', ''): continue
        for el in BeautifulSoup(r.text, 'html.parser').find_all('a', href=True):
            h = up.urljoin(B + u, el['href'].strip())
            if not h.startswith(B + prefix): continue
            p = h[len(B):].split('#')[0]
            if '.php' in p and not bad.search(p) and p not in seen: q.append(p)
    return len(seen)

pages = ['/dorf1.php', '/dorf2.php', '/dorf3.php', '/karte.php', '/statistiken.php', '/berichte.php', '/nachrichten.php',
         '/hero.php', '/hero_inventory.php', '/allianz.php', '/spieler.php', '/build.php?id=39', '/build.php?id=20', '/manual.php']
n = crawl(a, pages, int(os.environ.get('CRAWL', '250')))
print('crawled', n, 'player pages')
tile = check(a.get(B + '/map_block.php?tx0=-20&ty0=-20&tx1=-11&ty1=-11&w=600&h=600&version=0'), 'map tile')
print('map tile', tile.status_code, tile.headers.get('content-type'), len(tile.content))
if not tile.headers.get('content-type', '').startswith('image/'): problems.append('map tile is not an image')
adm = login('Multihunter', os.environ.get('ADMIN_PW', '12345'))
n2 = crawl(adm, ['/panel/index.php'], int(os.environ.get('CRAWL_PANEL', '80')), '/panel/')
print('crawled', n2, 'admin pages')

# ---------------------------------------------------------------- logs
def read(p):
    try: return open(p, encoding='utf8', errors='replace').read()
    except FileNotFoundError: return ''
server_log = read(os.path.join(DATA, 'logs', 'server.log'))
fatals = [l for l in server_log.splitlines() if 'Fatal' in l or 'Uncaught' in l]
sql_errors = read(os.path.join(DATA, 'www', 'local', 'logs', 'sql_errors.log')).strip().splitlines()
problems += [f'PHP: {l[:300]}' for l in fatals[:20]] + [f'SQL: {l[:300]}' for l in sql_errors[:20]]

print(f'\n=== {time.time() - t0:.0f}s, problems: {len(problems)}')
for p in problems: print(' -', p)
sys.exit(1 if problems else 0)
