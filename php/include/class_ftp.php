<?php

require_once dirname(__FILE__)."/data_ftp_auth.php";

$ftpauth = new DBAuth;

class FTP{
   private function ftpConnect(){ //connects to ftp-server. Credentials are in src/ftpAuthData.php file
      global $ftpauth;
         
      $conn_id = ftp_connect($ftpauth->$ftp_server) or die("Couldn't connect to $ftpauth->$ftp_server"); 
   
      if (@ftp_login($conn_id, $ftpauth->$ftp_user, $ftpauth->$ftp_pass)) 
         return $conn_id;
      else
         return "Couldn't connect as $ftpauth->$ftp_user\n";
   }
   
   public function ftpCreateDirectory($directory_name){ //creates directory $directory_name
      $conn_id = $this->ftpConnect();
      $result = 0;
      
      if (ftp_mkdir($conn_id, $directory_name))
         $result = "1";
      else
         $result = "0";
      
      ftp_close($conn_id);
      return $result;
   }
}
?>