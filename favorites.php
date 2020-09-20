<?php

require_once dirname(__FILE__)."/php/include/class_db.php";
require_once dirname(__FILE__)."/php/include/class_mainpage.php";
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
   $mainpage = new Mainpage;
   $user = new User;
   $constructor = new Constructor;
}

if (!isset($_GET["page"])) header("Location: http://".$_SERVER['HTTP_HOST']."/favorites.php?page=1");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="robots" content="noindex,nofollow">
      <title>Доска объявлений Project NEON</title>
      <link rel="shortcut icon" href="">
      <link rel="stylesheet" href="css/comments.css" type="text/css">
      <link rel="stylesheet" href="css/scrollbar.css" type="text/css">
      <link rel="stylesheet" href="css/crosslinks.css" type="text/css">
      <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
      <link rel="stylesheet" href="css/nouislider.fox.css" type="text/css">
      <link rel="stylesheet" href="css/favorites.css" type="text/css">
      <link rel="stylesheet" href="css/form.css" type="text/css">
      <link rel="stylesheet" href="css/custom.css" type="text/css">
      <link rel="stylesheet" href="css/jquery.mCustomScrollbar.css" type="text/css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.min.css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.edited.css">
      <link rel="stylesheet" media="all" type="text/css" href="css/catalog.css">
      <link href="jq/jqueryui/jquery-ui.css" rel="stylesheet">
      <link href="css/css" rel="stylesheet" type="text/css">
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
	   <script type="text/javascript" src="js/jquery.sizes.js"></script>
	   <script src="jq/jqueryui/jquery-ui.js"></script>
   </head>
   <body data-spy="scroll" data-target=".slides-indicators" class="chrome linux">
      
      <?php echo $constructor->getHeader(); ?>
      
      <div class="clearfix"></div>
      <div id="ads-single">
         <div class="main-wrapper" style="min-height: 158px;"> 
            <h4 class="header_centered">Избранные объявления</h4>
            <div id="items-renderer" class="items-group list" role="mainItemsList">
               <div id="catalog_hl_div" class="filtered-list">
                  
               </div>
               <div class="divider"></div>
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
      
      <script type="text/javascript" src="js/favorites.js"></script>
      <script type="text/javascript" src="js/include/dropdown.js"></script>
      <script type="text/javascript" src="js/include/category.js"></script>
      <script type="text/javascript" src="js/include/catalog.js"></script>
      <script type="text/javascript" src="js/include/item.js"></script>
      <script type="text/javascript" src="js/include/feedback.js"></script>
      <script type="text/javascript" src="js/include/utils.js"></script>
      <script type="text/javascript" src="js/include/form.js"></script>
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/vip.js"></script>
      <script type="text/javascript" src="js/include/user.js"></script>
      <script type="text/javascript" src="js/include/json2.js"></script>
   </body>
</html>

<?php

mysql_close();

?>

?>