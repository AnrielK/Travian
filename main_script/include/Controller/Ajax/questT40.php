<?php

namespace Controller\Ajax;

use Model\QuestT40 as Quest;
use resources\View\PHPBatchView;

/**
 * The Travian 4.0 task dialog (worlds with the T40 look, Model\QuestT40).
 * action: view (task = an optional task 30-40 to show), start (tasks = 1 to the first task, 0 play without tasks),
 * choose (economy = 1/0), answer (value or x, y), collect (task = optional task, else the current one).
 * data.html is the dialog content, data.reward whether a reward waits, data.reload after a reward.
 */
class questT40 extends AjaxBase
{
    public function dispatch()
    {
        if (!$this->session->isValid() || !is_t40_layout()) {
            return;
        }
        $quest = new Quest();
        $action = (string)($_REQUEST['action'] ?? 'view');
        $task = (int)($_REQUEST['task'] ?? 0);
        $wrong = false;
        switch ($action) {
            case 'start':
                $quest->start(!empty($_REQUEST['tasks']));
                break;
            case 'choose':
                $quest->choose(!empty($_REQUEST['economy']));
                break;
            case 'answer':
                $wrong = !$quest->answer($quest->getState(), $_REQUEST);
                break;
            case 'collect':
                $collected = $task >= 30 ? $quest->collectOptional($task) : $quest->collect();
                $this->response['data']['reload'] = $collected;
                break;
        }
        if ($action !== 'view' || $task < 30 || $task > 40 || $quest->getState() !== Quest::OPTIONAL) {
            $task = $action === 'collect' && $task >= 30 ? 0 : max(0, $quest->getState());
        }
        $this->response['data']['html'] = PHPBatchView::render('quest/t40', [
            'quest' => $quest,
            'task' => $task,
            'wrong' => $wrong,
        ]);
        $this->response['data']['reward'] = $quest->hasReward();
        $this->response['data']['declined'] = $quest->isDeclined();
    }
}
