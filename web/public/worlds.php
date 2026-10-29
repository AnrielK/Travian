<?php

declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
require __DIR__ . '/lobby-i18n.php';

/*
 * Game worlds of this server: the list, a form that creates a further world with its own look, speed and map
 * (scripts/native/runtime/worlds.php) and deleting a further world. The look is chosen here once: the world keeps
 * it (config.gpack). Creating and deleting work only from this computer (loopback), like the control endpoint.
 *
 * iOS app without a world yet (first start, Nástroje → Nový svět): the same form creates the app's own world 1.
 * It is sent to openvillage://create-world, which the app catches (BrowserModel) and creates the world with.
 */
$local = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
$supported = function_exists('ov_register_world');
$firstWorld = getenv('OV_IOS') === '1' && !(lobby_worlds()[1]['ready'] ?? false);
$gpacks = require dirname(__DIR__, 2) . '/sections/gpack/gpack.php';
$tribeLabels = [1 => 'Romans', 2 => 'Teutons', 3 => 'Gauls', 6 => 'Egyptians', 7 => 'Huns', 8 => 'Spartans'];
$message = '';
$error = '';
$form = ['name' => '', 'gpack' => $gpacks['default'], 'tribes' => 'all', 'speed' => 10, 'mapSize' => $firstWorld ? 25 : 50, 'roundDays' => 35,
    'heroSpeed' => 1];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !$firstWorld) {
    $action = (string)($_POST['action'] ?? '');
    if (!$local || !$supported) {
        $error = lt('Worlds can only be created and deleted on this computer.');
    } elseif (!hash_equals(csrf_token(), (string)($_POST['csrf'] ?? ''))) {
        $error = lt('Your form expired. Please try again.');
    } elseif ($action === 'delete') {
        try {
            ov_delete_world((int)($_POST['world'] ?? 0));
            $message = lt('The world was deleted.');
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    } elseif ($action === 'create') {
        $form = [
            'name' => trim((string)($_POST['name'] ?? '')),
            'gpack' => (string)($_POST['gpack'] ?? ''),
            'tribes' => ($_POST['tribes'] ?? '') === 'classic' ? 'classic' : 'all',
            'speed' => max(1, min(1000, (int)($_POST['speed'] ?? 10))),
            'mapSize' => max(25, min(200, (int)($_POST['mapSize'] ?? 50))),
            'roundDays' => max(1, min(365, (int)($_POST['roundDays'] ?? 35))),
            'heroSpeed' => max(1, min(10, (int)($_POST['heroSpeed'] ?? 1))),
        ];
        $password = (string)($_POST['password'] ?? '');
        if (preg_match('/^.{1,40}$/u', $form['name']) !== 1) {
            $error = lt('Enter a name of the world (at most 40 characters).');
        } elseif (!isset($gpacks['styles'][$form['gpack']])) {
            $error = lt('Choose a look.');
        } elseif (strlen($password) < 6) {
            $error = lt('The administrator password must have at least 6 characters.');
        } else {
            // The game's installer runs in this request with the new world's settings (minutes for a big map).
            session_write_close();
            set_time_limit(0);
            ignore_user_abort(true);
            $id = 0;
            try {
                $id = ov_register_world($form);
                ov_apply_world($id);
                putenv('GAME_TRIBES=' . $form['tribes']);   // stored in the new world's config by the installer
                ov_install_world($password);
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }
                header('Location: /?world=' . $id . '&open=worldDialog', true, 303);
                exit;
            } catch (Throwable $exception) {
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }
                if ($id > 0) {
                    try {
                        ov_delete_world($id);
                    } catch (Throwable $ignored) {
                    }
                }
                $error = lt('The world could not be created:') . ' ' . $exception->getMessage();
            }
        }
    }
}
$worlds = lobby_worlds();
$tribeText = static function (array $ids) use ($tribeLabels): string {
    return implode(', ', array_map(static fn ($id) => lt($tribeLabels[$id] ?? (string)$id), $ids));
};
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="<?= lobby_language() ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escape(lt('Game worlds')) ?> – OpenVillage</title>
    <style>
        :root { --bg: #efe6cf; --card: #fffaf0; --ink: #3b2f1e; --muted: #7a6a50; --line: #d9c9a3; --green: #5b8a2e; --green-dark: #46701f; --red: #b23a2a; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--ink); font: 15px/1.5 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        main { max-width: 760px; margin: 0 auto; padding: 24px 16px 48px; }
        h1 { margin: 0 0 16px; font-size: 26px; }
        h2 { margin: 0 0 12px; font-size: 19px; }
        a { color: var(--green-dark); }
        section { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 6px 4px; border-bottom: 1px solid var(--line); vertical-align: top; }
        th { font-size: 13px; color: var(--muted); font-weight: 600; }
        td.small { font-size: 13px; color: var(--muted); }
        label { display: block; margin: 10px 0 4px; font-weight: 600; }
        .hint { font-weight: normal; color: var(--muted); font-size: 13px; }
        input, select { width: 100%; padding: 8px 10px; border: 1px solid var(--line); border-radius: 6px; font: inherit; background: #fff; color: var(--ink); }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
        @media (max-width: 560px) { .row { grid-template-columns: 1fr; } table .small { display: none; } }
        .actions { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-top: 20px; }
        button { padding: 9px 18px; border: 0; border-radius: 6px; background: var(--green); color: #fff; font: inherit; font-weight: 600; cursor: pointer; }
        button:hover { background: var(--green-dark); }
        button.delete { background: none; color: var(--red); padding: 0; font-weight: normal; text-decoration: underline; }
        button:disabled { opacity: .5; cursor: default; }
        .msg { padding: 10px 12px; border-radius: 8px; margin-bottom: 16px; background: #dfeccb; }
        .msg.error { background: #f3d6cf; color: var(--red); }
        #busy { display: none; margin-top: 12px; color: var(--muted); }
        form.creating #busy { display: block; }
    </style>
</head>
<body>
<main>
    <h1><?= escape(lt('Game worlds')) ?></h1>
    <?php if (!$firstWorld): ?><p><a href="/">← <?= escape(lt('Lobby')) ?></a></p><?php endif; ?>
    <?php if ($message !== ''): ?><div class="msg"><?= escape($message) ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="msg error"><?= escape($error) ?></div><?php endif; ?>

    <?php if (!$firstWorld): ?>
    <section>
        <table>
            <tr><th><?= escape(lt('World')) ?></th><th><?= escape(lt('Look')) ?></th><th class="small"><?= escape(lt('Tribes')) ?></th>
                <th><?= escape(lt('Speed')) ?></th><th><?= escape(lt('Players:')) ?></th><th></th></tr>
            <?php foreach ($worlds as $w): ?>
                <?php $look = $gpacks['styles'][$w['gpack']] ?? ($w['id'] === 1 ? $gpacks['styles'][$gpacks['mainPage']] ?? [] : []); ?>
                <tr>
                    <td><a href="/?world=<?= $w['id'] ?>&amp;open=worldDialog"><?= escape($w['id'] === 1 && $w['name'] === 'Local World' ? lt('Local world') : $w['name']) ?></a></td>
                    <td><?= escape($w['lookName']) ?></td>
                    <td class="small"><?= escape($tribeText(array_values(array_intersect($look['tribes'] ?? [1, 2, 3, 6, 7], ($w['tribeMode'] ?? '') === 'classic' ? [1, 2, 3] : [1, 2, 3, 6, 7, 8])))) ?></td>
                    <td>×<?= escape(rtrim(rtrim(number_format($w['speed'], 2, '.', ''), '0'), '.')) ?></td>
                    <td><?= (int)$w['players'] ?></td>
                    <td>
                        <?php if ($w['id'] > 1 && $local && $supported): ?>
                            <form method="post" onsubmit="return confirm(<?= escape(json_encode(lt('Delete this world with all its players and villages?'), JSON_UNESCAPED_UNICODE)) ?>)">
                                <input type="hidden" name="csrf" value="<?= escape(csrf_token()) ?>">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="world" value="<?= $w['id'] ?>">
                                <button class="delete" type="submit"><?= escape(lt('Delete')) ?></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
    <?php endif; ?>

    <section>
        <h2><?= escape(lt($firstWorld ? 'Create your game world' : 'Create a new game world')) ?></h2>
        <?php if ($firstWorld): ?>
        <form method="get" action="openvillage://create-world" id="create">
        <?php elseif (!$local || !$supported): ?>
            <p class="hint"><?= escape(lt('Worlds can only be created and deleted on this computer.')) ?></p>
        <?php else: ?>
        <form method="post" id="create">
        <?php endif; ?>
        <?php if ($firstWorld || ($local && $supported)): ?>
            <input type="hidden" name="csrf" value="<?= escape(csrf_token()) ?>">
            <input type="hidden" name="action" value="create">
            <div class="row">
                <div><label for="name"><?= escape(lt('Name of the world')) ?></label>
                    <input id="name" name="name" maxlength="40" required value="<?= escape($form['name']) ?>"></div>
                <div><label for="gpack"><?= escape(lt('Look')) ?> <span class="hint">(<?= escape(lt('cannot be changed later')) ?>)</span></label>
                    <select id="gpack" name="gpack">
                        <?php foreach ($gpacks['styles'] as $style): ?>
                            <?php $hash = (string)$style['hash']; // numeric pack names become integer array keys ?>
                            <option value="<?= escape($hash) ?>" <?= $hash === $form['gpack'] ? 'selected' : '' ?>><?= escape($style['name'] . ' – ' . $tribeText($style['tribes'] ?? [])) ?></option>
                        <?php endforeach; ?>
                    </select></div>
            </div>
            <label for="tribes"><?= escape(lt('Tribes')) ?> <span class="hint">(<?= escape(lt('cannot be changed later')) ?>)</span></label>
            <select id="tribes" name="tribes">
                <option value="all" <?= $form['tribes'] === 'all' ? 'selected' : '' ?>><?= escape(lt('All tribes of the look (also Egyptians and Huns)')) ?></option>
                <option value="classic" <?= $form['tribes'] === 'classic' ? 'selected' : '' ?>><?= escape(lt('Only the three original tribes (Romans, Teutons, Gauls)')) ?></option>
            </select>
            <div class="row">
                <div><label for="speed"><?= escape(lt('Speed')) ?></label>
                    <select id="speed" name="speed">
                        <?php foreach ([1, 3, 5, 10, 50, 100] as $value): ?>
                            <option value="<?= $value ?>" <?= $value === $form['speed'] ? 'selected' : '' ?>><?= $value ?>×</option>
                        <?php endforeach; ?>
                    </select></div>
                <div><label for="mapSize"><?= escape(lt('Map size:')) ?> <span class="hint">(<?= escape(lt('a bigger map takes longer to create')) ?>)</span></label>
                    <select id="mapSize" name="mapSize">
                        <?php foreach ([25, 50, 100, 200] as $value): ?>
                            <option value="<?= $value ?>" <?= $value === $form['mapSize'] ? 'selected' : '' ?>><?= $value ?> × <?= $value ?></option>
                        <?php endforeach; ?>
                    </select></div>
            </div>
            <div class="row">
                <div><label for="roundDays"><?= escape(lt('Round length (days)')) ?></label>
                    <input id="roundDays" name="roundDays" type="number" min="1" max="365" value="<?= $form['roundDays'] ?>"></div>
                <div><label for="heroSpeed"><?= escape(lt('Hero speed')) ?></label>
                    <select id="heroSpeed" name="heroSpeed">
                        <?php foreach ([1, 2, 3, 5] as $value): ?>
                            <option value="<?= $value ?>" <?= $value === $form['heroSpeed'] ? 'selected' : '' ?>><?= $value ?>×</option>
                        <?php endforeach; ?>
                    </select></div>
            </div>
            <?php if (!$firstWorld): ?>
            <label for="password"><?= escape(lt('Administrator password')) ?> <span class="hint">(<?= escape(lt('Multihunter and Support of this world, at least 6 characters')) ?>)</span></label>
            <input id="password" name="password" type="password" minlength="6" required autocomplete="new-password">
            <?php endif; ?>
            <div class="actions"><span></span><button type="submit"><?= escape(lt('Create world')) ?></button></div>
            <p id="busy"><?= escape(lt('Creating the world, this can take several minutes…')) ?></p>
        </form>
        <script>
            document.getElementById('create').addEventListener('submit', function () {
                this.classList.add('creating');
                this.querySelector('button[type=submit]').disabled = true;
            });
        </script>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
