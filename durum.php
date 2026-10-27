<?php
require_once 'application/config.php';
try {
    $baglanDB = travian_pdo();   // shared connection (MySQL or SQLite)
    if (!$baglanDB) throw new PDOException('no database');
    header('Content-Type: application/json; charset=utf-8',true,200);
    $data['users']=safe_count($baglanDB->query('SELECT * FROM `users` WHERE `id` > 4')->fetchAll(PDO::FETCH_ASSOC));
    $data['online']=safe_count($baglanDB->query("SELECT * FROM `users` WHERE " . time() . "-`timestamp` < (120*120) AND tribe!=5 AND tribe!=0")->fetchAll(PDO::FETCH_ASSOC));
    $t = strtotime('26.02.2022 19:00:00');
    if ($t > time())
        $data['online'] = 0;
    $timeserver = round((time() - $t) / 86400);
    $data['winmoment']=$baglanDB->query('SELECT * FROM `config` WHERE 1')->fetch(PDO::FETCH_ASSOC);
    if(!isset($data['winmoment']))
        $data['winmoment'] = "";
    else
        $data['winmoment']=$data['winmoment']['winmoment'];
    exit(json_encode($data));
} catch(PDOException $e) {
    echo "Veritabanı Bağlantısı Başarısız : " . $e->getMessage();
    die();
}