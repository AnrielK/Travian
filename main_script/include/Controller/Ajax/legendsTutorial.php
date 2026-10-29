<?php

namespace Controller\Ajax;

use resources\View\LegendsTutorial as Guide;

/** Onboarding guide of the Legends look (js/Game/Legends/tutorial.js): POST thread + step reached (-1 = thread done). */
class legendsTutorial extends AjaxBase
{
    public function dispatch()
    {
        if (!$this->session->isValid() || !is_hero_v2() || $this->session->isSitter()) {
            return $this->error('Not available.');
        }
        $uid = (int)$this->session->getPlayerId();
        $thread = (string)($_POST['thread'] ?? '');
        $step = (int)($_POST['step'] ?? -1);
        $this->response['data']['state'] = Guide::set($uid, $thread, $step);
        return true;
    }
}
