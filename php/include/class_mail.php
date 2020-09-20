<?php

class Mail{
   public function sendMessage($sender, $recepient, $message, $guest_email){ // сообщение с созданием нового диалога. отправляется со страницы Обхявления или Профилья пользователя
      global $db;
      $dialoguid1 = null;
      $dialoguid2 = null;
      //$sender = isset($_SESSION["user_num"]) ? $_SESSION["user_num"] : 0;
      if ($sender == 0){
        $dialoguid = uniqid();
        $sql = sprintf("INSERT INTO `mail` (`dialoguid`, `message`, `sender`, `recepient`, `email`, `timestamp`) VALUES ('%s', '%s', %d, %d, '%s', %d);", // вставляем пост текущему юзеру в таблицу
            mysql_real_escape_string($dialoguid),
            mysql_real_escape_string($message),
            mysql_real_escape_string($sender),
            mysql_real_escape_string($recepient),
            mysql_real_escape_string($guest_email),
            mysql_real_escape_string(time()));
      }
      else{
        $sql = sprintf("SELECT `dialoguid` FROM `mail` WHERE `sender` = %d AND `recepient` = %d;",
           mysql_real_escape_string($sender),
           mysql_real_escape_string($recepient));
        $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
        $dialoguid1 = $result["dialoguid"];

        $sql = sprintf("SELECT `dialoguid` FROM `mail` WHERE `recepient` = %d AND `sender` = %d;",
           mysql_real_escape_string($sender),
           mysql_real_escape_string($recepient));
        $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
        $dialoguid2 = $result["dialoguid"];

        if ($dialoguid1 == null && $dialoguid2 == null){
           $dialoguid = uniqid();
           $sql = sprintf("INSERT INTO `mail` (`dialoguid`, `message`, `sender`, `recepient`, `email`, `timestamp`) VALUES ('%s', '%s', %d, %d, '%s', %d);", // вставляем пост текущему юзеру в таблицу
              mysql_real_escape_string($dialoguid),
              mysql_real_escape_string($message),
              mysql_real_escape_string($sender),
              mysql_real_escape_string($recepient),
              mysql_real_escape_string($guest_email),
              mysql_real_escape_string(time()));
        }
        elseif ($dialoguid1 != null){ // первый варик выборки сработал
           $sql = sprintf("INSERT INTO `mail` (`dialoguid`, `message`, `sender`, `recepient`, `email`, `timestamp`) VALUES ('%s', '%s', %d, %d, '%s', %d);", // вставляем пост текущему юзеру в таблицу
              mysql_real_escape_string($dialoguid1),
              mysql_real_escape_string($message),
              mysql_real_escape_string($sender),
              mysql_real_escape_string($recepient),
              mysql_real_escape_string($guest_email),
              mysql_real_escape_string(time()));
        }
        else{ // второй варик выборки сработал
           $sql = sprintf("INSERT INTO `mail` (`dialoguid`, `message`, `sender`, `recepient`, `email`, `timestamp`) VALUES ('%s', '%s', %d, %d, '%s', %d);", // вставляем пост текущему юзеру в таблицу
              mysql_real_escape_string($dialoguid2),
              mysql_real_escape_string($message),
              mysql_real_escape_string($sender),
              mysql_real_escape_string($recepient),
              mysql_real_escape_string($guest_email),
              mysql_real_escape_string(time()));
        }
      }
      
      return $db->db_query($sql, __LINE__, __FILE__);
   }
   
    public function sendEmail($to, $subject, $message){
        $header = "From: 1shekel.com <noreply@1shekel.com>\r\n"; 
        $header.= "MIME-Version: 1.0\r\n"; 
        $header.= "Content-Type: text/html; charset=utf-8\r\n"; 
        $header.= "X-Priority: 1\r\n"; 
      
        return mail($to, $subject, $message, $header);
   }
   
