<?php

require_once dirname(__FILE__)."/include/class_db.php";
require_once dirname(__FILE__)."/include/class_region.php";
require_once dirname(__FILE__)."/include/class_minuswords.php";
require_once dirname(__FILE__)."/include/class_item.php";
require_once dirname(__FILE__)."/include/class_comments.php";
require_once dirname(__FILE__)."/include/class_user.php";
require_once dirname(__FILE__)."/include/class_mail.php";
require_once dirname(__FILE__)."/include/class_feedback.php";
require_once dirname(__FILE__)."/include/class_idioma.php";
require_once dirname(__FILE__)."/include/class_category.php";
require_once dirname(__FILE__)."/include/class_catalog.php";
require_once dirname(__FILE__)."/include/class_parameter.php";
require_once dirname(__FILE__)."/include/class_filter.php";
require_once dirname(__FILE__)."/include/class_currency.php";
require_once dirname(__FILE__)."/include/class_profile.php";
require_once dirname(__FILE__)."/include/class_dummy.php";
require_once dirname(__FILE__)."/include/class_search.php";

$idioma = new Idioma;
$language = "ru";

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
   
   $region = new Region;
   $minuswords = new MinusWords;
   $item = new Item;
   $comments = new Comments;
   $user = new User;
   $mail = new Mail;
   $feedback = new Feedback;
   $category = new Category;
   $parameter = new Parameter;
   $catalog = new Catalog;
   $filter = new Filter;
   $currency = new Currency;
   $profile = new Profile;
   $dummy = new Dummy;
   $search = new Search;
}

