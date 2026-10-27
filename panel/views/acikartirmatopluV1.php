<?php
if(!defined("HLXGUVENLIK"))
	exit('OLDU');



if(isset($_POST['acikArtirmaToplu'])){
	
	$min = 10799;
    $max = 20000;
	$zaman = rand($min, $max)+time()+3600;
	
    $time = time()+3600;
    for($i=0; $i<$_POST['bandagebloop']; $i++){ 
        $numberr= safe_num($_POST['bandagebnum']);
        $silver=$numberr*5;
        
        
        $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 8, 0, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman)."', 0)");
            
            
    }
        for($i=0; $i<$_POST['bandagesloop']; $i++){ 
        $numberr= safe_num($_POST['bandagesnum']);
        $silver=$numberr*5;
        $zaman2 = rand($min, $max)+time()+3600;
        
        $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 7, 0, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman2)."', 0)");
            
            
    }	
            for($i=0; $i<$_POST['pkmloop']; $i++){ 
            
            $numberr=$_POST['pkmnum'];
            $silver=$numberr;
			$zaman3 = rand($min, $max)+time()+3600;
            $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 10, 0, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman3)."', 0)");
            
            $numberr=$_POST['pkmnum'];
            $silver=$numberr;
			$zaman4 = rand($min, $max)+time()+3600;
            $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 14, 0, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman4)."', 0)");
            
            $numberr=$_POST['pkmnum'];
            $silver=$numberr;
			$zaman5 = rand($min, $max)+time()+3600;
            $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 11, 0, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman5)."', 0)");
            
    }
    
        for($i=0; $i<$_POST['kbsloop']; $i++){ 
            
            $numberr=1;
            $silver=100;
			$zaman6 = rand($min, $max)+time()+3600;
            $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 12, 0, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman6)."', 0)");
            
            
            $numberr=1;
            $silver=100;
			$zaman7 = rand($min, $max)+time()+3600;
            $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 13, 0, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman7)."', 0)");
            
            
            $numberr=1;
            $silver=100;
			$zaman8 = rand($min, $max)+time()+3600;
            $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 15, 0, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman8)."', 0)");
            
            
    }


    for($i=0; $i<$_POST['helmetloop']; $i++){
    $numberr=1;
			$silver=100;
			$zaman9 = rand($min, $max)+time()+3600;
			$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 1, 1, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman9)."', 0)");
			
			
			$numberr=1;
			$silver=100;
			$zaman10 = rand($min, $max)+time()+3600;
			$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 1, 4, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman10)."', 0)");
			
			
			$numberr=1;
			$silver=100;
			$zaman11 = rand($min, $max)+time()+3600;
			$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 1, 7, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman11)."', 0)");
			
			
			$numberr=1;
			$silver=100;
			$zaman12 = rand($min, $max)+time()+3600;
			$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 1, 10, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman12)."', 0)");
			
			
			$numberr=1;
			$silver=100;
			$zaman13 = rand($min, $max)+time()+3600;
			$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 1, 13, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman13)."', 0)");
			
			
			$numberr=1;
			$silver=100;
			$zaman14 = rand($min, $max)+time()+3600;
			$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 1, 10, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman14)."', 0)");
			
			
			$numberr=1;
			$silver=100;
			$zaman15 = rand($min, $max)+time()+3600;
			$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 1, 13, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman15)."', 0)");
			
    }

    for($i=0; $i<$_POST['bodyloop']; $i++){ 
    $numberr=1;
			$silver=100;
			$zaman16 = rand($min, $max)+time()+3600;
			$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 2, 82, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman16)."', 0)");
			
			
			$numberr=1;
			$silver=100;
			$zaman17 = rand($min, $max)+time()+3600;
			$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 2, 85, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman17)."', 0)");
			
			
			$numberr=1;
			$silver=100;
			$zaman18 = rand($min, $max)+time()+3600;
			$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 2, 88, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman18)."', 0)");
			
			
			$numberr=1;
			$silver=100;
			$zaman19 = rand($min, $max)+time()+3600;
			$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 2, 91, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman19)."', 0)");
			

}

for($i=0; $i<$_POST['leftloop']; $i++){
    $numberr=1;
    $silver=100;
	$zaman20 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 3, 61, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman20)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman21 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 3, 64, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman21)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman22 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 3, 67, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman22)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman23 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 3, 73, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman23)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman24 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 3, 76, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman24)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman25 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 3, 79, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman25)."', 0)");
    
    
}

for($i=0; $i<$_POST['rightloop']; $i++){
    $numberr=1;
    $silver=100;
	$zaman26 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 16, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman26)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman27 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 19, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman27)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman28 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 22, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman28)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman29 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 25, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman29)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman30 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 28, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman30)."', 0)");
    
    $numberr=1;
    $silver=100;
	$zaman31 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 31, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman31)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman32 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 34, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman32)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman33 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 37, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman33)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman34 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 40, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman34)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman35 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 43, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman35)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman36 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 46, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman36)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman37 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 49, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman37)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman38 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 52, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman38)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman39 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 55, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman39)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman40 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 58, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman40)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman41= rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 115, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman41)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman42 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 118, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman42)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman43 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 121, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman43)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman44 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 124, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman44)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman45 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 127, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman45)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman46 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 130, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman46)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman47 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 133, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman47)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman48 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 136, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman48)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman49 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 139, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman49)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman50 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 4, 142, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman50)."', 0)");
    
}

