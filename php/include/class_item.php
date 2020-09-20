<?php

class Item{
   public function edit($json_string, $temp_item_num){
      global $db;
      global $user;
      global $mail;
      
      $json = json_decode(stripcslashes($json_string), true);
      $sql = sprintf("UPDATE `catalog` SET
         `title` = '%s', 
         `description`= '%s', 
         `full_description` = '%s', 
         `price` = %d, 
         `condition` = %d, 
         `auction` = %d, 
         `gift` = %d, 
         `exchange` = %d, 
         `currency` = %d,
         `category_code` = %d,
         `subcategory_code` = %d,
         `advsubcategory_code` = %d,
         `city` = %d,
         `metro` = %d,
         `address` = '%s',
         `temporary` = 0,
         `period` = %d,
         `timestamp` = %d WHERE `num` = %d ".($_SESSION["user_num"] == 1 ? "" : "AND `author` = ".$_SESSION["user_num"].";"), // вставляем пост текущему юзеру в таблицу
         mysql_real_escape_string($json["title"]),
         mysql_real_escape_string($json["description"]),
         mysql_real_escape_string($json["full_description"]),
         mysql_real_escape_string($json["price"]),
         mysql_real_escape_string($json["condition"]),
         mysql_real_escape_string($json["auction"]),
         mysql_real_escape_string($json["gift"]),
         mysql_real_escape_string($json["exchange"]),
         mysql_real_escape_string($json["currency"]),
         mysql_real_escape_string($json["category_code"]),
         mysql_real_escape_string($json["subcategory_code"]),
         mysql_real_escape_string($json["advsubcategory_code"]),
         mysql_real_escape_string($json["city"]),
         mysql_real_escape_string(-1),
         mysql_real_escape_string($json["address"]),
         mysql_real_escape_string($json["period"]),
         mysql_real_escape_string(time()),
         mysql_real_escape_string($temp_item_num));
         
      return $db->db_query($sql, __LINE__, __FILE__);
   }
   
   public function deleteTempPhoto($item, $photo){
      global $db;
      
      $sql = sprintf("SELECT `photo` FROM `catalog` WHERE `num` = %d;", mysql_real_escape_string($item));
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      
      $photos = json_decode(stripcslashes($result["photo"]), true);
      $photos_new = array();
      
      for ($i = 0; $i < count($photos); $i++){
         if ($photos[$i] != $photo)
            array_push($photos_new, $photos[$i]);
      }
      
      $sql = sprintf("UPDATE `catalog` SET `photo` = '%s' WHERE `num` = %d;",
         mysql_real_escape_string(json_encode($photos_new)),
         mysql_real_escape_string($item));
      $db->db_query($sql, __LINE__, __FILE__);
      
      return json_encode($photos_new);
   }
   
   public function report($item, $report_type){
      global $db;
      
      $sql = sprintf("INSERT INTO `reported` (`item`,`report`) VALUES (%d, %d);",
         mysql_real_escape_string($item),
         mysql_real_escape_string($report_type));
      
      return $db->db_query($sql, __LINE__, __FILE__);
   }
   
   public function getLoginedUserForNewitem(){
      global $db;
      $session_user_num = $_SESSION["user_num"];
      
      if (isset($session_user_num)){
         $sql = "SELECT `num`, `status`, `name`, `email`, `phone`, `hide_phone`, `skype`, (SELECT `city`.`city_name` FROM `city` WHERE `city`.`city_code` = `user`.`city`) AS `city_name`, `city`, `address` FROM `user` WHERE `num` = $session_user_num;";
         return json_encode($db->db_fetchone_array($sql, __LINE__, __FILE__));
      }
      else return -1;
   }
   
