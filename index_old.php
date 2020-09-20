<?php

require_once dirname(__FILE__)."/php/login.php";
require_once dirname(__FILE__)."/php/index.php";
require_once dirname(__FILE__)."/php/post.php";
require_once dirname(__FILE__)."/php/kernel/DB.php";
require_once dirname(__FILE__)."/php/kernel/User.php";

$db = new DB;

if (!$db->mysqlConnect()){
   mysql_query("SET NAMES 'utf8'");
   mysql_query("SET collation_connection = 'UTF-8_general_ci'");
   mysql_query("SET collation_server = 'UTF-8_general_ci'");
   mysql_query("SET character_set_client = 'UTF-8'");
   mysql_query("SET character_set_connection = 'UTF-8'");
   mysql_query("SET character_set_results = 'UTF-8'");
   mysql_query("SET character_set_server = 'UTF-8'");
   
   $user = new User;
}

switch ($_GET["parameter"]){
   case "login" :
      login(parseURL(1));
   break;
   
   case "" :
      index();
   break;

   default : 
      
      switch (parseURLparameters(0)){
         case "post" :
            post(parseURLparameters(1), parseURLparameters(2));
         default : 
            exit();
      }
}

function parseURLparameters($parameter_num){
   $b = explode("_", $_GET["parameter"]);
   return $b[$parameter_num];
}

function parseURL($parameter_num){
   $a = parse_url($_SERVER['REQUEST_URI']);
   $b = explode("=", $a["query"]);
   return $b[$parameter_num];
}


mysql_close();

?>