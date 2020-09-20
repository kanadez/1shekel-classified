<?php

class Dummy{
   public function feedback($name, $email, $message){
      global $db;
      
      $sql = sprintf("INSERT INTO `review` (`name`, `email`, `message`) VALUES ('%s', '%s', '%s');", // вставляем репост в таблицу продвинутых
         mysql_real_escape_string($name),
         mysql_real_escape_string($email),
         mysql_real_escape_string($message));
      return $db->db_query($sql, __LINE__, __FILE__);
   }
}

?>