<?php

namespace Model;

use Core\Config;
use Core\Database\DB;
use Game\Formulas;
use function getCustom;
use function getGameElapsedSeconds;
use function isServerFinished;
use function make_seed;

/**
 * Alliances of the computer-controlled players (bots, users.access = 3), under the players' rules:
 * - a bot accepts an invitation (from a player or a bot) once one of its villages has an embassy;
 * - a bot with an embassy on level 3 and without an alliance may found one (a bot alliance per 6 bots at most),
 *   and the founders invite bots without an alliance from within INVITE_RADIUS fields while the alliance has room
 *   (the members' embassies decide its size, AllianceModel::recalculateMaxUsers()).
 * Bots never raid members of their own alliance (BotRaidModel::chooseTarget()).
 */
class BotAllianceModel
{
    const FOUND_LEVEL = 3;
    const INVITE_RADIUS = 20;
    const BOTS_PER_ALLIANCE = 6;

    const NAMES = [
        'Vlčí smečka', 'Železný kruh', 'Rudá korouhev', 'Stříbrný štít', 'Bratrstvo dubu', 'Severní hvozd',
        'Kamenná stráž', 'Zlatý klas', 'Černý havran', 'Synové hor', 'Říční lid', 'Ohnivý kříž', 'Bílí jezdci',
        'Strážci brodu', 'Liga svobodných', 'Hraniční pakt', 'Medvědí klan', 'Orlí hnízdo',
    ];

    private function canRun(): bool
    {
        if (getGameElapsedSeconds() <= 0 || isServerFinished() || !Config::getProperty("dynamic", "fakeAccountProcess")) {
            return false;
        }
        make_seed();
        return true;
    }

    /** Highest embassy level of the player's villages. */
    public static function embassyLevel(int $uid): int
    {
        return (int)DB::getInstance()->fetchScalar("SELECT MAX(f.embassy) FROM fdata f JOIN vdata v ON v.kid=f.kid WHERE v.owner=$uid");
    }

    /** Bots with an embassy join the first alliance that invited them. */
    public function handleBotInvites()
    {
        if (!$this->canRun()) {
            return;
        }
        $db = DB::getInstance();
        $result = $db->query("SELECT i.id, i.uid FROM ali_invite i JOIN users u ON u.id=i.uid WHERE u.access=3 AND u.aid=0 ORDER BY i.id LIMIT 20");
        $alliance = new AllianceModel();
        $done = [];
        while ($result && ($row = $result->fetch_assoc())) {
            $uid = (int)$row['uid'];
            if (isset($done[$uid])) {
                continue;
            }
            if (self::embassyLevel($uid) < 1) {
                continue;   // the invitation waits until the bot builds an embassy
            }
            if ($alliance->acceptInvite($uid, (int)$row['id']) > 0) {
                $done[$uid] = true;
            }
        }
    }

    /** Bots found alliances and invite other bots near them. */
    public function handleBotAlliances()
    {
        if (!$this->canRun()) {
            return;
        }
        $this->foundAlliance();
        $this->inviteBots();
    }

    private function foundAlliance(): void
    {
        $db = DB::getInstance();
        $bots = (int)$db->fetchScalar("SELECT COUNT(id) FROM users WHERE access=3");
        $botAlliances = (int)$db->fetchScalar("SELECT COUNT(DISTINCT u.aid) FROM users u WHERE u.access=3 AND u.aid>0
            AND (u.alliance_role & " . AllianceModel::INVITE_PLAYER . ") > 0");
        if ($botAlliances >= max(1, (int)floor($bots / self::BOTS_PER_ALLIANCE))) {
            return;
        }
        // a bot that is waiting for an invitation it can accept does not found its own alliance
        $candidates = $db->query("SELECT u.id FROM users u WHERE u.access=3 AND u.aid=0
            AND (SELECT COUNT(i.id) FROM ali_invite i WHERE i.uid=u.id)=0 ORDER BY RAND() LIMIT 10");
        while ($candidates && ($row = $candidates->fetch_assoc())) {
            $uid = (int)$row['id'];
            if (self::embassyLevel($uid) < self::FOUND_LEVEL || mt_rand(1, 3) !== 1) {
                continue;
            }
            [$name, $tag] = $this->newNameAndTag();
            (new AllianceModel())->createAlliance($uid, $name, $tag);
            return;
        }
    }

    /** A free alliance name (at most 25 bytes) and tag (at most 8 bytes). */
    private function newNameAndTag(): array
    {
        $db = DB::getInstance();
        $names = self::NAMES;
        shuffle($names);
        for ($round = 0; $round < 20; ++$round) {
            foreach ($names as $name) {
                if ($round > 0) {
                    $name = $name . ' ' . ($round + 1);
                }
                $tag = '';
                foreach (preg_split('/\s+/u', $name) as $word) {
                    $tag .= mb_strtoupper(mb_substr($word, 0, 1));
                }
                $tag = mb_strcut($tag, 0, 8, 'UTF-8');
                $n = $db->real_escape_string($name);
                $t = $db->real_escape_string($tag);
                if (!$db->fetchScalar("SELECT COUNT(id) FROM alidata WHERE name='$n' OR tag='$t'")) {
                    return [$name, $tag];
                }
            }
        }
        $suffix = (string)mt_rand(100, 999);
        return ['Aliance ' . $suffix, 'A' . $suffix];
    }

    private function inviteBots(): void
    {
        $db = DB::getInstance();
        $alliance = new AllianceModel();
        $founders = $db->query("SELECT u.id, u.aid, u.kid, a.max FROM users u JOIN alidata a ON a.id=u.aid
            WHERE u.access=3 AND (u.alliance_role & " . AllianceModel::INVITE_PLAYER . ") > 0");
        while ($founders && ($founder = $founders->fetch_assoc())) {
            $aid = (int)$founder['aid'];
            $max = min((int)$founder['max'], (int)getCustom("allianceMembersLimit") ?: (int)$founder['max']);
            $members = (int)$db->fetchScalar("SELECT COUNT(id) FROM users WHERE aid=$aid");
            $pending = (int)$db->fetchScalar("SELECT COUNT(id) FROM ali_invite WHERE aid=$aid");
            $free = $max - $members - $pending;
            if ($free <= 0) {
                continue;
            }
            $bots = $db->query("SELECT u.id, u.kid FROM users u WHERE u.access=3 AND u.aid=0 AND u.id<>{$founder['id']}
                AND (SELECT COUNT(i.id) FROM ali_invite i WHERE i.uid=u.id AND i.aid=$aid)=0");
            $near = [];
            while ($bots && ($bot = $bots->fetch_assoc())) {
                $distance = Formulas::getDistance((int)$founder['kid'], (int)$bot['kid']);
                if ($distance <= self::INVITE_RADIUS) {
                    $near[(int)$bot['id']] = $distance;
                }
            }
            asort($near);
            foreach (array_slice(array_keys($near), 0, min(2, $free)) as $uid) {
                $alliance->sendInvite((int)$founder['id'], $aid, $uid);
            }
        }
    }
}
