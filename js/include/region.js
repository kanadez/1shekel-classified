function Region(){
   this.region_data = [];
   this.city_data = [];
   this.metro_data = [];
   this.current_region = -1;
   this.current_city = -1;
   this.current_metro = -1;
   this.current_city_name = null;
   
   this.getRegionData = function(){
      $('#region_list_ul').html("");
      
      if (!this.region_data.length){
         $.post(post_url,{
            a: "gRD"
         },function (result){
            region.region_data = eval("(" + result + ")");
            region.fillRegionList();
         });
      }
      else{
         this.fillRegionList();
      }
   };

   this.fillRegionList = function(){
      for (var i = 0; i < this.region_data.length; i++){
         var li = $("<li />",{
            region_code: this.region_data[i].region_code,
            onclick: "region.setSearchRegion("+this.region_data[i].region_code+",'"+this.region_data[i].region_name+"');if (location.pathname === '/catalog.php') catalog_common.reload();"
         });
         
         var a = $("<a />",{
            text: this.region_data[i].region_name
         });
         
         li.append(a);
         $('#region_list_ul').append(li);
      }
      
      var li = $("<li />",{
         region_code: -1,
         onclick: "region.setSearchRegion(-1,'Во всех регионах');if (location.pathname === '/catalog.php') catalog_common.reload();"
      });
      
      var a = $("<a />",{
         text: "Во всех регионах"
      });
      
      li.append(a);
      $('#region_list_ul').append(li);
   };

   this.getCityData = function(){
      $.post(post_url,{
         a: "gCD"
      },function (result){
         region.city_data = eval("(" + result + ")");
      });
   };

   this.fillCityList = function(list_id, input_id){
      var list = $('#'+list_id);
      list.html("");
      
      for (var i = 0; i < this.city_data.length; i++){
         var li = $("<li />",{
            city_code: this.city_data[i].city_code,
            onclick: "region.setSearchCity("+this.city_data[i].city_code+",'"+this.city_data[i].city_name.trim()+"', '"+input_id+"');if (location.pathname === '/catalog.php') catalog_common.reload();"
         });
         
         var a = $("<a />",{
            text: this.city_data[i].city_name
         });
         
         li.append(a);
         list.append(li);
      }
   };
   
   this.getMetroData = function(){
      $.post(post_url,{
         a: "region_get_metro"
      },function (result){
         region.metro_data = JSON.parse(result);
      });
   };

   this.fillMetroList = function(list_id, input_id){
      var list = $('#'+list_id);
      list.html("");
      
      for (var i = 0; i < this.metro_data.length; i++){
         if (this.metro_data[i].city_code == this.current_city){
            var li = $("<li />",{
               metro_code: this.metro_data[i].metro_code,
               city_code: this.metro_data[i].city_code,
               onclick: "region.setSearchMetro("+this.metro_data[i].city_code+", "+this.metro_data[i].metro_code+", '"+this.metro_data[i].title+"', '"+input_id+"')"
            });
            
            var a = $("<a />",{
               text: this.metro_data[i].title
            });
            
            li.append(a);
            list.append(li);
         }
      }
   };

   this.findRegion = function(input_id, ul_id){
      var input = $('#'+input_id);
      var ul = $('#'+ul_id);
      var value = input.val().trim(); 
      
      if (value !== ""){
         ul.html("");
         for (var i = 0; i < this.city_data.length; i++)
            if (dropdown.findsub(this.city_data[i].city_name, value)){
               var li = $("<li />",{
                  city_code: this.city_data[i].city_code,
                  onclick: "region.setSearchCity("+this.city_data[i].city_code+",'"+this.city_data[i].city_name.trim()+"', '"+input_id+"');if (location.pathname === '/catalog.php') catalog_common.reload();"
               });
               
               var a = $("<a />",{
                  text: this.city_data[i].city_name
               });
               
               li.append(a);
               ul.append(li);
            }
      }
      else{
         this.fillCityList(ul_id, input_id);
      }
      
      /*if (input.val() !== ""){
         ul.html("");
         for (var i = 0; i < this.region_data.length; i++)
            if (dropdown.findsub(this.region_data[i].region_name, input.val())){
               var li = $("<li />",{
                  city_code: this.region_data[i].region_code,
                  onclick: "region.setSearchRegion("+this.region_data[i].region_code+",'"+this.region_data[i].region_name+"')"
               });
            
               var a = $("<a />",{
                  text: this.region_data[i].region_name
               });
               
               li.append(a);
               ul.append(li);
            }
      }
      else{
         this.getRegionData();
      }*/
   };

   this.findCity = function(input_id, ul_id){
      var input = $('#'+input_id);
      var ul = $('#'+ul_id);
      var value = input.val().trim();
      
      if (value !== ""){
         ul.html("");
         for (var i = 0; i < this.city_data.length; i++)
            if (dropdown.findsub(this.city_data[i].city_name, value)){
               var li = $("<li />",{
                  city_code: this.city_data[i].city_code,
                  onclick: "region.setSearchCity("+this.city_data[i].city_code+",'"+this.city_data[i].city_name.trim()+"', '"+input_id+"');if (location.pathname === '/catalog.php') catalog_common.reload();"
               });
            
               var a = $("<a />",{
                  text: this.city_data[i].city_name
               });
               
               li.append(a);
               ul.append(li);
            }
      }
      else{
         this.fillCityList(ul_id, input_id);
      }
   };
   
   this.findMetro = function(input_id, ul_id){
      var input = $('#'+input_id);
      var ul = $('#'+ul_id);
      
      if (input.val() !== ""){
         ul.html("");
         for (var i = 0; i < this.metro_data.length; i++)
            if (this.metro_data[i].city_code == this.current_city && dropdown.findsub(this.metro_data[i].title, input.val())){
               var li = $("<li />",{
                  metro_code: this.metro_data[i].metro_code,
                  city_code: this.metro_data[i].city_code,
                  onclick: "region.setSearchMetro("+this.metro_data[i].city_code+","+this.metro_data[i].metro_code+",'"+this.metro_data[i].title+"', '"+input_id+"')"
               });
            
               var a = $("<a />",{
                  text: this.metro_data[i].title
               });
               
               li.append(a);
               ul.append(li);
            }
      }
      else{
         this.fillMetroList(ul_id, input_id);
      }
   };

   this.setSearchRegion = function(region_code, region_name){
      this.current_region = region_code;
      
      if (region_code == -1) this.current_city = -1;
      
      $('#region_dropdown_menu_a').text(region_name);
      var input_w = 637-$('#lang-swicher').outerWidth();
      $('#inputSearch').outerWidth(input_w);
      $('#search_panel_div').outerWidth(input_w);
   };
   
   this.setSearchCity = function(city_code, city_name, input_id){
      this.current_city = city_code;
      $('#'+input_id).val(city_name).attr("city_code",city_code);
      $('#region_dropdown_menu_a').text(city_name);
      var input_w = 637-$('#lang-swicher').outerWidth();
      $('#inputSearch').outerWidth(input_w);
      $('#search_panel_div').outerWidth(input_w);
      //$('#region_dropdown_menu_a').text(city_name);
      //var input_w = 637-$('#lang-swicher').outerWidth();
      //$('#inputSearch').outerWidth(input_w);
      //$('#search_panel_div').outerWidth(input_w);
   };
   
   this.setSearchMetro = function(city_code, metro_code, metro_name, input_id){
      this.current_metro = metro_code;
      $('#'+input_id).val(metro_name).attr({"city_code": city_code, "metro_code": metro_code});
      //$('#region_dropdown_menu_a').text(city_name);
      //var input_w = 637-$('#lang-swicher').outerWidth();
      //$('#inputSearch').outerWidth(input_w);
      //$('#search_panel_div').outerWidth(input_w);
   };

}