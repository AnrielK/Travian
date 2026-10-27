<?php
//////////////     made by ErenZazu   /////////////////////////
include("./baglanti.php");
// SQL injection: id selects a case and the column envanter0..envanter8
$_GET['id'] = (string)max(0, min(8, (int)($_GET['id'] ?? 0)));
$MyGold = mysqli_query($connection,"SELECT * FROM users WHERE `id`='".sql_esc($session->uid)."'");
                    $my = safe_mysqli_fetch_array($MyGold);					
  
  $MyGold2 = mysqli_query($connection,"SELECT * FROM kasalar WHERE `envanter`='".sql_esc($_GET['id'])."'");
                    $mykasa = safe_mysqli_fetch_array($MyGold2);
  

	  $envanterkacc = $my['envanter'.$mykasa['envanter'].''];



if($envanterkacc >= 1) {
	$envanterkacc1 = 'envanter'.$_GET['id'].'';
	$odun = $mykasa['odun']*SPEED;
	$tugla = $mykasa['tugla']*SPEED;
	$demir = $mykasa['demir']*SPEED;
	$tahil = $mykasa['tahil']*SPEED;
	$hayvan = $mykasa['hayvan'];
	$randomgold = rand(1,5);
	$envantersayulan = $my['envantersay']+1;

	mysqli_query($connection,"UPDATE vdata set clay = clay + ".sql_num(($session->clay+$tugla))." where `wref`='".sql_esc($village->wid)."'");
	mysqli_query($connection,"UPDATE vdata set iron = iron + ".sql_num(($session->iron+$demir))." where `wref`='".sql_esc($village->wid)."'");
	mysqli_query($connection,"UPDATE vdata set crop = crop + ".sql_num(($session->crop+$tahil))." where `wref`='".sql_esc($village->wid)."'");
	mysqli_query($connection,"UPDATE vdata set wood = wood + ".sql_num(($session->wood+$odun))." where `wref`='".sql_esc($village->wid)."'");
	mysqli_query($connection,"UPDATE users set gold = gold + ".sql_num($randomgold)." where `id`='".sql_esc($session->uid)."'");
	mysqli_query($connection,"UPDATE users set envantersay = envantersay + 1 where `id`='".sql_esc($session->uid)."'");

	if($mykasa['hayvan'] > 0) {
	mysqli_query($connection,"INSERT INTO enforcement  VALUES ('', '" . sql_esc($hayvan) . "', '" . sql_esc($hayvan) . "', '" . sql_esc($hayvan) . "', '" . sql_esc($hayvan) . "', '" . sql_esc($hayvan) . "', '" . sql_esc($hayvan) . "', '" . sql_esc($hayvan) . "', '" . sql_esc($hayvan) . "', '" . sql_esc($hayvan) . "', '" . sql_esc($hayvan) . "', '', '".sql_esc($session->uid)."', '".sql_esc($village->wid)."')");
	}
	
	date_default_timezone_set('Etc/GMT-3');
	$tarih = date("Y-m-d h:i:s");
	//$database->query("INSERT `rutbe_duyuru` set rutbe_duyuru='[".$session->username."] ".$envantersayulan.". Opened Case.', tarih='".$tarih."'");
	
	if($mykasa['goldclub'] > 0) {
	mysqli_query($connection,"UPDATE users set goldclub = 1 where `id`='".sql_esc($session->uid)."'");	
	}
	
	//mysqli_query($connection,"UPDATE users set kasaetkinlik = kasaetkinlik + 1 where `id`='".$session->uid."'");
	
	mysqli_query($connection,"UPDATE users set ".$envanterkacc1." = ".$envanterkacc1." - 1 where `id`='".sql_esc($session->uid)."'");

	echo '<center><b><img src="./gpack/img/oyun/case.gif" style="height:300px;"><br>You Opened the Resource Pack. <br>'.$odun.' Wood, '.$tugla.' Clay, '.$demir.' Iron, '.$tahil.' Crop ';
	if($mykasa['altinver'] > 0){
	echo'and <b>'.$randomgold.' Gold</b> ';
	}
    echo 'Wins.</b></center><br><br>';
	
	header("Refresh:3; url=envanter.php");
}
?>