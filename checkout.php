<?php

require_once dirname(__FILE__)."/php/include/class_db.php";
require_once dirname(__FILE__)."/php/include/class_profile.php";

session_start(); 

$db = new DB;

if (!$db->mysqlConnect()){
   mysql_query("SET NAMES 'utf8'");
   mysql_query("SET collation_connection = 'UTF-8_general_ci'");
   mysql_query("SET collation_server = 'UTF-8_general_ci'");
   mysql_query("SET character_set_client = 'UTF-8'");
   mysql_query("SET character_set_connection = 'UTF-8'");
   mysql_query("SET character_set_results = 'UTF-8'");
   mysql_query("SET character_set_server = 'UTF-8'");
   
   $profile = new Profile;
}

$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();

foreach ($raw_post_array as $keyval) {
    $keyval = explode ('=', $keyval);
    
    if (count($keyval) == 2){
        $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
}

$check_result = $profile->checkPayment($myPost["ik_pm_no"], $myPost["ik_inv_st"]);

if (is_array($check_result)){ // ошибка 
    header("Location: profile.php?action=payment&error=".$check_result["error"]["code"]);
}
else{
    $sql = sprintf("UPDATE `user` SET `coins` = `coins` + %d WHERE `num` = %d;", 
        mysql_real_escape_string($check_result),
        mysql_real_escape_string($_SESSION["user_num"]));
    $db->db_query($sql, __LINE__, __FILE__);

    $sql = sprintf("INSERT INTO `credits` (`user`, `total`, `type`, `timestamp`, `description`) VALUES (%d, %d, 1, %d, 'Пополнение баланса через платежную систему');", // добавляем транзкцию списания автору
        mysql_real_escape_string($_SESSION["user_num"]),
        mysql_real_escape_string($check_result),
        mysql_real_escape_string(time()));
    $db->db_query($sql, __LINE__, __FILE__);
    
    header("Location: profile.php?action=payment&success=true");
}