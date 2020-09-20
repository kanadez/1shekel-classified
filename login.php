<?php

require_once dirname(__FILE__)."/php/include/class_db.php";
require_once dirname(__FILE__)."/php/include/class_user.php";
require_once dirname(__FILE__)."/php/include/class_constructor.php";

//session_start(); 
//if (!isset($_SESSION["user_num"])) $_SESSION["user_num"] = 0;

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

session_start();

if (isset($_GET['logout'])){
   unset($_SESSION["user_num"]);
   session_destroy(); // разрушаем сессию
   header('Location: login.php');
}
elseif (isset($_SESSION['user_num'])){
   header('Location: profile.php');
}
elseif (isset($_GET['code'])){
    //$jsonUrl = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false";
    $url = "https://graph.facebook.com/oauth/access_token?client_id=1554246761276483&redirect_uri=http://".$_SERVER['HTTP_HOST']."/login.php&client_secret=459c281e962888320e798a055eed8697&code=".$_GET["code"];
    
    $geocurl = curl_init();
    curl_setopt($geocurl, CURLOPT_URL, $url);
    curl_setopt($geocurl, CURLOPT_HEADER,0); //Change this to a 1 to return headers
    curl_setopt($geocurl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
    curl_setopt($geocurl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($geocurl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($geocurl);
    curl_close($geocurl);
    $parsed = json_decode($result, true);
    $url2 = "https://graph.facebook.com/me?access_token=".$parsed["access_token"];
    $geocurl = curl_init();
    curl_setopt($geocurl, CURLOPT_URL, $url2);
    curl_setopt($geocurl, CURLOPT_HEADER,0); //Change this to a 1 to return headers
    curl_setopt($geocurl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
    curl_setopt($geocurl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($geocurl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($geocurl);
    curl_close($geocurl);
    $parsed = json_decode($result, true);
    //var_dump($parsed);
    //exit();
    try{
        if (isset($parsed["error"])){
            throw new Exception("fb_auth_error", 502);
        }
        
        $user->loginVK($parsed["id"], $parsed["name"], "", "http://graph.facebook.com/".$parsed["id"]."/picture", "");
        Header("Location: profile.php");
    }
    catch (Exception $e){
        Header("Location: login.php?error=".$e->getCode());
    }
}
else{
   if (isset($_POST['password'])){
      $login = $_POST['login'];
      $password = md5($_POST['password']);
      $query = "SELECT `num` FROM `user` WHERE `email` = '$login' AND `passwd` = '$password' LIMIT 1";
      $sql = mysql_query($query) or die(mysql_error());
   
      if (mysql_num_rows($sql) == 1){
         $row = mysql_fetch_assoc($sql);
         $_SESSION['user_num'] = $row['num'];
         $sql = sprintf("UPDATE `user` SET `last_seen` = '%s' WHERE `num` = %d;",
            mysql_real_escape_string(time()),
            mysql_real_escape_string($row['num']));
            
         mysql_query($sql);
         header('Location: profile.php');
      }
      else{
        header('Location: login.php?error=501');
      }
   }
   else{
      echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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
      <script type="text/javascript" src="js/include/md5.js"></script>
   </head>
   <body>
        <script type="text/javascript" src="js/include/facebook_auth.js"></script>
      '.$constructor->getHeader().'
      <div class="clearfix"></div>
      <div class="main-wrapper"> 
         <div id="content-area">
            <h4 class="header_centered">Вход на сайт</h4>
            <div class="block_wrapper">
            <div id="password_alert" class="alert" style="'.(isset($_GET["error"]) && $_GET["error"] == 501 ? "display:block" : "").'">Ошибка: неверные e-mail или пароль</div>
                <div id="fb_auth_error_alert" class="alert" style="'.(isset($_GET["error"]) && $_GET["error"] == 502 ? "display:block" : "").'">Ошибка авторизации через Facebook</div>
            <form id="login-form" action="login.php" method="post">   
               <label for="login_input" style="clear:both; display:none" class="neccesary">E-Mail: </label><input id="login_input" maxlength=100 name="login" placeholder="E-Mail" class="neccesary_input login_input" />
               <br><label for="password_input" class="neccesary" style="display:none">Пароль: </label><input id="password_input" type="password" maxlength=100 placeholder="Пароль" name="password" class="neccesary_input login_input" />
               <br><a id="forgot_a" href="forgot.php">Напомнить пароль</a>
               <p><input id="login_button" href="javascript:void(0)" class="btn transparent blue login_button" type="submit" value="Войти" /><a href="register.php" class="btn transparent grey login_button">Регистрация</a>
               <p>
               <br><span style="margin-right:54px; color:#777;">Войти через социальные сети:</span>
               <div id="socialpanel" class="pluso-sharer pluso--theme11 pluso--square-corner pluso--big pluso--horizontal pluso--multiline " style=""></a>
                  
                    <a id="sf_vkontakte" class="pluso__vkontakte pluso_link " style="display:none" target="_blank" title="ВКонтакте" to="1" onclick="VK.Auth.login(loginAuthInfo);"></a>
                  <a id="sf_odnoklassniki" class="pluso__odnoklassniki pluso_link" title="Одноклассники" to="2" href="https://connect.ok.ru/oauth/authorize?client_id=1251268608&scope=VALUABLE_ACCESS&response_type=token&redirect_uri=http://'.$_SERVER['HTTP_HOST'].'/login.php&layout=w"></a>
                  <a id="sf_facebook" class="pluso__facebook pluso_link " target="_blank" title="Facebook" to="1" href="https://www.facebook.com/dialog/oauth?client_id=1554246761276483&redirect_uri=http%3A%2F%2F'.$_SERVER['HTTP_HOST'].'%2Flogin.php&response_type=code"></a>
            </form>
            </div>
         </div>
      </div>
      '.$constructor->getFooter().'
      <script type="text/javascript" src="js/login.js"></script>
      <!--Мой профиль-->
      <script type="text/javascript" src="js/include/dropdown.js"></script>
      <script type="text/javascript" src="js/include/auth.js"></script>
      <!--Мои объявления-->
      <script type="text/javascript" src="js/include/form.js"></script>
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/utils.js"></script>
      <script type="text/javascript" src="js/include/json2.js"></script>
   </body>
</html>';
   }
}


mysql_close();

?>