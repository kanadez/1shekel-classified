var post_url = "./php/post.php";
var vipslider = null;
var categories_state = 0;
var region = null;
var dropdown = null;

$(document).ready(function(){
   setupDom();
   vipslider = new VipSlider();
   region = new Region();
   dropdown = new Dropdown();

   region.getRegionData();
   region.getCityData();
});

function setupDom(){
   $("#inputSearch").keypress(function(e){
      if(e.keyCode==13){
         location.href = "/catalog.php?page=1&query="+$("#inputSearch").val()+(region.current_region != -1 ? "&region="+region.current_region : "")+(region.current_city != -1 ? "&city="+region.current_city : "");
      }
   });
   
   $('#search_button').click(function(){
      location.href = "/catalog.php?page=1&query="+$("#inputSearch").val()+(region.current_region != -1 ? "&region="+region.current_region : "")+(region.current_city != -1 ? "&city="+region.current_city : "");
   });
   
   $('#inputSearch').focus(function(){
      $('#search-history').show().animate({"opacity":"1"},300);
   });
   
   $('#inputSearch').blur(function(){
      $('#search-history').animate({"opacity":"0"},300,function(){$('#search-history').hide()});
   });
   
   $('#region_dropdown_menu_a').click(function(){
      $('#region_dropdown_menu_ul').show();
   });
   
   $(document).click(function(e){
      var target = $(e.target);
        if (!target.is('#region_dropdown_menu_span_name') && !target.is('#region_dropdown_menu_ul') && !target.is('#region_dropdown_menu_a') && !target.is('#search-region-ac-title')) $('#region_dropdown_menu_ul').hide();
   });
   
   $('.gallery_element_box').each(function(){
      $(this).height($(this).width()/1.25);
   });
}

//############################### index.php script ####################################//

function switchCategories(state){ // state - состояние блока категорий. 0 - свернуто, 1 - развернуто
   if (categories_state != state){
      categories_state = state;
      
      switch (state){
         case 0:
            $('.subcategories').hide();
            break;
         case 1:
            $('.subcategories').show();
            break;
      }
      
      $('#short').toggleClass("active");
      $('#full').toggleClass("active");
   }
}

function resizeOnLoadVip(image){
   var elm = $(image);
   elm.height(elm.parent().height());
}