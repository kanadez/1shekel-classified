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
         <div id="content-area" style="padding-top:14% !important;text-align:left">
            <h3 >Правила модерации на проекте Project NEON</h3>
            <hr/>
            <div class="block_wrapper" style="width:100% !important; text-align:left;">
               <p><strong>Запрещено:</strong></p>
               <ol>
                <li>Одним пользователем регистрировать несколько учетных записей.</li>
                <li>В&nbsp;заголовке или описании объявления, в&nbsp;имени пользователя, на&nbsp;фотографии&nbsp;&mdash; указывать контактную или персональную информацию (ICQ, адрес сайта, телефон, и&nbsp;т.д.).</li>
                <li>Указывать в&nbsp;профиле информацию о&nbsp;третьих лицах.</li>
                <li>Публиковать объявления в&nbsp;категории/подкатегории ,которая не&nbsp;соответствует смыслу объявления.</li>
                <li>Публиковать одно и&nbsp;то&nbsp;же объявление в&nbsp;разных категориях.В описании товара/услуги использовать латинские буквы (если это не&nbsp;название бренда);</li>
                <li>В&nbsp;описании товара/услуги использовать:
                <ul>
                    <li>латинские буквы (если это не&nbsp;название бренда);</li>
                    <li>писать слова с&nbsp;пробелами между буквами;</li>
                    <li>весь текст объявления или отдельные слова писать заглавными буквами;</li>
                    <li>писать бессмысленный набор ключевых слов;</li>
                    <li>размещать ссылки на&nbsp;Интернет-ресурсы, которые могут нанести вред компьютеру пользователя;</li>
                    <li>в&nbsp;одном объявлении размещать информацию о&nbsp;нескольких товарах и&nbsp;услугах одновременно;</li>
                    <li>публиковать объявление о&nbsp;несуществующем товаре или услуге.</li>
                </ul>
                </li>
                <li>Размещать фотографии, которые содержат какую-либо рекламную информацию (название компании, логотип, координаты, и.т.д.), кроме объявлений магазинов.</li>
                <li>Размещать фотографии плохого качества, где изображаемый предмет неразличим.</li>
                <li>Размещать фотографии, не&nbsp;соответствующие тематике объявления.</li>
                <li>В&nbsp;строке &laquo;цена&raquo; вводить набор чисел, например: &laquo;110112&raquo;.</li>
                <li>Размещать объявления, баннеры предлагающие &laquo;лёгкий заработок&raquo; в&nbsp;интернете.</li>
                <li>В&nbsp;тексте объявлений и&nbsp;комментариях использовать нецензурную лексику.</li>
                <li>Вмешиваться в&nbsp;процессы функционирования сайта.</li>
                <li>Намеренно создавать имена пользователей, схожие с&nbsp;уже существующими именами.</li>
                <li>Дублировать объявления</li>
            </ol>
            <p>&nbsp;</p>
            <p><strong>Модератор имеет право:</strong></p>
            <ol>
                <li>Вносить изменения в&nbsp;текст объявления, касающиеся орфографии и&nbsp;пунктуации, не&nbsp;влияющие на&nbsp;общий смысл объявления.</li>
                <li>Переносить объявления в&nbsp;другие категории/подкатегории Сайта в&nbsp;случае выявления более подходящей категории/подкатегории для их&nbsp;размещения.</li>
                <li>Изменять порядок размещения фотографий в&nbsp;объявлении.</li>
                <li>Удалять фотографии при необходимости.</li>
                <li>Блокировать/удалять объявление пользователя при необходимости.</li>
                <li>Блокировать/удалять пользователя.</li>
            </ol>
            <p>&nbsp;</p>
            <p><strong>Основные причины удаления объявлений:</strong></p>
            <ol>
                <li>Не&nbsp;заполнены основные поля объявления.</li>
                <li>Заголовок объявления не&nbsp;соответствует описанию предлагаемого товара/услуги.</li>
                <li>В&nbsp;тексте или заголовке объявления содержатся множественные орфографические и&nbsp;пунктуационные ошибки.</li>
                <li>В&nbsp;объявлении размещено фото порнографического характера.</li>
                <li>Текст объявления содержит информацию ,связанную с&nbsp;пропагандой насилия, ненависти, расовой вражды, клеветой.</li>
                <li>В&nbsp;тексте объявления содержится информация о&nbsp;запрещенных законодательством Украины товарах/услугах: наркотических веществах; оружии; лекарственных препаратах; экзотических диких животных; покупке-продаже иностранной валюты; больничные листы; готовые дипломные работы; контрабандных товарах; покупке/продаже нелицензионного программного обеспечения и&nbsp;услуг по&nbsp;его установке и&nbsp;настройке; оказании услуг финансового характера (поручительство, кредит, и&nbsp;т.д.); услуги, связанные с&nbsp;оккультной тематикой: нетрадиционная медицина, колдовство, магия, и&nbsp;т.д.</li>
            </ol>
            </div>
         </div>
      </div>
      
      <?php echo $constructor->getFooter();?>
      
      <script type="text/javascript" src="js/terms.js"></script>
      <script type="text/javascript" src="js/include/utils.js"></script>
      <script type="text/javascript" src="js/include/region.js"></script>
      <script type="text/javascript" src="js/include/dropdown.js"></script>
   </body>
</html>

<?php

mysql_close();

?>