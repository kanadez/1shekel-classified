function CatalogCommon(){ // класс с общими возможностями построение каталога товаров. специфика в классе js/catalog.js
   this.page = 1;
   this.items_per_page = 0;
   this.curr_coef = 1;
   this.pages = 0;
   this.output_mode = utils.isset(utils.getUrlParameter("output")) == 1 ? utils.getUrlParameter("output") : 1;
   this.qview_clicked = 0;
   this.qview_item = null;
   
   this.getUserData = function(user_num){
      $.post(post_url,{
         a: "user_catalog_get",
         b: user_num, // user num
         c: category.category, // selected category
         d: this.page-1
      },function (result){
         catalog_data = eval("(" + result + ")");
         for (var i = 0; i < catalog_data.length; i++){
            if (i != catalog_data.length-1){
               if (utils.isset(catalog_data[i].photo)){
                  var photo_data = eval("(" + catalog_data[i].photo + ")");
                  $('#catalog_list_div').append('<div id="catalog_item_'+catalog_data[i].num+'" onmouseover="catalog_common.showQuickViewWrapper('+catalog_data[i].num+')" onmouseout="catalog_common.hideQuickViewWrapper('+catalog_data[i].num+')" class="item-wrapper"><div class="item-top-details" style="width: 876px;"></div><a class="item" href="item.php?num='+catalog_data[i].num+'"><div class="image-wrapper" style="background-image:url(catalog/'+photo_data[0]+')"><div class="gradient"></div></div><div class="content-wrapper"><div class="title-wrapper" style="clear: both"><h3 class="title">'+catalog_data[i].title+'</h3><span class="text-muted">'+utils.getDateTimeForTimestamp(catalog_data[i].timestamp)+'</span></div><strong class="price"> '+catalog_data[i].price+' '+catalog_data[i].currency_short_title+' '+(utils.isset(catalog_data[i].auction) ? '<span class="text-muted" style="float:right">торг</span>' : '')+'</strong><div class="clearfix"></div><div class="description"></div></div></a><div id="quick_view_'+catalog_data[i].num+'_wrapper_div"  class="quick_view_wrapper"></div><div id="quick_view_'+catalog_data[i].num+'_button_wrapper_div" style="cursor:pointer" onclick="catalog_common.fullView('+catalog_data[i].num+')" class="quick_view_button_wrapper"><button id="quick_view_'+catalog_data[i].num+'_button" onclick="catalog_common.quickView('+catalog_data[i].num+')" class="quick_view_button btn transparent orange">Быстрый просмотр</button></div></div>');
               }
               else{
                  $('#catalog_list_div').append('<div id="catalog_item_'+catalog_data[i].num+'" onmouseover="catalog_common.showQuickViewWrapper('+catalog_data[i].num+')" onmouseout="catalog_common.hideQuickViewWrapper('+catalog_data[i].num+')" class="item-wrapper"><div class="item-top-details" style="width: 876px;"></div><a class="item" href="item.php?num='+catalog_data[i].num+'"><div class="image-wrapper" style="background-image:url(img/camera.png)"><div class="gradient"></div></div><div class="content-wrapper"><div class="title-wrapper" style="clear: both"><h3 class="title">'+catalog_data[i].title+'</h3><span class="text-muted">'+utils.getDateTimeForTimestamp(catalog_data[i].timestamp)+'</span></div><strong class="price"> '+catalog_data[i].price+' '+catalog_data[i].currency_short_title+' '+(utils.isset(catalog_data[i].auction) ? '<span class="text-muted" style="float:right">торг</span>' : '')+'</strong><div class="clearfix"></div><div class="description"></div></div></a><div id="quick_view_'+catalog_data[i].num+'_wrapper_div"  class="quick_view_wrapper"></div><div id="quick_view_'+catalog_data[i].num+'_button_wrapper_div" style="cursor:pointer" onclick="catalog_common.fullView('+catalog_data[i].num+')" class="quick_view_button_wrapper"><button id="quick_view_'+catalog_data[i].num+'_button" onclick="catalog_common.quickView('+catalog_data[i].num+')" class="quick_view_button btn transparent orange">Быстрый просмотр</button></div></div>');
               }
            }
            else{
               catalog_common.items_per_page = catalog_data[i].catalog_items_per_page;
               catalog_common.pages = Math.ceil(catalog_data[i].items_count/catalog_common.items_per_page);
               catalog_common.setPagination();
               catalog_common.curr_coef = catalog_data[i].curr_coef;
            } 
         }
         catalog_common.switchOutputMode(catalog_common.output_mode);
      });
   };
   
   this.fullView = function(a){
      switch (location.pathname) {
         case "/catalog.php":
            if (!this.qview_clicked)
               location.href='http://'+location.hostname+'/item.php?num='+a+(catalog.filter_data.filter_curr != null ? "&filter_curr="+catalog.filter_data.filter_curr : "");
         break;
         case "/user.php":
            if (!this.qview_clicked)
               location.href='http://'+location.hostname+'/item.php?num='+a;
         break;
         case "/favorites.php":
            if (!this.qview_clicked)
               location.href='http://'+location.hostname+'/item.php?num='+a;
         break;
      }
   };
   
   this.quickView = function(item_num){
      this.qview_clicked = 1;
      this.qview_item = item_num;
      
      form = new Form("../dom/catalog_qview_form.html", 1024, $(window).height()-50, this.onQuickViewFormLoad);
      $('#form').children().hide();
   };
   
   this.onQuickViewFormLoad = function(){
      catalog_common.qview_clicked = 0;
      item = new Item(catalog_common.qview_item);
      item.qview_mode = 1;
      $('#full_view_a').attr("href","/item.php?num="+item.num);
      $('#add_to_favorites_a').click(function(){
         catalog_common.addToFavorites(item.num);
      });
   };
   
   this.hideQuickViewWrapper = function(item){
      $('#quick_view_'+item+'_wrapper_div').hide();
      $('#quick_view_'+item+'_button_wrapper_div').hide();
   };
   
   this.showQuickViewWrapper = function(item){
      $('#quick_view_'+item+'_wrapper_div').show();
      $('#quick_view_'+item+'_button_wrapper_div').show();
   };
   
   this.setPagination = function(){
      var p = Number(this.page);
      var ps = Number(this.pages);
      
      if (ps > 4){
         if (p == 1){
            $('.pagination').append('<li><a class="disabled" style="font-weight : bolder" onclick="catalog_common.switchPage(1);" href="javascript:void(0)">1</a></li>');
            $('.pagination').append('<li><a onclick="catalog_common.switchPage(2);" href="javascript:void(0)">2</a></li>');
            $('.pagination').append('<li><a onclick="catalog_common.switchPage(3);" href="javascript:void(0)">3</a></li>');
            $('.pagination').append('<li><a class="next" onclick="catalog_common.switchPage('+ps+');" href="javascript:void(0)">»</a></li>');
         }
         else if (p > 1 && p < ps-2){
            $('.pagination').append('<li><a class="prev" onclick="catalog_common.switchPage(1);" href="javascript:void(0)">«</a></li>');
            $('.pagination').append('<li><a onclick="catalog_common.switchPage('+(p-1)+');" href="javascript:void(0)">'+(p-1)+'</a></li>');
            $('.pagination').append('<li><a class="disabled" style="font-weight : bolder" onclick="catalog_common.switchPage('+p+');" href="javascript:void(0)">'+p+'</a></li>');
            $('.pagination').append('<li><a onclick="catalog_common.switchPage('+(p+1)+');" href="javascript:void(0)">'+(p+1)+'</a></li>');
            $('.pagination').append('<li><a class="next" onclick="catalog_common.switchPage('+ps+');" href="javascript:void(0)">»</a></li>');
         }
         else if (p > 1 && p < ps-1){
            $('.pagination').append('<li><a class="prev" onclick="catalog_common.switchPage(1);" href="javascript:void(0)">«</a></li>');
            $('.pagination').append('<li><a onclick="catalog_common.switchPage('+(p-1)+');" href="javascript:void(0)">'+(p-1)+'</a></li>');
            $('.pagination').append('<li><a class="disabled" style="font-weight : bolder" onclick="catalog_common.switchPage('+p+');" href="javascript:void(0)">'+p+'</a></li>');
            $('.pagination').append('<li><a onclick="catalog_common.switchPage('+(p+1)+');" href="javascript:void(0)">'+(p+1)+'</a></li>');
            $('.pagination').append('<li><a onclick="catalog_common.switchPage('+(p+2)+');" href="javascript:void(0)">'+(p+2)+'</a></li>');
         }
         else if (p > 1 && p < ps){
            $('.pagination').append('<li><a class="prev" onclick="catalog_common.switchPage(1);" href="javascript:void(0)">«</a></li>');
            $('.pagination').append('<li><a onclick="catalog_common.switchPage('+(p-1)+');" href="javascript:void(0)">'+(p-1)+'</a></li>');
            $('.pagination').append('<li><a class="disabled" style="font-weight : bolder" onclick="catalog_common.switchPage('+p+');" href="javascript:void(0)">'+p+'</a></li>');
            $('.pagination').append('<li><a onclick="catalog_common.switchPage('+(p+1)+');" href="javascript:void(0)">'+(p+1)+'</a></li>');
            
         }
         else if (p == ps){
            $('.pagination').append('<li><a class="prev" onclick="catalog_common.switchPage(1);" href="javascript:void(0)">«</a></li>');
            $('.pagination').append('<li><a onclick="catalog_common.switchPage('+(p-2)+');" href="javascript:void(0)">'+(p-2)+'</a></li>');
            $('.pagination').append('<li><a onclick="catalog_common.switchPage('+(p-1)+');" href="javascript:void(0)">'+(p-1)+'</a></li>');
            $('.pagination').append('<li><a class="disabled" style="font-weight : bolder" onclick="catalog_common.switchPage('+p+');" href="javascript:void(0)">'+p+'</a></li>');
            
         }
      }
      else{
         for (var i = 1; i <= ps; i++){
            $('.pagination').append('<li><a '+(i == p ? 'class="disabled" style="font-weight : bolder"' : '')+' onclick="catalog_common.switchPage('+i+');" href="javascript:void(0)">'+i+'</a></li>');
         }
      }
   };
   
   this.switchPage = function(page){
      this.page = page;
      this.reload();
   };
   
   this.switchOutputMode = function(mode){
      if (mode == 2){
         $("#output_mode_thumbs_switch").addClass("active");
         $("#output_mode_list_switch").removeClass("active");
         $('#items-renderer').addClass("thumbnails").removeClass("list");
         $('.quick_view_wrapper').addClass("quick_view_wrapper_thumbs").removeClass("quick_view_wrapper");
         $('.quick_view_button_wrapper').addClass("quick_view_button_wrapper_thumbs").removeClass("quick_view_button_wrapper");
         $('.price').css("font-size","2.3em");
         this.output_mode = 2;
      }
      else if (mode == 1){
         $("#output_mode_thumbs_switch").removeClass("active");
         $("#output_mode_list_switch").addClass("active");
         $('#items-renderer').removeClass("thumbnails").addClass("list");
         $('.quick_view_wrapper_thumbs').addClass("quick_view_wrapper").removeClass("quick_view_wrapper_thumbs");
         $('.quick_view_button_wrapper_thumbs').addClass("quick_view_button_wrapper").removeClass("quick_view_button_wrapper_thumbs");
         $('.price').css("font-size","1.2em");
         this.output_mode = 1;
      }
   };
   
   this.reload = function(){
      switch (location.pathname) {
         case "/catalog.php":
            this.reloadCatalog();
         break;
         case "/user.php":
            this.reloadUser();
         break;
         case "/favorites.php":
            this.reloadFavorites();
         break;
         default:
            this.reloadCatalog();
         break;
      }
   };
   
   this.reloadCatalog = function(){
      var url = "http://"+location.hostname+"/catalog.php?";
      
      if (utils.isset(catalog_common.page))
         url += "page="+this.page;
         
      url += "&output="+this.output_mode;
         
      if (utils.isset(category.category))
         url += "&cat="+category.category;
         
      if (utils.isset(category.subcategory))
         url += "&sub="+category.subcategory;
         
      if (utils.isset(category.advsubcategory))
         url += "&adv="+category.advsubcategory;
         
      if (region.current_region != -1 && region.current_city == -1)
         url += "&region="+region.current_region;
         
      if (region.current_city != -1)
         url += "&city="+region.current_city;
         
      for (var key in catalog.filter_data)
         if (catalog.filter_data[key] != undefined)
            url += "&"+key+"="+catalog.filter_data[key];
      
      if (catalog.query != undefined)
         url += "&query="+catalog.query;
         
      location.href = url;
   };
   
   this.reloadUser = function(){
      var url = "http://"+location.hostname+"/user.php?num="+user.num;
      
      if (utils.isset(this.page))
         url += "&page="+this.page;
         
      url += "&output="+this.output_mode;
         
      if (utils.isset(category.category))
         url += "&cat="+category.category;
         
      location.href = url;
   };
   
   this.reloadFavorites = function(){
      var url = "http://"+location.hostname+"/favorites.php?";
      
      if (utils.isset(this.page))
         url += "page="+this.page;
         
      location.href = url;
   };
   
   this.addToFavorites = function(item){
      this.qview_clicked = 1;
      var favorites = [];
      
      if (localStorage.favorites != undefined){
         favorites = JSON.parse(localStorage.favorites);
         favorites.push(item);
         localStorage.favorites = JSON.stringify(favorites);
      }
      else{
         favorites.push(item);
         localStorage.favorites = JSON.stringify(favorites);
      }
      
      $('#add_to_favorites_a').hide();
      $('#favorites_view_'+item+'_button').css({"background":"rgba(0,0,0,0)","opacity":1, "color":"#fff3"}).text("В Избранном").off("click");
      $('.sidebar').append("<div style='font-size:2.7em;width:100%;text-align:center;'>Объявление у Вас в Избранном.</div>");
   };
}