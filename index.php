<?php

require_once dirname(__FILE__)."/php/include/class_db.php";
require_once dirname(__FILE__)."/php/include/class_mainpage.php";
require_once dirname(__FILE__)."/php/include/class_user.php";
require_once dirname(__FILE__)."/php/include/class_constructor.php";
require_once dirname(__FILE__)."/php/include/class_currency.php";

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
   $currency = new Currency;
}

?>

<!DOCTYPE html>
<html class="csstransforms no-csstransforms3d csstransitions js rgba borderimage borderradius boxshadow textshadow opacity cssgradients csstransitions fontface generatedcontent">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="robots" content="noindex,nofollow">
      <meta name="interkassa-verification" content="8570c5d4630d2c1aa516a2e49d7681c4" />
      <title>Доска объявлений Project NEON</title>
      <link rel="shortcut icon" href="">
      <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
      <link rel="stylesheet" href="css/nouislider.fox.css" type="text/css">
      <link rel="stylesheet" href="css/custom.css" type="text/css">
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
      <div class="navbar navbar-inverse navbar-fixed-top bs-docs-nav" style="">
         <div id="header_left_block" style="padding:13px;float:left;">
            <a href="index.php" style="color: #444; font-size:2.7em;">Главная</a>
         </div>
         <!--<a class="navbar-brand" href="index.php" alt="NEON" title="NEON"><img style="height:50px;" src="img/logo.png"></a>-->
         <div class="navbar-block pull-right" id="add_item">
            <a href="new.php" id="new_item_a" class="loginLink">Подать объявление</a>
         </div>
          <div class="navbar-block pull-right" id="user-area-na">
              <?php if (isset($_SESSION["user_num"])) echo '<a id="login_a" href="profile.php">'.$user->getNameByNum($_SESSION["user_num"]).'</a>'; else echo '<a id="login_a" href="login.php">Вход</a>'; ?>
          </div>
          <div class="navbar-block pull-right" id="favorites" style="">
              <a href="favorites.php">Избранное</a>
          </div>
      </div>
      <div id="home" style="width: auto;">
         <div class="search-header" style="background: #fff; margin-top: 0; margin-bottom:19px;">
             <div class="container">
                 <div class="search-box">
                     <div class="search-input-wrapper">
                             <div class="table">
                                <div class="dropdown visible-lg visible-md pull-left" id="lang-swicher" style="float:left; border:1px solid #cecece">
                                      <a id="region_dropdown_menu_a" data-toggle="dropdown" href="javascript:void(0)" style="float:left">Во всех регионах</a><i class="caret" style="margin: 22px 8px 22px -2px;"></i>
                                      <ul id="region_dropdown_menu_ul" class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                         <div class="arrow"></div>
                                          <li>
                                             <input type="hidden" name="" id="search-region-ac-id" value="0">
                                             <input id="search-region-ac-title" type="text" onkeyup="region.findCity('search-region-ac-title','region_list_ul')" autocomplete="off" placeholder="Начните вводить город" name="">
                                             <ul id="region_list_ul" class="autocomplete" style="width: 100%; max-height:400px;"></ul>
                                          </li>
                                      </ul>
                                    </div>
                                 <div id="search_panel_div" class="table-cell input-cell" style="float:left;margin-left: -1px;">
                                    <input type="text" id="inputSearch" class="pull-left" placeholder="Что вы ищете?" name="q" autocomplete="off" value="" maxlength="80" x-webkit-speech="x-webkit-speech" style="border-bottom: 1px solid #aaa !important; float:left">
                                    <input type="submit" value="" style="position: absolute;left: -99999px;">
                                       <div id="search-history" class="search-history" style="max-width: 500px; font-size:2.3em;opacity:0; display:none;">
                                          <div class="arrow"></div>
                                          <div class="overflowBg"></div>
                                          <div class="txt" style="width:100%">
                                             <nobr>
                                                Например: <a href="/catalog.php?page=1&query=Сниму квартиру">Сниму квартиру</a>, <a href="/catalog.php?page=1&query=Куплю квартиру">Куплю квартиру</a>, <a href="/catalog.php?page=1&query=iphone">Iphone</a>, <a href="/catalog.php?page=1&query=сниму комнату">Сниму комнату</a>, <a href="/catalog.php?page=1&query=работа в it">Работа в IT</a>, <a href="/catalog.php?page=1&query=познакомлюсь">Познакомлюсь</a>                                        </nobr>
                                          </div>
                                      </div>
                                 </div>
                                 <div class="table-cell" style="float:left"> <button id="search_button" class="btn btn-search pull-left"></button> </div>
                                 <div class="table-cell promo-text-wrapper visible-lg visible-md">
                                     <!--<div class="promo-text pull-right"> Сейчас на доске <a href="http://rio.ua/search">122 692 объявлений</a></div>-->
                                 </div>
                             </div>
                             <div class="clearfix"></div>
                     </div>
                 </div>
             </div>
         </div>
         <!-- #page-wrap -->
         <div class="home-main-wrapper" style="min-height: 261px;">
             <div class="container">
                 <div style="display:table">
                     <div class="categories-list-wrapper">
                         <h2 class="pull-left">Разделы:</h2>
                         <div class="pull-right" id="homeCategoriesSwitcher"> 
                           <a id="short" href="javascript:void(0)" onclick="switchCategories(0)" class="active">кратко</a> 
                           <a id="full" href="javascript:void(0)" onclick="switchCategories(1)">развёрнуто</a> </div>
                         <div class="clearfix"></div>
                         <hr>
                         <div class="table">
                             <ul class="categories-list">
                                 <?php echo $mainpage->buildCategoriesSection(); ?>
                             </ul>
                         </div>
                     </div>
                     <div class="rightColWrappet" style="<?php echo $mainpage->getShowMapFlag() == "No" ? "display:none;" :""; ?> margin-left: 29px; margin-top: 4px;">
                         <h2>Объявления по региону:</h2>
                         <hr>
                         <div class="map">
                           <div class="mapWrapper" style="top: 0px; position: relative; margin-left: 0px;">
                              <img src="img/israeli.jpg" style="width: 614px; height: 1141px;" usemap="#regMap" />
                              <MAP name="regMap" id="regMap">
                                   <area alt="" title="" href="catalog.php?page=1&region=1" shape="poly" coords="337,110,326,155,334,180,344,185,348,199,343,217,329,233,316,247,332,262,351,256,366,258,383,270,400,267,408,283,411,298,441,304,448,282,448,261,448,236,460,225,451,216,460,193,461,180,459,163,456,144,463,104,471,68,454,68,440,56,434,88,429,109,408,116,390,122,383,109" />
                                    <area alt="" title="" href="catalog.php?page=1&region=2" shape="poly" coords="329,164,317,186,304,177,292,229,281,296,304,298,317,306,328,281,361,259,351,251,328,259,316,241,330,232,347,199,333,175" />
                                    <area alt="" title="" href="catalog.php?page=1&region=3" shape="poly" coords="458,65,471,56,492,45,500,70,507,90,504,107,512,123,516,147,521,155,509,179,489,207,459,223,463,183,457,158,462,117,466,91,469,69" />
                                    <area alt="" title="" href="catalog.php?page=1&region=4" shape="poly" coords="278,296,261,355,277,363,275,378,272,388,279,391,280,401,268,401,261,410,245,405,234,436,242,441,242,453,233,454,231,466,242,469,249,456,255,463,262,472,275,458,287,458,297,450,311,441,316,428,303,417,310,392,304,380,304,364,300,354,313,342,314,330,314,313,320,301,302,296,285,295" />
                                    <area alt="" title="" href="catalog.php?page=1&region=5" shape="poly" coords="262,353,276,361,276,377,272,389,280,398,270,399,263,408,244,405,264,352" />
                                    <area alt="" title="" href="catalog.php?page=1&region=6" shape="poly" coords="361,260,329,278,317,301,317,314,309,331,315,340,300,353,301,366,306,379,305,391,312,396,304,421,316,430,315,445,303,449,304,459,319,455,328,447,333,454,354,461,362,453,359,464,362,478,338,481,320,499,299,515,295,540,294,550,283,569,288,586,311,580,338,583,366,573,392,554,408,542,420,545,433,486,440,471,438,447,433,429,437,416,433,403,433,380,444,356,442,327,438,304,408,294,404,276,399,266,375,267" />
                                    <area alt="" title="" href="catalog.php?page=1&region=7" shape="poly" coords="268,463,278,480,280,502,296,513,323,496,345,480,359,479,362,455,352,458,338,456,325,447,313,455,299,458,301,444,288,455" />
                                    <area alt="" title="" href="catalog.php?page=1&region=8" shape="poly" coords="232,436,186,516,204,534,157,581,159,597,133,621,177,736,195,807,202,815,202,840,214,852,275,1020,273,1038,281,1053,277,1064,284,1087,300,1071,315,1053,317,1023,319,1013,319,1005,327,986,329,957,347,922,344,887,347,870,354,853,347,834,356,793,378,755,378,734,388,727,389,713,396,700,407,691,405,675,416,647,412,633,400,611,417,578,421,544,405,542,366,573,339,584,313,580,287,585,281,573,284,557,294,539,297,515,278,501,278,473,267,465,258,470,250,457,242,456,242,467,234,464,234,453,242,448,243,441" />
                                    <area alt="" title="" href="catalog.php?page=1&region=9" shape="poly" coords="189,517,125,588,135,617,153,610,162,592,158,578,181,551,203,534,190,519" />
                              </MAP>
                           </div>
                        </div>
                     </div>
                 </div>
             </div>
         </div>
      </div>
              
      <!------------------------------------------ Панель вип-обявлений начало ----------------------------------------->
      
      <div id="vip_pageblock_div">
         <a id="vip_slider_left_arrow_a" href="javascript:void(0)" onclick="vipslider.left()"><img id="vip_slider_left_arrow_img" src="img/left-arrow.png" /></a>
         <div id="vip_wrapper_div">
            <h2 class="h2_lefted">Вип-объявления:</h2>
            <div id="vip_content_wrapper_div">
               <?php echo $mainpage->buildVipSlider()?>
            </div>
         </div>
         <a id="vip_slider_right_arrow_a" href="javascript:void(0)" onclick="vipslider.right()"><img id="vip_slider_right_arrow_img" src="img/right-arrow.png" /></a>
      </div>
      
      <!------------------------------------------ Панель вип-обявлений конец ----------------------------------------->
      
      <?php echo $constructor->getFooter();?>
      
      <script type="text/javascript" src="js/main.js"></script>
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/dropdown.js"></script>
      <script type="text/javascript" src="js/include/vip.js"></script>
</body>
</html>

<?php

mysql_close();

?>