    public function sendRegisterFromNewEmail($to, $passwd, $token){
        global $db;

        $header = "From: 1shekel.com <noreply@1shekel.com>\r\n"; 
        $header.= "MIME-Version: 1.0\r\n"; 
        $header.= "Content-Type: text/html; charset=utf-8\r\n"; 
        $header.= "X-Priority: 1\r\n"; 
        $msg = ' 
             <table width="600" cellpadding="0" cellspacing="0" style="font-family:Arial,Helvetica,sans-serif; font-size: 14px; color: #666;" align="center">
                 <tbody>
                     <tr>
                         <td width="600" height="50" align="center" style="background: #F6F7F9; border: 1px solid #DDD; border-radius: 5px 5px 0 0;">
                             <img style="width: 130px;" src="http://isra-go.com/assets/images/logo/logo_default.png" />
                         </td>
                     </tr>
                     <tr>
                         <td width="600" align="center" style="background: #FFF; line-height: 18px; padding: 10px;  border-bottom: 1px solid #DDD; border-left: 1px solid #DDD; border-right: 1px solid #DDD;">
                             <p style="margin-bottom: 10px;">
                                 Здравствуйте! Благодарим вас за регистрацию на сайте <strong>бесплатных объявлений Израиля</strong>. 
                                 <p>Пожалуйста, нажмите на кнопку ниже, чтобы войти на сайт.
                             </p>
                             <p style="margin-bottom: 10px;">
                                 Ваш логин: '.$to.'
                                 <br>Ваш временный пароль: '.$passwd.'
                                 <br>Рекомендуем изменить его после первого входа.
                             </p>
                             <p style="margin-bottom: 10px;">
                                 <a style="text-decoration: none; color: #FFF; font-weight: bold; padding: 12px 20px; background: #3498db; border-radius: 3px; -webkit-border-radius: 3px;" rel="nofollow" href="http://'.$_SERVER["HTTP_HOST"].'/login.php">Войти на сайт</a></p>
                                <br><a style="text-decoration: none; color: #FFF; font-weight: bold; padding: 12px 20px; background: #ffa500; border-radius: 3px; -webkit-border-radius: 3px;" rel="nofollow" href="http://'.$_SERVER["HTTP_HOST"].'/reset.php?token='.$token.'&login='.$to.'">Изменить пароль</a></p>
                             </p>
                             <p style="text-align: center; font-size: 11px; padding: 5px 0 0 0;">
                                 С уважением, команда <a style="text-decoration: none; color: #3498db;" href="http://'.$_SERVER["HTTP_HOST"].'/">1shekel.com</a>! ☺
                             </p>
                             <p style="text-align: center; font-size: 10px;">Это письмо было отправлено автоматически, на него отвечать не нужно! </hr>Если вы не регистрировались на сайте, тогда просто проигнорируйте это письмо.</p>
                         </td>
                     </tr>
                 </tbody>
             </table>'; 
        mail($to, "Регистрация завершена", $msg, $header); 

        $sql = "SELECT `email` FROM `user` WHERE `num` = 1;";
        $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);

