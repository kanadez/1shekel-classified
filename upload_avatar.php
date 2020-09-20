<?php

require_once dirname(__FILE__)."/php/include/class_db.php";

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
}

//##########################################################################

$uploaddir = "user/";
$img_name = uniqid().".jpg";
$uploadfile0 = $uploaddir . $img_name;

moveUploadedFiles();
updateDBAva('http://'.$_SERVER['HTTP_HOST']."/".$uploaddir.$img_name);
echo 'http://'.$_SERVER['HTTP_HOST']."/".$uploaddir.$img_name;

function moveUploadedFiles(){
   global $uploadfile0;
   global $img_name;
   $result = true;
   $result *= move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile0);
   
   return $result;
}

mysql_close();

//#########################################################################

function updateDBAva($avatar){
   global $db;

   $sql = sprintf("UPDATE `user` SET `photo_100` = '%s' WHERE `num` = %d;",
      mysql_real_escape_string($avatar),
      mysql_real_escape_string($_SESSION["user_num"]));
      
   return $db->db_query($sql, __LINE__, __FILE__);
}

?>