switch ($_POST["a"]){
    case "user_get_recaptcha_response": 
        echo $user->getRecaptchaResponse($_POST["b"]);
    break;
    
    case "mail_setemailread":
        echo $mail->setEmailDialogRead($_POST["b"]);
    break;
    
    case "profile_getbannedstatus":
        echo $profile->getBannedStatus();
    break;
    
    case "item_check_minuswords":
        echo $minuswords->checkText($_POST["b"]);
    break;
    
    case "user_check_email_exist":
        echo $user->checkEmailExist($_POST["b"]);
    break;
    
    case "profile_get_coin_currency":
        echo $profile->getCoinCurrency();
    break;
    
    case "currency_convert":
        echo $currency->convert($_POST["b"], $_POST["c"], $_POST["d"]);
    break;
    
    case "profile_create_payment":
        echo $profile->createPayment($_POST["b"]);
    break;
    
    case "profile_prolong_item":
        echo $profile->prolongItem($_POST["b"]);
    break;
    
   case "profile_get_dummies":
      echo $profile->getDummies($_SESSION["user_num"]);
   break;
   
   case "newitem_photo_delete":
      echo $item->deleteTempPhoto($_POST["b"], $_POST["c"]);
   break;
   
   case "item_edit":
      echo $item->edit($_POST["b"], $_POST["c"]);
   break;
   
   case "catalog_get_favorites":
      echo $catalog->getFavorites($_POST["b"], $_POST["c"]);
   break;
   
   case "dummy_feedback":
      echo $dummy->feedback($_POST["b"], $_POST["c"], $_POST["d"]);
   break;
   
   case "user_register":
      echo $user->register($_POST["b"]);
   break;
   
   case "profile_get":
      echo $user->get($_SESSION["user_num"]);
   break;
   
   case "user_login_vk":
      echo $user->loginVK($_POST["b"], $_POST["c"], $_POST["d"], $_POST["e"], $_POST["f"]);
   break;
   
   case "user_login_ok":
      echo $user->loginOK($_POST["b"], $_POST["c"], $_POST["d"], $_POST["e"], $_POST["f"]);
   break;
   
   case "region_get_city_by_code":
      echo $region->getCityByCode($_POST["b"]);
   break;
   
   case "region_get_name_by_code":
      echo $region->getNameByCode($_POST["b"]);
   break;
   
   case "item_report":
      echo $item->report($_POST["b"], $_POST["c"]);
   break;
   
   case "newitem_get_user_logined":
      echo $item->getLoginedUserForNewitem();
   break;
   
   case "newitem_get_user":
      echo $item->getUserForNewitem($_POST["b"], $_POST["c"]);
   break;
   
   case "item_get_data_for_new_form":
      echo $item->getDataForNewForm($_POST["b"]);
   break;
   
   case "item_add_from_preview":
      echo $item->addFromPreview($_POST["b"], $_POST["c"]);
   break;
   
   case "item_preview":
      echo $item->preview($_POST["b"], $_POST["c"], isset($_SESSION["user_num"]) ? $_SESSION["user_num"] : -1, $_POST["d"]);
   break;
   
   case "item_add":
      echo $item->add($_POST["b"], $_POST["c"], isset($_SESSION["user_num"]) ? $_SESSION["user_num"] : -1, $_POST["d"]);
   break;
   
   case "region_get_metro":
      echo $region->getMetroData();
   break;
   
   case "category_get_everything":
      echo $category->getEverything();
   break;
   
   case "catalog_search_highlighted":
      echo $catalog->searchHighlighted($_POST["b"], $_POST["c"], $_POST["d"], $_POST["e"], $_POST["f"], $_POST["g"], $_POST["h"], $_POST["i"]);
   break;
   
   case "catalog_get_highlighted":
      echo $catalog->getHighlighted($_POST["b"], $_POST["c"], $_POST["d"], $_POST["e"], $_POST["f"], $_POST["g"], $_POST["h"]);
   break;
   
   case "feedback_highlight_item":
      echo $feedback->highlightItem($_SESSION["user_num"], $_POST["b"]);
   break;
   
   case "feedback_vip_item":
      echo $feedback->vipItem($_SESSION["user_num"], $_POST["b"]);
   break;
   
   case "feedback_get_vip_rates":
      echo $feedback->getVipRates();
   break;
   
   case "feedback_promote_item":
      echo $feedback->promoteItem($_SESSION["user_num"], $_POST["b"]);
   break;
   
    case "feedback_get_promote_limits":
      echo $feedback->getPromoteLimits($_SESSION["user_num"]);
   break;

   case "feedback_get_promoted_by_user":
      echo $feedback->getPromotedByUser($_SESSION["user_num"], $_POST["b"]);
   break;
   
   case "profile_get_promotions":
      echo $profile->getPromotions($_SESSION["user_num"], $_POST["b"], $_POST["c"], $_POST["d"]);
   break;
   
   case "profile_get_credits":
      echo $profile->getCredits($_SESSION["user_num"], $_POST["b"]);
   break;
   
   case "mail_write":
      echo $mail->write($_SESSION["user_num"], $_POST["b"], $_POST["c"], $_POST["d"], $_POST["e"]);
   break;
   
   case "mail_get_dialog":
      echo $mail->getDialog($_SESSION["user_num"], $_POST["b"]);
   break;
   
   case "mail_get_locutors":
      echo $mail->getLocutors($_SESSION["user_num"], $_POST["b"]);
   break;
   
   case "mail_get_dialogues":
      echo $mail->getDialogues($_SESSION["user_num"]);
   break;
   
   case "profile_restore_item":
      echo $profile->restoreItem($_SESSION["user_num"], $_POST["b"]);
   break;
   
   case "profile_close_item":
      echo $profile->closeItem($_SESSION["user_num"], $_POST["b"]);
   break;
   
   case "profile_remove_item":
      echo $profile->removeItem($_SESSION["user_num"], $_POST["b"]);
   break;
   
   case "profile_get_items":
      echo $profile->getItems($_SESSION["user_num"], $_POST["b"], $_POST["c"]);
   break;
   
   case "profile_set_personal_data":
      echo $profile->setPersonalData($_POST["b"], $_POST["c"], $_POST["d"], $_POST["e"], $_POST["f"]);
   break;
   
   case "profile_change_email":
      echo $profile->sendEmailConfirm($_POST["b"], $_POST["c"]);
   break;
   
   case "profile_compare_passwd":
      echo $profile->comparePasswd($_POST["b"], $_POST["c"], $_POST["d"]);
   break;
   
   case "category_getusercategorynames":
      echo $category->getUserCatalogCategoryNames($_POST["b"]);
   break;
   
   case "category_getusercategories":
      echo $category->getUserCatalogCategories($_POST["b"]);
   break;
   
   case "user_catalog_get" :
      echo $user->getCatalog($_POST["b"], $_POST["c"], $_POST["d"]);
   break;
   
   case "category_getforsidepanel" :
      echo $category->getSidePanelCategories($_POST["b"], $_POST["c"]);
   break;
   
   case "catalog_getpricerange" :
      echo $catalog->getPriceRange($_POST["b"], $_POST["c"], $_POST["d"]);
   break;
   
   case "currency_getlist" :
      echo $currency->getList();
   break;
   
   case "filter_getdefaults" :
      echo $filter->getDefaults($_POST["b"], $_POST["c"], $_POST["d"]);
   break;
   
   case "catalog_search" :
      echo $catalog->search($_POST["b"], $_POST["c"], $_POST["d"], $_POST["e"], $_POST["f"], $_POST["g"], $_POST["h"], $_POST["i"]);
   break;
   
   case "catalog_get" :
      echo $catalog->get($_POST["b"], $_POST["c"], $_POST["d"], $_POST["e"], $_POST["f"], $_POST["g"], $_POST["h"]);
   break;
   
   case "category_getadvsubcategories" :
      echo $category->getAdvSubCategories($_POST["b"], $_POST["c"]);
   break;
   
   case "category_getsubcategories" :
      echo $category->getSubCategories($_POST["b"]);
   break;
   
   case "category_getcategories" :
      echo $category->getCategories();
   break;
   
   case "item_getcrumbs" :
      echo $item->getCrumbs($_POST["b"], $_POST["c"], $_POST["d"]);
   break;
   
   case "idioma_get_variables" :
      echo $idioma->getVariables($_POST["b"]);
   break;
   
   case "feedback_repost_by" :
      echo $feedback->repostBy($_POST["b"], $_POST["c"]);
   break;
   
   case "mail_send" :
      echo $mail->sendMessage($_SESSION["user_num"], $_POST["b"], $_POST["c"], $_POST["d"]);
   break;
   
   case "crosslinks_get" :
      echo $item->getCrossLinks($_POST["b"]);
   break;
   
   case "user_getPhoto" :
      echo $user->getPhoto($_POST["b"]);
   break;
   
   case "user_getAlot" :
      echo $user->getAlot($_POST["b"]);
   break;
   
   case "user_get_myself" :
      echo $user->getMySelf($_SESSION["user_num"]);
   break;
   
   case "user_get" :
      echo $user->get($_POST["b"]);
   break;
   
   case "comments_write" :
      echo $comments->write($_POST["b"], $_POST["c"]);
   break;
   
   case "comments_get" :
      echo $comments->get($_POST["b"]);
   break;
   
   case "gI" :
      echo $item->getItemData($_POST["b"], $_POST["c"]);
   break;
   
   case "gRD" :
      echo $region->getRegionData();
   break;
   
   case "gCD" :
      echo $region->getCityData();
   break;
   
   case "tRC" :
      echo $region->test();
   break;
   
   case "testing" :
      echo "testing is ok";
   break;

   default : 
      exit();
}

mysql_close();

?>