var post_url = "./php/post.php";
var newitem = null;
var category = null;
var dropdown = null;
var region = null;
var utils = null;
var recaptcha_response = false; // ответ рекаптчи

$(document).ready(function(){
   newitem = new Newitem();
   category = new Category();
   dropdown = new Dropdown();
   region = new Region();
   utils = new Utils();

   region.getRegionData();
   region.getCityData();
   newitem.init();
   category.init();
   VK.init({apiId: 5188263});
});

function resizeUploadImg(img2){
   $(img2).parent().append("<div id='"+img2.id+"_delete_wrapper' class='delete_photo_wrapper'></div>");
   $('#'+img2.id+'_delete_wrapper').height($(img2).height()).css("margin-top", "-"+$(img2).height()+"px");
   $('#'+img2.id+'_delete_wrapper').click({img:img2},function(e){
      $.post(post_url,{
         a: "newitem_photo_delete",
         b: newitem.temp_item_num,
         c: img2.name
      },function(result){
         newitem.temp_imgs = [];
         $('#photo0').html("");
         $('#photo1').html("");
         $('#photo2').html("");
         $('#photo3').html("");
         var obj = JSON.parse(result);
         
         if (newitem.temp_item_num == -1){ 
            newitem.temp_item_num = obj.item_num;
            var img1 = "<img id='pht0' src='catalog/"+obj[0]+"' name='"+obj[0]+"' onload='resizeUploadImg(this)' />";
            newitem.temp_imgs.push(img1);
            $('#photo0').html(img1);
         }
         else{
            for (var i = 0; i < obj.length; i++){
               var img2 = "<img id='pht"+i+"' src='catalog/"+obj[i]+"' name='"+obj[i]+"' onload='resizeUploadImg(this)' />";
               newitem.temp_imgs.push(img2);
               $('#photo'+i).html(img2);
            }
         }
      });
      
      var img = $(e.data.img);
      img.parent().html("");
   });
}

