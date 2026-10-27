<?php
session_start();
$_SESSION['access']=9;$_SESSION['id']=6;
if($_SESSION['access'] < 9) die("<h1><font color=\"red\">Access Denied: You are not Admin!</font></h1>");
include_once("../../config.php");
include_once("../../DB.php");
include_once("../../Database/db_MYSQL.php");





$id = $_POST['id'];



$database->queryFetch("UPDATE fdata SET
	f1  = '".sql_esc($_POST['id1level'])."',
	f1t = '".sql_esc($_POST['id1gid'])."',
	f2  = '".sql_esc($_POST['id2level'])."',
	f2t = '".sql_esc($_POST['id2gid'])."',
	f3  = '".sql_esc($_POST['id3level'])."',
	f3t = '".sql_esc($_POST['id3gid'])."',
	f4  = '".sql_esc($_POST['id4level'])."',
	f4t = '".sql_esc($_POST['id4gid'])."',
	f5  = '".sql_esc($_POST['id5level'])."',
	f5t = '".sql_esc($_POST['id5gid'])."',
	f6  = '".sql_esc($_POST['id6level'])."',
	f6t = '".sql_esc($_POST['id6gid'])."',
	f7  = '".sql_esc($_POST['id7level'])."',
	f7t = '".sql_esc($_POST['id7gid'])."',
	f8  = '".sql_esc($_POST['id8level'])."',
	f8t = '".sql_esc($_POST['id8gid'])."',
	f9  = '".sql_esc($_POST['id9level'])."',
	f9t = '".sql_esc($_POST['id9gid'])."',
	f10  = '".sql_esc($_POST['id10level'])."',
	f10t = '".sql_esc($_POST['id10gid'])."',
	f11  = '".sql_esc($_POST['id11level'])."',
	f11t = '".sql_esc($_POST['id11gid'])."',
	f12  = '".sql_esc($_POST['id12level'])."',
	f12t = '".sql_esc($_POST['id12gid'])."',
	f13  = '".sql_esc($_POST['id13level'])."',
	f13t = '".sql_esc($_POST['id13gid'])."',
	f14  = '".sql_esc($_POST['id14level'])."',
	f14t = '".sql_esc($_POST['id14gid'])."',
	f15  = '".sql_esc($_POST['id15level'])."',
	f15t = '".sql_esc($_POST['id15gid'])."',
	f16  = '".sql_esc($_POST['id16level'])."',
	f16t = '".sql_esc($_POST['id16gid'])."',
	f17  = '".sql_esc($_POST['id17level'])."',
	f17t = '".sql_esc($_POST['id17gid'])."',
	f18  = '".sql_esc($_POST['id18level'])."',
	f18t = '".sql_esc($_POST['id18gid'])."',
	f19  = '".sql_esc($_POST['id19level'])."',
	f19t = '".sql_esc($_POST['id19gid'])."',
	f20  = '".sql_esc($_POST['id20level'])."',
	f20t = '".sql_esc($_POST['id20gid'])."',
	f21  = '".sql_esc($_POST['id21level'])."',
	f21t = '".sql_esc($_POST['id21gid'])."',
	f22  = '".sql_esc($_POST['id22level'])."',
	f22t = '".sql_esc($_POST['id22gid'])."',
	f23  = '".sql_esc($_POST['id23level'])."',
	f23t = '".sql_esc($_POST['id23gid'])."',
	f24  = '".sql_esc($_POST['id24level'])."',
	f24t = '".sql_esc($_POST['id24gid'])."',
	f25  = '".sql_esc($_POST['id25level'])."',
	f25t = '".sql_esc($_POST['id25gid'])."',
	f26  = '".sql_esc($_POST['id26level'])."',
	f26t = '".sql_esc($_POST['id26gid'])."',
	f27  = '".sql_esc($_POST['id27level'])."',
	f27t = '".sql_esc($_POST['id27gid'])."',
	f28  = '".sql_esc($_POST['id28level'])."',
	f28t = '".sql_esc($_POST['id28gid'])."',
	f29  = '".sql_esc($_POST['id29level'])."',
	f29t = '".sql_esc($_POST['id29gid'])."',
	f30  = '".sql_esc($_POST['id30level'])."',
	f30t = '".sql_esc($_POST['id30gid'])."',
	f31  = '".sql_esc($_POST['id31level'])."',
	f31t = '".sql_esc($_POST['id31gid'])."',
	f32  = '".sql_esc($_POST['id32level'])."',
	f32t = '".sql_esc($_POST['id32gid'])."',
	f33  = '".sql_esc($_POST['id33level'])."',
	f33t = '".sql_esc($_POST['id33gid'])."',
	f34  = '".sql_esc($_POST['id34level'])."',
	f34t = '".sql_esc($_POST['id34gid'])."',
	f35  = '".sql_esc($_POST['id35level'])."',
	f35t = '".sql_esc($_POST['id35gid'])."',
	f36  = '".sql_esc($_POST['id36level'])."',
	f36t = '".sql_esc($_POST['id36gid'])."',
	f37  = '".sql_esc($_POST['id37level'])."',
	f37t = '".sql_esc($_POST['id37gid'])."',
	f38  = '".sql_esc($_POST['id38level'])."',
	f38t = '".sql_esc($_POST['id38gid'])."',
	f39  = '".sql_esc($_POST['id39level'])."',
	f39t = '".sql_esc($_POST['id39gid'])."',
	f40  = '".sql_esc($_POST['id40level'])."',
	f40t = '".sql_esc($_POST['id40gid'])."'
	WHERE vref = " . sql_num($id) . "");

header("Location: ../../../panel/admin.php?action=recountPop&did=".$id."");
?>