   public function getUserForNewitem($item, $token){
      global $db;
      
      if (isset($session_user_num)){
         $sql = "SELECT `num`, `status`, `name`, `email`, `phone`, `hide_phone`, `skype` FROM `user` WHERE `num` = $session_user_num;";
         return json_encode($db->db_fetchone_array($sql, __LINE__, __FILE__));
      }
      else{
         $sql = "SELECT `num`, `status`, `name`, `email`, `phone`, `hide_phone`, `skype` FROM `user` WHERE `num` = (SELECT `author` FROM `catalog` WHERE `num` = $item AND `token` = '$token');";
         return json_encode($db->db_fetchone_array($sql, __LINE__, __FILE__));
      }
   }
   
   public function addFromPreview($item, $token){
      global $db;
      global $mail;
      global $user;
      
      if ($token != -1){
         $sql = sprintf("SELECT `email` FROM `user` WHERE `num` = (SELECT `author` FROM `catalog` WHERE `num` = %d AND `token` = '%s');",
            mysql_real_escape_string($item),
            mysql_real_escape_string($token));
         $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
         
         if (!isset($_SESSION["user_num"])){
            $passwd = $user->generatePassword();
            $mail->sendRegisterFromNewEmail($result['email'], $passwd, $token);

            $sql = sprintf("UPDATE `user` SET `temporary` = 0, `passwd` = '%s' WHERE `num` = (SELECT `author` FROM `catalog` WHERE `num` = %d AND `token` = '%s');",
               mysql_real_escape_string(md5($passwd)),
               mysql_real_escape_string($item),
               mysql_real_escape_string($token));
            $db->db_query($sql, __LINE__, __FILE__);

            $sql = sprintf("UPDATE `catalog` SET `temporary` = 0 WHERE `num` = %d AND `token` = '%s';",
               mysql_real_escape_string($item),
               mysql_real_escape_string($token));
            $db->db_query($sql, __LINE__, __FILE__);
         }
         
         $email_token = md5(rand());
         $mail->sendNewItemEmail($result['email'], $item, $email_token);
         $sql = sprintf("UPDATE `user` SET `item_token` = '%s' WHERE `email` = '%s';",
            mysql_real_escape_string($email_token),
            mysql_real_escape_string($result['email']));
        $db->db_query($sql, __LINE__, __FILE__);
         
         return 0;
      }else return -1;
   }
   
