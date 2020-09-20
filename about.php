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
      <link rel="stylesheet" href="css/form.css" type="text/css">
      <link rel="stylesheet" href="css/feedback.css" type="text/css">
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
         <div id="content-area" style="padding-top:14% !important; text-align:left">
            <h3>О нас</h3>
				<hr/>
				<div>
			      <p dir="ltr"><strong>Project NEON </strong>&mdash; это &nbsp;&nbsp;&nbsp;простая и &nbsp;эффективная &nbsp;&nbsp;доска &nbsp;&nbsp;&nbsp;&nbsp;бесплатных &nbsp;&nbsp;&nbsp;онлайн-объявлений.</p>
               <p dir="ltr"><strong>Project NEON </strong>&mdash; предоставляет &nbsp;&nbsp;эффективное &nbsp;&nbsp;решение &nbsp;&nbsp;&nbsp;сложностей, &nbsp;&nbsp;которые &nbsp;&nbsp;сопровождают процесс продажи, покупки, торговли самыми популярными &nbsp;товарами и услугами.</p>
               <p dir="ltr">С нами все очень просто: зашел, разместил объявления и купил или продал все что пожелаешь!</p>
               <p dir="ltr">На &nbsp;доске онлайн - объявлений Project NEON Вы сможете быстро &nbsp;разместить или &nbsp;найти объявление &nbsp;в таких разделах &nbsp;как: Недвижимость, Авто, мото, &nbsp;Электроника, &nbsp;Одежда, аксессуары, &nbsp;Работа, &nbsp;Бизнес и услуги, &nbsp;Детский мир, &nbsp;Животные, &nbsp;&nbsp;Дом и сад, &nbsp;Отдых, хобби, &nbsp;&nbsp;Друзья и знакомства. &nbsp;</p>
               <p dir="ltr">Наши пользователи &nbsp;&nbsp;&mdash; это люди, которые ценят свое время и &nbsp;деньги, любят торговаться, предпочитая покупать &nbsp;товар дешевле, получая при этом сервис, высокое качество товаров и услуг. &nbsp;</p>
               <p dir="ltr">Доска онлайн - объявлений &nbsp;Project NEON заполнена &nbsp;товарами &nbsp;разной &nbsp;ценовой категории &ndash; &nbsp;от дешевых &nbsp;до &nbsp;дорогих &nbsp;брендовых товаров, при этом они будут стоить в несколько раз дешевле, чем в магазине или в торговом центре.</p>
               <p dir="ltr">С &nbsp;&nbsp;Project NEON &nbsp;&nbsp;Вам будет &nbsp;&nbsp;просто &nbsp;&nbsp;продавать, &nbsp;&nbsp;&nbsp;&nbsp;и &nbsp;&nbsp;покупать товары в любое время суток, в любом городе Украины. &nbsp;</p>
               <p dir="ltr"><strong>Project NEON </strong>- это:</p>
               <p dir="ltr">&bull;	Простой способ размещения &nbsp;объявления;</p>
               <p dir="ltr">&bull;	Эффективный процесс покупки продажи товаров и услуг;</p>
               <p dir="ltr">&bull;	Высокий сервис;</p>
               <p dir="ltr">&bull;	Доступ к сайту через &nbsp;Ваш мобильный телефон или &nbsp;планшет;</p>
               <p dir="ltr">&bull; Высокое качество контента.</p>
               <p dir="ltr"><em>Project NEON &ndash; с нами все гораздо проще!</em><span style="font-size: 21px; font-family: Arial; background-color: transparent; font-weight: bold; vertical-align: baseline; white-space: pre-wrap;"><br />
               </span></p>				
            </div>
         </div>
      </div>
      
      <?php echo $constructor->getFooter();?>
      
      <script type="text/javascript" src="js/about.js"></script>
      <script type="text/javascript" src="js/include/dropdown.js"></script>
      <script type="text/javascript" src="js/include/auth.js"></script>
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/utils.js"></script>
   </body>
</html>

<?php

mysql_close();

?>