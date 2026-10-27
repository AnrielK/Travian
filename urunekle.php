<?php
include("baglanti.php");
include("application/Account.php"); 
// security: this admin tool creates auction items and had no access check at all
if (empty($session->logged_in) || $session->access < ADMIN) { header("Location: dorf1.php"); exit(); }
$min = 10799;
$max = 20000;

//////////////////////
$yilmazkafesmin = 50;
$yilmazkafesminmax = 160;
$yilmazsay1 = rand($yilmazkafesmin, $yilmazkafesminmax);

$yilmazbandanamin = 50;
$yilmazbandanamax = 125;
$yilmazsay2 = rand($yilmazbandanamin, $yilmazbandanamax);

$yilmazmerhemmin = 50;
$yilmazmerhemmax = 125;
$yilmazsay3 = rand($yilmazmerhemmin, $yilmazmerhemmax);

$yilmazfermanmin = 50;
$yilmazfermanmax = 125;
$yilmazsay4 = rand($yilmazfermanmin, $yilmazfermanmax);
//YEDEK KODUM $zaman = 14000+time();
///////////////////////


//sağlık kaskı 1x
 $zaman = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '1', '5', '1', '0', '0', '100', '0', '".sql_esc($zaman)."', '0')");
//Yenilenme Zırhı 1x
 $zaman2 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '2', '82', '1', '0', '0', '100', '0', '".sql_esc($zaman2)."', '0')");
//sol el Kalkan 1x
 $zaman3 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '3', '77', '1', '0', '0', '100', '0', '".sql_esc($zaman3)."', '0')");
//sağ el Mızrakçı Mızrağı 1x
 $zaman4 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '4', '51', '1', '0', '0', '100', '0', '".sql_esc($zaman4)."', '0')");
//Paralı Asker Çizmesi 1x
 $zaman5 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '5', '100', '1', '0', '0', '100', '0', '".sql_esc($zaman5)."', '0')");
  
//1 x Savaş Atı
$zaman6 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '6', '105', '1', '0', '0', '100', '0', '".sql_esc($zaman6)."', '0')");

//100 x ‬ Küçük Sargı
 $zaman7 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '7', '112', '100', '0', '0', '100', '0', '".sql_esc($zaman7)."', '0')");
//	99 x Bandaj
 $zaman8 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '8', '113', '".sql_esc($yilmazsay2)."', '0', '0', '100', '0', '".sql_esc($zaman8)."', '0')");
//Random x Kafes
 $zaman9 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '9', '114', '".sql_esc($yilmazsay1)."', '0', '0', '100', '0', '".sql_esc($zaman9)."', '0')");
//100 x Ferman Filtresi
 $zaman10 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '10', '107', '".sql_esc($yilmazsay4)."', '0', '0', '100', '0', '".sql_esc($zaman10)."', '0')");
//150 x Şifa Merhemi
 $zaman11 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '11', '106', '".sql_esc($yilmazsay3)."', '0', '0', '100', '0', '".sql_esc($zaman11)."', '0')");
//‭‭1‬×‬ Su Kovası	
 $zaman12 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '12', '108', '1', '0', '0', '100', '0', '".sql_esc($zaman12)."', '0')");
//	‭‭1‬×‬ Öngörü Kitabı
 $zaman13 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '13', '110', '1', '0', '0', '100', '0', '".sql_esc($zaman13)."', '0')");
//1 x Kanun YAZITLARI fİLTRESİ
 $zaman14 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '14', '109', '1', '0', '0', '100', '0', '".sql_esc($zaman14)."', '0')");
//1 x sANAT eSERİFİLTRESİ
 $zaman15 = rand($min, $max)+time();
  $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES ('0', '4', '15', '111', '1', '0', '0', '100', '0', '".sql_esc($zaman15)."', '0')");

  header("location:dorf1.php");
?>