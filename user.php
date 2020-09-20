<?php

require_once dirname(__FILE__)."/php/include/class_db.php";
require_once dirname(__FILE__)."/php/include/class_user.php";
require_once dirname(__FILE__)."/php/include/class_constructor.php";

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
   $user = new User;
   $constructor = new Constructor;
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
      <link rel="stylesheet" href="css/user.css" type="text/css">
      <link rel="stylesheet" href="css/form.css" type="text/css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.min.css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.edited.css">
      <link href="jq/jqueryui/jquery-ui.css" rel="stylesheet">
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
	   <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	   <script type="text/javascript" src="js/include/jquery.sizes.js"></script>
	   <script src="jq/jqueryui/jquery-ui.js"></script>
   </head>
   <body>
      
      <?php echo $constructor->getHeader("white"); ?>
      
      <div class="clearfix"></div>
      <div class="main-wrapper"> 
         <div id="userdata_wrapper">
            <img id="userphoto" class="light_shadow" src="user/default_user.png" />
            <div id="userdata">
               <span id="user_name"></span>
               <br><span id="user_lastseen" class="username_param"></span>
               <br><span id="user_phone" class="username_param"></span>
               <br><span id="user_email" class="username_param"></span>
               <br><a id="report_user" onclick="user.showContactForm()" class="light_shadow btn transparent blue fw">Написать</a>
            </div>
         </div>
         <div id="useritems_header">
            <div id="switch_view" class="dropdown pull-right filtering-item">
               <ul id="view-toggle" class="nav nav-pills">
                  <li id="output_mode_thumbs_switch">
                     <a view="thumbnails" onclick="catalog_common.switchOutputMode(2)" href="javascript:void(0)">
                        <img height="16" width="16" src="http://rio.ua/img/icon-thumnails.png">
                     </a>
                  </li>
                  <li id="output_mode_list_switch" class="active">
                     <a view="list" onclick="catalog_common.switchOutputMode(1)" href="javascript:void(0)">
                        <img height="16" width="16" src="http://rio.ua/img/icon-list.png">
                     </a>
                  </li>
               </ul>
            </div>
            <div id="useritems_cats_list">
            </div>
         </div>
         <div id="useritems_wrapper">
         <div id="items-renderer" class="items-group thumbnails" role="mainItemsList">
            <div id="catalog_list_div" class="filtered-list">
               
            </div>
         </div>
         <div class="clear"></div>
         <center style="clear:both">
         <ul class="pagination">
         
         </ul>
         </center>
         </div>
      </div>
      
      <?php echo $constructor->getFooter();?>
      
      <script type="text/javascript" src="js/user.js"></script>
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/dropdown.js"></script>
      <script type="text/javascript" src="js/include/category.js"></script>
      <script type="text/javascript" src="js/include/catalog.js"></script>
      <script type="text/javascript" src="js/include/utils.js"></script>
      <script type="text/javascript" src="js/include/user.js"></script>
      <script type="text/javascript" src="js/include/item.js"></script>
      <script type="text/javascript" src="js/include/form.js"></script>
      <script type="text/javascript" src="js/include/mail.js"></script>
      <script type="text/javascript" src="js/include/json2.js"></script>
   </body>
</html>

<?php

mysql_close();

?>