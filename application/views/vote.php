<?php
#Example PHP Postback Script
#Arena-Top100 does not take responsibility or liability for the use of this php snippet

// Database connection: taken from application/config.php (hard-coded credentials removed)
require_once dirname(__DIR__, 2) . '/baglanti.php';

// User
$userid = isset($_POST['userid']) ? $_POST['userid'] : null;

// User IP
$userip = isset($_POST['userip']) ? $_POST['userip'] : null;

// User voted on Arena-Top100? 1 = unique vote, 0 = already voted
$valid = isset($_POST['voted']) ? (int)$_POST['voted'] : 0;

// Make sure the user was not yet rewarded on your system
// For example by checking your reward logs
if (!is_null($userid) && $valid == 1) 
{		
    // userid is escaped with sql_esc() in the queries below

    // COUNT rows where reward_date is today
    $result = mysqli_query($connection, "SELECT COUNT(*) FROM `reward_log` WHERE `user` = '" . sql_esc($userid) . "' AND `reward_date` >= CURDATE()");
    $row = safe_mysqli_fetch_array($result);
    $rewarded = $row ? (int)$row[0] : 0; // COUNT(*) always returns one row; use its value
		
    // No results means not rewarded yet, so grant reward and set the reward date/time log
    if(!$rewarded) 
    {
         // Grant reward, for example vote points
        mysqli_query($connection, "UPDATE `users` SET `points` = `points` + 1 WHERE `user` = '" . sql_esc($userid) . "'");
			
         // Insert log
        mysqli_query($connection, "INSERT INTO `reward_log` (`user`, `reward_date`) VALUES ('" . sql_esc($userid) . "', NOW())");
    }
}

// Close connection
mysqli_close($connection);

?>