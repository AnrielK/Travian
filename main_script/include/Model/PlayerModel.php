<?php
namespace Model;
use Core\Database\DB;

class PlayerModel
{
	/** $pve: the attack was against Nature or the Natars, its points go to week_pve_points instead of week_attack_points. */
	public function setPlayerPoints($uid, $attack, $defense, $rob, $pve = false)
	{
		$db = DB::getInstance();
		$weekColumn = $pve ? 'week_pve_points' : 'week_attack_points';
		//TODO: add to plus statistics.
		$db->query("UPDATE users SET
        total_attack_points=total_attack_points+$attack,
        $weekColumn=$weekColumn+$attack,
        total_defense_points=total_defense_points+$defense,
        week_defense_points=week_defense_points+$defense,
        week_robber_points=week_robber_points+$rob
        WHERE id=$uid
        ");
	}
}