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

//##########################################################################

$uploaddir = "catalog/";
$temp = getTemporary();
$image_name = $temp[0];
$uploadfile0 = $uploaddir . $image_name;
$temp_item_num = 0;
$photo_array;

echo moveUploadedFiles();

function moveUploadedFiles(){
   global $uploadfile0;
   global $image_name;
   global $temp;
   global $photo_array;
   $result = true;
   $result *= move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile0);
   
   if ($_GET["item_num"] == -1)
      return json_encode(array($image_name, "item_num" => $temp[1]));
   else return json_encode($photo_array);
}

mysql_close();

//#########################################################################

function getTemporary(){
   global $db;
   global $photo_array;
   $img_name = uniqid().".jpg";
   
   
   if ($_GET["item_num"] == -1){
      $img_array = array();
      array_push($img_array, $img_name);
      $sql = sprintf("INSERT INTO `catalog` (`temporary`, `photo`, `token`) VALUES (1, '%s', '%s');",
         mysql_real_escape_string(json_encode($img_array)),
         mysql_real_escape_string($_GET["token"]));
   }
   else{
      $sql = sprintf("SELECT `photo` FROM `catalog` WHERE `num` = %d;", 
         mysql_real_escape_string($_GET["item_num"]));
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      if ($result["photo"] != "null" && $result["photo"] != "")
         $photo_array = json_decode(stripcslashes($result["photo"]), true);
      else $photo_array = array();
      array_push($photo_array, $img_name);
      
      $sql = sprintf("UPDATE `catalog` SET `photo` = '%s' WHERE `num` = %d;",
         mysql_real_escape_string(json_encode($photo_array)),
         mysql_real_escape_string($_GET["item_num"]));
   }
      
   $res1 = $db->db_query($sql, __LINE__, __FILE__);
   
   return array($img_name, mysql_insert_id());
}

?>