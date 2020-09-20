var post_url = "./php/post.php";
var user = null; // object
var utils = null;
var category = null;
var region = null;
var dropdown = null;
var form = null;
var mail = null;
var myself = null;

var user_num = null; // integer

$(document).ready(function(){
   setupDom();
   user = new User();
   utils = new Utils();
   category = new Category();
   region = new Region();
   dropdown = new Dropdown();
   catalog_common = new CatalogCommon();
   mail = new Mail();
   
   user_num = utils.getUrlParameter("num");
   catalog_common.page = utils.getUrlParameter("page");
   category.category = utils.getUrlParameter("cat");
   user.get(user_num);
   category.getUserCategories(user_num);
   catalog_common.getUserData(user_num);
   region.current_region = utils.getUrlParameter("region") != undefined && utils.getUrlParameter("region") != "" ? utils.getUrlParameter("region") : -1;

   region.getRegionData();
   region.getCityData();
   getMySelf();
});

function setupDom(){
   $('#region_dropdown_menu_a').click(function(){
      $('#region_dropdown_menu_ul').show();
   });
   
   $(document).click(function(e){
      var target = $(e.target);
        if (!target.is('#region_dropdown_menu_span_name') && !target.is('#region_dropdown_menu_ul') && !target.is('#region_dropdown_menu_a') && !target.is('#search-region-ac-title')) $('#region_dropdown_menu_ul').hide();
   });
   
   $("#catalog_search_input").keypress(function(e){
      if(e.keyCode==13){
         location.href = "/catalog.php?page=1&query="+$("#catalog_search_input").val()+(region.current_region != -1 ? "&region="+region.current_region : "")+(region.current_city != -1 ? "&city="+region.current_city : "");
      }
   });
   
   $('#search_button').click(function(){
      location.href = "/catalog.php?page=1&query="+$("#catalog_search_input").val()+(region.current_region != -1 ? "&region="+region.current_region : "")+(region.current_city != -1 ? "&city="+region.current_city : "");
   });
}