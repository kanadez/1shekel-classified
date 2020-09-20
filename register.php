<?php

require_once dirname(__FILE__)."/php/include/class_db.php";
require_once dirname(__FILE__)."/php/include/class_user.php";
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
      <link rel="stylesheet" href="css/register.css" type="text/css">
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
         <div id="content-area">
            <h4 class="header_centered">Регистрация на сайте</h4>
            <div class="block_wrapper" style="width:85% !important;">
               <div id="password_alert" class="alert" style="display:none">Ошибка: пароли не совпадают</div>
               <div id="registered_alert" class="alert" style="display:none">Ошибка: вы уже были зарегистрированы</div>
               <label for="personal_status_input" class="neccesary">*Ваш статус: </label>
               <input type="radio" name="personal_status_input" id="personal_status1_input" class="css-radio radGroup1" value="0" /><label for="personal_status1_input" class="css-label-radio css-label-radio2 radGroup1">Частное лицо</label>
               <input type="radio" name="personal_status_input" id="personal_status2_input" class="css-radio radGroup1" value="1" /><label for="personal_status2_input" class="css-label-radio css-label-radio2 radGroup1">Компания</label>
               <br><label for="name_input" class="neccesary">*Имя: </label><input id="name_input" class="neccesary_input input" maxlength=100 name="name_input" />
               <br><label for="phone_input" class="neccesary">*Номер телефона: </label><input id="phone_input" class="neccesary_input" maxlength=20 name="phone_input"  />
               <input type="checkbox" class="css-checkbox" style="display:none" id="hidephone_check" /> <label for="hidephone_check" id="hidephone_check_label" class="css-label lite-gray-check">Скрыть</label>
               <br><label for="email_input" class="neccesary">*Электронная почта: </label><input class="neccesary_input input" id="email_input" maxlength=100 name="email_input"   />
               <br><label for="pass_input" class="neccesary">*Пароль: </label><input class="neccesary_input input" id="pass_input" maxlength=100 name="pass_input"    type="password" />
               <br><label for="passagain_input" class="neccesary">*Пароль еще раз: </label><input class="neccesary_input input" id="passagain_input" maxlength=100 name="pass_input" type="password"   />
               <!--<br><label for="skype_input">Skype: </label><input id="skype_input" maxlength=100 name="skype_input" class="input"   />-->
               <p><a id="register_button" onclick="register.now()" href="javascript:void(0)" class="btn transparent blue">Зарегистрироваться</a>
            </div>
         </div>
      </div>
      
      <?php echo $constructor->getFooter();?>
      
      <script type="text/javascript" src="js/register.js"></script>
      <script type="text/javascript" src="js/include/dropdown.js"></script>
      <script type="text/javascript" src="js/include/auth.js"></script>
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/form.js"></script>
      <script type="text/javascript" src="js/include/utils.js"></script>
      <script type="text/javascript" src="js/include/json2.js"></script>
   </body>
</html>

<?php

mysql_close();

?>