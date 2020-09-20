// индивидуальный файл для страницы каталога

var post_url = "./php/post.php";
var utils = null;
var category = null;
var catalog = null;
var item = null;
var dropdown = null;
var region = null;
var items_total_counter = 0; // счетчик обвлений в списке всего

$(document).ready(function(){
   utils = new Utils();
   category = new Category();
   catalog = new Catalog();
   catalog_common = new CatalogCommon();
   dropdown = new Dropdown();
   region = new Region();
   
   category.category = utils.getUrlParameter("cat");
   category.subcategory = utils.getUrlParameter("sub");
   category.advsubcategory = utils.getUrlParameter("adv");
   category.fillSidePanel();
   catalog_common.page = utils.getUrlParameter("page");
   catalog_common.output_mode = utils.isset(utils.getUrlParameter("output")) == 1 ? utils.getUrlParameter("output") : 1;
   catalog.query = utils.getUrlParameter("query") != undefined ? decodeURIComponent(utils.getUrlParameter("query")) : "";
   catalog.filter_data.filter_curr = utils.getUrlParameter("filter_curr");
   catalog.filter_data.filter_rank = utils.getUrlParameter("filter_rank");
   catalog.filter_data.filter_price_floor = utils.getUrlParameter("filter_price_floor");
   catalog.filter_data.filter_price_ceil = utils.getUrlParameter("filter_price_ceil");
   catalog.filter_data.filter_photo = utils.getUrlParameter("filter_photo");
   catalog.filter_data.filter_gift = utils.getUrlParameter("filter_gift");
   catalog.filter_data.filter_exchange = utils.getUrlParameter("filter_exchange");
   region.current_region = utils.getUrlParameter("region") != undefined && utils.getUrlParameter("region") != "" ? utils.getUrlParameter("region") : -1;
   region.current_city = utils.getUrlParameter("city") != undefined && utils.getUrlParameter("city") != "" ? utils.getUrlParameter("city") : -1;
   
   if (catalog.query != undefined && catalog.query != "undefined" && catalog.query != ""){
      $('#catalog_search_input').val(catalog.query);
      catalog.searchHighlighted();
      catalog.search();
   }
   else{ 
      catalog.getHighlighted();
      catalog.get();
   }
   
   catalog.getCurrencyList();
   catalog.getBreadcrumbs();
   region.getRegionData();
   region.getCityData();
   
   initDOM();
});

function initDOM(){
   $('#region_dropdown_menu_a').click(function(){
      $('#region_dropdown_menu_ul').show();
   });
   
   $(document).click(function(e){
      var target = $(e.target);
        if (!target.is('#region_dropdown_menu_span_name') && !target.is('#region_dropdown_menu_ul') && !target.is('#region_dropdown_menu_a') && !target.is('#search-region-ac-title')) $('#region_dropdown_menu_ul').hide();
   });
   
   $('#subcats-panel').height(screen.height-274);
   
   $("#catalog_search_input").keypress(function(e){
      if(e.keyCode==13){
         catalog.doSearch();
      }
   });
   
   if (catalog.filter_data.filter_rank != null){
      $('#default_rank_li').removeClass("active");
      $('.rank_li').each(function(){
         if ($(this).attr("rank") == catalog.filter_data.filter_rank){
            $('#rank_dropdown_span').html($(this).children("a").text());
            $(this).addClass("active");
         }
      });
   }
   
   if (catalog.filter_data.filter_photo == 1)
      $('#filter_checks #photo').prop('checked', true);
   else $('#filter_checks #photo').prop('checked', false);
   $('#filter_checks #photo').change(function(){
      this.checked ? catalog.checkFilterPhoto(1) : catalog.checkFilterPhoto(0);
   });
   
   if (catalog.filter_data.filter_gift == 1)
      $('#filter_checks #gift').prop('checked', true);
   else $('#filter_checks #gift').prop('checked', false);
   $('#filter_checks #gift').change(function(){
      this.checked ? catalog.checkFilterGift(1) : catalog.checkFilterGift(0);
   });
   
   if (catalog.filter_data.filter_exchange == 1)
      $('#filter_checks #exchange').prop('checked', true);
   else $('#filter_checks #exchange').prop('checked', false);
   $('#filter_checks #exchange').change(function(){
      this.checked ? catalog.checkFilterExchange(1) : catalog.checkFilterExchange(0);
   });
   
   if (region.current_region != -1)
      $.post(post_url,{
         a: "region_get_name_by_code",
         b: region.current_region
      },function (result){
         var obj = JSON.parse(result);
         region.setSearchRegion(obj.region_code, obj.region_name);
      });
      
   if (region.current_city != -1)
      $.post(post_url,{
         a: "region_get_city_by_code",
         b: region.current_city
      },function (result){
         var obj = JSON.parse(result);
         region.setSearchCity(obj.city_code, obj.city_name);
      });
}

