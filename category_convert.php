<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   </head>
<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

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

// Получает содержимое файла в виде массива. В данном примере мы используем
// обращение по протоколу HTTP для получения HTML-кода с удаленного сервера.
$lines = file('advsubcat.txt');

$c = 104;
foreach ($lines as $line_num => $line) {
   $sql = sprintf("INSERT INTO `city` (`city_name`, `city_code`, `region_code`) VALUES ('%s', %d, 6);", // вставляем пост текущему юзеру в таблицу
      mysql_real_escape_string($line),
      mysql_real_escape_string($c));
   echo $db->db_query($sql, __LINE__, __FILE__);
   $c++;
}

mysql_close();

?>