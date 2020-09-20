// индивидуальный файл для страницы обявления

var post_url = "./php/post.php";
var item_num = null;
var slider = null;
var utils = null;
var comments = null;
var user = null;
var author = null; // здесь автор
var myself = null; // здесь текущией юзер если логинен, либо -1 если неь
var xlinks = null;
var item = null;
var mail = null;
var feedback = null;
var category = null;
var region = null;
var dropdown = null;
var form = null;


$(document).ready(function(){
   utils = new Utils();
   item_num = utils.getUrlParameter("num");
   item = new Item(item_num); // позже должно всместо 13 проставляться автоматически
   comments = new Comments();
   user = new User();
   xlinks = new Crosslinks();
   mail = new Mail();
   feedback = new Feedback();
   category = new Category();
   comments.get(item.num);
   xlinks.get(item.num);
   region = new Region();
   dropdown = new Dropdown();
   region.current_region = utils.getUrlParameter("region") != undefined && utils.getUrlParameter("region") != "" ? utils.getUrlParameter("region") : -1;

   region.getRegionData();
   region.getCityData();
   getMySelf();
   setupDom();
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
   
    $('.photos-wrapper').hover(
    function(){
        $('.photos-wrapper > .arrow')
            .show()
            .height($('.photos-wrapper').height())
            .css("margin-top", "-"+$('.photos-wrapper').height()+"px");
    }, 
    function(){
        $('.photos-wrapper > .arrow').hide()
    });
}