<?php

class User{
   private $params;
   
   public function register($user_data){
      global $db, $mail;
      $data = json_decode(stripcslashes($user_data), true);
      $token = md5(rand());
      
      //return $data["email"];
      $sql = sprintf("SELECT `num` FROM `user` WHERE `email` = '%s';",
         mysql_real_escape_string($data["email"]));
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      //return $result["num"];
      if (count($result) == 0){
         $sql = sprintf("INSERT INTO `user` (`name`, `status`, `email`, `passwd`, `phone`, `hide_phone`, `email_token`) VALUES ('%s', %d, '%s','%s', '%s', %d, '%s');",
            mysql_real_escape_string($data["name"]),
            mysql_real_escape_string($data["status"]),
            mysql_real_escape_string($data["email"]),
            mysql_real_escape_string(md5($data["passwd"])),
            mysql_real_escape_string($data["phone"]),
            mysql_real_escape_string($data["phone_hide"]),
            mysql_real_escape_string($token));
            
         if ($db->db_query($sql, __LINE__, __FILE__) == 1){
             $mail->sendRegisterEmail($data["email"], mysql_insert_id(), $token);
            
             return mysql_insert_id(); // its ok
         }
         else return -1; // something wrong
      }else return -2;
   }
   
   public function loginVK($id, $fullname, $phone, $photo, $city){
      global $db;
      
      $sql = sprintf("SELECT `num` FROM `user` WHERE `vk_id` = %d;",
         mysql_real_escape_string($id));
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      
      //return count($result);
      
      if (count($result) != 0){
         $_SESSION['user_num'] = $result["num"];
         $_SESSION['user_sn'] = 1;
         
         return 1;
      }
      else{
         $sql = sprintf("INSERT INTO `user` (`name`, `vk_id`, `phone`, `photo_100`) VALUES ('%s', %d, '%s', '%s');",
         mysql_real_escape_string($fullname),
         mysql_real_escape_string($id),
         mysql_real_escape_string($phone),
         mysql_real_escape_string($photo));
         $db->db_query($sql, __LINE__, __FILE__);
         $_SESSION['user_sn'] = 1;
         $_SESSION['user_num'] = mysql_insert_id();
         
         if ($city != "")
            $this->updateSNCity($_SESSION['user_num'], $city);
         
         return 1;
      }
   }
   
   public function loginOK($id, $fullname, $phone, $photo, $city){
      global $db;
      
      $sql = sprintf("SELECT `num` FROM `user` WHERE `ok_id` = '%s';",
         mysql_real_escape_string($id));
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      
      //return count($result);
      
      if (count($result) != 0){
         $_SESSION['user_num'] = $result["num"];
         $_SESSION['user_sn'] = 1;
         
         return 1;
      }
      else{
         $sql = sprintf("INSERT INTO `user` (`name`, `ok_id`, `phone`, `photo_100`) VALUES ('%s', '%s', '%s', '%s');",
         mysql_real_escape_string($fullname),
         mysql_real_escape_string($id),
         mysql_real_escape_string($phone),
         mysql_real_escape_string($photo));
         $db->db_query($sql, __LINE__, __FILE__);
         $_SESSION['user_sn'] = 1;
         $_SESSION['user_num'] = mysql_insert_id();
         
         if ($city != "")
            $this->updateSNCity($_SESSION['user_num'], $city);
         
         return 1;
      }
   }
   
   public function createNew($status, $name, $email, $phone, $phonehide, $skype, $temporary){
      global $db;
      global $mail;
      $passwd = $this->generatePassword();
      $token = md5(rand());
      
      if (!$temporary){
         //$mail->sendEmail($email, "Регистрация на neon.metateg.pro", "Ваш пароль: ".$passwd);
         $mail->sendRegisterFromNewEmail($email, $passwd, $token);
         $sql = sprintf("INSERT INTO `user` (`name`, `status`, `email`, `phone`, `hide_phone`, `skype`, `passwd`, `email_token`) VALUES ('%s', %d, '%s', '%s', %d, '%s', '%s', '%s');",
            mysql_real_escape_string($name),
            mysql_real_escape_string($status),
            mysql_real_escape_string($email),
            mysql_real_escape_string($phone),
            mysql_real_escape_string($phonehide),
            mysql_real_escape_string($skype),
            mysql_real_escape_string(md5($passwd)),
            mysql_real_escape_string($token));
            
         $db->db_query($sql, __LINE__, __FILE__);
         return mysql_insert_id(); // its ok
         //else return -1; // something wrong
      }
      else{
         $sql = sprintf("INSERT INTO `user` (`name`, `status`, `email`, `phone`, `hide_phone`, `skype`, `temporary`) VALUES ('%s', %d, '%s', '%s', %d, '%s', 1);",
            mysql_real_escape_string($name),
            mysql_real_escape_string($status),
            mysql_real_escape_string($email),
            mysql_real_escape_string($phone),
            mysql_real_escape_string($phonehide),
            mysql_real_escape_string($skype));
            
         if ($db->db_query($sql, __LINE__, __FILE__) == 1)
            return mysql_insert_id(); // its ok
         else return -1; // something wrong
      }
   }
   
