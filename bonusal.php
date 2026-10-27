<?php
include("baglanti.php");
include("application/Account.php");  
if($session->bonusver == '0'){
  $odun = 2000*SPEED;
	$tugla = 2000*SPEED;
	$demir = 2000*SPEED;
	$tahil = 2000*SPEED;
	mysqli_query($connection,"UPDATE vdata set clay = clay + ".sql_num(($session->clay+$tugla))." WHERE `wref` = ".sql_num($village->wid)); // bug fix: the bonus went to EVERY village on the server
	mysqli_query($connection,"UPDATE vdata set iron = iron + ".sql_num(($session->iron+$demir))." WHERE `wref` = ".sql_num($village->wid)); // bug fix: the bonus went to EVERY village on the server
	mysqli_query($connection,"UPDATE vdata set crop = crop + ".sql_num(($session->crop+$tahil))." WHERE `wref` = ".sql_num($village->wid)); // bug fix: the bonus went to EVERY village on the server
	mysqli_query($connection,"UPDATE vdata set wood = wood + ".sql_num(($session->wood+$odun))." WHERE `wref` = ".sql_num($village->wid)); // bug fix: the bonus went to EVERY village on the server
//for($i=1;$i<=50; $i++){
$database->query("INSERT `mdata` SET `target` = '".sql_esc($session->uid)."', `owner` = '6', topic = 'Raw Materials Bonus!', message = 'Köyünüze ".sql_esc(number_format($odun)).", ".sql_esc(number_format($tugla)).", ".sql_esc(number_format($demir)).", ".sql_esc(number_format($tahil))." Bonus hammadde aldınız.

İyi Oyunlar Dileriz..',`time` = ".time()." ");
$database->query("UPDATE `users` SET `bonusver` = 1 WHERE `id` = '".sql_esc($session->uid)."'");
$database->query("UPDATE `users` SET `gold` =  `gold` + 1 WHERE `id` = '".sql_esc($session->uid)."'");
//}
}
header("location:dorf1.php");
?>