   public function preview($json_string, $temp_item_num, $author, $token){ // автор еще в post.php подставляется. если залогинен то его номрер, елси нет то -1
      global $db;
      global $user;
      $json = json_decode(stripcslashes($json_string), true);
      if ($json["author"] == -1)
         $_author = $user->createNew($json["person_status"], $json["person_name"], $json["person_email"], $json["person_phone"], $json["person_phone_hide"], $json["person_skype"], 1);
      else $_author = $json["author"];
      
      if (isset($_SESSION["user_num"]))
         $_author = $_SESSION["user_num"];
      
      if ($temp_item_num == -1){
         $sql = sprintf("INSERT INTO `catalog` (
            `type`, 
            `status`, 
            `title`, 
            `description`, 
            `full_description`, 
            `price`, 
            `condition`, 
            `auction`, 
            `gift`, 
            `exchange`, 
            `currency`,
            `author`,
            `category_code`,
            `subcategory_code`,
            `advsubcategory_code`,
            `region`,
            `city`,
            `metro`,
            `address`,
            `temporary`,
            `period`,
            `token`,
            `timestamp`) VALUES (%d, %d, '%s', '%s', '%s', %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, (SELECT `city`.`region_code` FROM `city` WHERE `city`.`city_code` = %d), %d, %d, '%s', 1, %d, '%s', %d);", // вставляем пост текущему юзеру в таблицу
            mysql_real_escape_string(1), // item type 1=продам, 2-обмен, 3=даром
            mysql_real_escape_string(1),
            mysql_real_escape_string($json["title"]),
            mysql_real_escape_string($json["description"]),
            mysql_real_escape_string($json["full_description"]),
            mysql_real_escape_string($json["price"]),
            mysql_real_escape_string($json["condition"]),
            mysql_real_escape_string($json["auction"]),
            mysql_real_escape_string($json["gift"]),
            mysql_real_escape_string($json["exchange"]),
            mysql_real_escape_string($json["currency"]),
            mysql_real_escape_string($_author),
            mysql_real_escape_string($json["category_code"]),
            mysql_real_escape_string($json["subcategory_code"]),
            mysql_real_escape_string($json["advsubcategory_code"]),
            mysql_real_escape_string($json["city"]),
            mysql_real_escape_string($json["city"]),
            mysql_real_escape_string(-1),
            mysql_real_escape_string($json["address"]),
            mysql_real_escape_string($json["period"]),
            mysql_real_escape_string($token),
            mysql_real_escape_string(time()));
            
            if ($db->db_query($sql, __LINE__, __FILE__) == 1)
               return mysql_insert_id(); // its ok
            else return -1; // something wrong
      }
      else{ 
         $sql = sprintf("UPDATE `catalog` SET
            `type` = %d,
            `status` = %d, 
            `title` = '%s', 
            `description`= '%s', 
            `full_description` = '%s', 
            `price` = %d, 
            `condition` = %d, 
            `auction` = %d, 
            `gift` = %d, 
            `exchange` = %d, 
            `currency` = %d,
            `category_code` = %d,
            `author` = %d,
            `subcategory_code` = %d,
            `advsubcategory_code` = %d,
            `region` = (SELECT `city`.`region_code` FROM `city` WHERE `city`.`city_code` = %d),
            `city` = %d,
            `metro` = %d,
            `address` = '%s',
            `temporary` = 1,
            `period` = %d,
            `timestamp` = %d WHERE `num` = %d AND `token` = '%s';", // вставляем пост текущему юзеру в таблицу
            mysql_real_escape_string(1), // item type 1=продам, 2-обмен, 3=даром
            mysql_real_escape_string(1),
            mysql_real_escape_string($json["title"]),
            mysql_real_escape_string($json["description"]),
            mysql_real_escape_string($json["full_description"]),
            mysql_real_escape_string($json["price"]),
            mysql_real_escape_string($json["condition"]),
            mysql_real_escape_string($json["auction"]),
            mysql_real_escape_string($json["gift"]),
            mysql_real_escape_string($json["exchange"]),
            mysql_real_escape_string($json["currency"]),
            mysql_real_escape_string($json["category_code"]),
            mysql_real_escape_string($_author),
            mysql_real_escape_string($json["subcategory_code"]),
            mysql_real_escape_string($json["advsubcategory_code"]),
            mysql_real_escape_string($json["city"]),
            mysql_real_escape_string($json["city"]),
            mysql_real_escape_string(-1),
            mysql_real_escape_string($json["address"]),
            mysql_real_escape_string($json["period"]),
            mysql_real_escape_string(time()),
            mysql_real_escape_string($temp_item_num),
            mysql_real_escape_string($token));
            
            $result1 = $db->db_query($sql, __LINE__, __FILE__);
            
            $sql = sprintf("UPDATE `user` SET
            `status` = %d, 
            `name` = '%s',
            `email` = '%s',
            `phone` = '%s',
            `hide_phone` = %d,
            `skype` = '%s' WHERE `num` = (SELECT `author` from `catalog` WHERE `num` = %d AND `token` = '%s');", // вставляем пост текущему юзеру в таблицу
            mysql_real_escape_string($json["person_status"]),
            mysql_real_escape_string($json["person_name"]),
            mysql_real_escape_string($json["person_email"]),
            mysql_real_escape_string($json["person_phone"]),
            mysql_real_escape_string($json["person_phone_hide"]),
            mysql_real_escape_string($json["person_skype"]),
            mysql_real_escape_string($temp_item_num),
            mysql_real_escape_string($token));
            
            $result2 = $db->db_query($sql, __LINE__, __FILE__);
            
            if ($result1*$result2 == 1)
               return $temp_item_num; // its ok
            else return -1; // something wrong
      }
   }
   
   public function add($json_string, $temp_item_num, $author, $token){
      global $db;
      global $user;
      global $mail;
      
      $json = json_decode(stripcslashes($json_string), true);
      
      if ($json["author"] == -1)
         $_author = $user->createNew($json["person_status"], $json["person_name"], $json["person_email"], $json["person_phone"], $json["person_phone_hide"], $json["person_skype"], 0);
      else $_author = $json["author"];
      
      if (isset($_SESSION["user_num"]))
         $_author = $_SESSION["user_num"];
      
      if ($temp_item_num == -1){
         $sql = sprintf("INSERT INTO `catalog` (
            `type`, 
            `status`, 
            `title`, 
            `description`, 
            `full_description`, 
            `price`, 
            `condition`, 
            `auction`, 
            `gift`, 
            `exchange`, 
            `currency`,
            `author`,
            `category_code`,
            `subcategory_code`,
            `advsubcategory_code`,
            `region`,
            `city`,
            `metro`,
            `address`,
            `temporary`,
            `period`,
            `token`,
            `timestamp`) VALUES (%d, %d, '%s', '%s', '%s', %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, (SELECT `city`.`region_code` FROM `city` WHERE `city`.`city_code` = %d), %d, %d, '%s', 0, %d, '%s', %d);", // вставляем пост текущему юзеру в таблицу
            mysql_real_escape_string(1),
            mysql_real_escape_string(1),
            mysql_real_escape_string($json["title"]),
            mysql_real_escape_string($json["description"]),
            mysql_real_escape_string($json["full_description"]),
            mysql_real_escape_string($json["price"]),
            mysql_real_escape_string($json["condition"]),
            mysql_real_escape_string($json["auction"]),
            mysql_real_escape_string($json["gift"]),
            mysql_real_escape_string($json["exchange"]),
            mysql_real_escape_string($json["currency"]),
            mysql_real_escape_string($_author),
            mysql_real_escape_string($json["category_code"]),
            mysql_real_escape_string($json["subcategory_code"]),
            mysql_real_escape_string($json["advsubcategory_code"]),
            mysql_real_escape_string($json["city"]),
            mysql_real_escape_string($json["city"]),
            mysql_real_escape_string(-1),
            mysql_real_escape_string($json["address"]),
            mysql_real_escape_string($json["period"]),
            mysql_real_escape_string($token),
            mysql_real_escape_string(time()));
            
            if ($db->db_query($sql, __LINE__, __FILE__) == 1){
                $email_token = md5(rand());
                $mail->sendNewItemEmail($json["person_email"], mysql_insert_id(), $email_token);
                $item_num = mysql_insert_id();
                
                $sql = sprintf("UPDATE `user` SET `item_token` = '%s' WHERE `email` = '%s';",
                    mysql_real_escape_string($email_token),
                    mysql_real_escape_string($json["person_email"]));
                 $db->db_query($sql, __LINE__, __FILE__);
                
               return $item_num; // its ok
            }
            else return -1; // something wrong
      }
      else{ 
         $sql = sprintf("UPDATE `catalog` SET
            `type` = %d,
            `status` = %d, 
            `title` = '%s', 
            `description`= '%s', 
            `full_description` = '%s', 
            `price` = %d, 
            `condition` = %d, 
            `auction` = %d, 
            `gift` = %d, 
            `exchange` = %d, 
            `currency` = %d,
            `author` = %d,
            `category_code` = %d,
            `subcategory_code` = %d,
            `advsubcategory_code` = %d,
            `region` = (SELECT `city`.`region_code` FROM `city` WHERE `city`.`city_code` = %d),
            `city` = %d,
            `metro` = %d,
            `address` = '%s',
            `temporary` = 0,
            `period` = %d,
            `timestamp` = %d WHERE `num` = %d AND `token` = '%s';", // вставляем пост текущему юзеру в таблицу
            mysql_real_escape_string(1),
            mysql_real_escape_string(1),
            mysql_real_escape_string($json["title"]),
            mysql_real_escape_string($json["description"]),
            mysql_real_escape_string($json["full_description"]),
            mysql_real_escape_string($json["price"]),
            mysql_real_escape_string($json["condition"]),
            mysql_real_escape_string($json["auction"]),
            mysql_real_escape_string($json["gift"]),
            mysql_real_escape_string($json["exchange"]),
            mysql_real_escape_string($json["currency"]),
            mysql_real_escape_string($_author),
            mysql_real_escape_string($json["category_code"]),
            mysql_real_escape_string($json["subcategory_code"]),
            mysql_real_escape_string($json["advsubcategory_code"]),
            mysql_real_escape_string($json["city"]),
            mysql_real_escape_string($json["city"]),
            mysql_real_escape_string(-1),
            mysql_real_escape_string($json["address"]),
            mysql_real_escape_string($json["period"]),
            mysql_real_escape_string(time()),
            mysql_real_escape_string($temp_item_num),
            mysql_real_escape_string($token));
         $result1 = $db->db_query($sql, __LINE__, __FILE__);
         
         $sql = sprintf("UPDATE `user` SET `temporary` = 0 WHERE `num` = (SELECT `author` from `catalog` WHERE `num` = %d AND `token` = '%s');", // вставляем пост текущему юзеру в таблицу
            mysql_real_escape_string($temp_item_num),
            mysql_real_escape_string($token));
         $result2 = $db->db_query($sql, __LINE__, __FILE__);
         
         $sql = sprintf("SELECT `email` from `user` WHERE `num` = (SELECT `author` from `catalog` WHERE `num` = %d AND `token` = '%s');", // вставляем пост текущему юзеру в таблицу
            mysql_real_escape_string($temp_item_num),
            mysql_real_escape_string($token));
         $result3 = $db->db_fetchone_array($sql, __LINE__, __FILE__);
         
         if (!isset($_SESSION["user_num"])){
            $passwd = $user->generatePassword();
            $mail->sendRegisterFromNewEmail($result['email'], $passwd, $token);
            $sql = sprintf("UPDATE `user` SET `temporary` = 0, `passwd` = '%s' WHERE `num` = (SELECT `author` FROM `catalog` WHERE `num` = %d AND `token` = '%s');",
               mysql_real_escape_string(md5($passwd)),
               mysql_real_escape_string($temp_item_num),
               mysql_real_escape_string($token));
            $db->db_query($sql, __LINE__, __FILE__);
         }
            
         if ($result1*$result2 == 1){
             $email_token = md5(rand());
             $mail->sendNewItemEmail($result3['email'], $temp_item_num, $email_token);
             
             $sql = sprintf("UPDATE `user` SET `item_token` = '%s' WHERE `email` = '%s';",
               mysql_real_escape_string($email_token),
               mysql_real_escape_string($result3['email']));
            $db->db_query($sql, __LINE__, __FILE__);
             
            return $temp_item_num; // its ok
         }
         else return -1; // something wrong
      }
   }
   
   public function test($parameter){
      return "Region class testing OK! parameter = ".$parameter;
   }
   
   public function getPhoto($num){
      global $db;
      
      $sql = sprintf("SELECT `photo` FROM `catalog` WHERE `num` = %d;",
         mysql_real_escape_string($num));
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      $a = json_decode(stripcslashes($result["photo"]), true);
      return $a["1"];
   }
   
   public function getDescription($num){
      global $db;
      
      $sql = sprintf("SELECT `description` FROM `catalog` WHERE `num` = %d;",
         mysql_real_escape_string($num));
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      return $result["description"];
   }
   
   public function getTitle($num){
      global $db;
      
      $sql = sprintf("SELECT `title` FROM `catalog` WHERE `num` = %d;",
         mysql_real_escape_string($num));
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      return $result["title"];
   }
   
   public function getCrumbs($category, $subcategory, $advsubcategory){
      $obj = array();
      
      $sql = sprintf("SELECT `category_name` FROM `category` WHERE `category_code` = %d;",
         mysql_real_escape_string($category));
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      $obj["category"] = $result["category_name"];
      
      $sql = sprintf("SELECT `subcategory_name` FROM `subcategory` WHERE `subcategory_code` = %d AND `category_code` = %d;",
         mysql_real_escape_string($subcategory),
         mysql_real_escape_string($category));
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      $obj["subcategory"] = $result["subcategory_name"];
      
      $sql = sprintf("SELECT `advsubcategory_name` FROM `advsubcategory` WHERE `subcategory_code` = %d AND `category_code` = %d AND `advsubcategory_code` = %d;",
         mysql_real_escape_string($subcategory),
         mysql_real_escape_string($category),
         mysql_real_escape_string($advsubcategory));
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      $obj["advsubcategory"] = $result["advsubcategory_name"];
      
      return json_encode($obj);
   }
   
   public function getDataForNewForm($item_num){
      global $db;
      $sql = sprintf("SELECT * FROM `catalog` WHERE `num` = %d;",
         mysql_real_escape_string($item_num));
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      return json_encode($result);
   }
   
   public function getItemData($item_num, $filter_curr){
      global $currency;
      global $region;
      
      $sql = sprintf("SELECT * FROM `catalog` WHERE `num` = %d;",
         mysql_real_escape_string($item_num));
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      $result["currency_code"] = $result["currency"];
      $result["currency"] = $filter_curr != null ? $currency->getShortTitle($filter_curr) : $currency->getShortTitle($result["currency_code"]);
      
      if ($filter_curr != null && $filter_curr != $result["currency_code"])
            $result["price"] = round($result["price"]/$currency->getCoef($result["currency_code"])*$currency->getCoef($filter_curr), 2);
            
      $result["specifics"] = $this->getSpecifics($item_num, $result["category_code"]);
      $result["city_name"] = $region->getCityNameByCode($result["city"]);
      //$result["specifics_idioma"] = $this->getSpecificsIdioma($item_num, $result["category_code"]);
      return json_encode($result);
   }
   
   public function getCrossLinks($item_num){
      global $currency;
      global $db;
      
      $sql = sprintf("SELECT `category_code`, `subcategory_code` FROM `catalog` WHERE `num` = %d;",
         mysql_real_escape_string($item_num));
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      $category = $result["category_code"];
      $subcategory = $result["subcategory_code"];
      
      $sql = sprintf("SELECT `num`, `title`, `price`, `photo`, `currency` FROM `catalog` WHERE `category_code` = %d AND `subcategory_code` = %d AND `num` <> %d AND `status` = 1  AND ".time()."-`timestamp` < `period` LIMIT 3;",
         mysql_real_escape_string($category),
         mysql_real_escape_string($subcategory),
         mysql_real_escape_string($item_num));
      $result = $db->db_fetchall_array($sql, __LINE__, __FILE__);
      
      if (count($result) > 0){
            if (count($result) < 3)
                for ($i = 0; $i < count($result); $i++)
                   $result[$i]["currency_short_title"] = $currency->getShortTitle($result[$i]["currency"]);
            else 
                for ($i = 0; $i < 3; $i++)
                   $result[$i]["currency_short_title"] = $currency->getShortTitle($result[$i]["currency"]);
                
        shuffle($result);
        return json_encode($result);
      }else return 0;
      
   }
   
   private function getCurrency($currency_code){
      $sql = "SELECT `symbol` FROM `currency` WHERE `code` = $currency_code LIMIT 1;";
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      return $result["symbol"];
   }
   
   private function getSpecifics($item_num, $category_code){
      $sql = "SELECT * FROM `specifics_$category_code` WHERE `item` = $item_num LIMIT 1;";
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      return json_encode($result[0]);
   }
   
   /*private function getSpecificsIdioma($item_num, $category_code){
      global $idioma;
      $sql = "SELECT * FROM `specifics_$category_code` WHERE `item` = $item_num LIMIT 1;";
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      $xtracted = array();
      foreach ($result[0] as $key => $value)
         array_push($xtracted, $key);
      return $idioma->getVariables($xtracted);
   }*/
}

?>