   public function generatePassword($length = 8){
      $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      $count = mb_strlen($chars);
      
      for ($i = 0, $result = ''; $i < $length; $i++) {
         $index = rand(0, $count - 1);
         $result .= mb_substr($chars, $index, 1);
      }
      
      return $result;
   }
   
   public function getPhoto($user_num){
      $sql = sprintf("SELECT `photo_100` FROM `user` WHERE `num` = %d;",
         mysql_real_escape_string($user_num));
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      return $result["photo_100"];
   }
   
   public function getMySelf($session_user_num){
      global $region;
      global $db;
      
      if (isset($session_user_num) && $session_user_num != -1){
         $sql = sprintf("SELECT * FROM `user` WHERE `num` = %d;",
            mysql_real_escape_string($session_user_num));
         $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
         $result["city_code"] = $result["city"];
         $result["city"] = $region->getCityNameByCode($result["city"]);
         
         return json_encode($result);
      }
      else return -1;
   }
   
   public function getNameByNum($session_user_num){
      global $db;
      
         $sql = sprintf("SELECT `name` FROM `user` WHERE `num` = %d;",
            mysql_real_escape_string($session_user_num));
         $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
         return $result["name"];
   }
   
   public function get($user_num){
      global $region;
      
      $sql = sprintf("SELECT `num`, `name`, `photo_100`, `last_seen`, `coins`, `phone`, `city`, `address`, `email`, `vk_id`, `status` FROM `user` WHERE `num` = %d;",
         mysql_real_escape_string($user_num));
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      $result["city_code"] = $result["city"];
      $result["city"] = $region->getCityNameByCode($result["city"]);
      
      return json_encode($result);
   }
   
   public function getAlot($user_nums_string){
      $user_nums = json_decode(stripcslashes($user_nums_string), true);
      $sql = "SELECT * from `user` where `num` IN (";
      for ($i = 0; $i < count($user_nums); $i++)
         if ($i < count($user_nums)-1)
            $sql .= "'".$user_nums[$i]."',";
         else $sql .= "'".$user_nums[$i]."'";
      $sql .= ")";
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      
      $result2 = array();
      for ($i = 0; $i < count($result); $i++){
         $u = $result[$i]["num"];
         $result2[$u] = $result[$i];
      }
      
      return json_encode($result2);
   }
   
   public function getCatalog($user_num, $catalog_category, $catalog_page){
      global $db;
      global $parameter;
      global $currency;
      $this->params["catalog_items_per_page"] = $parameter->get("catalog_items_per_page");
      
      $user = intval($user_num);
      $filtered_cat = intval($catalog_category);
      $p = isset($catalog_page) ? intval($catalog_page) : 1;
      $sql = "SELECT `num`, `title`, `timestamp`, `price`, `auction`, `photo`, `currency` FROM `catalog` WHERE `status` = 1 AND `author` = $user ".($filtered_cat != 0 ? " AND `category_code` = $filtered_cat" : "")." AND ".time()." - `timestamp` < `period;";
      $result = $db->db_fetchall_array($sql, __LINE__, __FILE__);
      
      for ($i = 0; $i < count($result); $i++){
         $result[$i]["currency_short_title"] = $currency->getShortTitle($result[$i]["currency"]);
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
   
   public function updateSNCity($user, $sn_city){
      global $db;
      
      $sql = "SELECT `city_name_translit`, `city_code` FROM `city`";
      $cities = $db->db_fetchall_array($sql, __LINE__, __FILE__);
      
      for ($i = 0; $i < count($cities); $i++){
         if (strtoupper(trim($cities[$i]["city_name_translit"])) == strtoupper($this->translit($sn_city))){
            $city_code = $cities[$i]["city_code"];
            $sql = "UPDATE `user` SET `city` = $city_code WHERE `num` = $user";
            $db->db_query($sql, __LINE__, __FILE__);
         }
      }
   }
   
   private function translit($str) {
      $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
      $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
      return str_replace($rus, $lat, $str);
  }
  
    public function checkEmailExist($email){
        if (isset($_SESSION["user_num"])){
            return 0;
        }
        
        $sql = sprintf("SELECT `num` FROM `user` WHERE `email` = '%s' AND deleted = 0;",
           mysql_real_escape_string($email));
        $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
        
        if (count($result["num"])){
            return 1;
        }
        else{
            return 0;
        }
    }
    
    public function getRecaptchaResponse($client_response){ 
        $jsonUrl = "https://www.google.com/recaptcha/api/siteverify";

        $captchacurl = curl_init();
        curl_setopt($captchacurl, CURLOPT_URL, $jsonUrl);
        curl_setopt($captchacurl, CURLOPT_HEADER,0); //Change this to a 1 to return headers
        curl_setopt($captchacurl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_setopt($captchacurl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($captchacurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($captchacurl, CURLOPT_POSTFIELDS, [
            "secret" => "6LeWkCIUAAAAAIn7fsz7feYtX-4Iv-9-3RQ6eVmV",
            "response" => $client_response
        ]);

        $result = curl_exec($captchacurl);
        curl_close($result);
        return $result;
    }
}

?>