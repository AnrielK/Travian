<?php 
Class BBcode{
    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if (php7_is_zero($ini)) return '';
        $ini += strlen($start);
        $len = safe_strpos($string, $end, $ini) - $ini; // PHP 8: offset past the end throws ValueError
        return substr($string, $ini, $len);
    }    
}

$bbcode = new BBcode;