# Changelog

## Unreleased — OpenVillage modernization

### Added

- Village centre hover (`js/Game/Village/buildingHover.js`): as in Legends a
  white veil (20 %) lies over the building – an SVG colour filter, so it keeps
  the building's silhouette – wherever the pointer is on it, not only on the
  level circle. The building under the pointer is found by the pixels of its
  picture as well as its outline, so the parts our outlines miss react too.
  The wall is veiled without the bridges at its gates (a copy of the wall
  picture cut to the wall ring).
- Relocating buildings as in Travian Legends: the Main Building has a
  "Relocate buildings" section that opens the village centre in relocation mode
  (`dorf2.php?mode=rearrange`, as in Legends; `?relocate=1` still works). The page dims around the village, a building is
  picked up by a tap or dragged, the other buildings fade and every building
  site shows its plot (orange where it stands, blue where it would go); dropping
  it on another building swaps them, on an empty site moves it. The layout is
  saved with one button (POST with the session checker) by
  `Game\Buildings\BuildingRelocation`, which moves type and level together
  with queued construction and demolition. The Main Building, the rally point
  and the wall stay in place (tapping them shows "This building cannot be
  relocated."), World Wonder villages too. After picking a building the
  subtitle names it ("Now choose a new position for … (level …)"), and the save
  button explains that a building has to be moved first. The page uses the
  markup of the Legends rearrange mode (`.rearrangeBuildings` with the village
  mask, instruction steps `step1` / `step2` / `dragAndDrop`,
  `#villageContent.rearrangeMode` with `movable` / `selected` / `dragged` /
  `dropTarget` slots), so the Legends pack styles it with its own
  `legends_ltr.css` rules; saving asks for confirmation and leaving with unsaved
  changes asks whether to cancel the rearrangement — the Legends dialogV1
  dialogs (`rearrangeBuildingsConfirmDialog` / `rearrangeBuildingsAbortDialog`,
  drawn by legends_ltr.css; a lookalike in other packs), the
  cancel question at the top of the screen and the confirmation over the
  confirm button. As in Legends the village mask starts at the top of the map
  and the building queue is hidden while rearranging. The picked building gets an
  orange outline along its picture, the plot it leaves is drawn with the
  empty-site shape, and the place it would go — an empty site or a building it
  swaps with — shows the turquoise dashed plot of Legends. Only a finger on a
  movable building drags it; elsewhere the page scrolls as usual. Unlike Legends (20 gold) the
  relocation is free. Covered by `tests/building-relocation-regression.php`.
- Statistics → General in the Travian Legends look ("Všeobecné"): the Legends
  page rendered on the server with real data instead of the hard-coded sample
  of the old React view — world progress timeline (with the tribe of the first
  player per milestone), tribe distribution of real players, population rank
  with a day-by-day chart, resource rank (production, raid bounty and trading
  volume per day / so far), military strength (offence per village, defence
  per player with infantry / cavalry split) and culture points (rank,
  distribution, progression). Charts are SVG (`resources/View/StatisticsCharts.php`),
  sections collapse and switch views like in Legends. A new `stats_history`
  table (migration `007_statistics_history.sql`) keeps one row per player and
  day, refreshed by the `generalProgress:statisticsHistory` worker job; battles
  and merchant deliveries add the raided and traded resources.
- XAMPP runtime for Windows and Linux (`xampp/`): `start.bat` / `start.sh` run
  the game with XAMPP's PHP and MariaDB, and a web installation wizard checks
  the requirements, creates the database user and databases and installs the
  world. Redis and the worker processes are replaced as in the iOS app.
- Travian 4.0 graphic pack (`T40`): the 4.0 page frame (`layout/LayoutT40.php`:
  round navigation, resource line, village sign, hero / player / alliance /
  village list signs) around the T4.4 pages, with the Travian 4.0 pictures from
  `travian_Travian_4.0_41`, built by `scripts/gpack/import-t40-gpack.py`.
  The pages are restyled as in 4.0: green buttons that turn grey under the
  mouse, the white quest popup with its big picture, the building page, white
  level circles on the fields, and the tribe and start choice outside the game
  frame with the wooden menu and the 4.0 tribe portraits; the 4.0 logo and
  the 4.0 tabs.
- OpenVillage Local macOS app (`macos/`) and a Docker-free native runtime
  (`scripts/native/`): PHP built-in server router equivalent to the Apache
  vhost, private MariaDB/Redis instances, supervised worker, backup/restore.
- Regression tests resolve the checkout relative to `tests/` and spawn workers
  with `PHP_BINARY`, so they run both in the container and natively.
- Owner-confirmed JSON account downloads from Account options, with explicit
  current-world scope, credential exclusion, account isolation, and rate limits.
- Docker Compose runtime for PHP 8.3, MariaDB 11.4, Redis 7.4, Apache, bootstrap,
  and the game worker.
- Local launcher, registration, privacy/terms pages, and readiness endpoint.
- Versioned database migrations, gameplay smoke checks, repository-hygiene
  checks, a combined verifier, and CI.
- A recoverable scheduled-task failure ledger with five-attempt quarantine for
  poison events.
- OpenVillage identity, ruleset, operations, privacy, provenance, and release
  documentation.
- Database ownership, aggregate, economy, army, clock, migration, and
  scheduled-event invariants.
- An exact upstream baseline, read-only drift check, and guarded import guide
  for the sanitized modernization history.
- A pinned PHPStan static-analysis gate covering the maintained PHP source tree.
- A ruleset parity matrix linking supported behavior to canonical code and
  executable regression evidence.
- POST-only Rally Point troop and movement mutations with per-village checker
  validation and generated forms.
- POST-only account deletion and pending-email cancellation controls with
  checker validation.
- POST-only sitter assignment and removal controls with checker validation.
- Authenticated smoke coverage for the account-options page and its checker
  token, plus the sitter-options page.

### Changed

- Repository layout: `main.sql` and the filter lists live in `data/`, the
  clean-install compose override in `docker/`, and the checks and tests run by
  `verify.sh` and CI in `scripts/checks/`; the README describes the layout.
- Selected `main_script` as the canonical maintained game tree.
- Modernized password verification/migration, sessions, random tokens, and CSRF.
- Updated the application for PHP 8.3 and current MariaDB/Redis runtimes.
- Replaced hosted help/statistics/logout integrations with local behavior.
- Frozen the tested profile to Romans, Teutons, and Gauls on a 10× local world.
- Updated GitHub Actions to the current Node 24 checkout runtime.
- Added savepoint-backed nested database transactions for atomic worker effects
  that invoke existing transactional services.
- Added locked, idempotent referral reward processing with crash/retry coverage.
- Added transactional oasis deletion with recoverable failures and replay tests.
- Added transactional recurring trade-route dispatch and retry coverage.
- Added global notification delivery keys and migration tracking.
- Added idempotent delivery keys for activation mail outboxes.
- Added a disposable accelerated complete-round regression from registration
  through World Wonder level 100 and winner rendering.
- Added deterministic golden formula vectors for economy, construction,
  training, villages, heroes, artifacts, combat, and World Wonder endgame.
- Added process-local deterministic clock/seed controls and ordered disposable
  map fixtures for repeatable complete-round tests.
- Added static analysis to local verification and GitHub Actions, with runtime
  fixes for nested helper redeclarations, alliance loss charts, installer
  returns, and legacy CAPTCHA properties.

### Fixed

- T4.4 graphic packs (Classic, Seasonized, Seasonized v2) show the icons of the
  T4.6 markup and place the village centre buildings on their sites
  (`scripts/gpack/make-classic-compat.py`).
- iOS app on phones in portrait: every graphic pack fills the screen height;
  wider pages scroll sideways.
- Registration/activation/login routing and authenticated-page PHP failures.
- Database connection-age handling and worker polling/shutdown behavior.
- Regression-test auto-increment isolation and unsigned adventure ownership.
- World Wonder second-plan alliance semantics and fabricated online statistics.
- World Wonder Natar attack-level scheduling, two-wave timing, and deterministic
  army fixtures.
- Grey-area settlements now receive all 14 intended Natar waves.
- Quest silver now has atomic accounting; village-scoped troop punishment now
  recalculates crop upkeep for home, trapped, and reinforced armies.
- Public-truce notices render content, and vacation duration and infobox state
  remain synchronized on entry and abort.
- GitHub Actions now installs its verification tools, and repository hygiene
  fails closed when ripgrep is unavailable.
- Local verification now rejects stale application images instead of silently
  testing code from an earlier build.
- Research completion now locks and consumes its queue row in the same
  transaction as the technology effect, so crashes retry safely and duplicate
  delivery cannot apply the effect twice.
- Building completion and demolition now commit their queue consumption and
  game-state effects together, with duplicate delivery suppressed by row locks.
- Training and alliance-bonus completion now atomically persist queue state,
  troops/upkeep or bonus effects, and duplicate-delivery suppression.
- Merchant sends now commit queue consumption, resource movement, and the next
  route in one transaction and ignore duplicate delivery.
- Merchant delivery and return workers now lock both endpoint villages and
  retain retry/quarantine payloads when an endpoint or replacement route is
  missing, rolling back every resource effect after a failed route insert.
- Movement completion now commits battles, arrivals, or returns with event
  consumption under one row lock; return-arrival replay is regression-tested.
- Forked automation workers now have unique identities, unexpected child exits
  fail the parent for container recovery, and shutdown signals and reaps every
  tracked child.
- Voting rewards, purchase messages, and expired bans now commit queue and
  player-facing state atomically and suppress duplicate delivery.
- Referral rewards now commit referral state and gift gold atomically, with
  replay-safe processing and recoverable failures.
- Oasis deletion now commits release, enforcement returns, and movement
  cancellation together with queue consumption.
- Recurring trade routes now lock source resources and advance their schedule
  only after a successful dispatch or deliberate no-op.
- Notification queue rows now retry safely across the game/global databases;
  stable delivery keys suppress duplicate global notices.
- Activation reminder jobs now queue mail before marking reminders delivered;
  replayed reminders reuse a stable outbox key instead of duplicating mail rows.
- Troop sends, farm-list raids, settler dispatch, and reinforcement withdrawals
  now atomically debit their source state and queue movements, preventing
  failed inserts or concurrent requests from losing or overdrawing units.
- Settler cancellation now locks and reverses an outgoing movement atomically,
  so stale, late, concurrent, and replayed requests cannot duplicate its
  resource refund.
- Research, alliance-bonus, referral, and recurring-trade tasks now reject
  missing or failed effect targets instead of silently consuming or advancing
  them; retry and quarantine metadata preserve every failed payload.
- Marketplace sends and offer creation, acceptance, and cancellation now commit
  their resource and merchant effects atomically, so an insert failure cannot
  consume resources, delete an offer, or leave half of an accepted trade live.
- Winner and no-winner pages now preserve literal CSS percentages in translated
  markup instead of treating them as `vsprintf` format tokens.
- Nullable database and resource values are normalized before PHP 8.3 string
  helpers run, removing avoidable deprecation noise from normal gameplay.
- The registration smoke flow now submits the sector field used by the
  confirmation form, so clean-world activation is exercised accurately.
- Login JavaScript failures and external tracking requests.

### Removed

- Unused files: the unreferenced `main_default` and `newsletter` graphic
  folders, five templates nothing renders (old web installer, gold transfer
  popup, voting, verification progress), stray `Thumbs.db` / `error_log` /
  Python cache files and the agent working log `progress.md`; the roadmap
  moved to `docs/ROADMAP.md`.
- Payments, password-derived privileged login links, and obsolete hosted services.
- Generated/duplicate game trees, phpMyAdmin, legacy deploy services, and logs.
- Unused D3 and d3pie files.
- TweenMax and standalone/embedded MorphSVG copies; affected UI paths now use
  native DOM/SVG updates.
- Four unreachable duplicate graphic packs and their nonfunctional preference selector.
