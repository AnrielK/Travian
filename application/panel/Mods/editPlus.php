<?php
session_start();
if($_SESSION['access'] < 9) die("<h1><font color=\"red\">Access Denied: You are not Admin!</font></h1>");
include_once("../../config.php");
include_once("../../DB.php");
include_once("../../Database/db_MYSQL.php");





$id = $_POST['id'];


$pdur =  safe_num($_POST['plus']) * 86400;
$b1dur = safe_num($_POST['wood']) * 86400;
$b2dur = safe_num($_POST['clay']) * 86400;
$b3dur = safe_num($_POST['iron']) * 86400;
$b4dur = safe_num($_POST['crop']) * 86400;
$quest = $_POST['quest'];

if($pdur  > 1){ $plus = (time() + $pdur); } else { $pdur = 'plus'; }
if($b1dur > 1){ $wood = (time() + $b1dur); } else { $wood = 'b1'; }
if($b2dur > 1){ $clay = (time() + $b2dur); } else { $clay = 'b2'; }
if($b3dur > 1){ $iron = (time() + $b3dur); } else { $iron = 'b3'; }
if($b4dur > 1){ $crop = (time() + $b4dur); } else { $crop = 'b4'; }

$database->queryFetch("UPDATE users SET
	plus = '".sql_esc($plus)."',
	b1 = '".sql_esc($wood)."',
	b2 = '".sql_esc($clay)."',
	b3 = '".sql_esc($iron)."',
	b4 = '".sql_esc($crop)."',
	quest = '".sql_esc($quest)."'
	WHERE id = " . sql_num($id) . "");

header("Location: ../../../panel/admin.php?p=player&uid=".$id."");
?>