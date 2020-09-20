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

if (!isset($_GET["page"])) header("Location: http://".$_SERVER['HTTP_HOST']."/catalog.php?page=1");

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
      
      <?php echo $constructor->getHeaderForCatalog(); ?>
      
      <div class="clearfix"></div>
      <div class="filters-area fixed" style="width: 100%; font-size:2.7em">
         <div id="filter_checks" style="margin:20px 0">
            <span style="float:left; margin-right:20px">Цена:</span>
            <div style="width:20%;float:left; margin-right:40px;">
               <div id="slider_div"></div><span id="slider_leftedge_span" style="float:left"></span><span id="slider_rightedge_span" style="float:right"></span>
            </div>
            <input id="photo" type="checkbox" class="css-checkbox" style="display: none;margin-left: 40px;margin-top:0" /><label for="photo" id="photo_check_label" class="catalog_checkbox css-label lite-gray-check">С фото</label>
            <input id="gift" type="checkbox" class="css-checkbox" style="display: none;margin-left: 20px;margin-top:0" /><label for="gift" id="gift_check_label" class="catalog_checkbox css-label lite-gray-check">Даром</label>
            <input id="exchange" type="checkbox" class="css-checkbox" style="display: none;margin-left: 20px;margin-top:0" /><label for="exchange" id="exchange_check_label" class="catalog_checkbox css-label lite-gray-check">Обмен</label>
         </div>
         <div id="sorting" class="pull-right" style="margin-top:-70px; margin-right:13px;">
            <div id="view" class="dropdown pull-left filtering-item">
               <ul id="view-toggle" class="nav nav-pills">
                  <li id="output_mode_thumbs_switch">
                     <a href="javascript:void(0)" onclick="catalog_common.switchOutputMode(2)" view="thumbnails"><img width="16" height="16" src="http://rio.ua/img/icon-thumnails.png"></a>
                  </li>
                  <li id="output_mode_list_switch">
                     <a href="javascript:void(0)" onclick="catalog_common.switchOutputMode(1)" view="list"><img width="16" height="16" src="http://rio.ua/img/icon-list.png"></a>
                  </li>
               </ul>
            </div>
            <div id="sorting-price" class="dropdown pull-left filtering-item">
               <a class="dropdown-toggle" href="javascript:void(0)" onclick="dropdown.toggle('sorting-list')" data-toggle="dropdown">
                  <span class="visible-lg">
                     <span id="rank_dropdown_span">Сначала новые</span>
                     <b class="caret visible-lg"></b>
                  </span>
                  <span class="visible-md icon icon-sorting"></span>
               </a>
               <ul id="sorting-list" class="dropdown-menu" aria-labelledby="dropdownMenu" role="menu">
                  <li class="active rank_li" id="default_rank_li" rank="0">
                     <a href="javascript:void(0)" onclick="catalog.filterSetRank(0)">Сначала новые</a>
                  </li>
                  <li class="rank_li" rank="1">
                     <a href="javascript:void(0)" onclick="catalog.filterSetRank(1)">Сначала дешевые</a>
                  </li>
                  <li class="rank_li" rank="2">
                     <a href="javascript:void(0)" onclick="catalog.filterSetRank(2)">Сначала дорогие</a>
                  </li>
                  <li class="rank_li" rank="3">
                     <a href="javascript:void(0)" onclick="catalog.filterSetRank(3)">Сначала популярные</a>
                  </li>
               </ul>
            </div>
            <div id="sorting-curency" class="dropdown pull-left filtering-item visible-lg">
               <a class="dropdown-toggle" href="javascript:void(0)" onclick="dropdown.toggle('currensies-list')" data-toggle="dropdown">
                  <span><span id="current_curr"></span></span>
                  <b class="caret"></b>
               </a>
               <ul id="currensies-list" class="dropdown-menu" aria-labelledby="dropdownMenu" role="menu">
                  
               </ul>
            </div>
         </div>
         
         <a id="saveResults" class="pull-right hide"> Сохранить результаты поиска </a>
      </div>
      <div id="crumbs_panel" style="width:100%; height:50px;"></div>
      <div id="categories-block" class="appear-top-block" style="margin-top: -540px; position: fixed; width: 100%;">
         <div class="content">
            <div class="row">
               <div class="col-4 mCustomScrollbar _mCS_1">
                  <div class="mCustomScrollBox mCS-light" id="mCSB_1" style="position:relative; height:100%; max-width:100%;">
                     <div class="mCSB_container mCS_no_scrollbar" style="position: relative; top: 0px;">
                        <ul class="level1">
                           <li><a data-id="-1">Все</a></li>
                           
                        </ul>
                     </div>
                     <div class="mCSB_scrollTools" style="position: absolute; display: none;">
                        <div class="mCSB_draggerContainer">
                           <div class="mCSB_dragger" style="position: absolute; top: 0px;" oncontextmenu="return false;">
                              <div class="mCSB_dragger_bar" style="position:relative;"></div>
                           </div>
                           <div class="mCSB_draggerRail"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-4 mCustomScrollbar _mCS_2">
                  <div class="mCustomScrollBox mCS-light" id="mCSB_2" style="position:relative; height:100%; max-width:100%;">
                     <div class="mCSB_container mCS_no_scrollbar" style="position: relative; top: 0px;">
                        <ul class="level2" style="display: block">
                           
                        </ul>
                     </div>
                     <div class="mCSB_scrollTools" style="position: absolute; display: none;">
                        <div class="mCSB_draggerContainer">
                           <div class="mCSB_dragger" style="position: absolute; top: 0px;" oncontextmenu="return false;">
                              <div class="mCSB_dragger_bar" style="position:relative;"></div>
                           </div>
                           <div class="mCSB_draggerRail"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-4 mCustomScrollbar _mCS_3">
                  <div class="mCustomScrollBox mCS-light" id="mCSB_3" style="position:relative; height:100%;max-width:100%;">
                     <div class="mCSB_container mCS_no_scrollbar" style="position: relative; top: 0px;">
                        <ul class="level3" style="display: block">
                        </ul>
                     </div>
                     <div class="mCSB_scrollTools" style="position: absolute; display: none;">
                        <div class="mCSB_draggerContainer">
                           <div class="mCSB_dragger" style="position: absolute; top: 0px;" oncontextmenu="return false;">
                              <div class="mCSB_dragger_bar" style="position:relative;"></div>
                           </div>
                           <div class="mCSB_draggerRail"></div>
                        </div>
                        <br>
                     </div>
                  </div>
               </div>
            </div>
            <div class="clearfix"></div>
         </div>
         <a class="hide-toggle" href="javascript:void();" onclick="category.toggleBar()"><i class="icon icon-hide"></i>Свернуть</a> 
      </div>
      <input type="hidden" name="item_id" value="965913" />
      <div id="ads-single">
         <div id="subcats-panel" style="float:left;font-size:2.6em;padding:0 10px 10px 20px; overflow-y:scroll;">
            <span id="breadcrumbs_span"></span>:
         </div>
         <div class="main-wrapper" style="min-height: 158px;"> 
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
      
      <script type="text/javascript" src="js/catalog.js?1"></script>
      <script type="text/javascript" src="js/include/dropdown.js?1"></script>
      <script type="text/javascript" src="js/include/category.js?1"></script>
      <script type="text/javascript" src="js/include/catalog.js?1"></script>
      <script type="text/javascript" src="js/include/item.js?1"></script>
      <script type="text/javascript" src="js/include/feedback.js?1"></script>
      <script type="text/javascript" src="js/include/utils.js?1"></script>
      <script type="text/javascript" src="js/include/form.js?1"></script>
      <script type="text/javascript" src="js/include/region.js?1"></script>
      <script type="text/javascript" src="js/include/vip.js?1"></script>
      <script type="text/javascript" src="js/include/user.js?1"></script>
      <script type="text/javascript" src="js/include/json2.js?1"></script>
   </body>
</html>

<?php

mysql_close();

?>

?>