// Здесь все функции работы с категориями объявлений.

function Category(){
   this.category = 0; // 0 - значит все категории или подкатегрии катеогрии
   this.subcategory = 0;
   this.advsubcategory = 0;
   this.bar_opened = 0;
   this.categories_list = {};
   
   this.switchAdvSubCategory = function(code){
      this.advsubcategory = code;
      
      if (location.pathname == "/catalog.php")
         catalog_common.reload();
      else this.reload();
   };
   
   this.switchSubCategory = function(code){
      this.advsubcategory = 0;
      this.subcategory = code;
      
      if (location.pathname == "/catalog.php")
         catalog_common.reload();
      else this.reload();
   };
   
   this.switchCategory = function(code){
      this.advsubcategory = 0;
      this.subcategory = 0;
      this.category = code;
      
      if (location.pathname == "/catalog.php")
         catalog_common.reload();
      else this.reload();
   };
   
   this.switchEntire = function(){
      this.advsubcategory = 0;
      this.subcategory = 0;
      this.category = 0;
      
      if (location.pathname == "/catalog.php")
         catalog_common.reload();
      else this.reload();
   };
   
   this.fillCategoryLevel = function(){
      $('.level1').html("");
      $.post(post_url,{
         a: "category_getcategories"
      },function (result){
         var obj = eval("("+result+")");
         
         for(var key in obj){
            $('.level1').append('<li><a id="category_'+key+'" href="javascript:void(0)" onclick="category.fillSubcategoryLevel('+key+')"><img width="25" src="./img/cat_icons/'+key+'.png">'+obj[key]+'</a></li>');
            if (category.category == key)
               $('#category_'+key).addClass("category_bar_active_a");
         }
         
         $('#catcol').scrollTop($('.level1 .category_bar_active_a').offset().top - $('#catcol').offset().top + $('#catcol').scrollTop()-140);
      });
   };
   
   this.fillSubcategoryLevel = function(category_code){
      $('.level1 .category_bar_active_a').removeClass("category_bar_active_a");
      $('#category_'+category_code).addClass("category_bar_active_a");
      this.category = category_code;
      
      $('.level2').html("");
      $.post(post_url,{
         a: "category_getsubcategories",
         b: category.category
      },function (result){
         var obj = eval("("+result+")");
         
         for(var key in obj){
            $('.level2').append('<li><a id="subcategory_'+key+'" href="javascript:void(0)" class="subcategory_bar_a" onclick="category.onSubcategoryClick('+category.category+','+key+')">'+obj[key]+'</a></li>');
            if (category.subcategory == key)
               $('#subcategory_'+key).addClass("category_bar_active_a");
         }
         
         $('#subcatcol').scrollTop($('.level2 .category_bar_active_a').offset().top - $('#subcatcol').offset().top + $('#subcatcol').scrollTop()-140);
      });
   };
   
   this.fillAdvSubcategoryLevel = function(category_code, subcategory_code){
      $('.level2 .category_bar_active_a').removeClass("category_bar_active_a");
      $('#subcategory_'+subcategory_code).addClass("category_bar_active_a");
      this.subcategory = subcategory_code;
      
      $('.level3').html("");
      $.post(post_url,{
         a: "category_getadvsubcategories",
         b: category.category,
         c: category.subcategory
      },function (result){
         var obj = eval("("+result+")");
         
         if (obj.length != 0){
            for(var key in obj){
               $('.level3').append('<li><a id="advsubcategory_'+key+'" href="javascript:void(0)" class="advsubcategory_bar_a" onclick="category.switchAdvSubCategory('+key+')">'+obj[key]+'</a></li>');
               if (category.advsubcategory == key)
                  $('#advsubcategory_'+key).addClass("category_bar_active_a");
            }
            
            $('#advsubcatcol').scrollTop($('.level3 .category_bar_active_a').offset().top - $('#advsubcatcol').offset().top + $('#advsubcatcol').scrollTop()-140);
         }
         //else{
            //category.switchSubCategory(category.subcategory);
         //}
      });
   };
   
   this.onSubcategoryClick = function(category_code, subcategory_code){
      $('.level2 .category_bar_active_a').removeClass("category_bar_active_a");
      $('#subcategory_'+subcategory_code).addClass("category_bar_active_a");
      this.subcategory = subcategory_code;
      
      $('.level3').html("");
      $.post(post_url,{
         a: "category_getadvsubcategories",
         b: category.category,
         c: category.subcategory
      },function (result){
         var obj = eval("("+result+")");
         
         if (obj.length != 0){
            for(var key in obj){
               $('.level3').append('<li><a id="advsubcategory_'+key+'" href="javascript:void(0)" class="advsubcategory_bar_a" onclick="category.switchAdvSubCategory('+key+')">'+obj[key]+'</a></li>');
               if (category.advsubcategory == key)
                  $('#advsubcategory_'+key).addClass("category_bar_active_a");
            }
            
            $('#advsubcatcol').scrollTop($('.level3 .category_bar_active_a').offset().top - $('#advsubcatcol').offset().top + $('#advsubcatcol').scrollTop()-140);
         }
         else{
            category.switchSubCategory(category.subcategory);
         }
      });
   };
   
   this.toggleBar = function(){
      if (this.bar_opened){
         $('#categories-block').hide().css("marginTop","-540px");
         this.bar_opened = 0;
      }
      else{
         this.fillCategoryLevel();
         this.fillSubcategoryLevel(this.category);
         this.fillAdvSubcategoryLevel(this.category, this.subcategory);
         $('#categories-block').show().css("marginTop",0);
         this.bar_opened = 1;
      }
   };
   
   this.fillSidePanel = function(){
      $.post(post_url,{
         a: "category_getforsidepanel",
         b: this.category,
         c: this.subcategory
      },function (result){
         var obj = eval("("+result+")");
         
         $('#subcats-panel').html("");
         
         if (obj.length > 0)
            for (var key in obj)
               if (category.category == null)
                  $('#subcats-panel').append('<a href="javascript:void(0)" onclick="category.switchCategory('+obj[key].category_code+')">'+obj[key].category_name+'</a><br>');
               else if (category.subcategory == null)
                  $('#subcats-panel').append('<a href="javascript:void(0)" onclick="category.switchSubCategory('+obj[key].subcategory_code+')">'+obj[key].subcategory_name+'</a><br>');
               else $('#subcats-panel').append('<a href="javascript:void(0)" onclick="category.switchAdvSubCategory('+obj[key].advsubcategory_code+')">'+obj[key].advsubcategory_name+'</a><br>');
         else $('#subcats-panel').hide();
      });
   };
   
   this.reload = function(){
      var url = "http://"+location.hostname+"/catalog.php?page=1";
         
      if (utils.isset(category.category))
         url += "&cat="+category.category;
         
      if (utils.isset(category.subcategory))
         url += "&sub="+category.subcategory;
         
      if (utils.isset(category.advsubcategory))
         url += "&adv="+category.advsubcategory;
         
      location.href = url;
   };
   
   this.getUserCategories = function(user){
      $.post(post_url,{
         a: "category_getusercategories",
         b: user
      },function(result){
         var obj = eval("("+result+")");
         console.log(obj);
         var tmp = [];
         for (var i = 0; i < obj.length; i++)
            tmp.push(obj[i].category_code);
         $.post(post_url,{
            a: "category_getusercategorynames",
            b: JSON.stringify(category.reduceArray(tmp))
         },function(result){
            $('#useritems_cats_list').html('<a class="useritem_cat_link" onclick="category.switchUserCategory(0)" href="javascript:void(0)">Все категории</a>');
            
            var obj = eval("("+result+")");
            for (var i = 0; i < obj.length; i++)
               $('#useritems_cats_list').append('<a class="useritem_cat_link" onclick="category.switchUserCategory('+obj[i].category_code+')" href="javascript:void(0)">'+obj[i].category_name+'</a>');
         });
      });
   };
   
   this.reduceArray = function(array){
      var elems = array, res = [], index;// переменную вынес что бы она каждый раз не иницилиазировалась в функции
      elems.forEach(function (item, i) {
         index = res.indexOf(item);// ищем элемент в новом массиве
         if(!~index) // если его нет 
            res.push(item);//добавляем 
      });
      
      return res;
   };
   
   this.switchUserCategory = function(category_code){
      category.category = category_code;
      catalog_common.reloadUser();
   };
   
   this.switchUserCategoriesPanel = function(){
      
   };
   
   this.init = function(){
      $.post(post_url,{
         a: "category_get_everything"
      },function(result){
         category.categories_list = JSON.parse(result);
      });
   };
   
   this.fillCategoryList = function(list_id, input_id){
      var list = $('#'+list_id);
      var categories = this.categories_list[0];
      
      list.html("");
      
      for (var i = 0; i < categories.length; i++){
         var li = $("<li />",{
            category_code: categories[i].category_code,
            onclick: "category.setSearchCategory("+categories[i].category_code+",'"+categories[i].category_name+"', '"+input_id+"')"
         });
         
         var a = $("<a />",{
            text: categories[i].category_name
         });
         
         li.append(a);
         list.append(li);
      }
   };
   
   this.setSearchCategory = function(category_code, category_name, input_id){
      this.category = category_code;
      if (category_code == 1 && $('#advsubcategory_input_label').length > 0) 
         $('#advsubcategory_input_label').text("Тип объявления:");
      else $('#advsubcategory_input_label').text("Марка/Тип товара:");
      $('#'+input_id).val(category_name).attr("category_code", category_code);
   };
   
   this.findCategory = function(input_id, ul_id){
      var input = $('#'+input_id);
      var ul = $('#'+ul_id);
      var categories = this.categories_list[0];
      
      if (input.val() !== ""){
         ul.html("");
         for (var i = 0; i < categories.length; i++)
            if (dropdown.findsub(categories[i].category_name, input.val())){
               var li = $("<li />",{
                  category_code: categories[i].category_code,
                  onclick: "category.setSearchCategory("+categories[i].category_code+",'"+categories[i].category_name+"', '"+input_id+"')"
               });
            
               var a = $("<a />",{
                  text: categories[i].category_name
               });
               
               li.append(a);
               ul.append(li);
            }
      }
      else{
         this.fillCategoryList(ul_id, input_id);
      }
   };
   
   this.fillSubCategoryList = function(list_id, input_id){
      var list = $('#'+list_id);
      var subcategories = this.categories_list[1];
      //console.log(subcategories);
      list.html("");
      
      for (var i = 0; i < subcategories.length; i++){
         if (subcategories[i].category_code == this.category){
            var li = $("<li />",{
               category_code: subcategories[i].category_code,
               subcategory_code: subcategories[i].subcategory_code,
               onclick: "category.setSearchSubCategory("+subcategories[i].subcategory_code+",'"+subcategories[i].subcategory_name+"', '"+input_id+"')"
            });
            
            var a = $("<a />",{
               text: subcategories[i].subcategory_name
            });
            
            li.append(a);
            list.append(li);
         }
      }
   };
   
   this.setSearchSubCategory = function(subcategory_code, subcategory_name, input_id){
      this.subcategory = subcategory_code;
      $('#'+input_id).val(subcategory_name).attr("subcategory_code", subcategory_code);
   };
   
   this.findSubCategory = function(input_id, ul_id, category_input_id){
      var input = $('#'+input_id);
      var ul = $('#'+ul_id);
      var subcategories = this.categories_list[1];
      
      if (input.val() !== ""){
         ul.html("");
         for (var i = 0; i < subcategories.length; i++)
            if (subcategories[i].category_code == this.category && dropdown.findsub(subcategories[i].subcategory_name, input.val())){
               var li = $("<li />",{
                  category_code: subcategories[i].subcategory_code,
                  onclick: "category.setSearchSubCategory("+subcategories[i].subcategory_code+",'"+subcategories[i].subcategory_name+"', '"+input_id+"')"
               });
            
               var a = $("<a />",{
                  text: subcategories[i].subcategory_name
               });
               
               li.append(a);
               ul.append(li);
            }
      }
      else{
         this.fillSubCategoryList(ul_id, input_id);
      }
   };
   
   this.fillAdvSubCategoryList = function(list_id, input_id){
      var list = $('#'+list_id);
      var advsubcategories = this.categories_list[2];
      //console.log(subcategories);
      list.html("");
      
      for (var i = 0; i < advsubcategories.length; i++){
         if (advsubcategories[i].category_code == this.category && advsubcategories[i].subcategory_code == this.subcategory){
            var li = $("<li />",{
               category_code: advsubcategories[i].category_code,
               subcategory_code: advsubcategories[i].subcategory_code,
               advsubcategory_code: advsubcategories[i].advsubcategory_code,
               onclick: "category.setSearchAdvSubCategory("+advsubcategories[i].advsubcategory_code+",'"+advsubcategories[i].advsubcategory_name.trim()+"', '"+input_id+"')"
            });
            
            var a = $("<a />",{
               text: advsubcategories[i].advsubcategory_name
            });
            
            li.append(a);
            list.append(li);
         }
      }
   };
   
   this.setSearchAdvSubCategory = function(advsubcategory_code, advsubcategory_name, input_id){
      this.advsubcategory = advsubcategory_code;
      $('#'+input_id).val(advsubcategory_name).attr("advsubcategory_code", advsubcategory_code);
   };
   
   this.findAdvSubCategory = function(input_id, ul_id, category_input_id){
      var input = $('#'+input_id);
      var ul = $('#'+ul_id);
      var advsubcategories = this.categories_list[2];
      
      if (input.val() !== ""){
         ul.html("");
         for (var i = 0; i < advsubcategories.length; i++)
            if (advsubcategories[i].category_code == this.category && advsubcategories[i].subcategory_code == this.subcategory && dropdown.findsub(advsubcategories[i].advsubcategory_name, input.val())){
               var li = $("<li />",{
                  category_code: advsubcategories[i].category_code,
                  subcategory_code: advsubcategories[i].subcategory_code,
                  advsubcategory_code: advsubcategories[i].advsubcategory_code,
                  onclick: "category.setSearchAdvSubCategory("+advsubcategories[i].advsubcategory_code+",'"+advsubcategories[i].advsubcategory_name.trim()+"', '"+input_id+"')"
               });
            
               var a = $("<a />",{
                  text: advsubcategories[i].advsubcategory_name
               });
               
               li.append(a);
               ul.append(li);
            }
      }
      else{
         this.fillAdvSubCategoryList(ul_id, input_id);
      }
   };
}