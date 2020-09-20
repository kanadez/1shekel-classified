<?php

require_once dirname(__FILE__)."/php/include/class_db.php";

$db = new DB;

if (!$db->mysqlConnect()){
   mysql_query("SET NAMES 'utf8'");
   mysql_query("SET collation_connection = 'UTF-8_general_ci'");
   mysql_query("SET collation_server = 'UTF-8_general_ci'");
   mysql_query("SET character_set_client = 'UTF-8'");
   mysql_query("SET character_set_connection = 'UTF-8'");
   mysql_query("SET character_set_results = 'UTF-8'");
   mysql_query("SET character_set_server = 'UTF-8'");
}

$code_filtered = intval($_GET["code"]);
$user_filtered = intval($_GET["user"]);
$email_filtered = strval($_GET["email"]);
$sql = "SELECT `email_confirm` FROM `user` WHERE `num` = $user_filtered;";
$result = $db->db_fetchone_array($sql, __LINE__, __FILE__);

if ($result["email_confirm"] == $code_filtered){
   $sql1 = "UPDATE `user` SET `email` = '$email_filtered' WHERE `num` = $user_filtered;";
   $db->db_query($sql1, __LINE__, __FILE__);
   
   Header("Location: profile.php?action=change_email&status=success");
}
else{
    Header("Location: profile.php?action=change_email&status=fail");
    exit();
}

mysql_close();

?>