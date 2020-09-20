<?php
   class Feedback{
      public function repostBy($item, $to){
         global $db;
         $user_num = $_SESSION["user_num"];
         
         $sql1 = sprintf("SELECT `num` FROM `promoted` WHERE `user` = %d AND `item` = %d AND `to` = %d;", 
            mysql_real_escape_string($user_num),
            mysql_real_escape_string($item),
            mysql_real_escape_string($to));
         $result1 = $db->db_fetchone_array($sql1, __LINE__, __FILE__);
         
         $sql2 = sprintf("SELECT `num` FROM `promoted` WHERE `user` = %d AND `timestamp` = %d;", 
            mysql_real_escape_string($user_num),
            mysql_real_escape_string(strtotime('12:00:00')));
         $result2 = $db->db_fetchall_array($sql2, __LINE__, __FILE__);
         
         if (!count($result1) && count($result2) <= 20){ // если НЕ репостил это обявление в эту соцсеть
            $sql = sprintf("INSERT INTO `promoted` (`user`, `item`, `to`, `timestamp`) VALUES (%d, %d, %d, %d);", // вставляем репост в таблицу продвинутых
               mysql_real_escape_string($user_num),
               mysql_real_escape_string($item),
               mysql_real_escape_string($to),
               mysql_real_escape_string(strtotime('12:00:00')));
            $db->db_query($sql, __LINE__, __FILE__);
            
            $sql = sprintf("SELECT `author` FROM `catalog` WHERE `num` = %d;", 
               mysql_real_escape_string($item));
            $result2 = $db->db_fetchone_array($sql, __LINE__, __FILE__);
            
            $sql = sprintf("INSERT INTO `credits` (`user`, `total`, `type`, `timestamp`, `description`) VALUES (%d, 1, 1, %d, 'Вознаграждение за репост');", // добавляем транзкцию пополнения юзеру
               mysql_real_escape_string($user_num),
               mysql_real_escape_string(time()));
            $db->db_query($sql, __LINE__, __FILE__);
            
            //$sql = sprintf("INSERT INTO `credits` (`user`, `total`, `type`, `timestamp`, `description`) VALUES (%d, 1, 0, %d, 'Списание за продвижение');", // добавляем транзкцию списания автору
               //mysql_real_escape_string($result2["author"]),
               //mysql_real_escape_string(time()));
            //$db->db_query($sql, __LINE__, __FILE__);
            
            $sql = "UPDATE `user` SET `coins` = `coins` + 1 WHERE `num` = $user_num;"; // даем вознаграждение юзеру
            $db->db_query($sql, __LINE__, __FILE__);
            //$sql = sprintf("UPDATE `user` SET `coins` = `coins` - 1 WHERE `num` = %d AND `coins` > 0;", // снимаем балл у автора
               //mysql_real_escape_string($result2["author"]));
            //$db->db_query($sql, __LINE__, __FILE__);
            
            //$sql = sprintf("UPDATE `catalog` SET `credit` = `credit` - 1 WHERE `num` = %d;", // снимаем балл у объявления
               //mysql_real_escape_string($item));
            //$db->db_query($sql, __LINE__, __FILE__);
            
            return 0;
         }
         else return -1;
      }
      
      public function getPromotedByUser($user, $item){
         global $db;
         
         $sql = sprintf("SELECT `to` FROM `promoted` WHERE `user` = %d AND `item` = %d;", 
               mysql_real_escape_string($user),
               mysql_real_escape_string($item));
         return json_encode($db->db_fetchall_array($sql, __LINE__, __FILE__));
      }
      
      public function getPromoteLimits($user){
         global $db;
         $day_before = time()-86400;
         
         $sql = sprintf("SELECT `to` FROM `promoted` WHERE `user` = %d AND `timestamp` > %d;", 
               mysql_real_escape_string($user),
               mysql_real_escape_string($day_before));
         return json_encode($db->db_fetchall_array($sql, __LINE__, __FILE__));
      }
      
      public function promoteItem($user, $item){
         global $db;
         
         $sql = "SELECT `coins` FROM `coins` WHERE `action` = 'promote';";
         $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
         $_sum = $result["coins"];
         
         $sql = sprintf("SELECT `coins` FROM `user` WHERE `num` = %d;", 
               mysql_real_escape_string($user));
         $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
         
         if ($result["coins"] >= $_sum){
            $sql = sprintf("UPDATE `catalog` SET `promoted` = %d WHERE `num` = %d;", 
               mysql_real_escape_string(time()),
               mysql_real_escape_string($item));
            $db->db_query($sql, __LINE__, __FILE__);
            
            $sql = sprintf("UPDATE `user` SET `coins` = `coins` - %d WHERE `num` = %d;", 
               mysql_real_escape_string($_sum),
               mysql_real_escape_string($user));
            $db->db_query($sql, __LINE__, __FILE__);
            
            $sql = sprintf("INSERT INTO `credits` (`user`, `total`, `type`, `timestamp`, `description`) VALUES (%d, %d, 0, %d, 'Списание за продвижение');", // добавляем транзкцию списания автору
               mysql_real_escape_string($result2["author"]),
               mysql_real_escape_string($_sum),
               mysql_real_escape_string(time()));
            $db->db_query($sql, __LINE__, __FILE__);
            
            return 0;
         }
         else return -1;
      }
      
      public function getVipRates(){
         global $db;
         $complete = array();
         
         $sql = "SELECT `coins` FROM `coins` WHERE `action` IN ('vip_1', 'vip_7', 'vip_30');";
         $o = $db->db_fetchall_array($sql, __LINE__, __FILE__);
         $complete["vip_1"] = $o[0]["coins"];
         $complete["vip_7"] = $o[1]["coins"];
         $complete["vip_30"] = $o[2]["coins"];
         
         return json_encode($complete);
      }
      
      public function vipItem($user, $item){
         global $db;
         $user_balance;
         $sql = "SELECT `coins` FROM `coins` WHERE `action` = 'vip';";
         $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
         $vip_rate = $result["coins"];
         
         $sql = sprintf("SELECT `coins` FROM `user` WHERE `num` = %d;", 
               mysql_real_escape_string($user));
         $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
         $user_balance = $result["coins"];
         
         if ($user_balance > $vip_rate){
            $sql = sprintf("UPDATE `catalog` SET `vip` = %d WHERE `num` = %d;",
               mysql_real_escape_string(time()),
               mysql_real_escape_string($item));
            $db->db_query($sql, __LINE__, __FILE__);
            
            $sql = sprintf("UPDATE `user` SET `coins` = `coins` - %d WHERE `num` = %d;", 
               mysql_real_escape_string($vip_rate),
               mysql_real_escape_string($user));
            $db->db_query($sql, __LINE__, __FILE__);
            
            $sql = sprintf("INSERT INTO `credits` (`user`, `total`, `type`, `timestamp`, `description`) VALUES (%d, %d, 0, %d, 'Покупка VIP-статуса для объявления');", // добавляем транзкцию списания автору
               mysql_real_escape_string($user),
               mysql_real_escape_string($vip_rate),
               mysql_real_escape_string(time()));
            $db->db_query($sql, __LINE__, __FILE__);
            
            return 0;
         }
         else return -1;
      }
      
      public function highlightItem($user, $item){
         global $db;
         
         $sql = "SELECT `coins` FROM `coins` WHERE `action` = 'highlight';";
         $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
         $_sum = $result["coins"];
         
         $sql = sprintf("SELECT `coins` FROM `user` WHERE `num` = %d;", 
               mysql_real_escape_string($user));
         $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
         
         if ($result["coins"] >= $_sum){
            $sql = sprintf("UPDATE `catalog` SET `promoted` = %d, `highlighted` = %d WHERE `num` = %d;", 
               mysql_real_escape_string(time()),
               mysql_real_escape_string(time()),
               mysql_real_escape_string($item));
            $db->db_query($sql, __LINE__, __FILE__);
            
            $sql = sprintf("UPDATE `user` SET `coins` = `coins` - %d WHERE `num` = %d;", 
               mysql_real_escape_string($_sum),
               mysql_real_escape_string($user));
            $db->db_query($sql, __LINE__, __FILE__);
            
            $sql = sprintf("INSERT INTO `credits` (`user`, `total`, `type`, `timestamp`, `description`) VALUES (%d, %d, 0, %d, 'Списание за продвижение');", // добавляем транзкцию списания автору
               mysql_real_escape_string($result2["author"]),
               mysql_real_escape_string($_sum),
               mysql_real_escape_string(time()));
            $db->db_query($sql, __LINE__, __FILE__);
            
            return 0;
         }
         else return -1;
      }
   }
?>