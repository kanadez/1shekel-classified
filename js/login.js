var post_url = "./php/post.php";
var utils = null;
var region = null;
var dropdown = null;

$(document).ready(function(){
   VK.init({
     apiId: 5188263
   });
   
   utils = new Utils();
   region = new Region();
   dropdown = new Dropdown();
   region.current_region = utils.getUrlParameter("region") != undefined && utils.getUrlParameter("region") != "" ? utils.getUrlParameter("region") : -1;

   region.getRegionData();
   region.getCityData();
   
   setupDom();
   
   if (utils.getHashParameter("access_token") != undefined)
      okLogin();
  
    
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