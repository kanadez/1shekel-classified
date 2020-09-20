<?php

class Parameter{
   public function get($parameter){
      $sql = sprintf("SELECT `value` FROM `parameter` WHERE `parameter` = '%s';", // вставляем пост текущему юзеру в таблицу
         mysql_real_escape_string($parameter));
      $result = DB::getInstance()->db_fetchone_array($sql, __LINE__, __FILE__);
      
      return $result["value"];
   }
}

?>