for($i=0; $i<$_POST['shoesloop']; $i++){
    $numberr=1;
    $silver=100;
	$zaman51 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 5, 94, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman51)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman52 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 5, 97, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman52)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman53 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 5, 100, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman53)."', 0)");
    
    
    $numberr=1;
    $silver=100;
	$zaman54 = rand($min, $max)+time()+3600;
    $database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 6, 103, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman54)."', 0)");
    

}

for($i=0; $i<$_POST['cageloop']; $i++){ 
	$numberr= safe_num($_POST['cagenum']);
	$silver=$numberr*5;
	$zaman55 = rand($min, $max)+time()+3600;
	$database->query("INSERT INTO auction (`owner`, `itemid`, `btype`, `type`, `num`, `uid`, `bids`, `silver`, `newsilver`, `time`, `finish`) VALUES (6, 1, 9, 0, '".sql_esc($numberr)."', 0, 0, '".sql_esc($silver)."', '".sql_esc($silver)."', '".sql_esc($zaman55)."', 0)");
		
}

            echo 'Açık Artırma İtemler Toplu Olarak Eklendi.';
        
    

}
?>




<form action="" method="POST">
	<input class="form-control"  type="hidden" name="admid" id="admid" value="<?php echo $_SESSION['id']; ?>">
	<table id="member" style="width:500px;">
		<thead>
			<tr>
				<th colspan="2">Eşya Adı</th>
				<th >Döngü Sayısı</th>
				<th >Adet Miktarı</th>
			</tr>

		</thead>
		<tbody>
	
			<tr>
				<td colspan="2">
					<center>
						<b>KAFES : </b>
					</center>
				</td>
				<td>
					<center>
						<input class="form-control"  type="number" name="cageloop" id="cageloop" min="0" max="15" value="10">
						
					</center>
				</td>
				<td >
					<center>
						<input class="form-control"  type="number" name="cagenum" id="cagenum" min="1" max="10000" value="100">
						
					</center>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<center>
						<b>SARGI Büyük : </b>
					</center>
				</td>
				<td>
					<center>
						<input class="form-control"  type="number" name="bandagebloop" id="bandagebloop" min="0" max="15" value="5">
						
					</center>
				</td>
				<td >
					<center>
						<input class="form-control"  type="number" name="bandagebnum" id="bandagebnum" min="1" max="10000" value="200">
						
					</center>
				</td>
			</tr>
				<tr>
				<td colspan="2">
					<center>
						<b>SARGI Küçük : </b>
					</center>
				</td>
				<td>
					<center>
						<input class="form-control"  type="number" name="bandagesloop" id="bandagesloop" min="0" max="15" value="5">
						
					</center>
				</td>
				<td >
					<center>
						<input class="form-control"  type="number" name="bandagesnum" id="bandagesnum" min="1" max="10000" value="150">
						
					</center>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<center>
						<b>Parşomen-Kanun-Merhem : </b>
					</center>
				</td>
				<td>
					<center>
						<input class="form-control"  type="number" name="pkmloop" id="pkmloop" min="0" max="15" value="3">
						
					</center>
				</td>
				<td >
					<center>
						<input class="form-control"  type="number" name="pkmnum" id="pkmnum" min="1" max="10000" value="100">
						
					</center>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<center>
						<b>Kova-Bilgelik-SanatEseri : </b>
					</center>
				</td>
				<td >
					<center>
						<input class="form-control"  type="number" name="kbsloop" id="kbsloop" min="0" max="15" value="4">
						
					</center>
				</td>
				
			</tr>
			<tr>
				<td colspan="2">
					<center>
						<b>Miğferler :</b>
					</center>
				</td>
				<td >
					<center>
						<input class="form-control"  type="number" name="helmetloop" id="helmetloop" min="0" max="4" value="1">
						
					</center>
				</td>
				
			</tr>
			<tr>
				<td colspan="2">
					<center>
						<b>Zırhlar :</b>
					</center>
				</td>
				<td >
					<center>
						<input class="form-control"  type="number" name="bodyloop" id="bodyloop" min="0" max="4" value="1">
						
					</center>
				</td>
				
			</tr>
			<tr>
				<td colspan="2">
					<center>
						<b>Kahraman Sol El :</b>
					</center>
				</td>
				<td >
					<center>
						<input class="form-control"  type="number" name="leftloop" id="leftloop" min="0" max="4" value="1">
						
					</center>
				</td>
				
			</tr>
			<tr>
				<td colspan="2">
					<center>
						<b>Kahraman Sağ El :</b>
					</center>
				</td>
				<td >
					<center >
						<input class="form-control"  type="number" name="rightloop" id="rightloop" min="0" max="4" value="1">
						
					</center>
				</td>
				
			</tr>
			<tr>
				<td colspan="2">
					<center>
						<b>Çizmeler+At :</b>
					</center>
				</td>
				<td >
					<center >
						<input class="form-control"  type="number" name="shoesloop" id="shoesloop" min="0" max="4" value="1">
						
					</center>
				</td>
				
			</tr>
			
			<tr>
				<td colspan="3">
                <input type="submit" class="btn btn-primary" name="acikArtirmaToplu" value="TOPLU Açık Artırma Ekle">
				</td>
			</tr>
		</tbody>
	</table>
</form>