        $msg = ' 
           <html> 
               <head> 
                   <title>Зарегистрирован новый пользователь</title> 
               </head> 
               <body> 
                  Зарегистрировался новый пользователь: <a href="http://'.$_SERVER["HTTP_HOST"].'/user.php?num='.$id.'">'.$to.'</a>
               </body> 
           </html>'; 
        mail($result["email"], "Зарегистрирован новый пользователь", $msg, $header);
    }
   
   public function sendRegisterEmail($to, $id, $token){
       global $db;
       
      $header = "From: 1shekel.com <noreply@1shekel.com>\r\n"; 
        $header.= "MIME-Version: 1.0\r\n"; 
        $header.= "Content-Type: text/html; charset=utf-8\r\n"; 
        $header.= "X-Priority: 1\r\n"; 
      $msg = ' 
            <table width="600" cellpadding="0" cellspacing="0" style="font-family:Arial,Helvetica,sans-serif; font-size: 14px; color: #666;" align="center">
                <tbody>
                    <tr>
                        <td width="600" height="50" align="center" style="background: #F6F7F9; border: 1px solid #DDD; border-radius: 5px 5px 0 0;">
                            <img style="width: 130px;" src="http://isra-go.com/assets/images/logo/logo_default.png" />
                        </td>
                    </tr>
                    <tr>
                        <td width="600" align="center" style="background: #FFF; line-height: 18px; padding: 10px;  border-bottom: 1px solid #DDD; border-left: 1px solid #DDD; border-right: 1px solid #DDD;">
                            <p style="margin-bottom: 10px;">
                                Здравствуйте! Благодарим вас за регистрацию на сайте <strong>бесплатных объявлений Израиля</strong>. 
                                <p>Пожалуйста, нажмите на кнопку ниже, чтобы войти на сайт или изменить пароль.
                            </p>
                            <p style="margin-bottom: 10px;">
                                <a style="text-decoration: none; color: #FFF; font-weight: bold; padding: 12px 20px; background: #3498db; border-radius: 3px; -webkit-border-radius: 3px;" rel="nofollow" href="http://'.$_SERVER["HTTP_HOST"].'/login.php">Войти на сайт</a></p>
                                <br><a style="text-decoration: none; color: #FFF; font-weight: bold; padding: 12px 20px; background: #ffa500; border-radius: 3px; -webkit-border-radius: 3px;" rel="nofollow" href="http://'.$_SERVER["HTTP_HOST"].'/reset.php?token='.$token.'&login='.$to.'">Изменить пароль</a></p>
                            </p>
                            <p style="text-align: center; font-size: 11px; padding: 5px 0 0 0;">
                                С уважением, команда <a style="text-decoration: none; color: #3498db;" href="http://'.$_SERVER["HTTP_HOST"].'/">1shekel.com</a>! ☺☺
                            </p>
                            <p style="text-align: center; font-size: 10px;">Это письмо было отправлено автоматически, на него отвечать не нужно! </hr>Если вы не регистрировались на сайте, тогда просто проигнорируйте это письмо.</p>
                        </td>
                    </tr>
                </tbody>
            </table>'; 
      mail($to, "Регистрация завершена", $msg, $header);
      
      $sql = "SELECT `email` FROM `user` WHERE `num` = 1;";
      $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
      
      $msg = ' 
         <html> 
             <head> 
                 <title>Зарегистрирован новый пользователь</title> 
             </head> 
             <body> 
                Зарегистрировался новый пользователь: <a href="http://'.$_SERVER["HTTP_HOST"].'/user.php?num='.$id.'">'.$to.'</a>
             </body> 
         </html>'; 
      mail($result["email"], "Зарегистрирован новый пользователь", $msg, $header);
   }
   
   public function sendNewItemEmail($to, $id, $token){
       global $db;
       
        $header = "From: 1shekel.com <noreply@1shekel.com>\r\n"; 
        $header.= "MIME-Version: 1.0\r\n"; 
        $header.= "Content-Type: text/html; charset=utf-8\r\n"; 
        $header.= "X-Priority: 1\r\n"; 
        $msg = ' 
          <table width="600" cellpadding="0" cellspacing="0" style="font-family:Arial,Helvetica,sans-serif; font-size: 14px; color: #666;" align="center">
                <tbody>
                    <tr>
                        <td width="600" height="50" align="center" style="background: #F6F7F9; border: 1px solid #DDD; border-radius: 5px 5px 0 0;">
                            <img style="width: 130px;" src="http://isra-go.com/assets/images/logo/logo_default.png" />
                        </td>
                    </tr>
                    <tr>
                        <td width="600" align="center" style="background: #FFF; line-height: 18px; padding: 10px;  border-bottom: 1px solid #DDD; border-left: 1px solid #DDD; border-right: 1px solid #DDD;">
                            <p style="margin-bottom: 10px;">
                                Здравствуйте! Вы разместили объявление на сайте 1shekel.com.
                                <p>Вы можете посмотреть объявление прямо сейчас:
                            </p>
                            <p style="margin-bottom: 10px;">
                                <a style="text-decoration: none; color: #FFF; font-weight: bold; padding: 12px 20px; background: #3498db; border-radius: 3px; -webkit-border-radius: 3px;" rel="nofollow" href="http://'.$_SERVER["HTTP_HOST"].'/edit.php?item='.$id.'&token='.$token.'">Редактировать объявление</a></p>
                                <br><p><a style="text-decoration: none; color: #FFF; font-weight: bold; padding: 12px 20px; background: #3498db; border-radius: 3px; -webkit-border-radius: 3px;" rel="nofollow" href="http://'.$_SERVER["HTTP_HOST"].'/profile.php?action=remove&item='.$id.'&token='.$token.'">Удалить объявление</a></p>
                                <br><a style="text-decoration: none; color: #FFF; font-weight: bold; padding: 12px 20px; background: #ffa500; border-radius: 3px; -webkit-border-radius: 3px;" rel="nofollow" href="http://'.$_SERVER["HTTP_HOST"].'/profile.php?action=promote&item='.$id.'&token='.$token.'">Продвинуть объявление</a></p>
                            </p>
                            <p>Объявление появится в общем каталоге сразу после проверки модератором.
                            <p style="text-align: center; font-size: 11px; padding: 5px 0 0 0;">
                                С уважением, команда <a style="text-decoration: none; color: #3498db;" href="http://'.$_SERVER["HTTP_HOST"].'/">1shekel.com</a>! ☺
                            </p>
                            <p style="text-align: center; font-size: 10px;">Это письмо было отправлено автоматически, на него отвечать не нужно! </hr>Если вы не регистрировались на сайте, тогда просто проигнорируйте это письмо.</p>
                        </td>
                    </tr>
                </tbody>
            </table>'; 
      mail($to, "Вы разместили объявление", $msg, $header);
   }
   
   public function sendNewCommentEmail($to, $item){
        $header = "From: 1shekel.com <noreply@1shekel.com>\r\n"; 
        $header.= "MIME-Version: 1.0\r\n"; 
        $header.= "Content-Type: text/html; charset=utf-8\r\n"; 
        $header.= "X-Priority: 1\r\n"; 
        $msg = ' 
             <table width="600" cellpadding="0" cellspacing="0" style="font-family:Arial,Helvetica,sans-serif; font-size: 14px; color: #666;" align="center">
                 <tbody>
                     <tr>
                         <td width="600" height="50" align="center" style="background: #F6F7F9; border: 1px solid #DDD; border-radius: 5px 5px 0 0;">
                             <img style="width: 130px;" src="http://isra-go.com/assets/images/logo/logo_default.png" />
                         </td>
                     </tr>
                     <tr>
                         <td width="600" align="center" style="background: #FFF; line-height: 18px; padding: 10px;  border-bottom: 1px solid #DDD; border-left: 1px solid #DDD; border-right: 1px solid #DDD;">
                             <p style="margin-bottom: 10px;">
                                 Здравствуйте!
                                 К Вашему объявлению на сайте 1shekel.com был оставлен новый комментарий.
                             </p>
                             <p style="margin-bottom: 10px;">
                                 <a style="text-decoration: none; color: #FFF; font-weight: bold; padding: 12px 20px; background: #3498db; border-radius: 3px; -webkit-border-radius: 3px;" rel="nofollow" href="http://'.$_SERVER["HTTP_HOST"].'/item.php?num='.$item.'">Открыть объявление</a></p>
                             </p>
                             <p style="text-align: center; font-size: 11px; padding: 5px 0 0 0;">
                                 С уважением, команда <a style="text-decoration: none; color: #3498db;" href="http://'.$_SERVER["HTTP_HOST"].'/">1shekel.com</a>! ☺
                             </p>
                             <p style="text-align: center; font-size: 10px;">Это письмо было отправлено автоматически, на него отвечать не нужно! </hr>Если вы не регистрировались на сайте, тогда просто проигнорируйте это письмо.</p>
                         </td>
                     </tr>
                 </tbody>
             </table>'; 
        mail($to, "Новый комментарий к объявлению", $msg, $header);
    }
   
   public function getDialogues($user){
      global $db;
      
      $sql = sprintf("SELECT `dialoguid`, `message`, `sender`, `recepient`, `timestamp`, `unread`, `email` FROM `mail` WHERE `sender` = %d OR `recepient` = %d ORDER BY `timestamp` DESC;",
         mysql_real_escape_string($user),
         mysql_real_escape_string($user));
      $result = $db->db_fetchall_array($sql, __LINE__, __FILE__);
      
      return json_encode($result);
   }
   
   public function getLocutors($user, $locutors){
      global $db;
      
      $locutors_obj = json_decode(stripcslashes($locutors), true);
      $sql = "SELECT `num`, `name`, `photo_100` FROM `user` WHERE `num` IN (";
      for ($i = 0; $i < count($locutors_obj); $i++)
         if ($i != count($locutors_obj)-1)
            $sql .= $locutors_obj[$i].",";
         else $sql .= $locutors_obj[$i];
      $sql .= ");";
      $result = $db->db_fetchall_array($sql, __LINE__, __FILE__);
      
      return json_encode($result);
   }
   
   public function getDialog($user, $dialoguid){
      global $db;
      
      $sql = sprintf("UPDATE `mail` SET `unread` = 0 WHERE `unread` = 1 AND `dialoguid` = '%s' AND `sender` <> %d;",
         mysql_real_escape_string($dialoguid),
         mysql_real_escape_string($user));
      $db->db_query($sql, __LINE__, __FILE__);
      
      $sql = sprintf("SELECT `num`, `dialoguid`, `message`, `sender`, `recepient`, `timestamp` FROM `mail` WHERE `dialoguid` = '%s' ORDER BY `timestamp`;",
         mysql_real_escape_string($dialoguid));
      $result = $db->db_fetchall_array($sql, __LINE__, __FILE__);
      
      return json_encode($result);
   }
   
    public function setEmailDialogRead($dialoguid){
        global $db;

        $sql = sprintf("UPDATE `mail` SET `unread` = 0 WHERE `unread` = 1 AND `dialoguid` = '%s';",
            mysql_real_escape_string($dialoguid));
        $db->db_query($sql, __LINE__, __FILE__);
    }
   
   public function write($user, $dialoguid, $recepient, $message, $message_num){ //  написать сообщение в личном кабинете (для залогиненных юзеров). Всегда пишется из диалогов и в существующий диаог
      global $db;
      
      $sql = sprintf("INSERT INTO `mail` (`dialoguid`, `sender`, `recepient`, `message`, `timestamp`) VALUES ('%s', %d, %d, '%s', '%s');", // вставляем пост текущему юзеру в таблицу
         mysql_real_escape_string($dialoguid),
         mysql_real_escape_string($user),
         mysql_real_escape_string($recepient),
         mysql_real_escape_string($message),
         mysql_real_escape_string(time()));
      $delivery_result = $db->db_query($sql, __LINE__, __FILE__);
      
      if ($delivery_result == 1)
         return $message_num;
      else return -1;
   }
}

?>