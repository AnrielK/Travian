<?php

use Core\Helper\WebService;
use Game\Helpers\BotNames;
use Model\RegisterModel;

class FakeUserCtrl
{
    public function __construct()
    {
        $result = false;
        if(!isServerFinished() && WebService::isPost()){
            set_time_limit(0);
            ignore_user_abort(true);
            $m = new RegisterModel();
            $names = trim(strip_tags((string)($_POST['names'] ?? '')));
            if ($names === '') {
                $names = BotNames::generate(max(0, min(100, (int)($_POST['count'] ?? 0))));
            }
            $result = $m->addFakeUser($names);
        }
        Dispatcher::getInstance()->appendContent(Template::getInstance()->load(['result' => $result], 'tpl/fakeUser.tpl')->getAsString());
    }
}