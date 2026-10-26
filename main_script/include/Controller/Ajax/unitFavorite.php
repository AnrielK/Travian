<?php

namespace Controller\Ajax;

use Model\UnitFavorites;

/** The star on a unit of a training building: unitId, favorite (1/0). */
class unitFavorite extends AjaxBase
{
    public function dispatch()
    {
        if (!$this->session->isValid()) {
            return;
        }
        $unitId = (int)($_REQUEST['unitId'] ?? 0);
        if ($unitId < 1 || $unitId > 100) {
            $this->response['error'] = true;
            $this->response['errorMsg'] = 'Unit is not valid!';
            return;
        }
        UnitFavorites::set($this->session->getPlayerId(), $unitId, !empty($_REQUEST['favorite']));
        $this->response['data']['success'] = true;
    }
}
