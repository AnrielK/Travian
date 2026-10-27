<?php
include("application/Account.php");
if($session->gold >= 50){

  $sql = $database->query("SELECT * FROM farmlist WHERE `owner` = '".sql_esc($session->uid)."' and wref = '".sql_esc($village->wid)."'");
  $query = safe_count($sql);
  foreach($sql as $row0){
      $lid = $row0["id"];
      $lname = $row0["name"];
      $lowner = $row0["owner"];
      $lwref = $row0["wref"];
      $lstart=$row0['laststart'];
      $lvname = $village->vname;
      $FLs=$database->getVilUFl($village->wid,$session->uid); 
      $coor = $village->coor;

      $varray = $database->getProfileVillages(3);
    foreach($varray as $vil) {

      $distance1 = $database->getDistance($coor['x'], $coor['y'], $vil["vx"], -$vil["vy"]);
      $speed = SPEED*15;
      $database->query("INSERT INTO raidlist (lid,towref,x,y,distance,t1) VALUES('" . sql_esc($lid) . "','".sql_esc($vil["wref"])."','".sql_esc($vil["vx"])."','".sql_esc($vil["vy"])."','" . sql_esc($distance1) . "','" . sql_esc($speed) . "')");


    }

  }

  $database->query('UPDATE users SET gold = gold - 50 WHERE id = '.sql_num($session->uid).'');

header("location:build.php?id=39&t=99");
}