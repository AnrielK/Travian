<?php

namespace Controller\Ajax;

use resources\View\LegendsTutorial as Guide;

/** Onboarding guide of the Legends look (js/Game/Legends/tutorial.js): POST step = the step reached (0 = finished). */
class legendsTutorial extends AjaxBase
{
    public function dispatch()
    {
        if (!$this->session->isValid() || !is_hero_v2() || $this->session->isSitter()) {
            return $this->error('Not available.');
        }
        $uid = (int)$this->session->getPlayerId();
        $current = Guide::step($uid);
        $step = (int)($_POST['step'] ?? 0);
        if ($current === 0) {
            $this->response['data']['step'] = 0;
            return true;
        }
        $this->response['data']['step'] = Guide::set($uid, $step);
        return true;
    }
}
