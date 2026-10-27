<?php
Class Farms{
    function addList($post){
        global $database, $session;

        foreach($session->vvillages as $vil){ // check if did input is his village
            if($post['did'] == $vil['wref']){
                $listName = $database->RemoveXSS($post['listName']);
                $database->query('INSERT INTO farmlist VALUES(NULL, '.sql_num($post['did']).', '.sql_num($session->uid).', "'.sql_esc($listName).'", 0)');
                
                return TRUE;
            }
        }
    }
}

$farm = new Farms;