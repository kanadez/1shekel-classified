var post_url = "./php/post.php";
var utils = null;
var dropdown = null;
var region = null;
var favorites = null;
var catalog_common = null;

$(document).ready(function(){
   utils = new Utils();
   dropdown = new Dropdown();
   region = new Region();
   favorites = new Favorites();
   catalog_common = new CatalogCommon();
   catalog_common.page = utils.getUrlParameter("page");
   region.current_region = utils.getUrlParameter("region") != undefined && utils.getUrlParameter("region") != "" ? utils.getUrlParameter("region") : -1;
   
   region.getRegionData();
   region.getCityData();
   
   initDOM();
   favorites.get();
});

function initDOM(){
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

function Favorites(){
   this.data = {};
   
   this.get = function(){ // все ф-ии рабобтают зависимо от текущего состояния фильрации, пагинации и категорий
      $.post(post_url,{
         a: "catalog_get_favorites",
         b: localStorage.favorites,
         c: catalog_common.page-1
      },function (result){
         favorites.data = JSON.parse(result);
         favorites.showCatalogList(favorites.data);
      });
   };
   
   this.showCatalogList = function(favorites_data){ // output_mode 1 - list, 2 - cells
      for (var i = 0; i < favorites_data.length; i++){
         if (i != favorites_data.length-1){
            if (utils.isset(favorites_data[i].photo)){
               var photo_data = JSON.parse(favorites_data[i].photo);
               $('#catalog_list_div').append('<div id="catalog_item_'+favorites_data[i].num+'" onmouseover="catalog_common.showQuickViewWrapper('+favorites_data[i].num+')" onmouseout="catalog_common.hideQuickViewWrapper('+favorites_data[i].num+')" class="item-wrapper"><div class="item-top-details" style="width: 876px;"></div><a class="item" href="item.php?num='+favorites_data[i].num+'"><div class="image-wrapper" style="background-image:url(catalog/'+photo_data[0]+')"><div class="gradient"></div></div><div class="content-wrapper"><div class="title-wrapper" style="clear: both"><h3 class="title">'+favorites_data[i].title+'</h3><span class="text-muted">'+utils.getDateTimeForTimestamp(favorites_data[i].timestamp)+'</span></div><strong class="price"> '+favorites_data[i].price+' '+favorites_data[i].currency_short_title+' '+(utils.isset(favorites_data[i].auction) ? '<span class="text-muted" style="float:right">торг</span>' : '')+'</strong><div class="clearfix"></div><div class="description"></div></div></a><div id="quick_view_'+favorites_data[i].num+'_wrapper_div"  class="quick_view_wrapper"></div><div id="quick_view_'+favorites_data[i].num+'_button_wrapper_div" style="cursor:pointer" onclick="catalog_common.fullView('+favorites_data[i].num+')" class="quick_view_button_wrapper"><button id="quick_view_'+favorites_data[i].num+'_button" onclick="catalog_common.quickView('+favorites_data[i].num+')" class="quick_view_button btn transparent orange">Быстрый просмотр</button><button id="favorites_view_'+favorites_data[i].num+'_button" onclick="favorites.remove('+favorites_data[i].num+')" class="favorites_view_button btn transparent grey">Убрать из Избранного</button></div></div>');
            }
            else{
               $('#catalog_list_div').append('<div id="catalog_item_'+favorites_data[i].num+'" onmouseover="catalog_common.showQuickViewWrapper('+favorites_data[i].num+')" onmouseout="catalog_common.hideQuickViewWrapper('+favorites_data[i].num+')" class="item-wrapper"><div class="item-top-details" style="width: 876px;"></div><a class="item" href="item.php?num='+favorites_data[i].num+'"><div class="image-wrapper" style="background-image:url(img/camera.png)"><div class="gradient"></div></div><div class="content-wrapper"><div class="title-wrapper" style="clear: both"><h3 class="title">'+favorites_data[i].title+'</h3><span class="text-muted">'+utils.getDateTimeForTimestamp(favorites_data[i].timestamp)+'</span></div><strong class="price"> '+favorites_data[i].price+' '+favorites_data[i].currency_short_title+' '+(utils.isset(favorites_data[i].auction) ? '<span class="text-muted" style="float:right">торг</span>' : '')+'</strong><div class="clearfix"></div><div class="description"></div></div></a><div id="quick_view_'+favorites_data[i].num+'_wrapper_div"  class="quick_view_wrapper"></div><div id="quick_view_'+favorites_data[i].num+'_button_wrapper_div" style="cursor:pointer" onclick="catalog_common.fullView('+favorites_data[i].num+')" class="quick_view_button_wrapper"><button id="quick_view_'+favorites_data[i].num+'_button" onclick="catalog_common.quickView('+favorites_data[i].num+')" class="quick_view_button btn transparent orange">Быстрый просмотр</button><button id="favorites_view_'+favorites_data[i].num+'_button" onclick="favorites.remove('+favorites_data[i].num+')" class="favorites_view_button btn transparent grey">Убрать из Избранного</button></div></div>');
            }
         }
         else{
            catalog_common.items_per_page = favorites_data[i].catalog_items_per_page;
            catalog_common.pages = Math.ceil(favorites_data[i].items_count/catalog_common.items_per_page);
            catalog_common.setPagination();
            catalog_common.curr_coef = favorites_data[i].curr_coef;
         } 
      }
   };
   
   this.remove = function(item){
      catalog_common.qview_clicked = 1;
      var favorites = [];
      var favorites_new = [];
      favorites = JSON.parse(localStorage.favorites);
      
      for (var i = 0; i < favorites.length; i++)
         if (favorites[i] != item)
            favorites_new.push(favorites[i]);
      
      localStorage.favorites = JSON.stringify(favorites_new);
      
      $('#catalog_item_'+item).remove();
      $('#favorites_view_'+item+'_button').css({"background":"rgba(0,0,0,0)","opacity":1, "color":"#fff3"}).text("В Избранном").off("click");
   };
}