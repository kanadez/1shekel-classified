var region_data = [];
var city_data = [];
var current_region = -1;
var current_city = -1;

function getRegionData(){
   $('#region_list_ul').html("");
   
   if (!region_data.length){
      $.post(post_url,{
         a: "gRD"
      },function (result){
         region_data = eval("(" + result + ")");
         fillRegionList();
      });
   }
   else{
      fillRegionList();
   }
}

function fillRegionList(){
   for (var i = 0; i < region_data.length; i++){
      var li = $("<li />",{
         region_code: region_data[i].region_code,
         onclick: "setSearchRegion("+region_data[i].region_code+",'"+region_data[i].region_name+"')"
      });
      
      var a = $("<a />",{
         text: region_data[i].region_name
      });
      
      li.append(a);
      $('#region_list_ul').append(li);
   }
}

function getCityData(){
   $.post(post_url,{
      a: "gCD"
   },function (result){
      city_data = eval("(" + result + ")");
   });
}

function fillCityList(list_id, input_id){
   var list = $('#'+list_id);
   list.html("");
   
   for (var i = 0; i < city_data.length; i++){
      var li = $("<li />",{
         city_code: city_data[i].city_code,
         onclick: "setSearchCity("+city_data[i].city_code+",'"+city_data[i].city_name+"', '"+input_id+"')"
      });
      
      var a = $("<a />",{
         text: city_data[i].city_name
      });
      
      li.append(a);
      list.append(li);
   }
}

function findRegion(input_id, ul_id){
   var input = $('#'+input_id);
   var ul = $('#'+ul_id);
   
   if (input.val() !== ""){
      ul.html("");
      for (var i = 0; i < region_data.length; i++)
         if (findsub(region_data[i].region_name, input.val())){
            var li = $("<li />",{
               city_code: region_data[i].region_code,
               onclick: "setSearchRegion("+region_data[i].region_code+",'"+region_data[i].region_name+"')"
            });
         
            var a = $("<a />",{
               text: region_data[i].region_name
            });
            
            li.append(a);
            ul.append(li);
         }
   }
   else{
      getRegionData();
   }
}

function findCity(input_id, ul_id){
   var input = $('#'+input_id);
   var ul = $('#'+ul_id);
   
   if (input.val() !== ""){
      ul.html("");
      for (var i = 0; i < city_data.length; i++)
         if (findsub(city_data[i].city_name, input.val())){
            var li = $("<li />",{
               city_code: city_data[i].city_code,
               onclick: "setSearchCity("+city_data[i].city_code+",'"+city_data[i].city_name+"', '"+input_id+"')"
            });
         
            var a = $("<a />",{
               text: city_data[i].city_name
            });
            
            li.append(a);
            ul.append(li);
         }
   }
   else{
      fillCityList(ul_id, input_id);
   }
}

function findsub(str, sub){
    if (str.toUpperCase().indexOf(sub.toUpperCase()) + 1) return 1; else return 0;
}

function setSearchRegion(region_code, region_name){
   current_region = region_code;
   $('#region_dropdown_menu_a').text(region_name);
   var input_w = 637-$('#lang-swicher').outerWidth();
   $('#inputSearch').outerWidth(input_w);
   $('#search_panel_div').outerWidth(input_w);
}

function setSearchCity(city_code, city_name, input_id){
   current_city = city_code;
   $('#'+input_id).val(city_name).attr("city_code",city_code);
   //$('#region_dropdown_menu_a').text(city_name);
   //var input_w = 637-$('#lang-swicher').outerWidth();
   //$('#inputSearch').outerWidth(input_w);
   //$('#search_panel_div').outerWidth(input_w);
}