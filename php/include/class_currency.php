<?php

class Currency{
   function __construct() {
      // class init code here
      global $db;
      
      $sql = "SELECT `num`, `symbol`, `last_updated` FROM `currency`;";
      $result = $db->db_fetchall_array($sql, __LINE__, __FILE__);
      
      if (time() - $result[0]["last_updated"] > 86400){
         $request_string = "https://query.yahooapis.com/v1/public/yql?q=select+*+from+yahoo.finance.xchange+where+pair+=+%22";
         
         for ($i = 0; $i < count($result); $i++)
            if ($i < count($result)-1)
               $request_string .= "USD".$result[$i]["symbol"].",";
            else $request_string .= "USD".$result[$i]["symbol"];
            
         $request_string .= "%22&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=";
         $myCurl = curl_init();
         
         curl_setopt_array($myCurl, array(
            CURLOPT_URL => $request_string,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => false
         ));
         
         $response = curl_exec($myCurl);
         curl_close($myCurl);
         
         $array = json_decode(stripcslashes($response), true);
         $rates = $array["query"]["results"]["rate"];
         
         for ($z = 0; $z < count($rates); $z++){
            $request_string2 = "UPDATE `currency` SET `exchange` = ";
            $rate = $rates[$z]["Rate"];
            $names = explode("/", $rates[$z]["Name"]);
            $name = $names[1];
            $request_string2 .= "$rate WHERE `symbol` = '$name';";
            $db->db_query($request_string2, __LINE__, __FILE__);
            
            $request_string3 = "UPDATE `currency` SET `last_updated` = ".time()." WHERE `symbol` = '".$name."';";
            $db->db_query($request_string3, __LINE__, __FILE__);
         }
      }
   }
   
   public function convert($item_price, $item_currency, $filter_currency){
      return round($item_price/$this->getCoef($item_currency)*$this->getCoef($filter_currency), 2);
   }
   
   public function get($currency_code){
      $sql = "SELECT `symbol`, `title` FROM `currency` WHERE `code` = $currency_code LIMIT 1;";
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      return json_encode($result);
   }
   
   public function getList(){
      $sql = "SELECT `code`, `symbol`, `title`, `short_title`, `default` FROM `currency`;";
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      return json_encode($result);
   }
   
   public function getCoef($code){
      $sql = "SELECT `exchange` FROM `currency` WHERE `code` = $code;";
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      return $result["exchange"];
   }
   
   public function getShortTitle($code){
      $sql = "SELECT `short_title` FROM `currency` WHERE `code` = $code;";
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      return $result["short_title"];
   }
   
   public function getDefaultShortTitle(){
      $sql = "SELECT `short_title` FROM `currency` WHERE `default` = 1;";
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      return $result["short_title"];
   }
   
   public function getDefaultCoef(){
      $sql = "SELECT `exchange` FROM `currency` WHERE `default` = 1;";
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      return $result["exchange"];
   }
   
   public function getDefaultCurrencyCode(){
      global $db;
      
      $sql = "SELECT `code` FROM `currency` WHERE `default` = 1;";
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      return $result["code"];
   }
}

?>