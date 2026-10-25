<?php

namespace Controller;

use Core\Config;
use Core\Database\DB;
use Core\Helper\WebService;
use resources\View\OutOfGameView;
use resources\View\PHPBatchView;

/**
 * Registration as a page of the world in its own look (register.php), like the login page. The lobby sends
 * worlds whose look has such a page ('ownRegistration' in sections/gpack/gpack.php, Travian 4.0) here; the
 * others register in the lobby (web/public/register.php). Same rules and the same activation row as the
 * lobby: the player then chooses the tribe and the start on activate.php.
 */
class RegisterCtrl extends OutOfGameCtrl
{
    public function __construct()
    {
        parent::__construct();
        if (Config::getInstance()->dynamic->maintenance == TRUE) {
            $this->redirect('login.php');
        }
        $this->view = new OutOfGameView();
        $this->view->vars['titleInHeader'] = T('Register', 'title', 'Join the game world');
        $this->view->vars['bodyCssClass'] = 'perspectiveBuildings';
        $this->view->vars['contentCssClass'] = 'login';                // the outside frame of the login page
        $this->view->vars['registerPage'] = TRUE;
        $form = new PHPBatchView('system/register');
        $form->vars = ['name' => '', 'email' => '', 'error' => ''];
        if (WebService::isPost()) {
            $form->vars = $this->post() + $form->vars;
        }
        $form->vars['csrf'] = $this->csrfToken();
        $this->view->vars['content'] .= $form->output();
    }

    private function csrfToken()
    {
        $key = WebService::fixSessionPrefix('register_csrf');
        if (empty($_SESSION[$key])) {
            $_SESSION[$key] = bin2hex(random_bytes(32));
        }
        return $_SESSION[$key];
    }

    /** Checks the form and creates the activation row; returns the form values with an error, or redirects. */
    private function post()
    {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $vars = ['name' => $name, 'email' => $email];
        $attemptsKey = WebService::fixSessionPrefix('register_attempts');
        $attempts = array_values(array_filter($_SESSION[$attemptsKey] ?? [], function ($time) {
            return $time > time() - 60;
        }));
        if (!hash_equals($this->csrfToken(), (string)($_POST['csrf'] ?? ''))) {
            return $vars + ['error' => T('Register', 'expired', 'Your form expired. Please try again.')];
        }
        if (count($attempts) >= 5) {
            return $vars + ['error' => T('Register', 'tooMany', 'Too many attempts. Please wait one minute.')];
        }
        $attempts[] = time();
        $_SESSION[$attemptsKey] = $attempts;
        if (preg_match('/^[A-Za-z0-9 _-]{3,15}$/', $name) !== 1) {
            return $vars + ['error' => T('Register', 'nameInvalid',
                    'Player name must be 3–15 letters, numbers, spaces, underscores, or hyphens.')];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 90) {
            return $vars + ['error' => T('Register', 'emailInvalid', 'Enter a valid email address.')];
        }
        if (strlen($password) < 10 || strlen($password) > 128) {
            return $vars + ['error' => T('Register', 'passwordInvalid', 'Password must be 10–128 characters.')];
        }
        if (!isset($_POST['terms'])) {
            return $vars + ['error' => T('Register', 'termsMissing', 'You must accept the terms and privacy notice.')];
        }
        $db = DB::getInstance();
        $taken = $db->run('SELECT 1 FROM users WHERE name=? OR email=? UNION ALL SELECT 1 FROM activation WHERE name=? OR email=? LIMIT 1',
            [$name, $email, $name, $email]);
        if ($taken === FALSE) {
            return $vars + ['error' => T('Register', 'unavailable', 'Registration is temporarily unavailable.')];
        }
        if ($taken->get_result()->num_rows) {
            return $vars + ['error' => T('Register', 'taken', 'That player name or email is already registered.')];
        }
        $token = bin2hex(random_bytes(24));
        $hash = password_hash($password, defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT);
        if ($db->run('INSERT INTO activation (name, password, email, token, refUid, time) VALUES (?, ?, ?, ?, 0, ?)',
                [$name, $hash, $email, $token, time()]) === FALSE) {
            return $vars + ['error' => T('Register', 'unavailable', 'Registration is temporarily unavailable.')];
        }
        unset($_SESSION[WebService::fixSessionPrefix('register_csrf')]);
        $this->redirect('activate.php?token=' . rawurlencode($token), 303);
    }
}
