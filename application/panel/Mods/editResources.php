<?php
session_start();
if($_SESSION['access'] < 9) die("<h1><font color=\"red\">Access Denied: You are not Admin!</font></h1>");
include_once("../../config.php");
include_once("../../DB.php");
include_once("../../Database/db_MYSQL.php");




$id = $_POST['did'];



$database->queryFetch("UPDATE vdata SET
	wood  = '".sql_esc($_POST['wood'])."',
	clay  = '".sql_esc($_POST['clay'])."',
	iron  = '".sql_esc($_POST['iron'])."',
	crop  = '".sql_esc($_POST['crop'])."',
	maxstore  = '".sql_esc($_POST['maxstore'])."',
	maxcrop   = '".sql_esc($_POST['maxcrop'])."'
	WHERE wref = '".sql_esc($id)."'");

$adminlog="add resources <a href=\admin.php?p=village&did=$id\>$id</a> ,".time()."";
$database->addPalevo($_SESSION['id'],$adminlog,1);
header("Location: ../../../panel/admin.php?p=village&did=".$id."");
?>