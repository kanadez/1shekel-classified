<?php

class Filter{
   public function getDefaults($category, $subcategory, $advsubcategory){
      $filtered_cat = isset($category) ? intval($category) : null;
      $filtered_sub = isset($subcategory) ? intval($subcategory) : null;
      $filtered_adv = isset($advsubcategory) ? intval($advsubcategory) : null;
      $defaults = array();
      
      $sql = "SELECT MIN(`price`), MAX(`price`) FROM `catalog` ".(isset($filtered_cat) ? "WHERE `category_code` = $filtered_cat" : "")." ".(isset($filtered_sub) ? "AND `subcategory_code` = $filtered_sub" : "")." ".(isset($filtered_adv) ? "AND `advsubcategory_code` = $filtered_adv" : "").";";
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      $defaults["price_floor"] = $result["MIN(`price`)"];
      $defaults["price_ceil"] = $result["MAX(`price`)"];
      
      $sql = "SELECT `code` FROM `currency` WHERE `default` = 1;";
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      $defaults["currency"] = $result["code"];
      
      return json_encode($defaults);
   }
}

?>