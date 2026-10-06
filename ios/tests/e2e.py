#!/usr/bin/env python3
"""End-to-end gameplay test over HTTP only (Python standard library, no extra packages).

Runs against any OpenVillage instance – the iOS app in the simulator, or the native runtime:

    python3 ios/tests/e2e.py http://127.0.0.1:8080 <control-token>

The control token (X-OV-Token) unlocks /__openvillage/sql and /tick, which the test uses to fast-forward
the build queue and to read the database. Exit code 0 = everything passed.
"""
import html
import http.cookiejar
import json
import re
import sys
import time
import urllib.error
import urllib.parse
import urllib.request

BASE = (sys.argv[1] if len(sys.argv) > 1 else "http://127.0.0.1:8080").rstrip("/")
TOKEN = sys.argv[2] if len(sys.argv) > 2 else ""
PROBLEMS = re.compile(r"Fatal error|Uncaught (Error|Exception)|Parse error|Warning</b>:|Notice</b>:|"
                      r"There were errors in App|We are having issues", re.I)
failures = []
step_no = 0


def step(title):
    global step_no
    step_no += 1
    print(f"[{step_no:02d}] {title}", flush=True)


def fail(msg):
    print("   FAIL: " + msg, flush=True)
    failures.append(msg)


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, *a, **k):
        return None


class Client:
    def __init__(self, name):
        self.name = name
        self.jar = http.cookiejar.CookieJar()
        self.open = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(self.jar)).open
        self.raw = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(self.jar), NoRedirect()).open

    def req(self, path, data=None, follow=True, headers=None, timeout=120):
        url = path if path.startswith("http") else BASE + path
        body = urllib.parse.urlencode(data).encode() if isinstance(data, dict) else data
        r = urllib.request.Request(url, data=body, headers=headers or {})
        try:
            resp = (self.open if follow else self.raw)(r, timeout=timeout)
            return resp.status, resp.geturl(), resp.read().decode("utf-8", "replace"), dict(resp.headers)
        except urllib.error.HTTPError as e:
            return e.code, url, e.read().decode("utf-8", "replace"), dict(e.headers or {})

    def page(self, path, data=None, expect=200, check=True):
        time.sleep(0.3)                                   # the game bans "overclicking" accounts for a few seconds
        code, url, text, _ = self.req(path, data)
        if "has been banned" in text and "overclick" in text:
            time.sleep(6)
            code, url, text, _ = self.req(path, data)
        if code != expect:
            fail(f"{self.name}: {path} -> HTTP {code} (expected {expect}) {text[:200]!r}")
        elif check:
            m = PROBLEMS.search(text)
            if m:
                i = m.start()
                fail(f"{self.name}: {path} contains {m.group(0)!r}: {re.sub(r'<[^>]+>', '', text[i:i + 300])!r}")
        return code, url, text


def control(action, data=None):
    c = Client("control")
    code, _, text, _ = c.req(f"/__openvillage/{action}", data, headers={"X-OV-Token": TOKEN})
    try:
        return code, json.loads(text)
    except ValueError:
        return code, {"raw": text[:500]}


def sql(query):
    code, res = control("sql", {"query": query, "db": "game"})
    if code != 200 or not res.get("ok"):
        fail(f"sql failed: {query[:80]} -> {res}")
        return []
    sets = res.get("sets") or []
    if not sets:
        return []
    cols = sets[-1]["columns"]
    return [dict(zip(cols, row)) for row in sets[-1]["rows"]]


def register_and_activate(c, name, password, tribe):
    code, _, text = c.page("/")
    m = re.search(r'name="csrf" value="([^"]+)"', text)
    if not m:
        fail("launcher has no CSRF token")
        return False
    code, url, text, headers = c.req("/register.php", {
        "csrf": m.group(1), "username": name, "email": f"{name.lower()}@example.test",
        "password": password, "terms": "1"}, follow=False)
    loc = headers.get("Location", "")
    if code not in (302, 303) or "/game/activate.php?token=" not in loc:
        fail(f"registration of {name}: HTTP {code} location={loc!r} {re.sub(r'<[^>]+>', ' ', text)[:200]!r}")
        return False
    c.page(loc)
    c.page("/game/activate.php?page=vid", {"vid": str(tribe)})
    for sector in ["sw", "se", "nw", "ne", "no", "so"]:
        code, _, text = c.page("/game/activate.php?page=confirmation", {"sector": sector})
        code, url, text, headers = c.req("/game/activate.php?page=dorf", {"sector": sector, "vid": str(tribe)}, follow=False)
        if code == 302:
            c.page("/game/dorf1.php?finished=1")
            return True
    fail(f"no free starting sector for {name}")
    return False


def login(c, name, password):
    c.page("/game/login.php")
    code, _, text, headers = c.req("/game/dorf1.php", {"name": name, "password": password, "s1": "Login", "w": "1440:900"}, follow=False)
    if code != 302:
        fail(f"login {name}: HTTP {code}")
        return False
    return True


