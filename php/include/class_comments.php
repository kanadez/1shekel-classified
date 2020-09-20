<?php

class Comments{
    public function write($comment_body, $item_num){
        global $profile, $mail;

        try{
            if ($profile->getBannedStatus() === 1){
                throw new Exception("User banned, forbidden", 501);
            }
            
            $sql = sprintf("INSERT INTO `comments` (`user_num`, `item_num`, `comment_text`, `timestamp`) VALUES (%d, %d, '%s', '%s');", // вставляем пост текущему юзеру в таблицу
                mysql_real_escape_string(isset($_SESSION["user_num"]) ? $_SESSION["user_num"] : -1),
                mysql_real_escape_string($item_num),
                mysql_real_escape_string($comment_body),
                mysql_real_escape_string(time()));
            $response = DB::getInstance()->db_query($sql, __LINE__, __FILE__);
            
            $sql2 = sprintf("SELECT `author` FROM `catalog` WHERE `num` = %d",
                mysql_real_escape_string($item_num));
            $response2 = DB::getInstance()->db_fetchone_array($sql2, __LINE__, __FILE__);
            
            $sql3 = sprintf("SELECT `email` FROM `user` WHERE `num` = %d",
                mysql_real_escape_string($response2["author"]));
            $response3 = DB::getInstance()->db_fetchone_array($sql3, __LINE__, __FILE__);
            
            $mail->sendNewCommentEmail($response3["email"], intval($item_num));
        } 
        catch (Exception $e){
            $response = array('error' => array('code' => $e->getCode(), 'description' => $e->getMessage()));
        }
        
        return json_encode($response);
    }
   
    public function get($item_num){
        $sql = sprintf("SELECT `num`, `user_num`, (SELECT `name` from `user` WHERE `user`.`num` = `comments`.`user_num`) AS `user_name`, (SELECT `photo_100` from `user` WHERE `user`.`num` = `comments`.`user_num`) AS `user_photo`, `comment_text`, `timestamp` FROM `comments` WHERE `item_num` = %d;",
            mysql_real_escape_string($item_num));
        $result = DB::getInstance()->db_fetchall_array($sql, __LINE__, __FILE__);

        return json_encode($result);
    }
}

?>