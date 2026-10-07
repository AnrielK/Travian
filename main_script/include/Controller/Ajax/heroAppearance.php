<?php

namespace Controller\Ajax;

use Game\Hero\HeroAppearanceV2;

/** Saves the Travian Legends hero appearance (graphic packs with heroAppearance/index.json). */
class heroAppearance extends AjaxBase
{
    public function dispatch()
    {
        if (!$this->session->isValid() || !HeroAppearanceV2::available()) {
            return $this->error('Hero appearance is not available.');
        }
        if ($this->session->isSitter()) {
            return $this->error('Sitters cannot change the hero.', 403);
        }
        $config = json_decode((string)($_POST['config'] ?? ''), true);
        if (!is_array($config) || !HeroAppearanceV2::isValid($config)) {
            return $this->error('Invalid appearance.');
        }
        HeroAppearanceV2::save((int)$this->session->getPlayerId(), $config);
        $this->response['data']['saved'] = true;
        return true;
    }
}
