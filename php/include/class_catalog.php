<?php
   class Catalog{
      private $params;
      
      function __construct(){
         global $parameter;
         $this->params["catalog_items_per_page"] = $parameter->get("catalog_items_per_page");
      }
      
      public function getFavorites($items_array, $page){
         global $db;
         global $currency;
         $p = isset($page) ? intval($page) : 1;
         $items = json_decode(stripcslashes($items_array), true);
         
         $sql = "SELECT `num`, `title`, `timestamp`, `price`, `auction`, `photo` FROM `catalog` WHERE `num` IN (";
         
         for ($i = 0; $i < count($items); $i++)
            $sql .= $items[$i].",";
         
         $sql = substr($sql, 0, -1).");";
         $result = $db->db_fetchall_array($sql, __LINE__, __FILE__);
         
         for ($c = 0; $c < count($result); $c++){
            $result[$c]["price"] *= $currency->getDefaultCoef();
            $result[$c]["currency_short_title"] = $currency->getDefaultShortTitle();
         }
         
         $this->params["items_count"] = count($result); // количество элементов по данной категории всего
         $complete = array(); // новый массив для элментов текщей страницы
         $ipp = $this->params["catalog_items_per_page"];
         for ($i = $ipp*$p; $i < $ipp*($p+1); $i++){
            isset($result[$i]) ? array_push($complete, $result[$i]) : $i++;
         }
         array_push($complete, $this->params);
         return json_encode($complete);
      }
      
      public function getPriceRange($cat, $subcat, $advsubcat){
         $filtered_cat = isset($cat) ? intval($cat) : null;
         $filtered_sub = isset($subcat) ? intval($subcat) : null;
         $filtered_adv = isset($advsubcat) ? intval($advsubcat) : null;
         
         $sql = "SELECT MIN(`price`), MAX(`price`) FROM `catalog` WHERE `status` = 1 ".(isset($filtered_cat) ? " AND `category_code` = $filtered_cat" : "")." ".(isset($filtered_sub) ? "AND `subcategory_code` = $filtered_sub" : "")." ".(isset($filtered_adv) ? "AND `advsubcategory_code` = $filtered_adv" : "").";";
         $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
         $obj["min"] = $result["MIN(`price`)"];
         $obj["max"] = $result["MAX(`price`)"];
         
         return json_encode($obj);
      }
      
      public function get($category, $subcategory, $advsubcategory, $filter, $page, $region, $city){
         global $currency;
         $filtered_cat = isset($category) ? intval($category) : null;
         $filtered_sub = isset($subcategory) ? intval($subcategory) : null;
         $filtered_adv = isset($advsubcategory) ? intval($advsubcategory) : null;
         $p = isset($page) ? intval($page) : 1;
         $filter_obj = isset($filter) ? json_decode(stripcslashes($filter), true) : null;
         $filter_currency = isset($filter_obj["filter_curr"]) ? $filter_obj["filter_curr"] : $currency->getDefaultCurrencyCode();
         $filter_rank_value = isset($filter_obj["filter_rank"]) ? intval($filter_obj["filter_rank"]) : 0;
         $filter_rank_sql_string = "";
         $filter_price_floor = isset($filter_obj["filter_price_floor"]) ? intval($filter_obj["filter_price_floor"]) : null;
         $filter_price_ceil = isset($filter_obj["filter_price_ceil"]) ? intval($filter_obj["filter_price_ceil"]) : null;
         $filter_price_sql_string = "";
         $filter_photo = isset($filter_obj["filter_photo"]) ? intval($filter_obj["filter_photo"]) : null;
         $filter_photo_sql_string = "";
         $filter_gift = isset($filter_obj["filter_gift"]) ? intval($filter_obj["filter_gift"]) : null;
         $filter_gift_sql_string = "";
         $filter_exchange = isset($filter_obj["filter_exchange"]) ? intval($filter_obj["filter_exchange"]) : null;
         $filter_exchange_sql_string = "";
         $filtered_region = $region != -1 ? intval($region) : null;
         $filtered_city = $city != -1 ? intval($city) : null;
         $cur_time = time();
         
         if (isset($filter_rank_value))
            switch ($filter_rank_value){
               case 0 :
                  $filter_rank_sql_string = "ORDER BY `timestamp` DESC";
               break;
               
               case 1 :
                  if (isset($filter_currency)) $coef = $currency->getCoef($filter_currency);
                  else $coef = $currency->getDefaultCoef();
                  $filter_rank_sql_string = "ORDER BY `price`/(SELECT `currency`.`exchange` FROM `currency` WHERE `currency`.`code` = `catalog`.`currency`)*$coef";
               break;
               
               case 2 :
                  if (isset($filter_currency)) $coef = $currency->getCoef($filter_currency);
                  else $coef = $currency->getDefaultCoef();
                  $filter_rank_sql_string = "ORDER BY `price`/(SELECT `currency`.`exchange` FROM `currency` WHERE `currency`.`code` = `catalog`.`currency`)*$coef DESC";
               break;
               
               case 3 :
                  $filter_rank_sql_string = "ORDER BY `promoted` DESC";
               break;
            }
         else $filter_rank_sql_string = "ORDER BY `promoted` DESC";
            
         if (isset($filter_price_floor)){
            $coef = $currency->getCoef($filter_currency);
            $filter_price_sql_string = "AND `price`/(SELECT `currency`.`exchange` FROM `currency` WHERE `currency`.`code` = `catalog`.`currency`)*$coef BETWEEN $filter_price_floor AND $filter_price_ceil";
         }
            
         if (isset($filter_photo))
            $filter_photo_sql_string = $filter_photo == 1 ? "AND `photo` <> '' AND `photo` <> '[]' " : "";
            
         if (isset($filter_gift))
            $filter_gift_sql_string = $filter_gift == 1 ? "AND `gift` = 1 " : "AND `gift` <> 1 ";
            
         if (isset($filter_exchange))
            $filter_exchange_sql_string = $filter_exchange == 1 ? "AND `exchange` = 1 " : "AND `exchange` <> 1 ";
         
         $sql = "SELECT `num`, `title`, `timestamp`, `price`, `auction`, `photo`, `currency` FROM `catalog` WHERE `status` = 1 AND `highlighted` = 0 AND `temporary` <> 1 ".(isset($filtered_cat) ? " AND `category_code` = $filtered_cat" : "")." ".(isset($filtered_sub) ? "AND `subcategory_code` = $filtered_sub" : "")." ".(isset($filtered_adv) ? "AND `advsubcategory_code` = $filtered_adv" : "")." ".(isset($filtered_region) ? "AND `region` = $filtered_region" : "")." ".(isset($filtered_city) ? "AND `city` = $filtered_city" : "")." AND $cur_time - `timestamp` < `period` $filter_price_sql_string $filter_photo_sql_string $filter_exchange_sql_string $filter_gift_sql_string $filter_rank_sql_string;";
         $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
         
         if (isset($filter_currency)){
            if ($filter_currency != $result[$c]["currency"])
               for ($c = 0; $c < count($result); $c++){
                  $result[$c]["price"] = $currency->convert($result[$c]["price"], $result[$c]["currency"], $filter_currency);
                  $result[$c]["currency_short_title"] = $currency->getShortTitle($filter_currency);
               }
         }
         else{
            for ($c = 0; $c < count($result); $c++){
               //$result[$c]["price"] *= $currency->getDefaultCoef();
               $result[$c]["currency_short_title"] = $currency->getShortTitle($result[$c]["currency"]);
            }
         }
               
         $this->params["curr_coef"] = isset($filter_currency) ? $currency->getCoef($filter_currency) : $currency->getDefaultCoef();
         $this->params["items_count"] = count($result); // количество элементов по данной категории всего
         $complete = array(); // новый массив для элментов текщей страницы
         $ipp = $this->params["catalog_items_per_page"];
         for ($i = $ipp*$p; $i < $ipp*($p+1); $i++){
            isset($result[$i]) ? array_push($complete, $result[$i]) : $i++;
         }
         array_push($complete, $this->params);
         return json_encode($complete);
      }
      
      public function search($category, $subcategory, $advsubcategory, $filter, $page, $query, $region, $city){
         global $currency;
         global $search;
         $filtered_cat = isset($category) ? intval($category) : null;
         $filtered_sub = isset($subcategory) ? intval($subcategory) : null;
         $filtered_adv = isset($advsubcategory) ? intval($advsubcategory) : null;
         $p = isset($page) ? intval($page) : 1;
         $filter_obj = isset($filter) ? json_decode(stripcslashes($filter), true) : null;
         $filter_currency = isset($filter_obj["filter_curr"]) ? $filter_obj["filter_curr"] : $currency->getDefaultCurrencyCode();
         $filter_rank_value = isset($filter_obj["filter_rank"]) ? intval($filter_obj["filter_rank"]) : null;
         $filter_rank_sql_string = "";
         $filter_price_floor = isset($filter_obj["filter_price_floor"]) ? intval($filter_obj["filter_price_floor"]) : null;
         $filter_price_ceil = isset($filter_obj["filter_price_ceil"]) ? intval($filter_obj["filter_price_ceil"]) : null;
         $filter_price_sql_string = "";
         $filter_photo = isset($filter_obj["filter_photo"]) ? intval($filter_obj["filter_photo"]) : null;
         $filter_photo_sql_string = "";
         $filter_gift = isset($filter_obj["filter_gift"]) ? intval($filter_obj["filter_gift"]) : null;
         $filter_gift_sql_string = "";
         $filter_exchange = isset($filter_obj["filter_exchange"]) ? intval($filter_obj["filter_exchange"]) : null;
         $filter_exchange_sql_string = "";
         $filtered_region = $region != -1 ? intval($region) : null;
         $filtered_city = $city != -1 ? intval($city) : null;
         $cur_time = time();
         
         if (isset($filter_rank_value))
            switch ($filter_rank_value){
               case 0 :
                  $filter_rank_sql_string = "ORDER BY `timestamp` DESC";
               break;
               
               case 1 :
                  if (isset($filter_currency)) $coef = $currency->getCoef($filter_currency);
                  else $coef = $currency->getDefaultCoef();
                  $filter_rank_sql_string = "ORDER BY `price`/(SELECT `currency`.`exchange` FROM `currency` WHERE `currency`.`code` = `catalog`.`currency`)*$coef";
               break;
               
               case 2 :
                  if (isset($filter_currency)) $coef = $currency->getCoef($filter_currency);
                  else $coef = $currency->getDefaultCoef();
                  $filter_rank_sql_string = "ORDER BY `price`/(SELECT `currency`.`exchange` FROM `currency` WHERE `currency`.`code` = `catalog`.`currency`)*$coef DESC";
               break;
               
               case 3 :
                  $filter_rank_sql_string = "ORDER BY `promoted` DESC";
               break;
            }
         else $filter_rank_sql_string = "ORDER BY `promoted` DESC";
            
         if (isset($filter_price_floor)){
            $coef = $currency->getCoef($filter_currency);
            $filter_price_sql_string = "AND `price`/(SELECT `currency`.`exchange` FROM `currency` WHERE `currency`.`code` = `catalog`.`currency`)*$coef BETWEEN $filter_price_floor AND $filter_price_ceil";
         }
            
         if (isset($filter_photo))
            $filter_photo_sql_string = $filter_photo == 1 ? "AND `photo` <> '' AND `photo` <> '[]' " : "";
            
         if (isset($filter_gift))
            $filter_gift_sql_string = $filter_gift == 1 ? "AND `gift` = 1 " : "AND `gift` <> 1 ";
            
         if (isset($filter_exchange))
            $filter_exchange_sql_string = $filter_exchange == 1 ? "AND `exchange` = 1 " : "AND `exchange` <> 1 ";
         
         $sql = "SELECT `num`, `title`, `timestamp`, `price`, `auction`, `photo`, `currency` FROM `catalog` WHERE (`title` LIKE ".$search->constructSQL($query).") AND `highlighted` = 0 AND `status` = 1 AND `temporary` <> 1 ".(isset($filtered_cat) ? " AND `category_code` = $filtered_cat" : "")." ".(isset($filtered_sub) ? "AND `subcategory_code` = $filtered_sub" : "")." ".(isset($filtered_adv) ? "AND `advsubcategory_code` = $filtered_adv" : "")." ".(isset($filtered_region) ? "AND `region` = $filtered_region" : "")." ".(isset($filtered_city) ? "AND `city` = $filtered_city" : "")." AND $cur_time - `timestamp` < `period` $filter_price_sql_string $filter_photo_sql_string $filter_exchange_sql_string $filter_gift_sql_string $filter_rank_sql_string;";
         $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
         
         if (isset($filter_currency)){
            if ($filter_currency != $result[$c]["currency"])
               for ($c = 0; $c < count($result); $c++){
                  $result[$c]["price"] = $currency->convert($result[$c]["price"], $result[$c]["currency"], $filter_currency);
                  $result[$c]["currency_short_title"] = $currency->getShortTitle($filter_currency);
               }
         }
         else{
            for ($c = 0; $c < count($result); $c++){
               //$result[$c]["price"] *= $currency->getDefaultCoef();
               $result[$c]["currency_short_title"] = $currency->getShortTitle($result[$c]["currency"]);
            }
         }
               
         $this->params["curr_coef"] = isset($filter_currency) ? $currency->getCoef($filter_currency) : $currency->getDefaultCoef();
         $this->params["items_count"] = count($result); // количество элементов по данной категории всего
         $complete = array(); // новый массив для элментов текщей страницы
         $ipp = $this->params["catalog_items_per_page"];
         for ($i = $ipp*$p; $i < $ipp*($p+1); $i++){
            isset($result[$i]) ? array_push($complete, $result[$i]) : $i++;
         }
         array_push($complete, $this->params);
         return json_encode($complete);
      }
      
      public function getHighlighted($category, $subcategory, $advsubcategory, $filter, $page, $region, $city){
         global $currency;
         $filtered_cat = isset($category) ? intval($category) : null;
         $filtered_sub = isset($subcategory) ? intval($subcategory) : null;
         $filtered_adv = isset($advsubcategory) ? intval($advsubcategory) : null;
         $p = isset($page) ? intval($page) : 1;
         $filter_obj = isset($filter) ? json_decode(stripcslashes($filter), true) : null;
         $filter_currency = isset($filter_obj["filter_curr"]) ? $filter_obj["filter_curr"] : $currency->getDefaultCurrencyCode();
         $filter_rank_value = isset($filter_obj["filter_rank"]) ? intval($filter_obj["filter_rank"]) : null;
         $filter_rank_sql_string = "";
         $filter_price_floor = isset($filter_obj["filter_price_floor"]) ? intval($filter_obj["filter_price_floor"]) : null;
         $filter_price_ceil = isset($filter_obj["filter_price_ceil"]) ? intval($filter_obj["filter_price_ceil"]) : null;
         $filter_price_sql_string = "";
         $filter_photo = isset($filter_obj["filter_photo"]) ? intval($filter_obj["filter_photo"]) : null;
         $filter_photo_sql_string = "";
         $filter_gift = isset($filter_obj["filter_gift"]) ? intval($filter_obj["filter_gift"]) : null;
         $filter_gift_sql_string = "";
         $filter_exchange = isset($filter_obj["filter_exchange"]) ? intval($filter_obj["filter_exchange"]) : null;
         $filter_exchange_sql_string = "";
         $filtered_region = $region != -1 ? intval($region) : null;
         $filtered_city = $city != -1 ? intval($city) : null;
         $cur_time = time();
         
         
         $filter_rank_sql_string = "ORDER BY `highlighted` DESC";
            
         if (isset($filter_price_floor)){
            $coef = $currency->getCoef($filter_currency);
            $filter_price_sql_string = "AND `price`/(SELECT `currency`.`exchange` FROM `currency` WHERE `currency`.`code` = `catalog`.`currency`)*$coef BETWEEN $filter_price_floor AND $filter_price_ceil";
         }
            
         if (isset($filter_photo))
            $filter_photo_sql_string = $filter_photo == 1 ? "AND `photo` <> '' AND `photo` <> '[]' " : "";
            
         if (isset($filter_gift))
            $filter_gift_sql_string = $filter_gift == 1 ? "AND `gift` = 1 " : "AND `gift` <> 1 ";
            
         if (isset($filter_exchange))
            $filter_exchange_sql_string = $filter_exchange == 1 ? "AND `exchange` = 1 " : "AND `exchange` <> 1 ";
         
         $sql = "SELECT `num`, `title`, `timestamp`, `price`, `auction`, `photo`, `currency` FROM `catalog` WHERE `status` = 1 AND `highlighted` <> 0 ".(isset($filtered_cat) ? " AND `category_code` = $filtered_cat" : "")." ".(isset($filtered_sub) ? "AND `subcategory_code` = $filtered_sub" : "")." ".(isset($filtered_adv) ? "AND `advsubcategory_code` = $filtered_adv" : "")." ".(isset($filtered_region) ? "AND `region` = $filtered_region" : "")." ".(isset($filtered_city) ? "AND `city` = $filtered_city" : "")." AND $cur_time - `timestamp` < `period` $filter_price_sql_string $filter_photo_sql_string $filter_exchange_sql_string $filter_gift_sql_string $filter_rank_sql_string LIMIT 5;";
         $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
         
         if (isset($filter_currency)){
            if ($filter_currency != $result[$c]["currency"])
               for ($c = 0; $c < count($result); $c++){
                  $result[$c]["price"] = $currency->convert($result[$c]["price"], $result[$c]["currency"], $filter_currency);
                  $result[$c]["currency_short_title"] = $currency->getShortTitle($filter_currency);
               }
         }
         else{
            for ($c = 0; $c < count($result); $c++){
               //$result[$c]["price"] *= $currency->getDefaultCoef();
               $result[$c]["currency_short_title"] = $currency->getShortTitle($result[$c]["currency"]);
            }
         }
               
         $this->params["curr_coef"] = isset($filter_currency) ? $currency->getCoef($filter_currency) : $currency->getDefaultCoef();
         $this->params["items_count"] = count($result); // количество элементов по данной категории всего
         $complete = array(); // новый массив для элментов текщей страницы
         $ipp = $this->params["catalog_items_per_page"];
         for ($i = $ipp*$p; $i < $ipp*($p+1); $i++){
            isset($result[$i]) ? array_push($complete, $result[$i]) : $i++;
         }
         array_push($complete, $this->params);
         return json_encode($complete);
      }
      
      public function searchHighlighted($category, $subcategory, $advsubcategory, $filter, $page, $query, $region, $city){
         global $currency;
         global $search;
         $filtered_cat = isset($category) ? intval($category) : null;
         $filtered_sub = isset($subcategory) ? intval($subcategory) : null;
         $filtered_adv = isset($advsubcategory) ? intval($advsubcategory) : null;
         $p = isset($page) ? intval($page) : 1;
         $filter_obj = isset($filter) ? json_decode(stripcslashes($filter), true) : null;
         $filter_currency = isset($filter_obj["filter_curr"]) ? $filter_obj["filter_curr"] : $currency->getDefaultCurrencyCode();
         $filter_rank_value = isset($filter_obj["filter_rank"]) ? intval($filter_obj["filter_rank"]) : null;
         $filter_rank_sql_string = "";
         $filter_price_floor = isset($filter_obj["filter_price_floor"]) ? intval($filter_obj["filter_price_floor"]) : null;
         $filter_price_ceil = isset($filter_obj["filter_price_ceil"]) ? intval($filter_obj["filter_price_ceil"]) : null;
         $filter_price_sql_string = "";
         $filter_photo = isset($filter_obj["filter_photo"]) ? intval($filter_obj["filter_photo"]) : null;
         $filter_photo_sql_string = "";
         $filter_gift = isset($filter_obj["filter_gift"]) ? intval($filter_obj["filter_gift"]) : null;
         $filter_gift_sql_string = "";
         $filter_exchange = isset($filter_obj["filter_exchange"]) ? intval($filter_obj["filter_exchange"]) : null;
         $filter_exchange_sql_string = "";
         $filtered_region = $region != -1 ? intval($region) : null;
         $filtered_city = $city != -1 ? intval($city) : null;
         $cur_time = time();
         
         $filter_rank_sql_string = "ORDER BY `highlighted` DESC";
            
         if (isset($filter_price_floor)){
            $coef = $currency->getCoef($filter_currency);
            $filter_price_sql_string = "AND `price`/(SELECT `currency`.`exchange` FROM `currency` WHERE `currency`.`code` = `catalog`.`currency`)*$coef BETWEEN $filter_price_floor AND $filter_price_ceil";
         }
            
         if (isset($filter_photo))
            $filter_photo_sql_string = $filter_photo == 1 ? "AND `photo` <> '' AND `photo` <> '[]' " : "";
            
         if (isset($filter_gift))
            $filter_gift_sql_string = $filter_gift == 1 ? "AND `gift` = 1 " : "AND `gift` <> 1 ";
            
         if (isset($filter_exchange))
            $filter_exchange_sql_string = $filter_exchange == 1 ? "AND `exchange` = 1 " : "AND `exchange` <> 1 ";
         
         $sql = "SELECT `num`, `title`, `timestamp`, `price`, `auction`, `photo`, `currency` FROM `catalog` WHERE `status` = 1 AND (`title` LIKE ".$search->constructSQL($query).") AND `highlighted` <> 0 ".(isset($filtered_cat) ? " AND `category_code` = $filtered_cat" : "")." ".(isset($filtered_sub) ? "AND `subcategory_code` = $filtered_sub" : "")." ".(isset($filtered_adv) ? "AND `advsubcategory_code` = $filtered_adv" : "")." ".(isset($filtered_region) ? "AND `region` = $filtered_region" : "")." ".(isset($filtered_city) ? "AND `city` = $filtered_city" : "")." AND $cur_time - `timestamp` < `period` $filter_price_sql_string $filter_photo_sql_string $filter_exchange_sql_string $filter_gift_sql_string $filter_rank_sql_string LIMIT 5;";
         $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
         //return $sql;
         if (isset($filter_currency)){
            if ($filter_currency != $result[$c]["currency"])
               for ($c = 0; $c < count($result); $c++){
                  $result[$c]["price"] = $currency->convert($result[$c]["price"], $result[$c]["currency"], $filter_currency);
                  $result[$c]["currency_short_title"] = $currency->getShortTitle($filter_currency);
               }
         }
         else{
            for ($c = 0; $c < count($result); $c++){
               //$result[$c]["price"] *= $currency->getDefaultCoef();
               $result[$c]["currency_short_title"] = $currency->getShortTitle($result[$c]["currency"]);
            }
         }
         
         $this->params["curr_coef"] = isset($filter_currency) ? $currency->getCoef($filter_currency) : $currency->getDefaultCoef();
         $this->params["items_count"] = count($result); // количество элементов по данной категории всего
         $complete = array(); // новый массив для элментов текщей страницы
         $ipp = $this->params["catalog_items_per_page"];
         for ($i = $ipp*$p; $i < $ipp*($p+1); $i++){
            isset($result[$i]) ? array_push($complete, $result[$i]) : $i++;
         }
         array_push($complete, $this->params);
         return json_encode($complete);
      }
      
   }
?>