<?php

require_once dirname(__FILE__)."/php/include/class_db.php";
require_once dirname(__FILE__)."/php/include/class_user.php";
require_once dirname(__FILE__)."/php/include/class_constructor.php";

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
   $constructor = new Constructor;
}

session_start(); 

if (isset($_GET['token'])){
    $sql = sprintf("SELECT `num` FROM `user` WHERE `item_token` = '%s';",
         mysql_real_escape_string($_GET['token']));
    $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
    
    if (count($result) > 0){
        
        $_SESSION["user_num"] = $result["num"];
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="robots" content="noindex,nofollow">
      <title>Доска объявлений Project NEON</title>
      <link rel="shortcut icon" href="">
      <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
      <link rel="stylesheet" href="css/nouislider.fox.css" type="text/css">
      <link rel="stylesheet" href="css/custom.css" type="text/css">
      <link rel="stylesheet" href="css/new.css" type="text/css">
      <link rel="stylesheet" href="css/form.css" type="text/css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.min.css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.edited.css">
      <link rel="stylesheet" href="/jq/jqueryui/jquery-ui.css">
      <style type="text/css">
         @font-face {
         	font-family: "Conv_Hattori_Hanzo";
         	src: url("fonts/Hattori_Hanzo.eot");
         	src: local("☺"), url("fonts/Hattori_Hanzo.woff") format("woff"), url("fonts/Hattori_Hanzo.ttf") format("truetype"), url("fonts/Hattori_Hanzo.svg") format("svg");
         	font-weight: normal;
         	font-style: normal;
         }
			
			body{
            font-family:"Conv_Hattori_Hanzo" !important;
            font-size: 62.5% !important;
            margin: 0;
            outline: none;
            overflow: auto;
            padding: 0;	
			}
		</style>
	   
      <script src="//code.jquery.com/jquery-1.10.2.js"></script>
      <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
      <script src="//vk.com/js/api/openapi.js" type="text/javascript"></script>
      <script type="text/javascript" src="/js/upload.js"></script>
      <script type="text/javascript" src="js/include/jquery.sizes.js"></script>
   </head>
   <body>
      
      <?php echo $constructor->getHeader(); ?>
      
      <div class="clearfix"></div>
      <div class="main-wrapper"> 
         <div id="content-area"></div>
      </div>
      
      <?php echo $constructor->getFooter();?>
      
      <script type="text/javascript" src="js/edit.js"></script>
      <!--Мой профиль-->
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/feedback.js"></script>
      <script type="text/javascript" src="js/include/category.js"></script>
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/dropdown.js"></script>
      <script type="text/javascript" src="js/include/auth.js"></script>
      <!--Мои объявления-->
      <script type="text/javascript" src="js/include/form.js"></script>
      <script type="text/javascript" src="js/include/currency.js"></script>
      <script type="text/javascript" src="js/include/utils.js"></script>
      <script type="text/javascript" src="js/include/json2.js"></script>
   </body>
</html>

<?php

mysql_close();

?>

?>