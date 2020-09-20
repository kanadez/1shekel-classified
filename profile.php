<?php

require_once dirname(__FILE__)."/php/include/class_db.php";
require_once dirname(__FILE__)."/php/include/class_user.php";
require_once dirname(__FILE__)."/php/include/class_constructor.php";
require_once dirname(__FILE__)."/php/include/class_profile.php";

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
   $profile = new Profile;
}

if (isset($_GET['token'])){
    $sql = sprintf("SELECT `num` FROM `user` WHERE `item_token` = '%s';",
         mysql_real_escape_string($_GET['token']));
    $result = $db->db_fetchone_array($sql, __LINE__, __FILE__);
    
    if (count($result) > 0){
        $_SESSION["user_num"] = $result["num"];
    }
    else{
        header('Location: login.php');
    }
}
elseif (!isset($_SESSION['user_num'])) {
   header('Location: login.php');
}

$profile->seen();

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
      <link rel="stylesheet" href="css/nouislider.fox.css" type="text/css">
      <link rel="stylesheet" href="css/custom.css" type="text/css">
      <link rel="stylesheet" href="css/user.css" type="text/css">
      <link rel="stylesheet" href="css/profile.css" type="text/css">
      <link rel="stylesheet" href="css/form.css" type="text/css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.min.css">
      <link rel="stylesheet" media="all" type="text/css" href="css/screen.edited.css">
      <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
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
      <script type="text/javascript" src="js/include/jquery.sizes.js"></script>
      <script type="text/javascript" src="js/include/md5.js"></script>
      <script src="//vk.com/js/api/openapi.js" type="text/javascript"></script>
   </head>
   <body>
      <script>
           (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
          }(document, 'script', 'facebook-jssdk'));
          
          window.fbAsyncInit = function() {
            FB.init({
              appId      : '1797482193845398',
              cookie     : true,
              xfbml      : true,
              version    : 'v2.8'
            });
          }
      </script>
      <?php echo $constructor->getHeader(); ?>
      
      <div class="clearfix"></div>
      <div class="main-wrapper"> 
          <div id="side-panel" <?php echo $profile->getBannedStatus() == 1 ? "style='display:none;'" : ""; ?>>
            <div id="profiledata-wrapper" class="data-wrapper">
               <div id="profilephoto-wrapper">
                  <div id="profile_photo_img"><img id="profilephoto" src="http://avral.by/img/camera.png" /></div>
                  <div id="profile_photo_edit_wrapper"></div>
                  <input id="profile_photo_edit_input" type="file" name="file" data-url="upload_avatar.php" />
               </div>
               <div id="profilename"></div>
            </div>
            <br><div class="panel-divider"></div>
            <div id="accountdata_wrapper" class="data-wrapper">
               
               <div id="balance_div">0 баллов</div>
               <div style="color:#777; font-size:0.9em">на Вашем балансе</div>
               <a href="javascript:void(0)" onclick="profile.fillBalance()" class="btn transparent orange fw">Пополнить баланс</a>
               <a id="bill-button" href="javascript:void(0)" onclick="profile.promotions()" class="btn transparent blue fw">Заработать баллы</a>
            </div>
            <br><div class="panel-divider"></div>
            <div class="data-wrapper">
               <ul id="side_panel">
                  <li><a id="edit_button" href="javascript:void(0)" onclick="profile.edit()">Мой профиль</a></li>
                  <li><a id="items_button" href="javascript:void(0)" onclick="profile.items()">Мои объявления</a></li>
                  <li><a id="mail_button" href="javascript:void(0)" onclick="profile.mail()">Мои сообщения</a></li>
                  <li><a id="credits_button" href="javascript:void(0)" onclick="profile.credits()">Мои баллы</a></li>
                  <li><a id="credits_button" href="javascript:void(0)" onclick="profile.logout()">Выйти</a></li>
               </ul>
            </div>
         </div>
         <div id="content-area" <?php echo $profile->getBannedStatus() == 1 ? "style='display:none;'" : ""; ?>></div>
         <div id="banned-content-area" <?php echo $profile->getBannedStatus() == 0 ? "style='display:none;'" : ""; ?>>
             <div>
                 К сожалению, Ваш профиль забанен администратором.
             </div>
         </div>
      </div>
      
      <?php echo $constructor->getFooter();?>
      
      <script type="text/javascript" src="js/profile.js"></script>
      <!--Мой профиль-->
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/category.js"></script>
      <script type="text/javascript" src="js/include/feedback.js"></script>
      <script type="text/javascript" src="js/include/auth.js"></script>
      <!--Мои объявления-->
      <script type="text/javascript" src="js/include/form.js"></script>
      <script type="text/javascript" src="js/include/dropdown.js"></script>
      <script type="text/javascript" src="js/include/utils.js"></script>
      <script type="text/javascript" src="js/include/json2.js"></script>
      
      <script src="js/include/jquery.ui.widget.js"></script>
      <script src="js/include/jquery.iframe-transport.js"></script>
      <script src="js/include/jquery.fileupload.js"></script>
   </body>
</html>

<?php

mysql_close();

?>