function Newitem(){
   this.currency_data = {}; // all currencies data
   this.selected_currency = 3; // default is 0 = dollar
   this.temp_item_num = -1; // temporary item number while uploading photos
   this.upload = null; // Upload object
   this.temp_imgs = []; // for temporary images while uploading
   this.temp_img_names = [];
   this.selected_period = 604800; // defaul is a week
   this.scrolled = 0; // scrolling flag for input mistakes notification
   this.errors = 0; // for input errors
   this.author = -1;
   this.token = -1;
   
   this.init = function(){
      $('#content-area').load("./dom/item_new.html", this.oninit);
      this.token = utils.getUrlParameter("token") != undefined ? utils.getUrlParameter("token") : -1;
      if (this.token == -1) this.token = utils.token();
   };
   
   this.oninit = function(){
      $(document).click(function(e){
         var target = $(e.target);
         
         if (!target.is('#category_list') && 
         !target.is('#category_input')) $('#category_list').hide();
         
         if (!target.is('#subcategory_list') && 
         !target.is('#subcategory_input')) $('#subcategory_list').hide();
         
         if (!target.is('#advsubcategory_list') && 
         !target.is('#advsubcategory_input')) $('#advsubcategory_list').hide();
         
         if (!target.is('#city_list') && 
         !target.is('#city_input')) $('#city_list').hide();
         
         if (!target.is('#region_list') && 
         !target.is('#region_input')) $('#region_list').hide();
      });
      
      newitem.upload = $('#item_photo_upload_a').upload({
         name: 'file',
         method: 'post',
         enctype: 'multipart/form-data',
         action: '../upload.php?item_num=-1&token='+newitem.token,
         onSelect: function(){
            if ($(document.getElementsByName("file"))[0].files[0].size < 1000000){
               $('#upload_alert').hide();
               newitem.upload.submit();
            }
            else $('#upload_alert').show();
         },
         onSubmit: function(){
            $('#item_photo_upload_a').text('Отправка файла...');
         },
         onComplete: function(data){
            if (newitem.temp_imgs.length < 2)
               $('#item_photo_upload_a').text('Загрузить еще изображение'); 
            else if (newitem.temp_imgs.length == 2)
               $('#item_photo_upload_a').text('Загрузить последнее изображение').css("color","rgb(255,200,0)");
            else if (newitem.temp_imgs.length > 2){
               $('#item_photo_upload_a').hide();
               $('#item_photo_upload_a').parent('div').remove();
               $('#photos_table').css("margin-bottom","-61px");
               $('#photo_upload_label').css("margin-top",0);
            }
            
            var obj = JSON.parse(data);
            var imgs = [];
            
            $('#photos_table').show();
            
            if (newitem.temp_item_num == -1){ 
               newitem.temp_item_num = obj.item_num;
               var img1 = "<img id='pht0' src='catalog/"+obj[0]+"' name='"+obj[0]+"' onload='resizeUploadImg(this)' />";
               newitem.temp_imgs.push(img1);
               $('#photo0').html(img1);
            }
            else{
               var img2 = "<img id='pht"+(obj.length-1)+"' src='catalog/"+obj[obj.length-1]+"' name='"+obj[obj.length-1]+"' onload='resizeUploadImg(this)' />";
               newitem.temp_imgs.push(img2);
               
               for (var i = 0; i < obj.length; i++){
                  $('#photo'+i).html(newitem.temp_imgs[i]);
               }
            }
            
            newitem.upload.action('../upload.php?item_num='+newitem.temp_item_num+"&token="+newitem.token);
         }
      });
      
      //$('#item_photo_upload_a').css("width","50%");
      $('#item_photo_upload_a').parent("div").css({"margin":"-34px 0 0 317px", "width":"314px"});
      $('input[type=file]').css("margin-left","79px");
      
      $.post(post_url,{
         a: "currency_getlist"
      },function(result){
         newitem.currency_data = JSON.parse(result);
         
         for (var i = 0; i < newitem.currency_data.length; i++){
            var option = $("<option />", {id: "option"+i, value: newitem.currency_data[i].code, text: newitem.currency_data[i].short_title+" ("+newitem.currency_data[i].symbol+")"});
            $('#price_select').append(option);
         }
         
         $("#option2").attr("selected", true);
         $('#price_select').selectmenu({
            change: function(event, ui) {
               newitem.selected_currency = ui.item.value;
            }
         });
      });
      
      $('#period_select').selectmenu({
         change: function(event, ui) {
            newitem.selected_period = ui.item.value;
         }
      });
      
      $.post(post_url,{
         a: "currency_getlist"
      },function(result){
         newitem.currency_list = JSON.parse(result);
      });
      
      if (utils.getUrlParameter("item"))
         newitem.fillForm(utils.getUrlParameter("item"));
         
      $.post(post_url,{
         a: "newitem_get_user_logined"
      },function(result){
         var obj = JSON.parse(result);
         if (obj != -1){
            newitem.author = obj.num;
            $('.radGroup1[value='+obj.status+']').prop("checked",1);
            $('#name_input').val(obj.name);
            $('#phone_input').val(obj.phone);
            $('#email_input').val(obj.email);
            $('#skype_input').val(obj.skype);
            $('#hidephone_check').prop("checked",obj.hide_phone);
            $('#address_input').val(obj.address);
            var cities = region.city_data;
            
            for (var i = 0; i < cities.length; i++)
               if (cities[i].city_code == obj.city){
                  region.current_city = obj.city;
                  region.setSearchCity(cities[i].city_code,cities[i].city_name, 'city_input');
               }
         }
      });
      
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
      
      VK.Auth.getLoginStatus(newAuthInfo);
   };
   
   this.fillForm = function(item){
      $.post(post_url,{
         a: "item_get_data_for_new_form",
         b: item
      },function(result){
         var obj = JSON.parse(result);
         
         newitem.temp_item_num = obj.num;
         newitem.upload.action('../upload.php?item_num='+newitem.temp_item_num+'&token='+newitem.token);
         $('#title_input').val(obj.title);
         $('#short_desc_input').val(obj.description);
         $('#full_desc_input').val(obj.full_description);
         $('#price_input').val(obj.price);
         $('#price_select').val(obj.currency).selectmenu("refresh");
         newitem.selected_currency = obj.currency;
         $('#auction_check').prop("checked", Number(obj.auction));
         $('#gift_check').prop("checked", Number(obj.gift));
         $('#exchange_check').prop("checked", Number(obj.exchange));
         $('.radGroup2[value='+obj.condition+']').prop("checked",1);
         
         if (obj.photo != "" && obj.photo != "null"){
            var imgs = JSON.parse(obj.photo);
            $('#photos_table').show();
            
            for (var i = 0; i < imgs.length; i++){
               var img2 = "<img id='pht"+i+"' src='catalog/"+imgs[i]+"' name='"+imgs[i]+"' onload='resizeUploadImg(this)' />";
               $('#photo'+i).html(img2);
               newitem.temp_imgs.push(img2);
            }
            
            if (imgs.length > 2){
               $('#item_photo_upload_a').hide();
               $('#item_photo_upload_a').parent('div').remove();
               $('#photos_table').css("margin-bottom","-61px");
               $('#photo_upload_label').css("margin-top",0);
            }
         }
         
         var cats = category.categories_list[0];
         var subcats = category.categories_list[1];
         var advsubcats = category.categories_list[2];
         var cities = region.city_data;
         
         for (var i = 0; i < cats.length; i++)
            if (cats[i].category_code == obj.category_code){
               category.category = obj.category_code;
               category.setSearchCategory(cats[i].category_code, cats[i].category_name, "category_input");
            }
               
         for (var i = 0; i < subcats.length; i++)
            if (subcats[i].subcategory_code == obj.subcategory_code && subcats[i].category_code == obj.category_code){
               category.setSearchSubCategory(subcats[i].subcategory_code, subcats[i].subcategory_name, "subcategory_input");
               category.subcategory = obj.subcategory_code;
            }
               
         for (var i = 0; i < advsubcats.length; i++)
            if (advsubcats[i].advsubcategory_code == obj.advsubcategory_code){
               category.advsubcategory = obj.advsubcategory_code;
               category.setSearchAdvSubCategory(advsubcats[i].advsubcategory_code, advsubcats[i].advsubcategory_name, "advsubcategory_input");
            }
         
         for (var i = 0; i < cities.length; i++)
            if (cities[i].city_code == obj.city){
               region.current_city = obj.city;
               region.setSearchCity(cities[i].city_code,cities[i].city_name, 'city_input');
            }
            
         $('#address_input').val(obj.address);
         $('#period_select').val(obj.period).selectmenu("refresh");
         $('#period_select').val(obj.period).selectmenu("refresh");
         
         $.post(post_url,{
            a: "newitem_get_user",
            b: newitem.temp_item_num,
            c: newitem.token
         },function(result){
            var obj = JSON.parse(result);
            if (obj != -1){
               newitem.author = obj.num;
               $('.radGroup1[value='+obj.status+']').prop("checked",1);
               $('#name_input').val(obj.name);
               $('#phone_input').val(obj.phone);
               $('#email_input').val(obj.email);
               $('#hidephone_check').prop("checked",Number(obj.hide_phone));
               $('#skype_input').val(obj.skype);
            }
         });
      });
   };
   
   this.fillCategoryList = function(){
      $('#category_list').html("");
      $('#category_list').show();
      category.fillCategoryList('category_list', 'category_input');
   };
   
   this.fillSubCategoryList = function(){
      $('#subcategory_list').html("");
      $('#subcategory_list').show();
      category.fillSubCategoryList('subcategory_list', 'subcategory_input');
   };
   
   this.fillAdvSubCategoryList = function(){
      $('#advsubcategory_list').html("");
      $('#advsubcategory_list').show();
      category.fillAdvSubCategoryList('advsubcategory_list', 'advsubcategory_input');
   };
   
   this.fillCityList = function(){
      $('#city_list').html("");
      $('#city_list').show();
      region.fillCityList('city_list', 'city_input');
   };
   
   this.checkEmpty = function(){
      $('.neccesary_input').each(function(){
         if ($(this).val().length == 0){
            newitem.errors++;
            newitem.hlInput($(this));
          
            if(!newitem.scrolled){
               $('html,body').stop().animate({ scrollTop: $(this).offset().top-100 }, 200);
               newitem.scrolled++;
            }
         }
      });
      
      return this.errors;
   };
   
   this.hlInput = function(element){
      element.css({background:"#c6123d"});
      element.animate({backgroundColor: "rgba(0,0,0,0)"}, 1000);
   };
   
   this.checkNotChecked = function(){
      if ($('input.radGroup1:checked').length == 0){
         newitem.errors++;
         newitem.hlText($('label.radGroup1'));
       
         if(!newitem.scrolled){
            $('html,body').stop().animate({ scrollTop: $('label.radGroup1').offset().top-100 }, 200);
            newitem.scrolled++;
         }
      }
      
      if ($('#agreement_check:checked').length == 0){
         newitem.errors++;
         newitem.hlText($('#agreement_check_label'));
      }
      
      
      return this.errors;
   };
   
   this.hlText = function(element){
      element.css({color:"red"});
      element.animate({color: "#000"}, 2000);
   };
   
    this.add = function(){
        if (
                !this.checkEmpty() && 
                !this.checkNotChecked() && 
                !this.showMinusWords() &&
                this.captchaIsTrue()
        ){
            $.post(post_url,{
                a: "item_add",
                b: this.collectData(),
                c: this.temp_item_num,
                d: this.token
            },function(result){
                if (result != -1){
                    $('#submit_buttons_wrapper').hide();
                    $('#content-area').html("<div id='add_result_msg' style='width:100%;text-align:center;'>Объявление успешно добавлено. Посмотреть его можно <a href='item.php?num="+(newitem.temp_item_num != -1 ? newitem.temp_item_num : result)+"' target='_blank'>по ссылке</a>.</div>");
                }
                else{
                    $('#content-area').html("<div id='add_result_msg' style='width:100%;text-align:center;'>Что-то пошло не так. Приносим извинения за неудобства.</div>.");
                }

                $('#add_result_msg').css("line-height", $('#content-area').height()+"px");
            });
        }
        
        this.scrolled = 0;
        this.errors = 0;
    };
    
    this.captchaIsTrue = function(){
        if (recaptcha_response){
            $('#captcha_error_span').hide();
            
            return true;
        }
        else{  
            $('#captcha_error_span').show();
            
            return false;
        }
    }
   
   this.preview = function(){
        if (
                !this.checkEmpty() && 
                !this.checkNotChecked() && 
                !this.showMinusWords() &&
                this.captchaIsTrue()
        ){
            $.post(post_url,{
                a: "item_preview",
                b: this.collectData(),
                c: this.temp_item_num,
                d: this.token
            },function(result){
                location.href = location.origin+"/preview.php?item="+result+"&token="+newitem.token;
            });
        }
        
        this.scrolled = 0;
        this.errors = 0;
   };
   
   this.collectData = function(){
      var data = {};
      
      data.title = $('#title_input').val();
      data.description = $('#short_desc_input').val();
      data.full_description = $('#full_desc_input').val();
      data.price = $('#price_input').val();
      data.currency = this.selected_currency;
      data.auction = $('#auction_check:checked').length;
      data.gift = $('#gift_check:checked').length;
      data.exchange = $('#exchange_check:checked').length;
      data.condition = $('.radGroup2:checked').val();
      data.category_code = $('#category_input').attr("category_code");
      data.subcategory_code = $('#subcategory_input').attr("subcategory_code");
      data.advsubcategory_code = $('#advsubcategory_input').attr("advsubcategory_code");
      data.city = $('#city_input').attr("city_code");
      data.address = $('#address_input').val();
      data.author = newitem.author;
      data.person_status = $('.radGroup1:checked').val();
      data.person_name = $('#name_input').val();
      data.person_phone = $('#phone_input').val();
      data.person_phone_hide = $('#hidephone_check:checked').length;
      data.person_email = $('#email_input').val();
      data.person_skype = $('#skype_input').val();
      data.period = this.selected_period;
      
      var myJSONString = JSON.stringify(data);
      var myEscapedJSONString = myJSONString.replace(/\\n/g, " ");
      return myEscapedJSONString;
   };
   
    this.lockPrice = function(){
        if ($('#gift_check:checked').length > 0){
            $('#price_input').attr("disabled", true).val("");
            $('#price_select').selectmenu("disable");
            $('#price_input').removeClass("neccesary_input");
        }
        else{
            $('#price_input').addClass("neccesary_input");
            $('#price_input').attr("disabled", false);
            $('#price_select').selectmenu("enable")
        }
    };
   
    this.checkMinusWords = function(input){
        $.post(post_url,{
            a: "item_check_minuswords",
            b: $(input).val().trim()
        },function(result){
            if (result == 1){
                $("#"+$(input).attr("id")+"_minus_words_error_span").show();
            }
            else{
                $("#"+$(input).attr("id")+"_minus_words_error_span").hide();
            }
        });
    };
   
    this.showMinusWords = function(){ // если вернула false то минус-слов нет
        if ($('.minus_words_error:visible').length > 0){
            $('html,body').stop().animate({ scrollTop: $('.minus_words_error:visible').offset().top-300 }, 200);
            this.scrolled++;
            return true;
        }
        else{
            return false;
        }
    };
}
 
function getReCaptchaResponse(){ // получает ответ капчи с серва
    $.post(post_url,{
        a: "user_get_recaptcha_response",
        b: grecaptcha.getResponse()
    },function (response){
        var obj = JSON.parse(response);
        console.log(obj);
        
        if (response.error != undefined){
            //showErrorMessage(response.error.description);
        }
        else{
            recaptcha_response = obj.success;
        }
    });
}