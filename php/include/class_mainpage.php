<?php

class Mainpage{
   public function test(){
      return "Mainpage class testing OK!!!";
   }
   
    public function getShowMapFlag(){
        $sql = "SELECT `values` FROM `dbc_options` WHERE `key` = 'classified_settings';";
        $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
        
        $parsed = json_decode($result["values"], true);
        return $parsed["show_map_on_homepage"];
    }   
   
   public function buildCategoriesSection(){
      $section_dom = ""; // у рио делает тоггл на класс fullViewShowed если развернуть категории
      $sql = "SELECT * FROM `category` ORDER BY `num`;";
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      
      for ($i = 0; $i < count($result); $i++){
         $section_dom .= 
         '<li class="category" '.($this->getShowMapFlag() == "No" ? "style='width:20%;'" : "").'>
            <a style="background-image: url(\'img/cat_icons/'.$result[$i]["category_code"].'.png\')" href="http://'.$_SERVER['HTTP_HOST'].'/catalog.php?page=1&cat='.$result[$i]["category_code"].'">'.$result[$i]["category_name"].'</a>
            <ul class="subcategories mCustomScrollbar _mCS_1" style="display: none;">
               <div id="mCSB_1" class="mCustomScrollBox mCS-light" style="position:relative; height:100%; overflow:hidden; max-width:100%;">
                  <div class="mCSB_container mCS_no_scrollbar" style="position:relative; top:0;">';
                  
         $category_code = $result[$i]["category_code"];
         $sql1 = "SELECT * FROM `subcategory` WHERE `category_code` = $category_code ORDER BY `num`;";
         $result1 = DB::getInstance()->db_fetchall_array($sql1, __LINE__, __FILE__);
         
         for ($z = 0; $z < count($result1); $z++)
            $section_dom .= 
            '<li>
               <a href="http://'.$_SERVER['HTTP_HOST'].'/catalog.php?page=1&cat='.$result[$i]["category_code"].'&sub='.$result1[$z]["subcategory_code"].'&output=2">'.$result1[$z]["subcategory_name"].'</a>
            </li>';
            
         $section_dom .= 
                  '</div>
                  <div class="mCSB_scrollTools" style="position: absolute; display: none;">
                     <div class="mCSB_draggerContainer">
                        <div class="mCSB_dragger" oncontextmenu="return false;" style="position: absolute; top: 0px;">
                           <div class="mCSB_dragger_bar" style="position:relative;"></div>
                        </div>
                        <div class="mCSB_draggerRail"></div>
                     </div>
                  </div>
               </div>
            </ul>
         </li>';
      }      
      return $section_dom;
   }
   
   public function buildVipSlider(){
      global $currency;
      
      $dom = ""; // у рио делает тоггл на класс fullViewShowed если развернуть категории
      $sql = "SELECT * FROM `catalog` WHERE `vip` <> 0 AND `status` = 1 AND ".time()."-`timestamp` < `period` ORDER BY `vip` DESC LIMIT 15;";
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      
      for ($i = 0; $i < count($result); $i++){
         $out = 0;
         
         $photo_array = json_decode($result[$i]["photo"], true);
         $dom .= 
         '<div '.($i == 0 ? 'id="first_gallery_box_div"' : '').' class="gallery_element_box" onmouseenter="vipslider.showDescription(this)" onmouseleave="vipslider.hideDescription(this)">
            <a href="item.php?num='.$result[$i]["num"].'"><div class="gallery_element_box_img_wrapper">
               <img id="vip_img_'.$result[$i]["num"].'" src="'.($photo_array[0] != "" ? "catalog/".$photo_array[0] : "img/camera.png").'" style="" onload="resizeOnLoadVip(this)"/>
            </div>
            <div class="vip_desc_div"><span id="vip_title_'.$i.'">'.$result[$i]["title"].'</span><p><p><span class="vip_desc_div_price_span">'.$result[$i]["price"].' '.$currency->getShortTitle($result[$i]["currency"]).'.</span></div></a>
         </div>';
      }
      
      return $dom;
   }
}

?>