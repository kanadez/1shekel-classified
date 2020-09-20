<?php

class Search{
   private $response = "'%";
   
   public function constructSQL($query){
      $query = substr($query, 0, 128);
      $query = preg_replace("/[^\w\x7F-\xFF\s]/", " ", $query);
      //$good = trim(preg_replace("/\s(\S{1,2})\s/", " ", ereg_replace(" +", "  "," $query ")));
      $good = ereg_replace(" +", " ", $query);
      
      return $this->mixQuery($good);
   }
   
   private function mixQuery($query){
      $this->permute(explode(" ", $query));
      $this->response .= $query."%'";
      
      return $this->response;
   }
   
   private function permute($items, $perms = array()) {
      if (empty($items)) {
         $this->response .= join('%', $perms) . "%' OR `title` LIKE '%";
      } 
      else{
         for ($i = count($items) - 1; $i >= 0; --$i) {
            $newitems = $items;
            $newperms = $perms;
            list($foo) = array_splice($newitems, $i, 1);
            array_unshift($newperms, $foo);
            $this->permute($newitems, $newperms);
         }
      }
   }
}

?>