function Catalog(){
   this.data = {};
   this.filter_data = {}; // данные фильтрации. при измененни любого параметра фильтра сюда вностяся изменения, а затем перезапрашивается каталог.
   this.filter_data.filter_curr = null;
   this.filter_data.filter_rank = null;
   this.filter_data.filter_price_floor = null;
   this.filter_data.filter_price_ceil = null;
   this.filter_data.filter_photo = null;
   this.currency_data = {};
   //this.page = null; // номер текущей страницы каталога
   //this.pages = null;
   //this.items_per_page = null;
   this.qview_item = null;
   this.qview_clicked = 0;
   //this.curr_coef = null;
   //this.output_mode = 1; // 1 - list, 2 - thumbnails
   this.query = "";
   
   this.doSearch = function(){
      this.query = $('#catalog_search_input').val();
      catalog_common.reload();
   };
   
   this.get = function(){ // все ф-ии рабобтают зависимо от текущего состояния фильрации, пагинации и категорий
      $.post(post_url,{
         a: "catalog_get",
         b: category.category,
         c: category.subcategory,
         d: category.advsubcategory,
         e: JSON.stringify(this.filter_data),
         f: catalog_common.page-1,
         g: region.current_region,
         h: region.current_city
      },function (result){
         catalog.data = eval("(" + result + ")");
         catalog.getSliderPriceRange();
         catalog.showCatalogList(catalog.data);
         catalog_common.switchOutputMode(catalog_common.output_mode);
      });
   };
   
   this.search = function(){ // все ф-ии рабобтают зависимо от текущего состояния фильрации, пагинации и категорий
      $.post(post_url,{
         a: "catalog_search",
         b: category.category,
         c: category.subcategory,
         d: category.advsubcategory,
         e: JSON.stringify(this.filter_data),
         f: catalog_common.page-1,
         g: this.query,
         h: region.current_region,
         i: region.current_city
      },function (result){
         catalog.data = eval("(" + result + ")");
         catalog.getSliderPriceRange();
         catalog.showCatalogList(catalog.data);
         catalog_common.switchOutputMode(catalog_common.output_mode);
      });
   };
   
   this.showCatalogList = function(catalog_data){ // output_mode 1 - list, 2 - cells
       for (var i = 0; i < catalog_data.length; i++){
         if (catalog_data.length === 1){
            $('#catalog_list_div').html('<div id="no_items_alert_wrapper" style="width:100%;margin-top:200px;text-align:center;color:#aaa;font-size: 2.6em;">В этой категории объявлений пока нет. Ваше может быть первым.<br><a style="width: 200px !important;" href="new.php" class="btn transparent orange fw">Подать объявление</a></div>');   
         }
         else if (i != catalog_data.length-1){
            if (utils.isset(catalog_data[i].photo)){
               var photo_data = eval("(" + catalog_data[i].photo + ")");
               $('#catalog_list_div').append('<div id="catalog_item_'+catalog_data[i].num+'" onmouseover="catalog_common.showQuickViewWrapper('+catalog_data[i].num+')" onmouseout="catalog_common.hideQuickViewWrapper('+catalog_data[i].num+')" class="item-wrapper"><div class="item-top-details" style="width: 876px;"></div><a class="item" href="item.php?num='+catalog_data[i].num+'"><div class="image-wrapper" style="background-image:url(catalog/'+photo_data[0]+')"><div class="gradient"></div></div><div class="content-wrapper"><div class="title-wrapper" style="clear: both"><h3 class="title">'+catalog_data[i].title+'</h3><span class="text-muted">'+utils.getDateTimeForTimestamp(catalog_data[i].timestamp)+'</span></div><strong class="price"> '+catalog_data[i].price+' '+catalog_data[i].currency_short_title+' '+(utils.isset(catalog_data[i].auction) ? '<span class="text-muted" style="float:right">торг</span>' : '')+'</strong><div class="clearfix"></div><div class="description"></div></div></a><div id="quick_view_'+catalog_data[i].num+'_wrapper_div"  class="quick_view_wrapper"></div><div id="quick_view_'+catalog_data[i].num+'_button_wrapper_div" style="cursor:pointer" onclick="catalog_common.fullView('+catalog_data[i].num+')" class="quick_view_button_wrapper"><button id="quick_view_'+catalog_data[i].num+'_button" onclick="catalog_common.quickView('+catalog_data[i].num+')" class="quick_view_button btn transparent orange">Быстрый просмотр</button><button id="favorites_view_'+catalog_data[i].num+'_button" onclick="catalog_common.addToFavorites('+catalog_data[i].num+')" class="favorites_view_button btn transparent grey">Добавить в Избранное</button></div></div>');
            }
            else{
               $('#catalog_list_div').append('<div id="catalog_item_'+catalog_data[i].num+'" onmouseover="catalog_common.showQuickViewWrapper('+catalog_data[i].num+')" onmouseout="catalog_common.hideQuickViewWrapper('+catalog_data[i].num+')" class="item-wrapper"><div class="item-top-details" style="width: 876px;"></div><a class="item" href="item.php?num='+catalog_data[i].num+'"><div class="image-wrapper" style="background-image:url(img/camera.png)"><div class="gradient"></div></div><div class="content-wrapper"><div class="title-wrapper" style="clear: both"><h3 class="title">'+catalog_data[i].title+'</h3><span class="text-muted">'+utils.getDateTimeForTimestamp(catalog_data[i].timestamp)+'</span></div><strong class="price"> '+catalog_data[i].price+' '+catalog_data[i].currency_short_title+' '+(utils.isset(catalog_data[i].auction) ? '<span class="text-muted" style="float:right">торг</span>' : '')+'</strong><div class="clearfix"></div><div class="description"></div></div></a><div id="quick_view_'+catalog_data[i].num+'_wrapper_div"  class="quick_view_wrapper"></div><div id="quick_view_'+catalog_data[i].num+'_button_wrapper_div" style="cursor:pointer" onclick="catalog_common.fullView('+catalog_data[i].num+')" class="quick_view_button_wrapper"><button id="quick_view_'+catalog_data[i].num+'_button" onclick="catalog_common.quickView('+catalog_data[i].num+')" class="quick_view_button btn transparent orange">Быстрый просмотр</button><button id="favorites_view_'+catalog_data[i].num+'_button" onclick="catalog_common.addToFavorites('+catalog_data[i].num+')" class="favorites_view_button btn transparent grey">Добавить в Избранное</button></div></div>');
            }
            
            this.checkForFavorite(catalog_data[i].num);
            items_total_counter++;
         }
         else{
            catalog_common.items_per_page = catalog_data[i].catalog_items_per_page;
            catalog_common.pages = Math.ceil(catalog_data[i].items_count/catalog_common.items_per_page);
            catalog_common.setPagination();
            catalog_common.curr_coef = catalog_data[i].curr_coef;
         } 
      }
      
      if (items_total_counter > 0){
           $('#no_items_alert_wrapper').hide();
       }
   };
   
   this.checkForFavorite = function(item){
      if (localStorage.favorites != undefined){
         var favorites = JSON.parse(localStorage.favorites);
         
         for (var i = 0; i < favorites.length; i++)
            if (favorites[i] == item)
               $('#favorites_view_'+item+'_button').css({"background":"rgba(0,0,0,0)","opacity":1, "color":"#fff3"}).text("В Избранном").off("click");
      }
   };
   
   this.getHighlighted = function(){ // все ф-ии рабобтают зависимо от текущего состояния фильрации, пагинации и категорий
      $.post(post_url,{
         a: "catalog_get_highlighted",
         b: category.category,
         c: category.subcategory,
         d: category.advsubcategory,
         e: JSON.stringify(this.filter_data),
         f: catalog_common.page-1,
         g: region.current_region,
         h: region.current_city
      },function (result){
         catalog.data = eval("(" + result + ")");
         catalog.showHighlightedList(catalog.data);
      });
   };
   
   this.searchHighlighted = function(){ // все ф-ии рабобтают зависимо от текущего состояния фильрации, пагинации и категорий
      $.post(post_url,{
         a: "catalog_search_highlighted",
         b: category.category,
         c: category.subcategory,
         d: category.advsubcategory,
         e: JSON.stringify(this.filter_data),
         f: catalog_common.page-1,
         g: this.query,
         h: region.current_region,
         i: region.current_city
      },function (result){
         catalog.data = eval("(" + result + ")");
         catalog.showHighlightedList(catalog.data);
      });
   };
   
   this.showHighlightedList = function(catalog_data){ // output_mode 1 - list, 2 - cells
       for (var i = 0; i < catalog_data.length; i++){
         if (i != catalog_data.length-1){
            if (utils.isset(catalog_data[i].photo)){
               var photo_data = eval("(" + catalog_data[i].photo + ")");
               $('#catalog_hl_div').append('<div id="catalog_item_'+catalog_data[i].num+'" onmouseover="catalog_common.showQuickViewWrapper('+catalog_data[i].num+')" onmouseout="catalog_common.hideQuickViewWrapper('+catalog_data[i].num+')" class="item-wrapper"><div class="item-top-details" style="width: 876px;"></div><a class="item yellow" href="item.php?num='+catalog_data[i].num+'"><div class="image-wrapper" style="background-image:url(catalog/'+photo_data[0]+')"><div class="gradient"></div></div><div class="content-wrapper"><div class="title-wrapper" style="clear: both"><h3 class="title">'+catalog_data[i].title+'</h3><span class="text-muted">'+utils.getDateTimeForTimestamp(catalog_data[i].timestamp)+'</span></div><strong class="price"> '+catalog_data[i].price+' '+catalog_data[i].currency_short_title+' '+(utils.isset(catalog_data[i].auction) ? '<span class="text-muted" style="float:right">торг</span>' : '')+'</strong><div class="clearfix"></div><div class="description"></div></div></a><div id="quick_view_'+catalog_data[i].num+'_wrapper_div"  class="quick_view_wrapper"></div><div id="quick_view_'+catalog_data[i].num+'_button_wrapper_div" style="cursor:pointer" onclick="catalog_common.fullView('+catalog_data[i].num+')" class="quick_view_button_wrapper"><button id="quick_view_'+catalog_data[i].num+'_button" onclick="catalog_common.quickView('+catalog_data[i].num+')" class="quick_view_button btn transparent orange">Быстрый просмотр</button><button id="favorites_view_'+catalog_data[i].num+'_button" onclick="catalog_common.addToFavorites('+catalog_data[i].num+')" class="favorites_view_button btn transparent grey">Добавить в Избранное</button></div></div>');
            }
            else{
               $('#catalog_hl_div').append('<div id="catalog_item_'+catalog_data[i].num+'" onmouseover="catalog_common.showQuickViewWrapper('+catalog_data[i].num+')" onmouseout="catalog_common.hideQuickViewWrapper('+catalog_data[i].num+')" class="item-wrapper"><div class="item-top-details" style="width: 876px;"></div><a class="item yellow" href="item.php?num='+catalog_data[i].num+'"><div class="image-wrapper" style="background-image:url(img/camera.png)"><div class="gradient"></div></div><div class="content-wrapper"><div class="title-wrapper" style="clear: both"><h3 class="title">'+catalog_data[i].title+'</h3><span class="text-muted">'+utils.getDateTimeForTimestamp(catalog_data[i].timestamp)+'</span></div><strong class="price"> '+catalog_data[i].price+' '+catalog_data[i].currency_short_title+' '+(utils.isset(catalog_data[i].auction) ? '<span class="text-muted" style="float:right">торг</span>' : '')+'</strong><div class="clearfix"></div><div class="description"></div></div></a><div id="quick_view_'+catalog_data[i].num+'_wrapper_div"  class="quick_view_wrapper"></div><div id="quick_view_'+catalog_data[i].num+'_button_wrapper_div" style="cursor:pointer" onclick="catalog_common.fullView('+catalog_data[i].num+')" class="quick_view_button_wrapper"><button id="quick_view_'+catalog_data[i].num+'_button" onclick="catalog_common.quickView('+catalog_data[i].num+')" class="quick_view_button btn transparent orange">Быстрый просмотр</button><button id="favorites_view_'+catalog_data[i].num+'_button" onclick="catalog_common.addToFavorites('+catalog_data[i].num+')" class="favorites_view_button btn transparent grey">Добавить в Избранное</button></div></div>');
            }
            
            this.checkForFavorite(catalog_data[i].num);
            items_total_counter++;
         }
      }
      
      if (items_total_counter > 0){
           $('#no_items_alert_wrapper').hide();
       }
   };
   
   this.getBreadcrumbs = function(){
      $.post(post_url,{
         a: "item_getcrumbs",
         b: category.category,
         c: category.subcategory,
         d: category.advsubcategory
      },function (result){
         var obj = eval("(" + result + ")");
         
         $('.main-wrapper').innerWidth($(document).width()-$('#subcats-panel').outerWidth()-250);
         $('#crumbs_panel').html("<a href='javascript:void(0)' onclick='category.switchEntire()'>Все категории</a>"+(utils.isset(obj.category) ? " → <a href='javascript:void(0)' onclick='category.switchCategory("+category.category+")'>"+obj.category+"</a>" : "") +(utils.isset(obj.subcategory) ? " → "+"<a href='javascript:void(0)' onclick='category.switchSubCategory("+category.subcategory+")'>"+obj.subcategory+"</a>" : "")+(utils.isset(obj.advsubcategory) ? " → "+"<a href='javascript:void(0)' onclick='category.switchAdvSubCategory("+category.advsubcategory+")'>"+obj.advsubcategory+"</a>" : ""));
      });
   };
   
   this.getCurrencyList = function(){
      $.post(post_url,{
         a: "currency_getlist"
      },function (result){
         var obj = eval("(" + result + ")");
         catalog.currency_data = obj;
         var list_dom = "";
         
         if (catalog.filter_data.filter_curr == null){ // если параметр валюты не получили из УРЛ, делаем умолчальный
            for (var c = 0; c < obj.length; c++){
               list_dom += '<li><a onclick="catalog.filterSetCurrency('+obj[c].code+')" code="'+obj[c].code+'">В '+obj[c].short_title+' ('+obj[c].symbol+')'+'</a></li>';
               $('#current_curr').html("Выбрать валюту");
            }
         }
         else{ // если получили
            for (var i = 0; i < obj.length; i++){
               list_dom += '<li '+(obj[i].code == catalog.filter_data.filter_curr ? 'class="active"' : '' )+'><a onclick="catalog.filterSetCurrency('+obj[i].code+')" code="'+obj[i].code+'">В '+obj[i].short_title+' ('+obj[i].symbol+')'+'</a></li>';
               if (obj[i].code == catalog.filter_data.filter_curr)
                  $('#current_curr').html('В '+obj[i].short_title+' ('+obj[i].symbol+')');
            }
         }
         
         $('#currensies-list').html(list_dom);
      });
   };
   
   this.checkFilterExchange = function(value){
      this.filter_data.filter_exchange = value;
      catalog_common.reload();
   };
   
   this.checkFilterGift = function(value){
      this.filter_data.filter_gift = value;
      catalog_common.reload();
   };
   
   this.checkFilterPhoto = function(value){
      this.filter_data.filter_photo = value;
      catalog_common.reload();
   };
   
   this.getSliderPriceRange = function(){
      $.post(post_url,{
         a: "catalog_getpricerange",
         b: category.category,
         c: category.subcategory,
         d: category.advsubcategory
      },function (result){
         //var obj = eval("(" + result + ")");
         var min = 0;
         var max = 100000;
         var floor = catalog.filter_data.filter_price_floor !== undefined ? Number(catalog.filter_data.filter_price_floor) : Number(min);
         var ceil = catalog.filter_data.filter_price_ceil !== undefined ? Number(catalog.filter_data.filter_price_ceil) : Number(max);

         $("#slider_div").slider({
            range: true,
            min: min,
            max: max,
            step: 1000,
            values: [ floor, ceil ],
            slide: function(event, ui){
               $("#slider_leftedge_span").text(Math.ceil(Number(ui.values[0])));
               $("#slider_rightedge_span").text(Math.ceil(Number(ui.values[1])));
            },
            stop: function(event, ui){
               catalog.filter_data.filter_price_floor = ui.values[0];
               catalog.filter_data.filter_price_ceil = ui.values[1];
               catalog_common.reload();
            }
         });
         $("#slider_leftedge_span").text(Math.ceil(Number($("#slider_div").slider("values", 0))));
         $("#slider_rightedge_span").text(Math.ceil(Number($("#slider_div").slider("values", 1))));
      });
   };
   
   this.switchPage = function(page){
      catalog_common.page = page;
      catalog_common.reload();
   };
   
   this.filterSetDefaults = function(){ // эта ф-ия собирает параметры фильрации и запрашивает с сервера каталог, отдавая фильтрацию
      $.post(post_url,{
         a: "filter_getdefaults",
         b: category.category,
         c: category.subcategory,
         d: category.advsubcategory
      },function (result){
         //var obj = eval("(" + result + ")");
         console.log(result);
      });
   };
   
   this.filterSetCurrency = function(code){
      this.filter_data.filter_curr = code;
      catalog_common.reload();
   };
   
   this.filterSetRank = function(value){
      this.filter_data.filter_rank = value;
      catalog_common.reload();
   };
}