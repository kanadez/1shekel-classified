<?php

require_once dirname(__FILE__)."/php/include/class_db.php";
require_once dirname(__FILE__)."/php/include/class_mainpage.php";
require_once dirname(__FILE__)."/php/include/class_item.php";
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
   $item = new Item;
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
      <meta property="og:image" content="http://neon.metateg.pro/catalog/<?php echo $item->getPhoto($_GET["item"]) ?>" />
      <meta property="og:description" content="<?php echo $item->getDescription($_GET["item"]) ?>" />
      <title>Доска объявлений Project NEON | <?php echo $item->getTitle($_GET["item"]) ?></title>
      <link rel="shortcut icon" href="">
      <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
      <link rel="stylesheet" href="css/nouislider.fox.css" type="text/css">
      <link rel="stylesheet" href="css/custom.css" type="text/css">
      <link rel="stylesheet" href="css/preview.css" type="text/css">
      <link rel="stylesheet" href="css/form.css" type="text/css">
      <link rel="stylesheet" href="css/jquery.mCustomScrollbar.css" type="text/css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.min.css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.edited.css">
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
	   <script type="text/javascript" src="js/include/jquery.sizes.js"></script>
   </head>
   <body data-spy="scroll" data-target=".slides-indicators" class="chrome linux">
      
      <?php echo $constructor->getHeader(); ?>
      
      <div class="clearfix"></div>
      
      <input type="hidden" name="item_id" value="965913" />
      <div id="ads-single">
         <div class="main-wrapper" style="min-height: 158px;"> 
            <a id="move-top" style="display: none;"></a>
            <div class="container">
               <article>
                  <div class="row">
                     <br>
                     <div class="col-12" style="text-align:center; font-size:2.3em; margin-bottom: 25px;">
                        <a class="btn transparent grey" href="new.php?item=<?php echo $_GET["item"] ?>&token=<?php echo $_GET["token"] ?>">Назад к редактированию</a>
                        <a class="btn transparent orange" style="margin-left:20px" onclick="preview.add(<?php echo $_GET["item"] ?>)">Разместить объявление</a>
                     </div>
                  </div>
                  <div class="alertsWrapper"></div>
                  <div class="article-wrapper">
                     <div class="article-header-wrapper">
                        <div id="article_header" class="article-header"></div>
                     </div>
                     <div class="article-middle">
                        <div class="article-content">
                           <div class="photos-wrapper" style="cursor: pointer;" onclick="slider.right()">
                              <div class="main-photo">
                                 <a id="main_image_wrapper_div" class="openPhotoModal"><img style="width:100%" id="item_main_img" src="img/camera.jpg"></a>
                                    <!--<a href="javascript:void(0)" onclick="slider.left()"><img src="img/left-arrow_white.png" class="left_arrow" /></a>
                                    <a href="javascript:void(0)" onclick="slider.right()"><img src="img/right-arrow_white.png" class="right_arrow" /></a>-->
                              </div>
                           </div>
                           <table id="specs_table">
                              <tr>
                                 <td id="td0"><span class="key" id="key"></span><br><span id="value"></span></td>
                                 <td id="td1"><span class="key" id="key"></span><br><span id="value"></span></td>
                                 <td id="td2"><span class="key" id="key"></span><br><span id="value"></span></td>
                                 <td id="td3"><span class="key" id="key"></span><br><span id="value"></span></td>
                              </tr>
                           </table>
                           <div id="article_description" class="article-description">
                              <p></p>
                              <center></center>
                              <p></p>
                           </div>
                        </div>
                        <div class="sidebar article-sidebar">
                           <div class="sidebar-content article-sidebar-content">
                              <div id="article_price" class="price"></div>
                              <div class="">
                                 <div class="seller-wrapper pad">
                                    <div id="item_cond_div" style="width:100%"><span style="color:#777">Состояние: </span></div>
                                    <div id="author_name_div" style="width:100%"><span style="color:#777">Автор: </span><a id="author_name_a" href="javascript:void(0)"></a></div>
                                    <div id="item_address_div" style="width:100%"><span style="color:#777">Адрес: </span><a id="item_address_a" href="javascript:void(0)"></a></div>
                                    <div id="author_phone_div" style="width:100%"><span style="color:#777">Телефон: </span><a href="javascript:void(0)" onclick="preview.showPhone(this)">показать</a></div>
                                    <a onclick="" class="btn transparent orange fw">Написать автору</a>
                                    <a onclick="" class="btn transparent blue fw">Поделиться</a>
                                    <a class="btn transparent grey fw">Пожаловаться</a> </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <br>
                     <div class="col-12" style="text-align:center; font-size:2.3em">
                        <a class="btn transparent grey" href="new.php?item=<?php echo $_GET["item"] ?>&token=<?php echo $_GET["token"] ?>">Назад к редактированию</a>
                        <a class="btn transparent orange" style="margin-left:20px" onclick="preview.add(<?php echo $_GET["item"] ?>)">Разместить объявление</a>
                     </div>
                  </div>
               </article>
            </div>
         </div>
      </div>
      
      <?php echo $constructor->getFooter();?>
      
      <script type="text/javascript" src="js/include/utils.js"></script>
      <script type="text/javascript" src="js/include/dropdown.js"></script>
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/item.js"></script>
      <script type="text/javascript" src="js/include/preview.js"></script>
      <script type="text/javascript" src="js/preview.js"></script>
      <script type="text/javascript" src="js/include/user.js"></script>
      <script type="text/javascript" src="js/include/json2.js"></script>
   </body>
</html>

<?php

mysql_close();

?>