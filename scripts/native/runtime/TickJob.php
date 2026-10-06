<?php
/*
 * Replacement for Core\Jobs\Job used by the cooperative scheduler (control.php?action=tick).
 *
 * Core\Jobs\Launcher::lunchJobs() normally forks one daemon per job group (pcntl_fork). Platforms
 * without processes (iOS) load this class first, so the unchanged Launcher only *registers* the job
 * tree; control.php then runs whatever is due on every tick, following the same rules as the
 * original daemon loop (config reload, needsRestart, finishStatusSet, automationState, intervals).
 */

namespace Core\Jobs;

class Job
{
    /** @var Job[] top-level daemons in registration order */
    public static array $daemons = [];

    public string $name;
    public $interval;
    public $callback;
    public bool $daemon;

    public function __construct($name, $interval, $callBack, $daemon = false)
    {
        global $prgName;
        $prgName = $name;
        $this->name = (string)$name;
        $this->interval = $interval;
        $this->callback = $callBack;
        $this->daemon = (bool)$daemon;
        if ($daemon) {
            self::$daemons[] = $this;
        }
    }

    /** Sub-jobs (array callbacks) or null for a single callable. */
    public function children(): ?array
    {
        return is_callable($this->callback) ? null : (array)$this->callback;
    }

    public function runAction()
    {
        if (is_callable($this->callback)) {
            ($this->callback)();
        }
    }
}
