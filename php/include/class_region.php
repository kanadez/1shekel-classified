<?php

class Region{
   public function test(){
      return "Region class testing OK!";
   }
   
   public function getCityByCode($code){
      global $db;
      $code_intvaled = intval($code);
      $sql = "SELECT `city_code`, `city_name` FROM `city` WHERE `city_code` = $code_intvaled;";
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      
      return json_encode($result);
   }
   
   public function getNameByCode($code){
      global $db;
      $code_intvaled = intval($code);
      $sql = "SELECT `region_code`, `region_name` FROM `region` WHERE `region_code` = $code_intvaled;";
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      
      return json_encode($result);
   }
   
   public function getRegionData(){
      $sql = "SELECT * FROM `region` ORDER BY `num`;";
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      
      return json_encode($result);
   }
   
   public function getMetroData(){
      global $db;
      
      $sql = "SELECT * FROM `metro` ORDER BY `num`";
      return json_encode($db->db_fetchall_array($sql, __LINE__, __FILE__));
   }
   
   public function getCityData(){
      $sql = "SELECT * FROM `city` ORDER BY `num`";
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      
      return json_encode($result);
   }
   
   public function getCityNameByCode($code){
      $sql = sprintf("SELECT `city_name` FROM `city` WHERE `city_code` = %d",
         mysql_real_escape_string($code));
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      
      return $result["city_name"];
   }
}

?>