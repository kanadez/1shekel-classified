<?php

class Constructor{
   public function getFooter(){
      return '<footer class="footer" style="width: auto; text-align:center;">
            <div class="text-muted" id="service-description"></div>
            <div class="table">
               <div class="table-cell">
                  <div class="footer-nav">
                     <a class="feedback footer_a" href="feedback.php" style="margin-left:0">Обратная связь</a>
                     <a href="terms.php" class="footer_a">Правила</a>
                     <a href="help.php" class="footer_a">Помощь</a>
                     <a href="about.php" class="footer_a">О нас</a>
                     <a href="new.php" class="footer_a">Подать объявление</a>            
                  </div>
               </div>
            </div>
            <table style="width:20%; margin-left:auto; margin-right:auto;">
               <tr>
                  <td><a href="javascript:void(0)" class="sn_a"><img src="img/sn_logos/sn_vk_logo.png" style="width:90%" /></a></td>
                  <td><a href="javascript:void(0)" class="sn_a"><img src="img/sn_logos/sn_fb_logo.png" style="width:90%"/></a></td>
                  <td><a href="javascript:void(0)" class="sn_a"><img src="img/sn_logos/sn_tw_logo.png" style="width:90%"/></a></td>
                  <td><a href="javascript:void(0)" class="sn_a"><img src="img/sn_logos/sn_ok_logo.png" style="width:90%"/></a></td>
                  <td><a href="javascript:void(0)" class="sn_a"><img src="img/sn_logos/sn_mm_logo.png" style="width:90%"/></a></td>
                  <td><a href="javascript:void(0)" class="sn_a"><img src="img/sn_logos/sn_gp_logo.png" style="width:90%"/></a></td>
               </tr>
            </table>
            <div id="copyr_div">© Project NEON 2015. Все права защищены / <a style="font-size:1em;" href="mailto:hello@neon.by">hello@neon.by</a> / 8 (800) 123-456</div>
            <!--<div id="madeinmetateg" style="margin:10px;position: absolute;bottom: 0px;">
                  <a href="http://metateg.pro" target="_blank"><img style="height: 2.3em;" src="http://metateg.pro/madeinmetateg.png" /></a>
               </div>-->
            <img src="img/li_counter.png" style="float:right;margin: -46px 26px 20px 0;" />
         </footer>';
   }
   
   public function getHeaderForCatalog(){
      global $user;
      
      $header = '<div class="navbar navbar-inverse navbar-fixed-top bs-docs-nav" style="width: auto !important; padding:0 20px; opacity:1;">
         <div id="header_left_block" style="padding:13px;float:left;">
            <a href="index.php" style="color: #444; font-size:2.7em;">Главная</a>
         </div>
         <div class="dropdown visible-lg visible-md pull-left" id="header-lang-swicher" style="float:left; border:1px solid #cecece">
           <a id="region_dropdown_menu_a" data-toggle="dropdown" href="javascript:void(0)" style="float:left">Во всех регионах</a><i class="caret" style="margin: 22px 8px 22px -2px;"></i>
           <ul id="region_dropdown_menu_ul" class="dropdown-menu" role="menu" aria-labelledby="dLabel">
              <div id="region_dropdown_arrow" class="arrow"></div>
               <li>
                  <input type="hidden" name="" id="search-region-ac-id" value="0">
                  <input type="text" name="" id="search-region-ac-title" placeholder="Начните вводить город" autocomplete="off" onkeyup="region.findRegion(\'search-region-ac-title\',\'region_list_ul\')">
                  <ul id="region_list_ul" class="autocomplete" style="width: 100%;font-size: 1.3em;padding-left: 0px;"></ul>
               </li>
           </ul>
         </div>
         <div id="catalog_search_wrapper">
            <input id="catalog_search_input" placeholder="Введите запрос для поиска.." />
            <button onclick="catalog.doSearch()" class="btn btn-search2 pull-left"></button>
         </div>
         <!--<a class="navbar-brand" href="index.php" alt="NEON" title="NEON"><img style="height:50px;" src="img/logo.png"></a>-->
         <div class="navbar-block pull-right" id="add_item">
           <a href="new.php" id="new_item_a" class="loginLink">Подать объявление</a>
         </div>
         <div class="navbar-block pull-right" id="user-area-na">';
           if (isset($_SESSION["user_num"])) $header.= '<a id="login_a" href="profile.php">'.$user->getNameByNum($_SESSION["user_num"]).'</a>'; else $header.= '<a id="login_a" href="login.php">Вход</a>';
         $header.= '</div>
         <div class="navbar-block pull-right" id="favorites" style="">
           <a href="favorites.php">Избранное</a>
         </div>
      </div>';
      
      return $header;
   }
   
   public function getHeader(){
      global $user;
      
      $header = '<div class="navbar navbar-inverse navbar-fixed-top bs-docs-nav" style="width: auto !important; padding:0 20px; opacity:1;">
         <div id="header_left_block" style="padding:13px;float:left;">
            <a href="index.php" style="color: #444; font-size:2.7em;">Главная</a>
         </div>
         <div class="dropdown visible-lg visible-md pull-left" id="header-lang-swicher" style="float:left; border:1px solid #cecece">
           <a id="region_dropdown_menu_a" data-toggle="dropdown" href="javascript:void(0)" style="float:left">Во всех регионах</a><i class="caret" style="margin: 22px 8px 22px -2px;"></i>
           <ul id="region_dropdown_menu_ul" class="dropdown-menu" role="menu" aria-labelledby="dLabel">
              <div id="region_dropdown_arrow" class="arrow"></div>
               <li>
                  <input type="hidden" name="" id="search-region-ac-id" value="0">
                  <input type="text" name="" id="search-region-ac-title" placeholder="Начните вводить город" autocomplete="off" onkeyup="region.findRegion(\'search-region-ac-title\',\'region_list_ul\')">
                  <ul id="region_list_ul" class="autocomplete" style="width: 100%;font-size: 1.3em;padding-left: 0px;"></ul>
               </li>
           </ul>
         </div>
         <div id="catalog_search_wrapper">
            <input id="catalog_search_input" placeholder="Введите запрос для поиска.." />
            <button id="search_button" class="btn btn-search2 pull-left"></button>
         </div>
         <!--<a class="navbar-brand" href="index.php" alt="NEON" title="NEON"><img style="height:50px;" src="img/logo.png"></a>-->
         <div class="navbar-block pull-right" id="add_item">
           <a href="new.php" id="new_item_a" class="loginLink">Подать объявление</a>
         </div>
         <div class="navbar-block pull-right" id="user-area-na">';
           if (isset($_SESSION["user_num"])) $header.= '<a id="login_a" href="profile.php">'.$user->getNameByNum($_SESSION["user_num"]).'</a>'; else $header.= '<a id="login_a" href="login.php">Вход</a>';
         $header.= '</div>
         <div class="navbar-block pull-right" id="favorites" style="">
           <a href="favorites.php">Избранное</a>
         </div>
      </div>';
      
      return $header;
   }
   
}

?>