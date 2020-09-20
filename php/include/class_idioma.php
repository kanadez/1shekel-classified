<?php

class Idioma{
   public function getVariables($variables_array){
      global $language;
      
      $array = json_decode(stripcslashes($variables_array), true);
      $sql = "SELECT `$language` FROM `idioma` WHERE `variable` IN (";
      for ($i = 0; $i < count($array); $i++)
         if ($i < count($array)-1)
            $sql .= "'".$array[$i]."',";
         else $sql .= "'".$array[$i]."'";
      $sql .= ")";
      $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);
      
      $tmp = array();
      for ($i = 0; $i < count($result); $i++){
         $tmp[$array[$i]] = $result[$i]["ru"];
      }
      return json_encode($tmp);
   }
}

?>