def main():
    t0 = time.time()
    suffix = str(int(time.time()))[-6:]

    step("health.php reports ready")
    for _ in range(60):
        code, _, text, _ = Client("h").req("/health.php")
        if code == 200 and '"status":"ready"' in text:
            break
        time.sleep(1)
    else:
        fail(f"health not ready: {code} {text[:200]}")
        return

    code, st = control("status")
    print(f"     server: PHP {st.get('php')}, {st.get('mariadb')}, installed={st.get('installed')}")

    step("automation tick runs without errors")
    code, res = control("tick")
    if code != 200 or not res.get("ok") or res.get("errors"):
        fail(f"tick: {code} {res}")

    a, b = Client("A"), Client("B")
    pa, pb = f"Alpha{suffix}", f"Beta{suffix}"
    pw = f"Test-{suffix}-heslo"

    step(f"register + activate {pa} (Romans) and {pb} (Gauls)")
    if not register_and_activate(a, pa, pw, 1) or not register_and_activate(b, pb, pw, 3):
        return

    step("log in again with a fresh session")
    a = Client("A")
    if not login(a, pa, pw):
        return

    step("authenticated pages render without PHP errors")
    pages = ["dorf1.php", "dorf2.php", "dorf3.php", "karte.php", "karte.php?x=0&y=0", "statistiken.php",
             "statistiken.php?id=1", "statistiken.php?id=2", "berichte.php", "nachrichten.php",
             "messages.php?t=1", "messages.php?t=2", "allianz.php", "profile.php", "spieler.php", "hero.php",
             "hero_inventory.php", "hero_adventure.php", "hero_auction.php", "options.php", "options.php?s=2",
             "options.php?s=3", "manual.php", "messages.php", "plus.php", "build.php?id=39", "build.php?id=26"]
    pages += [f"build.php?id={i}" for i in range(1, 19)]
    pages += [f"build.php?id={i}" for i in range(19, 41)]
    for p in pages:
        a.page("/game/" + p)

    step("queue a resource field upgrade")
    code, _, text = a.page("/game/build.php?id=1")
    m = re.search(r"dorf1\.php\?a=1&(?:amp;)?c=([0-9a-f]+)", text)
    if not m:
        fail("no upgrade button on build.php?id=1")
    else:
        a.page("/game/dorf1.php?a=1&c=" + m.group(1))
        rows = sql(f"SELECT b.id FROM building_upgrade b JOIN vdata v ON v.kid=b.kid JOIN users u ON u.id=v.owner WHERE u.name='{pa}'")
        if not rows:
            fail("upgrade was not queued")
        else:
            step("fast-forward the queue; the automation tick completes it")
            sql(f"UPDATE building_upgrade SET commence = UNIX_TIMESTAMP() WHERE kid IN (SELECT v.kid FROM vdata v JOIN users u ON u.id=v.owner WHERE u.name='{pa}')")
            for _ in range(45):
                control("tick")
                done = sql(f"SELECT f.f1 FROM fdata f JOIN vdata v ON v.kid=f.kid JOIN users u ON u.id=v.owner WHERE u.name='{pa}'")
                if done and str(done[0]["f1"]) == "1":
                    break
                time.sleep(1)
            else:
                fail("field 1 did not reach level 1 after the tick")

    step("send a message from A to B and read it as B")
    sql(f"UPDATE users SET total_pop = 500 WHERE name = '{pa}'")   # the game requires population to send messages
    code, _, text = a.page("/game/messages.php?t=1")
    m = re.search(r'name="c" value="([^"]+)"', text)
    if not m:
        fail("message form has no checker")
    else:
        code, _, text = a.page("/game/messages.php?t=1", {"an": pb, "be": "Hello neighbour", "message": "Hello friend, how are you",
                                                         "c": m.group(1), "s1": "send"})
        if not login(b, pb, pw):
            return
        code, _, text = b.page("/game/messages.php")
        if "Hello neighbour" not in html.unescape(text):
            sent = sql(f"SELECT COUNT(*) AS n FROM mdata WHERE topic LIKE 'Hello%'")
            fail(f"B did not receive the message (mdata rows: {sent})")

    step("admin (Support) can open the admin panel")
    admin_pw = sys.argv[3] if len(sys.argv) > 3 else ""
    if admin_pw:
        adm = Client("admin")
        if login(adm, "Support", admin_pw):
            adm.page("/game/admin.php")

    step("no quarantined scheduled tasks")
    q = sql("SELECT COUNT(*) AS n FROM scheduled_task_failures")
    if q and q[0]["n"] != "0":
        fail(f"{q[0]['n']} quarantined tasks: {sql('SELECT task_table, last_error FROM scheduled_task_failures LIMIT 5')}")

    print(f"done in {time.time() - t0:.1f}s, {len(pages)} pages crawled")


if __name__ == "__main__":
    main()
    if failures:
        print(f"\nE2E FAILED ({len(failures)} problem(s))")
        sys.exit(1)
    print("\nE2E PASSED")
