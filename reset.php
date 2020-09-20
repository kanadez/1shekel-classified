<?php

require_once dirname(__FILE__)."/php/include/class_db.php";
require_once dirname(__FILE__)."/php/include/class_user.php";
require_once dirname(__FILE__)."/php/include/class_mail.php";
require_once dirname(__FILE__)."/php/include/class_constructor.php";

session_start(); 

$db = new DB;

if (!$db->mysqlConnect()){
   mysql_query("SET NAMES 'utf8'");
   mysql_query("SET collation_connection = 'UTF-8_general_ci'");
   mysql_query("SET collation_server = 'UTF-8_general_ci'");
   mysql_query("SET character_set_client = 'UTF-8'");
   mysql_query("SET character_set_connection = 'UTF-8'");
   mysql_query("SET character_set_results = 'UTF-8'");
   mysql_query("SET character_set_server = 'UTF-8'");
   $user = new User;
   $mail = new Mail;
   $constructor = new Constructor;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="robots" content="noindex,nofollow">
      <title>Доска объявлений Project NEON</title>
      <link rel="shortcut icon" href="">
      <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
      <link rel="stylesheet" href="css/custom.css" type="text/css">
      <link rel="stylesheet" href="css/new.css" type="text/css">
      <link rel="stylesheet" href="css/login.css" type="text/css">
      <link rel="stylesheet" href="css/form.css" type="text/css">
      <link rel="stylesheet" href="css/social_panel.css" type="text/css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.min.css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.edited.css">
      <link rel="stylesheet" href="/jq/jqueryui/jquery-ui.css">
      <style type="text/css">
         @font-face {
         	font-family: "Conv_Hattori_Hanzo";
         	src: url("fonts/Hattori_Hanzo.eot");
         	src: local("☺"), url("fonts/Hattori_Hanzo.woff") format("woff"), url("fonts/Hattori_Hanzo.ttf") format("truetype"), url("fonts/Hattori_Hanzo.svg") format("svg");
         	font-weight: normal;
         	font-style: normal;
         }
			
			body{
            font-family:"Conv_Hattori_Hanzo" !important;
            font-size: 62.5% !important;
            margin: 0;
            outline: none;
            overflow: auto;
            padding: 0;	
			}
		</style>
	   
      <script src="//code.jquery.com/jquery-1.10.2.js"></script>
      <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
      <script src="//vk.com/js/api/openapi.js" type="text/javascript"></script>
      <script type="text/javascript" src="js/include/jquery.sizes.js"></script>
   </head>
   <body>
      
      <?php echo $constructor->getHeader(); ?>
      
      <div class="clearfix"></div>
      <div class="main-wrapper"> 
         <div id="content-area" style="padding-top:14% !important">
            <h4 class="header_centered">Сброс пароля</h4>
            <form id="reset-password-form" action="reset.php" method="post">
            <?php
            
            if (isset($_POST["password"])){ // 3 step
               $password = md5($_POST['password']);
               $login = $_POST['login'];
               $token = $_POST['token'];
               $sql = sprintf("UPDATE `user` SET `passwd` = '%s' WHERE `email_token` = '%s' AND `email` = '%s'",
                     mysql_real_escape_string($password),
                     mysql_real_escape_string($token),
                     mysql_real_escape_string($login));
                     
               $db->db_query($sql, __LINE__, __FILE__);
               
               echo '
               <div class="block_wrapper" style="width:75% !important;">
                  <span style="margin-right:68px;">Пароль успешно сброшен!</span>
                  <p><a id="restore_button" href="login.php" class="btn transparent blue" style="width:67% !important;">Войти с новыми данными</a>
               </div>';
            }
            elseif (isset($_GET["token"]) && isset($_GET["login"])){ // 2 step
               echo '
               <div class="block_wrapper" style="width:75% !important;">
                  <label for="password" class="neccesary">Новый пароль: </label><input id="password" type="password" maxlength=100 style="width:46%" name="password" class="neccesary_input" />
                  <input name="token" id="token" type="text" style="display:none" value="'.$_GET["token"].'" />
                  <input name="login" id="login" type="text" style="display:none" value="'.$_GET["login"].'" />
                  <p><input id="restore_button" type="submit" class="btn transparent blue" style="width:67% !important;" value="Сохранить" />
               </div>';
            }
            elseif ($_POST["email"] != ""){ // 1 step
                $message = 'Уважаемый пользователь!<br>Для сброса пароля на сайте 1shekel.com перейдите по <a href="http://'.$_SERVER['HTTP_HOST'].'/reset.php?token=';
                $email = $_POST["email"];
                $token = md5(rand());

                $sql = sprintf("UPDATE `user` SET `email_token` = '%s' WHERE `email` = '%s';",
                    mysql_real_escape_string($token),
                    mysql_real_escape_string($email));

                $db->db_query($sql, __LINE__, __FILE__);
                
                $message .= $token.'&login='.$email.'">этой ссылке</a>.';
                $mail->sendEmail($email, "Восстановление пароля", $message);
                echo '
                <div class="block_wrapper" style="width:100% !important;text-align:center;">
                   На указанный Вами E-Mail выслано письмо с дальнейшими указаниями. Проверьте почту.
                </div>';
            }
            ?>
            </form>
         </div>
      </div>
      
      <?php echo $constructor->getFooter();?>
      
      <script type="text/javascript" src="js/reset.js"></script>
      <script type="text/javascript" src="js/include/utils.js"></script>
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/dropdown.js"></script>
      <script type="text/javascript" src="js/include/json2.js"></script>
   </body>
</html>

<?php

mysql_close();

?>