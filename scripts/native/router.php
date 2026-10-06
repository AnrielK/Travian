<?php
/*
 * Router for PHP's built-in web server (native, Docker-free local mode).
 *
 * Reproduces docker/php/openvillage.conf:
 *   /        -> web/public                      (launcher, register, health)
 *   /game/   -> main_script/copyable/public      (static files + index.php front controller)
 *   /gpack/  -> sections/gpack                   (static graphic pack)
 * plus the security headers, the .htaccess deny rules and long-lived caching for assets.
 *
 * Start:  php -S 127.0.0.1:8080 -t web/public scripts/native/router.php
 */

declare(strict_types=1);

// php -S does not apply auto_prepend_file to router scripts – do it here (used for redis-polyfill.php).
if (($ovPrepend = (string)ini_get('auto_prepend_file')) !== '' && is_file($ovPrepend)) {
    require_once $ovPrepend;
}

function ov_route(): array|bool
{
$root = dirname(__DIR__, 2);
$path = rawurldecode((string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'));

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; connect-src 'self'; frame-ancestors 'self'");

function ov_deny(int $code = 403): bool
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=utf-8');
    echo $code === 404 ? "Not Found\n" : "Forbidden\n";
    return true;
}

function ov_mime(string $file): string
{
    static $types = [
        'css' => 'text/css; charset=utf-8', 'js' => 'application/javascript; charset=utf-8',
        'json' => 'application/json', 'html' => 'text/html; charset=utf-8', 'htm' => 'text/html; charset=utf-8',
        'txt' => 'text/plain; charset=utf-8', 'xml' => 'application/xml',
        'png' => 'image/png', 'gif' => 'image/gif', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
        'svg' => 'image/svg+xml', 'ico' => 'image/x-icon', 'webp' => 'image/webp',
        'woff' => 'font/woff', 'woff2' => 'font/woff2', 'ttf' => 'font/ttf', 'eot' => 'application/vnd.ms-fontobject',
        'otf' => 'font/otf', 'swf' => 'application/x-shockwave-flash', 'mp3' => 'audio/mpeg', 'ogg' => 'audio/ogg',
    ];
    return $types[strtolower(pathinfo($file, PATHINFO_EXTENSION))] ?? 'application/octet-stream';
}

function ov_static(string $file): bool
{
    $mtime = (int)filemtime($file);
    $size = (int)filesize($file);
    $etag = '"' . dechex($mtime) . '-' . dechex($size) . '"';
    header('Content-Type: ' . ov_mime($file));
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
    header('ETag: ' . $etag);
    header('Cache-Control: public, max-age=' . (preg_match('/\.(png|gif|jpe?g|ico|css|js|woff2?|ttf|eot|svg)$/i', $file) ? 2592000 : 1800));
    if (preg_match('/\.(ttf|ttc|otf|eot|woff2?|css|js)$/i', $file)) {
        header('Access-Control-Allow-Origin: *');
    }
    if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
        http_response_code(304);
        return true;
    }
    header('Content-Length: ' . $size);
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'HEAD') {
        readfile($file);
    }
    return true;
}

/** A PHP script to execute (in global scope, see bottom of file) as if Apache had mapped it to $scriptName. */
function ov_run_php(string $file, string $scriptName): array
{
    return [$file, $scriptName];
}

/** Resolves $relative inside $base; null when it escapes $base or is hidden. */
function ov_resolve(string $base, string $relative): ?string
{
    foreach (explode('/', $relative) as $segment) {
        if ($segment === '..' || ($segment !== '' && $segment[0] === '.')) {
            return null;
        }
    }
    // ".." and hidden segments are rejected above, so the path cannot leave $base. Symlinks are allowed
    // on purpose: the iOS app links read-only asset folders from its bundle into the document root.
    return $base . '/' . ltrim($relative, '/');
}

// ---- /__openvillage/<action>  (local control endpoint, see runtime/control.php) -----
if (preg_match('#^/__openvillage/([a-z]+)$#', $path, $m)) {
    $GLOBALS['ovControlAction'] = $m[1];
    return ov_run_php(__DIR__ . '/runtime/control.php', '/__openvillage/' . $m[1]);
}

// ---- /game/ ---------------------------------------------------------------
if ($path === '/game') {
    header('Location: /game/', true, 302);
    return true;
}
if (str_starts_with($path, '/game/')) {
    $base = $root . '/main_script/copyable/public';
    $relative = substr($path, strlen('/game/'));
    if (preg_match('#php.*/.*$#', $relative)) {          // .htaccess: RewriteRule .*php.*\/.*$ [R=404]
        return ov_deny(404);
    }
    if (preg_match('#\.(zip|tar\.gz|gz|gzip|rar|tar|exe)$#i', $relative)
        || str_starts_with($relative, 'img/hero/female/body/')) {
        return ov_deny(403);
    }
    $file = ov_resolve($base, $relative);
    if ($file === null) {
        return ov_deny(403);
    }
    if (is_dir($file) && is_file($file . '/index.php')) {
        return ov_run_php(realpath($file . '/index.php'), '/game/' . trim($relative, '/') . ($relative === '' ? '' : '/') . 'index.php');
    }
    if (is_file($file)) {
        if (str_ends_with($file, '.php')) {
            return ov_run_php($file, '/game/' . $relative);
        }
        return ov_static($file);
    }
    // Front controller (RewriteRule ^ index.php)
    return ov_run_php($base . '/index.php', '/game/index.php');
}

// ---- /gpack/ --------------------------------------------------------------
if (str_starts_with($path, '/gpack/')) {
    $base = $root . '/sections/gpack';
    $relative = substr($path, strlen('/gpack/'));
    if (preg_match('#\.(php|zip|tar\.gz|gz|gzip|rar|tar|exe)$#i', $relative)) {
        return ov_deny(403);
    }
    $file = ov_resolve($base, $relative);
    if ($file === null) {
        return ov_deny(403);
    }
    if (!is_file($file) && preg_match('#^([A-Za-z0-9]+)/mainPage/(.+)$#', $relative, $m) && $m[1] !== 'a17a8f72') {
        // Older styles (GAME_GPACK) lack some images the 4.6 templates use: fall back to the default style.
        $alt = $m[2];
        foreach ([$m[1] . '/mainPage/' . preg_replace('#^img_(ltr|rtl)/#', 'img/', $alt), 'a17a8f72/mainPage/' . $alt] as $candidate) {
            $fallback = ov_resolve($base, $candidate);
            if ($fallback !== null && is_file($fallback)) {
                return ov_static($fallback);
            }
        }
    }
    return is_file($file) ? ov_static($file) : ov_deny(404);
}

// ---- / (web/public) -------------------------------------------------------
$base = $root . '/web/public';
$file = ov_resolve($base, $path);
if ($file === null) {
    return ov_deny(403);
}
if (is_dir($file)) {
    $file = rtrim($file, '/') . '/index.php';
}
if (!is_file($file)) {
    return ov_deny(404);
}
if (str_ends_with($file, '.php')) {
    return ov_run_php($file, '/' . ltrim(substr($file, strlen($base)), '/'));
}
return ov_static($file);
}

$ovTarget = ov_route();
if (!is_array($ovTarget)) {
    return $ovTarget;
}
$_SERVER['SCRIPT_FILENAME'] = $ovTarget[0];
$_SERVER['SCRIPT_NAME'] = $ovTarget[1];
$_SERVER['PHP_SELF'] = $ovTarget[1];
chdir(dirname($ovTarget[0]));
require $ovTarget[0];
