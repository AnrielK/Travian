<?php $e = function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<div class="outerLoginBox registerBox">
    <h2><?=$e(T('Register', 'heading', 'Create your account')); ?></h2>
    <div class="innerLoginBox">
        <form name="register" method="POST" action="register.php">
            <input type="hidden" name="csrf" value="<?=$e($vars['csrf']); ?>">
            <?php if ($vars['error'] !== ''): ?>
                <div class="error <?=getDirection(); ?>"><?=$e($vars['error']); ?></div>
            <?php endif; ?>
            <table class="transparent loginTable">
                <tbody>
                <tr class="account">
                    <td><?=$e(T('Register', 'name', 'Player name')); ?>:</td>
                    <td><input type="text" name="name" value="<?=$e($vars['name']); ?>" class="text" minlength="3" maxlength="15"
                               pattern="[A-Za-z0-9 _\-]+" required autocomplete="username"></td>
                </tr>
                <tr class="account">
                    <td><?=$e(T('Register', 'email', 'Email')); ?>:</td>
                    <td><input type="email" name="email" value="<?=$e($vars['email']); ?>" class="text" maxlength="90" required
                               autocomplete="email"></td>
                </tr>
                <tr class="pass">
                    <td><?=$e(T('Login', 'pass')); ?>:</td>
                    <td><input type="password" name="password" class="text" minlength="10" maxlength="128" required
                               autocomplete="new-password"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><div style="width: 230px"><label style="white-space: normal"><input type="checkbox" class="checkbox" name="terms" value="1" required>
                            <?=T('Register', 'terms', 'I accept the <a href="/terms.html">terms</a> and <a href="/privacy.html">privacy notice</a>.'); ?></label></div></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="submit" value="register" name="s1" id="s1" class="green ">
                            <div class="button-container addHoverClick ">
                                <div class="button-background">
                                    <div class="buttonStart">
                                        <div class="buttonEnd">
                                            <div class="buttonMiddle"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="button-content"><?=$e(T('Register', 'submit', 'Register')); ?></div>
                            </div>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <p style="margin-top: 8px"><?=$e(T('Register', 'next', 'Next you choose your tribe and the starting position of your village.')); ?></p>
        <p><a href="login.php"><?=$e(T('Register', 'haveAccount', 'I already have an account – log in')); ?></a></p>
    </div>
</div>
