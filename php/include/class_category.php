<?php

class Category{
   public function getEverything(){
      global $db;
      $complete = array();
      
      $sql = "SELECT `category_code`, `category_name` FROM `category`;";
      array_push($complete, $db->db_fetchall_array($sql, __LINE__, __FILE__));
      
      $sql = "SELECT `subcategory_code`, `subcategory_name`, `category_code` FROM `subcategory`";
      array_push($complete, $db->db_fetchall_array($sql, __LINE__, __FILE__));
      
      $sql = "SELECT `advsubcategory_code`, `advsubcategory_name`, `subcategory_code`, `category_code` FROM `advsubcategory`";
      array_push($complete, $db->db_fetchall_array($sql, __LINE__, __FILE__));
      
      return json_encode($complete);
   }
   
   public function getCategories(){
      $sql = "SELECT `category_code`, `category_name` FROM `category`;";
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      $complete = array();
      for ($i = 0; $i < count($result); $i++)
         $complete[$result[$i]["category_code"]] = $result[$i]["category_name"];
      return json_encode($complete);
   }
   
   public function getSubCategories($category){
      $sql = sprintf("SELECT `subcategory_code`, `subcategory_name` FROM `subcategory` WHERE `category_code` = %d;",
         mysql_real_escape_string($category));
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      $complete = array();
      for ($i = 0; $i < count($result); $i++)
         $complete[$result[$i]["subcategory_code"]] = $result[$i]["subcategory_name"];
      return json_encode($complete);
   }
   
   public function getAdvSubCategories($category, $subcategory){
      $sql = sprintf("SELECT `advsubcategory_code`, `advsubcategory_name` FROM `advsubcategory` WHERE `category_code` = %d AND `subcategory_code` = %d;",
         mysql_real_escape_string($category),
         mysql_real_escape_string($subcategory));
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      $complete = array();
      for ($i = 0; $i < count($result); $i++)
         $complete[$result[$i]["advsubcategory_code"]] = $result[$i]["advsubcategory_name"];
      return json_encode($complete);
   }
   
   public function getSidePanelCategories($category, $subcategory){
      if ($subcategory != null){
         $sql = sprintf("SELECT `advsubcategory_code`, `advsubcategory_name` FROM `advsubcategory` WHERE `category_code` = %d AND `subcategory_code` = %d;",
            mysql_real_escape_string($category),
            mysql_real_escape_string($subcategory));
         $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      }
      elseif ($category != null){
         $sql = sprintf("SELECT `subcategory_code`, `subcategory_name` FROM `subcategory` WHERE `category_code` = %d;",
            mysql_real_escape_string($category));
         $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      }
      else{
         $sql = "SELECT `category_code`, `category_name` FROM `category`;";
         $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      }
      
      return json_encode($result);
   }
   
   public function getUserCatalogCategories($user_num){
      global $db;
      
      $user = intval($user_num);
      $sql = "SELECT `category_code` FROM `catalog` WHERE `author` = $user;";
      $result = $db->db_fetchall_array($sql, __LINE__, __FILE__);
      
      return json_encode($result);
   }
   
   public function getUserCatalogCategoryNames($category_codes_object){
      $categories = json_decode(stripcslashes($category_codes_object), true);
      $sql = "SELECT `category_name`, `category_code` FROM `category` WHERE `category_code` IN (";
      for ($i = 0; $i < count($categories); $i++){
         $filtered = intval($categories[$i]);
         $sql .= $filtered.($i != count($categories)-1 ? "," : "");
      }
      $sql .= ");";
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      
      return json_encode($result);
   }
}

?>