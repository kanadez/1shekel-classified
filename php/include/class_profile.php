<?php

class Profile{
    public function getBannedStatus(){
        global $db;
        
        if (isset($_SESSION["user_num"])){
            $sql = "SELECT `banned`, `deleted` FROM `user` WHERE `num` = ".$_SESSION["user_num"].";";
            $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);

            if ($result["banned"] == 1 || $result["deleted"] == 1){
                $response = 1;
            }
        }
        else{
            $response = 0;
        }
        
        return $response;
    }
    
    public function checkPayment($pm_no, $status){
        global $db;
        
        try{
            $sql = "SELECT `id`, `amount` FROM `payment` WHERE `pm_no` = '".strval($pm_no)."';";
            $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);

            if (count($result) === 0){
                throw new Exception("Payment not exist", 401);
            }
            
            $sql = sprintf("UPDATE `payment` SET `status` = '%s' WHERE `id` = %d", // вставляем пост текущему юзеру в таблицу
                mysql_real_escape_string($status),
                mysql_real_escape_string($result["id"]));
            
            $db->db_query($sql, __LINE__, __FILE__);
            
            if ($status === "canceled"){
                throw new Exception("Payment failed", 501);
            }
            
            if ($status === "waitAccept"){
                throw new Exception("Payment pending", 501);
            }
            
            if ($status === "success"){
                $response = $result["amount"];
            }
        }
        catch(Exception $e){
            $response = array('error' => array('code' => $e->getCode(), 'description' => $e->getMessage()));
        }
        
        return $response;
    }
    
    public function getCoinCurrency(){
        global $db;
        
        $sql = "SELECT `values` FROM `dbc_options` WHERE `key` = 'classified_settings';";
        $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
        
        $parsed = json_decode($result["values"], true);
        return $parsed["coin_price_in_shekels"];
    }
    
    public function createPayment($coins){
        global $db;
        $pm_no = uniqid();
        
        $sql = sprintf("INSERT INTO `payment` (`pm_no`,`amount`, `timestamp`) VALUES ('%s', %f, %d);",
            mysql_real_escape_string($pm_no),
            mysql_real_escape_string(intval($coins)),
            mysql_real_escape_string(time()));

        $db->db_query($sql, __LINE__, __FILE__);
        
        return $pm_no;
    }
    
    public function prolongItem($item_num){
        global $db;
      
        $sql = sprintf("UPDATE `catalog` SET `timestamp` = %d WHERE `num` = %d AND `author` = %d;", // вставляем пост текущему юзеру в таблицу
            mysql_real_escape_string(time()),
            mysql_real_escape_string($item_num),
            mysql_real_escape_string($_SESSION["user_num"]));
        return $db->db_query($sql, __LINE__, __FILE__);
    }
    
   public function seen(){
      global $db;
      
      $sql1 = "UPDATE `user` SET `last_seen` = ".time()." WHERE `num` = ".$_SESSION["user_num"];
      $db->db_query($sql1, __LINE__, __FILE__);
   }
   
   public function comparePasswd($user, $oldpasswd, $newpasswd){
      global $db;
      $hash = md5($oldpasswd);
      $newhash = md5($newpasswd);
      $user_filtered = intval($user);
      $sql = "SELECT `passwd` FROM `user` WHERE `num` = $user_filtered;";
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      if ($result["passwd"] == $hash){
         $sql1 = "UPDATE `user` SET `passwd` = '$newhash' WHERE `num` = $user_filtered;";
         return $db->db_query($sql1, __LINE__, __FILE__);
      }
      else return -1;
   }
   
   public function sendEmailConfirm($user, $newemail){
      global $db;
      global $mail;
      
      $user_filtered = intval($user);
      $newemail_filtered = strval($newemail);
      $confirm_code = rand(999, 9999);
      $sql = "UPDATE `user` SET `email_confirm` = $confirm_code WHERE `num` = $user_filtered;";
      $db->db_query($sql, __LINE__, __FILE__);
      
      $confirm_msg = ' 
         <html> 
             <head> 
                 <title>Подтверждение электронного адреса</title> 
             </head> 
             <body> 
                 <p>Чтобы подтвердить этот адрес, нажмите ссылку: <a href="http://'.$_SERVER["HTTP_HOST"].'/confirm.php?user='.$user_filtered.'&code='.$confirm_code.'&email='.$newemail_filtered.'">НАЖАТЬ</a>
             </body> 
         </html>'; 
      
      return $mail->sendEmail($newemail, "Подтверждение электронного адреса", $confirm_msg);
   }
   
   public function setPersonalData($user, $name, $city, $address, $phone){
      global $db;
      
      $sql = sprintf("UPDATE `user` SET `name` = '%s', `city` = %d, `address` = '%s', `phone` = '%s' WHERE `num` = %d;", // вставляем пост текущему юзеру в таблицу
         mysql_real_escape_string($name),
         mysql_real_escape_string($city),
         mysql_real_escape_string($address),
         mysql_real_escape_string($phone),
         mysql_real_escape_string($user));
      return $db->db_query($sql, __LINE__, __FILE__);
   }
   
   public function getDummies($user){
      global $db;
      
      $sql = "SELECT * FROM `mail` WHERE `recepient` = $user AND `unread` = 1;";
      $complete["mail"] = count($db->db_fetchall_array($sql, __LINE__, __FILE__));
      
      $sql = "SELECT * FROM `catalog` WHERE `author` = $user AND `status` = 1;";
      $complete["items"] = count($db->db_fetchall_array($sql, __LINE__, __FILE__));
      
      return json_encode($complete);
   }
   
   public function getCities(){
      global $db;
      
      $sql = "SELECT * FROM `city` ORDER BY `num`";
      $result = $db->db_fetchall_array($sql, __LINE__, __FILE__);
      
      return json_encode($result);
   }
   
   public function getItems($user_num, $catalog_page, $items_status){
      global $db;
      global $parameter;
      global $currency;
      $this->params["catalog_items_per_page"] = $parameter->get("catalog_items_per_page");
      
      $user = intval($user_num);
      $filtered_status = intval($items_status);
      $p = isset($catalog_page) ? intval($catalog_page) : 1;
      $sql = "SELECT `num`, `title`, `timestamp`, `period`, `price`, `currency`, `auction`, `photo`, `promoted`, `vip` FROM `catalog` WHERE `author` = $user AND `status` = $filtered_status AND `status` <> 0 ORDER BY `timestamp` DESC;";
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
   
   public function closeItem($user, $item){
      global $db;
      
      $sql = sprintf("UPDATE `catalog` SET `status` = 2 WHERE `num` = %d AND `author` = %d;", // вставляем пост текущему юзеру в таблицу
         mysql_real_escape_string($item),
         mysql_real_escape_string($user));
      return $db->db_query($sql, __LINE__, __FILE__);
   }
   
   public function removeItem($user, $item){
      global $db;
      
      $sql = sprintf("UPDATE `catalog` SET `status` = 0 WHERE `num` = %d AND `author` = %d;", // вставляем пост текущему юзеру в таблицу
         mysql_real_escape_string($item),
         mysql_real_escape_string($user));
      return $db->db_query($sql, __LINE__, __FILE__);
   }
   
   public function restoreItem($user, $item){
      global $db;
      
      $sql = sprintf("UPDATE `catalog` SET `status` = 1 WHERE `num` = %d AND `author` = %d;", // вставляем пост текущему юзеру в таблицу
         mysql_real_escape_string($item),
         mysql_real_escape_string($user));
      return $db->db_query($sql, __LINE__, __FILE__);
   }
   
   public function getCredits($user, $p){
      global $db;
      $complete = array(); // новый массив для элментов текщей страницы
      
      $sql = sprintf("SELECT * FROM `credits` WHERE `user` = %d ORDER BY `timestamp` DESC;", // вставляем пост текущему юзеру в таблицу
         mysql_real_escape_string($user));
      $result = $db->db_fetchall_array($sql, __LINE__, __FILE__);
         
      for ($i = 10*$p; $i < 10*($p+1); $i++)
         isset($result[$i]) ? array_push($complete, $result[$i]) : $i++;
      $params["items_count"] = count($result);
      array_push($complete, $params);
      return json_encode($complete);
   }
   
   public function getPromotions($user, $p, $category, $region){
      global $db;
      $complete = array(); // новый массив для элментов текщей страницы
      $result3 = array();
      $category_intvaled = $category != 0 ? "AND `category_code` = ".intval($category) : "";
      $region_intvaled = $region != -1 && $region != 0 ? "AND `city` = ".intval($region) : "";
      
      $sql = sprintf("SELECT `num`, `title`, (SELECT `city_name` FROM `city` WHERE `city`.`city_code` = `catalog`.`city`) AS `city`, `photo` FROM `catalog` WHERE `promoted` > 0 AND `author` <> %d $category_intvaled $region_intvaled ORDER BY `promoted` DESC;", // вставляем пост текущему юзеру в таблицу
         mysql_real_escape_string($user));
      $result = $db->db_fetchall_array($sql, __LINE__, __FILE__);
      
      for ($i = 0; $i < count($result); $i++){
         $sql2 = sprintf("SELECT `to` FROM `promoted` WHERE `user` = %d AND `item` = %d;", // вставляем пост текущему юзеру в таблицу
            mysql_real_escape_string($user),
            mysql_real_escape_string($result[$i]["num"]));
         $result2 = $db->db_fetchall_array($sql2, __LINE__, __FILE__);
         
         if (count($result2) < 5){
            array_push($result3, $result[$i]);
         }
      }
      
      for ($i = 8*$p; $i < 8*($p+1); $i++)
         isset($result3[$i]) ? array_push($complete, $result3[$i]) : $i++;
      $params["items_count"] = count($result3);
      array_push($complete, $params);
      return json_encode($complete);
   }
}

?>