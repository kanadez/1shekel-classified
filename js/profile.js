var post_url = "./php/post.php";
var profile = null;
var utils = null;
var feedback = null;
var region = null;
var dropdown = null;
var category = null;

$(document).ready(function(){
   utils = new Utils();
   profile = new Profile();
   feedback = new Feedback();
   region = new Region();
   dropdown = new Dropdown();
   category = new Category();
   region.getCityData();
   region.getRegionData();
   profile.setupDOM();
   profile.edit();
   category.init();
   VK.init({apiId: 5188263});
});

function Profile(){
   this.user_num = null; // будет чиатьтся сюда на основе текущей сессии
   this.user_obj = null;
   this.current_side_button = $('#edit_button');
   this.items_per_page = null;
   this.pages = null;
   this.page = 1;
   this.credits_page = 1;
   this.curr_coef = null;
   this.items_status = 1;
   this.item_cash = {};
   this.item_status_cash = {};
   this.people_object = [];
   this.dialogues = [];
   this.messages = {};
   this.locutors = [];
   this.current_locutor = null;
   this.current_dialoguid = null;
   this.current_dialog = null;
   this.promoting_item = null;// объявление, которое в аднный момент расшаривается
   this.promoting_item_image = null;
   this.promoting_item_title = null;
   this.vip_rates = {};
   this.vk_authorized = 0;
   this.coauth = 0;
   
   this.setupDOM = function(){
      $(document).click(function(e){
         var target = $(e.target);
         
         if (!target.is('#city_list') && !target.is('#profile_city_input')) $('#city_list').hide();
           
         if (!target.is('#promotions_category_list') && !target.is('#promotions_category_input')) $('#promotions_category_list').hide();
         
         if (!target.is('#promotions_city_list') && !target.is('#promotions_city_input')) $('#promotions_city_list').hide();
      });
      
      $.post(post_url,{
         a: "profile_get_dummies"
      },function (result){
         var obj = JSON.parse(result);
         
         if (obj.mail > 0) $('#mail_button').text("Мои сообщения ("+obj.mail+")");
         if (obj.items > 0) $('#items_button').text("Мои объявления ("+obj.items+")");
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
      
      $('#profilephoto-wrapper').mouseover(function(){
         $('#profile_photo_edit_wrapper').css("opacity",0.7);
      });
      
      $('#profilephoto-wrapper').mouseout(function(){
         $('#profile_photo_edit_wrapper').css("opacity",0.1);
      });
      
      $('#profile_photo_edit_input').fileupload({
         done: function (e, data) {
            var ava = new Image();
            ava.id = "profilephoto";
            ava.src = data.result;
            ava.onload = function(){$('#profilephoto').width(70).height(70)};
            $('#profile_photo_img').html(ava);
            $('#profile_photo_edit_wrapper').css("opacity",0.1);
        }
      });
   };
   
   this.switchSideButton = function(button){
      $('#content-area').css("padding","20px");
      this.current_side_button.removeClass("focus_side_button");
      this.current_side_button = button;
      this.current_side_button.addClass("focus_side_button");
   };
   
   this.setPagination = function(switch_function){
      var p = Number(this.page);
      var ps = Number(this.pages);
      
      $('.pagination').html("");
      
      if (ps > 4){
         if (p == 1){
            $('.pagination').append('<li><a class="disabled" style="font-weight : bolder" onclick="'+switch_function+'(1);" href="javascript:void(0)">1</a></li>');
            $('.pagination').append('<li><a onclick="'+switch_function+'(2);" href="javascript:void(0)">2</a></li>');
            $('.pagination').append('<li><a onclick="'+switch_function+'(3);" href="javascript:void(0)">3</a></li>');
            $('.pagination').append('<li><a class="next" onclick="'+switch_function+'('+ps+');" href="javascript:void(0)">»</a></li>');
         }
         else if (p > 1 && p < ps-2){
            $('.pagination').append('<li><a class="prev" onclick="'+switch_function+'(1);" href="javascript:void(0)">«</a></li>');
            $('.pagination').append('<li><a onclick="'+switch_function+'('+(p-1)+');" href="javascript:void(0)">'+(p-1)+'</a></li>');
            $('.pagination').append('<li><a class="disabled" style="font-weight : bolder" onclick="'+switch_function+'('+p+');" href="javascript:void(0)">'+p+'</a></li>');
            $('.pagination').append('<li><a onclick="'+switch_function+'('+(p+1)+');" href="javascript:void(0)">'+(p+1)+'</a></li>');
            $('.pagination').append('<li><a class="next" onclick="'+switch_function+'('+ps+');" href="javascript:void(0)">»</a></li>');
         }
         else if (p > 1 && p < ps-1){
            $('.pagination').append('<li><a class="prev" onclick="'+switch_function+'(1);" href="javascript:void(0)">«</a></li>');
            $('.pagination').append('<li><a onclick="'+switch_function+'('+(p-1)+');" href="javascript:void(0)">'+(p-1)+'</a></li>');
            $('.pagination').append('<li><a class="disabled" style="font-weight : bolder" onclick="'+switch_function+'('+p+');" href="javascript:void(0)">'+p+'</a></li>');
            $('.pagination').append('<li><a onclick="'+switch_function+'('+(p+1)+');" href="javascript:void(0)">'+(p+1)+'</a></li>');
            $('.pagination').append('<li><a onclick="'+switch_function+'('+(p+2)+');" href="javascript:void(0)">'+(p+2)+'</a></li>');
         }
         else if (p > 1 && p < ps){
            $('.pagination').append('<li><a class="prev" onclick="'+switch_function+'(1);" href="javascript:void(0)">«</a></li>');
            $('.pagination').append('<li><a onclick="'+switch_function+'('+(p-1)+');" href="javascript:void(0)">'+(p-1)+'</a></li>');
            $('.pagination').append('<li><a class="disabled" style="font-weight : bolder" onclick="'+switch_function+'('+p+');" href="javascript:void(0)">'+p+'</a></li>');
            $('.pagination').append('<li><a onclick="'+switch_function+'('+(p+1)+');" href="javascript:void(0)">'+(p+1)+'</a></li>');
            
         }
         else if (p == ps){
            $('.pagination').append('<li><a class="prev" onclick="'+switch_function+'(1);" href="javascript:void(0)">«</a></li>');
            $('.pagination').append('<li><a onclick="'+switch_function+'('+(p-2)+');" href="javascript:void(0)">'+(p-2)+'</a></li>');
            $('.pagination').append('<li><a onclick="'+switch_function+'('+(p-1)+');" href="javascript:void(0)">'+(p-1)+'</a></li>');
            $('.pagination').append('<li><a class="disabled" style="font-weight : bolder" onclick="'+switch_function+'('+p+');" href="javascript:void(0)">'+p+'</a></li>');
            
         }
      }
      else{
         for (var i = 1; i <= ps; i++){
            $('.pagination').append('<li><a '+(i == p ? 'class="disabled" style="font-weight : bolder"' : '')+' onclick="'+switch_function+'('+i+');" href="javascript:void(0)">'+i+'</a></li>');
         }
      }
   };
   
   //################################# Мой профиль ###################################//
   
   this.edit = function(){
        this.switchSideButton($('#edit_button'));
        $('#content-area').load("./dom/profile_edit.html", this.loadUserData);
        
        if (utils.getURLParameter("action") === "payment"){
            $('#content-area').load("./dom/fill_balance.html", function(){
                $('#result_wrapper_div').show();
                $('#input_wrapper_div, #header_wrapper_div, .profile_button_wrapper').hide();
                
                if (utils.getURLParameter("success") === "true"){
                    $('#payment_result_span').text("Платеж прошел успешно. Баланс пополнен.");
                }
                else if (utils.getURLParameter("error") != undefined){
                    $('#payment_result_span').text("Платеж не прошёл. Попробуйте снова сейчас или позднее.");
                    $('#result_callback_button').html("Попробовать ещё раз").attr("onclick", "profile.fillBalance()");
                }
            });
        }
        else if (utils.getURLParameter("action") === "promote"){
            profile.promoteItem(utils.getURLParameter("item"));
        }
        else if (utils.getURLParameter("action") === "remove"){
            profile.switchSideButton($('#items_button'));
            $('#content-area').load("./dom/profile_items.html", function(){
                profile.removeItem(utils.getURLParameter("item"));
                $('#catalog_list_div').append('<div style="width:100%;margin-top:200px;text-align:center;color:#aaa">Объявление успешно удалено.<br><a style="width: 200px !important;" href="new.php" class="btn transparent orange fw">Подать другое объявление</a></div>');
            });
        }
   };
   
   this.loadUserData = function(){
      $.post(post_url,{
         a: "profile_get"
      },function (result){
         var obj = profile.user_obj = eval("("+result+")");
         profile.user_num = obj.num;
         region.current_city = obj.city_code;
         region.current_city_name = obj.city;
         var ava = new Image();
         ava.id = "profilephoto";
         ava.src = obj.photo_100;
         ava.onload = function(){$('#profilephoto').width(70).height(70)};
         
         $('#profile_name_input').val(obj.name);
         $('#side-panel #profilename').html(obj.name);
         $('#profile_city_input').val(obj.city).attr("city_code", obj.city_code);
         $('#profile_address_input').val(obj.address);
         $('#profile_phone_input').val(obj.phone);
         $('#balance_div').html(obj.coins+" баллов");
         $('#profile_photo_img').html(ava);
         $('#profile_oldemail_input').val(utils.hideEmail(obj.email));
         
         if (utils.getURLParameter("action") === "change_email"){
            if (utils.getURLParameter("status") == "success")
                $('#personal_alert').show().addClass("profile_notify").removeClass("profile_alert").html("e-Mail успешно обновлен.");
            else $('#personal_alert').show().html("Что-то пошло не так. Попробуйте заново.");
         }
         
         VK.Auth.getLoginStatus(authInfo);
      });
   };
   
   this.changeUserPasswd = function(){
      if (!this.checkPasswdEmpty() && !this.checkNewPasswdCorrect())
         $.post(post_url,{
            a: "profile_compare_passwd",
            b: this.user_num,
            c: $('#profile_oldpasswd_input').val(),
            d: $('#profile_newpasswd_input').val()
         },function (result){
            if (result == 1)
               $('#password_alert').show().addClass("profile_notify").removeClass("profile_alert").html("Пароль успешно изменён.");
            else if (result == -1) 
               $('#password_alert').show().html("Пароль не изменён, так как старый пароль введён неверно.");
         });
   };
   
   this.checkPasswdEmpty = function(){
      var error = 0;
      var oldpass = $.trim($('#profile_oldpasswd_input').val());
      var newpass = $.trim($('#profile_newpasswd_input').val());
      var newpassagain = $.trim($('#profile_newpasswdagain_input').val());
      
      if (oldpass.length == 0){
         $('#profile_oldpasswd_input').animate({backgroundColor: '#c36868'}, {queue:false, duration:0, complete: function(){$(this).animate({backgroundColor: '#FFF'}, {queue:false, duration:1000})}});
         error = 1;
      }
      
      if (newpass.length == 0){
         $('#profile_newpasswd_input').animate({backgroundColor: '#c36868'}, {queue:false, duration:0, complete: function(){$(this).animate({backgroundColor: '#FFF'}, {queue:false, duration:1000})}});
         error = 1;
      }
      
      if (newpassagain.length == 0){
         $('#profile_newpasswdagain_input').animate({backgroundColor: '#c36868'}, {queue:false, duration:0, complete: function(){$(this).animate({backgroundColor: '#FFF'}, {queue:false, duration:1000})}});
         error = 1;
      }
      
      return error;
   };
   
   this.checkNewPasswdCorrect = function(){
      var error = 0;
      var newpass = $.trim($('#profile_newpasswd_input').val());
      var newpassagain = $.trim($('#profile_newpasswdagain_input').val());
      
      if (newpass !== newpassagain){
         $('#password_alert').show().html("Пароль не изменён, так как новый пароль повторен неправильно.");
         error++;
      }
      else if (newpass.length < 6){
         $('#password_alert').show().html("Пароль не изменён, так как новый пароль слишком короткий. Должен быть больше 6 символов.");
         error++;
      }
      
      return error;
   };
   
   this.changeUserEmail = function(){
      if (!this.checkEmailEmpty() && this.emailIsOK())
         $.post(post_url,{
            a: "profile_change_email",
            b: this.user_num,
            c: $('#profile_newemail_input').val()
         },function (result){
            if (result == 1)
               $('#email_alert').show().addClass("profile_notify").removeClass("profile_alert").html("На Ваш новый E-Mail выслано подтверждение. Проверьте почту.");
            else $('#email_alert').show().html("Что-то пошло не так. Попробуйте заново.");
         });
   };
   
   this.checkEmailEmpty = function(){
      var newemail = $.trim($('#profile_newemail_input').val());
      
      if (newemail.length == 0){
         $('#profile_newemail_input').animate({backgroundColor: '#c36868'}, {queue:false, duration:0, complete: function(){$(this).animate({backgroundColor: '#FFF'}, {queue:false, duration:1000})}});
         return 1;
      }
      else return 0;
   };
   
   this.emailIsOK = function(){
      var email = $.trim($('#profile_newemail_input').val());
      var at = email.indexOf("@");
      var dot = email.indexOf(".");
      
      if (at*dot > 1)
         return 1;
      else{
         $('#email_alert').show().html("E-Mail не изменён, так как электронный адрес введён некорректно.");
         return 0;
      }
   };
   
   this.changePersonalData = function(){
      $.post(post_url,{
         a: "profile_set_personal_data",
         b: this.user_num,
         c: $('#profile_name_input').val(),
         d: $('#profile_city_input').attr("city_code"),
         e: $('#profile_address_input').val(),
         f: $('#profile_phone_input').val()
      },function (result){
         if (result == 1)
            $('#personal_alert').show().addClass("profile_notify").removeClass("profile_alert").html("Данные успешно обновлены.");
         else $('#personal_alert').show().html("Что-то пошло не так. Попробуйте заново.");
      });
   };
   
   this.fillCityList = function(){
      $('#city_list').html("");
      $('#city_list').show();
      region.fillCityList('city_list', 'profile_city_input');
   };
   
   //######################################### Мои объявления ##############################################//
   
   this.items = function(){
      this.switchSideButton($('#items_button'));
      $('#content-area').load("./dom/profile_items.html", function(){profile.getItems(1)});
   };
   
   this.getItems = function(page){
      this.page = page;
      
      $('#catalog_list_div').html("");
      
      $.post(post_url,{
         a: "profile_get_items",
         b: page-1, // selected category
         c: this.items_status
      },function (result){
         var catalog_data = eval("(" + result + ")");
            console.log(catalog_data);
         
            var getButtons = function(item, balance){
               if (profile.items_status == 1)
                  return '<div class="item_buttons_block"><span style="margin-right:10px"><a href="javascript:void(0)" onclick="profile.promoteItem('+item+')" class="repost_btn promote_btn">Продвинуть</a><a href="/edit.php?item='+item+'" class="item_button">Редактировать</a><a href="javascript:void(0)" onclick="profile.closeItem('+item+')" class="item_button">Закрыть</a><a onclick="profile.removeItem('+item+')" href="javascript:void(0)">Удалить</a></div>';
               else return '<div class="item_buttons_block"><span style="margin-right:10px"><a href="javascript:void(0)" onclick="profile.restoreItem('+item+')" class="item_button">Активировать</a><a onclick="profile.removeItem('+item+')" href="javascript:void(0)">Удалить безвозвратно</a></div>';
            };
            
            var getVipStatus = function(item){
               if (item.vip != 0)
                  return '<span class="vip_badge">vip</span>';
               else return "";
            };
            if (catalog_data[catalog_data.length-1].items_count > 0){
               for (var i = 0; i < catalog_data.length; i++)
                  if (i != catalog_data.length-1){
                     var left = utils.getDaysLeft(catalog_data[i].timestamp, catalog_data[i].period) != -1 ? ' (осталось '+utils.getDaysLeft(catalog_data[i].timestamp, catalog_data[i].period)+' дней)' : ' (объявление просрочено, <a href="javascript:void(0)" onclick="profile.prolongItem('+catalog_data[i].num+')">продлить</a>) ';
                     
                     if (utils.isset(catalog_data[i].photo)){
                        var photo_data = eval("(" + catalog_data[i].photo + ")");
                        $('#catalog_list_div').append('<div id="catalog_item_'+catalog_data[i].num+'" class="item-wrapper"><div class="item-top-details" style="width: 876px;"></div><div id="_item_'+catalog_data[i].num+'" class="item" href="item.php?num='+catalog_data[i].num+'"><div class="image-wrapper" style="background-image:url(catalog/'+photo_data[0]+')"><div class="gradient"></div></div><div class="content-wrapper"><div class="title-wrapper" style="clear: both"><h3 class="title"><a target="_blank" href="item.php?num='+catalog_data[i].num+'">'+catalog_data[i].title+'</a>'+getVipStatus(catalog_data[i])+'</h3><span class="text-muted">'+utils.getDateTimeForTimestamp(catalog_data[i].timestamp)+left+'</span></div><strong class="price"> '+catalog_data[i].price+' '+catalog_data[i].currency_short_title+' '+(utils.isset(catalog_data[i].auction) ? '<span class="text-muted" style="float:right">торг</span>' : '')+'</strong><div class="clearfix"></div><div class="description"></div>'+getButtons(catalog_data[i].num, catalog_data[i].credit)+'</div></div>');
                     }
                     else{
                        $('#catalog_list_div').append('<div id="catalog_item_'+catalog_data[i].num+'" class="item-wrapper"><div class="item-top-details" style="width: 876px;"></div><div id="_item_'+catalog_data[i].num+'" class="item" href="item.php?num='+catalog_data[i].num+'"><div class="image-wrapper" style="background-image:url(img/camera.png)"><div class="gradient"></div></div><div class="content-wrapper"><div class="title-wrapper" style="clear: both"><h3 class="title"><a target="_blank" href="item.php?num='+catalog_data[i].num+'">'+catalog_data[i].title+'</a>'+getVipStatus(catalog_data[i])+'</h3><span class="text-muted">'+utils.getDateTimeForTimestamp(catalog_data[i].timestamp)+left+'</span></div><strong class="price"> '+catalog_data[i].price+' '+catalog_data[i].currency_short_title+' '+(utils.isset(catalog_data[i].auction) ? '<span class="text-muted" style="float:right">торг</span>' : '')+'</strong><div class="clearfix"></div><div class="description"></div>'+getButtons(catalog_data[i].num, catalog_data[i].credit)+'</div></div>');
                     }
                  }
                  else{
                     profile.items_per_page = catalog_data[i].catalog_items_per_page;
                     profile.pages = Math.ceil(catalog_data[i].items_count/profile.items_per_page);
                     profile.setPagination("profile.getItems");
                     profile.curr_coef = catalog_data[i].curr_coef;
                  }
            }
            else{
               if (profile.items_status == 1)
                  $('#catalog_list_div').html('<div style="width:100%;margin-top:200px;text-align:center;color:#aaa">Объявлений пока нет.<br><a style="width: 200px !important;" href="new.php" class="btn transparent orange fw">Подать объявление</a></div>');
               else $('#catalog_list_div').html('<div style="width:100%;margin-top:200px;text-align:center;color:#aaa">Закрытых объявлений пока нет..</div>');
            }
      });
   };
   
   this.changeItemsStatus = function(value){
      this.items_status = value;
      
      this.getItems(1);
      
      if (value == 1){
         $('#show_active_items_button').addClass("focus_side_button");
         $('#show_closed_items_button').removeClass("focus_side_button");
      }
      else{
         $('#show_active_items_button').removeClass("focus_side_button");
         $('#show_closed_items_button').addClass("focus_side_button");
      }
   };
   
   this.closeItem = function(item){
      this.item_cash[item] = $('#_item_'+item).children();
      
      $('#_item_'+item).css({"text-align": "center", "line-height": "93px"});
      $('#_item_'+item).html('Объявление закрыто. <a href="javascript:void(0)" onclick="profile.restoreItem('+item+')">Восставновить</a>.');
      
      $.post(post_url,{
         a: "profile_close_item",
         b: item
      },function (result){});
   };
   
   this.removeItem = function(item){
      this.item_cash[item] = $('#_item_'+item).children();
      
      if (this.items_status == 2){
         $('#_item_'+item).css({"text-align": "center", "line-height": "93px"});
         $('#_item_'+item).html('Объявление безвозвратно удалено.');
      }
      else{
         $('#_item_'+item).css({"text-align": "center", "line-height": "93px"});
         $('#_item_'+item).html('Объявление удалено. <a href="javascript:void(0)" onclick="profile.restoreItem('+item+')">Восставновить</a>.');
      }
      
      $.post(post_url,{
         a: "profile_remove_item",
         b: item
      },function (result){});
   };
   
   this.restoreItem = function(item){
      if (this.items_status == 2){
         $('#_item_'+item).css({"text-align": "center", "line-height": "93px"});
         $('#_item_'+item).html('Объявление снова открыто.');
      }
      else{
         $('#_item_'+item).css({"text-align": "left", "line-height": "19px"});
         $('#_item_'+item).html(this.item_cash[item]);
      }
      
      $.post(post_url,{
         a: "profile_restore_item",
         b: item
      },function (result){});
   };
   
   // ###################################################### Мои сообщения ############################################
   
   this.mail = function(){
      this.switchSideButton($('#mail_button'));
      $('#content-area').load("./dom/profile_mail.html", function(){profile.getDialogues()});
   };
   
   this.getDialogues = function(){
      this.dialogues = [];
      this.locutors = [];
      
      $('#content-area').css({"padding":0});
      
      $.post(post_url,{
         a: "mail_get_dialogues"
      },function (result){
         var locutors = [];
         var obj = eval("("+result+")");
         
         try {
             if (obj.length === 0)
                 throw "Сообщений пока нет.";
             
            profile.dialogueFilterExist(obj);

            for (var i = 0; i < profile.dialogues.length; i++)
               if (profile.dialogues[i].sender != profile.user_num)
                  locutors.push(profile.dialogues[i].sender);
               else 
                  locutors.push(profile.dialogues[i].recepient);

            $.post(post_url,{
               a: "mail_get_locutors",
               b: JSON.stringify(locutors)
            },function (result){
               var obj = eval("("+result+")");

               for (var i = 0; i < obj.length; i++)
                  profile.locutors[obj[i].num] = obj[i];

               for (var i = 0; i < profile.dialogues.length; i++)
                  if (profile.dialogues[i].sender == profile.user_num && profile.locutors[profile.dialogues[i].recepient] != undefined){
                     var user = profile.locutors[profile.dialogues[i].recepient];
                     $('#mail_side_panel').append('<div id="'+profile.dialogues[i].dialoguid+'" class="friend_block" onclick=\'profile.getDialog("'+profile.dialogues[i].dialoguid+'",'+user.num+')\'><div class="frienddata_wrapper"><div class="friendphoto_wrapper"><img id="friend_2_photo" src="'+user.photo_100+'" class="friend_image_w"></div><span class="friendname">'+user.name+'</span><br><div class="mywrapper '+(profile.dialogues[i].unread == 1 ? "unread_sended" : "")+'"><div class="myphoto_wrapper"><img class="my_image_w" src="'+profile.user_obj.photo_100+'" /></div><span id="message_preview">'+profile.dialogues[i].message+'</span></div><span class="msg_time">'+utils.getTimeForTimestamp(profile.dialogues[i].timestamp)+'</span><div class="friend_status_indicator"></div></div>');
                  }
                  else if (profile.locutors[profile.dialogues[i].sender] != undefined){
                     var user = profile.locutors[profile.dialogues[i].sender];
                     $('#mail_side_panel').append('<div id="'+profile.dialogues[i].dialoguid+'" class="friend_block '+(profile.dialogues[i].unread == 1 ? "unread_received" : "")+'" onclick=\'profile.getDialog("'+profile.dialogues[i].dialoguid+'",'+user.num+')\'><div class="frienddata_wrapper"><div class="friendphoto_wrapper"><img id="friend_2_photo" src="'+user.photo_100+'" class="friend_image_w"></div><span class="friendname">'+user.name+'</span><br><span id="message_preview">'+profile.dialogues[i].message+'</span></div><span class="msg_time">'+utils.getTimeForTimestamp(profile.dialogues[i].timestamp)+'</span><div class="friend_status_indicator"></div></div>');
                  }
                  else if (profile.dialogues[i].sender == 0){
                        console.log(profile.dialogues[i].message);
                        var user = {
                            num: 0,
                            photo_100: "http://1shekel.com/user/default_user.png",
                            name: profile.dialogues[i].email
                        };
                        $('#mail_side_panel').append('<div id="'+profile.dialogues[i].dialoguid+'" class="friend_block '+(profile.dialogues[i].unread == 1 ? "unread_received" : "")+'" onclick=\'profile.getEmailDialog("'+profile.dialogues[i].dialoguid+'","'+user.name+'", "'+profile.dialogues[i].message+'", "'+profile.dialogues[i].timestamp+'")\'><div class="frienddata_wrapper"><div class="friendphoto_wrapper"><img id="friend_2_photo" src="'+user.photo_100+'" class="friend_image_w"></div><span class="friendname">'+user.name+'</span><br><span id="message_preview">'+profile.dialogues[i].message+'</span></div><span class="msg_time">'+utils.getTimeForTimestamp(profile.dialogues[i].timestamp)+'</span><div class="friend_status_indicator"></div></div>');
                  }
            });
         }
         catch(error){
             $('#content-area').html('<div style="width:100%;padding-top:200px;text-align:center;color:#aaa">'+error+'<br><a style="width: 200px !important;" href="new.php" class="btn transparent orange fw">Подать объявление</a></div>');
         }
      });
   };
   
   this.getDialog = function(dialoguid, locutor){
      $('#mail_content_panel').html("");
      
      if (this.current_dialoguid != null)
         $('#'+this.current_dialoguid).removeClass("current_dialog");
         
      $('#'+dialoguid).addClass("current_dialog");
      
      this.current_locutor = locutor;
      this.current_dialoguid = dialoguid;
      
      $.post(post_url,{
         a: "mail_get_dialog",
         b: dialoguid
      },function (result){
         var obj = eval("("+result+")");
         profile.messages[obj[0].dialoguid] = obj;
         
         for (var i = 0; i < obj.length; i++)
            if (obj[i].sender == profile.user_num)
               $('#mail_content_panel').append('<div class="message_wrapper"><div class="my_message"><div class="dialog_left_message_wrapper_arrow"></div><div class="dialog_left_message_wrapper">'+obj[i].message+'</div></div><span class="dialog_left_message_timespan">'+utils.getTimeForTimestamp(obj[i].timestamp)+'</span></div>');
            else $('#mail_content_panel').append('<div class="message_wrapper"><div class="interlocutor_message"><div class="dialog_right_message_wrapper">'+obj[i].message+'</div><div class="dialog_right_message_wrapper_arrow_div"><img class="dialog_right_message_wrapper_arrow_img" src="img/dialog_arrow_right.png" /></div></div><span class="dialog_right_message_timespan">'+utils.getTimeForTimestamp(obj[i].timestamp)+'</span></div>');
         
         var element = document.getElementById("mail_content_panel");
         element.scrollTop = element.scrollHeight;
      });
   };
   
   this.getEmailDialog = function(dialoguid, email, message, timestamp){
        $('#mail_content_panel').html("");

        if (this.current_dialoguid != null)
           $('#'+this.current_dialoguid).removeClass("current_dialog");

        $('#'+dialoguid).addClass("current_dialog");

        this.current_locutor = 0;
        this.current_dialoguid = dialoguid;

        $('#mail_content_panel').append('<div class="message_wrapper"><div class="interlocutor_message"><div class="dialog_right_message_wrapper">'+message+'</div><div class="dialog_right_message_wrapper_arrow_div"><img class="dialog_right_message_wrapper_arrow_img" src="img/dialog_arrow_right.png" /></div></div><span class="dialog_right_message_timespan">'+utils.getTimeForTimestamp(timestamp)+'</span><a id="email_answer_button" style="margin-left: 44px;margin-top: 50px;width: 78% !important;" class="btn transparent orange fw" href="mailto:'+email+'">Ответить по электронной почте</a></div>');
         
        var element = document.getElementById("mail_content_panel");
        element.scrollTop = element.scrollHeight;
        
        $.post(post_url,{
            a: "mail_setemailread",
            b: dialoguid
        }, null);
   };
   
   this.dialogueFilterExist = function(dialogues_obj){
      this.dialogues.push(dialogues_obj[0]);
      
      for (var c = 0; c < dialogues_obj.length; c++){
         var exist = 0;
         
         for (var i = 0; i < this.dialogues.length; i++)
            if (dialogues_obj[c].dialoguid == this.dialogues[i].dialoguid)
               exist++;
               
         if (!exist)
            this.dialogues.push(dialogues_obj[c]);
      }
   };
   
   this.writeMessage = function(){
      if ($('#message_area').val() != ""){
         var time = new Date();
         this.messages[this.current_dialoguid].push({
            dialoguid: this.current_dialoguid,
            message: $('#message_area').val(),
            recepient: this.current_locutor,
            sender: this.user_obj.num,
            timestamp: utils.getTimestamFromDate(),
            type: 1
         });
         
         for (var i = 0; i < profile.dialogues.length; i++)
            if (profile.dialogues[i].dialoguid == this.current_dialoguid){
               profile.dialogues[i].message = $('#message_area').val();
               profile.dialogues[i].recepient = this.current_locutor;
               profile.dialogues[i].sender = this.user_obj.num;
               profile.dialogues[i].timestamp = utils.getTimestamFromDate();
            }
         
         $('#mail_side_panel').html("");
         
         for (var i = 0; i < profile.dialogues.length; i++)
            if (profile.dialogues[i].sender == profile.user_num && profile.locutors[profile.dialogues[i].recepient] != undefined){
               var user = profile.locutors[profile.dialogues[i].recepient];
               $('#mail_side_panel').append('<div id="'+profile.dialogues[i].dialoguid+'" class="friend_block" onclick=\'profile.getDialog("'+profile.dialogues[i].dialoguid+'",'+user.num+')\'><div class="frienddata_wrapper"><div class="friendphoto_wrapper"><img id="friend_2_photo" src="'+user.photo_100+'" class="friend_image_w"></div><span class="friendname">'+user.name+'</span><br><div class="mywrapper '+(profile.dialogues[i].unread == 1 ? "unread_sended" : "")+'"><div class="myphoto_wrapper"><img class="my_image_w" src="'+profile.user_obj.photo_100+'" /></div><span id="message_preview">'+profile.dialogues[i].message+'</span></div><span class="msg_time">'+utils.getTimeForTimestamp(profile.dialogues[i].timestamp)+'</span><div class="friend_status_indicator"></div></div>');
            }
            else if (profile.locutors[profile.dialogues[i].sender] != undefined){
               var user = profile.locutors[profile.dialogues[i].sender];
               $('#mail_side_panel').append('<div id="'+profile.dialogues[i].dialoguid+'" class="friend_block '+(profile.dialogues[i].unread == 1 ? "unread_received" : "")+'" onclick=\'profile.getDialog("'+profile.dialogues[i].dialoguid+'",'+user.num+')\'><div class="frienddata_wrapper"><div class="friendphoto_wrapper"><img id="friend_2_photo" src="'+user.photo_100+'" class="friend_image_w"></div><span class="friendname">'+user.name+'</span><br><span id="message_preview">'+profile.dialogues[i].message+'</span></div><span class="msg_time">'+utils.getTimeForTimestamp(profile.dialogues[i].timestamp)+'</span><div class="friend_status_indicator"></div></div>');
            }
            else if (profile.dialogues[i].sender == 0){
                var user = {
                    num: 0,
                    photo_100: "http://1shekel.com/user/default_user.png",
                    name: profile.dialogues[i].email
                };
                $('#mail_side_panel').append('<div id="'+profile.dialogues[i].dialoguid+'" class="friend_block '+(profile.dialogues[i].unread == 1 ? "unread_received" : "")+'" onclick=\'profile.getEmailDialog("'+profile.dialogues[i].dialoguid+'","'+user.name+'", "'+profile.dialogues[i].message+'", "'+profile.dialogues[i].timestamp+'")\'><div class="frienddata_wrapper"><div class="friendphoto_wrapper"><img id="friend_2_photo" src="'+user.photo_100+'" class="friend_image_w"></div><span class="friendname">'+user.name+'</span><br><span id="message_preview">'+profile.dialogues[i].message+'</span></div><span class="msg_time">'+utils.getTimeForTimestamp(profile.dialogues[i].timestamp)+'</span><div class="friend_status_indicator"></div></div>');
            }
            
        $('#'+this.current_dialoguid).addClass("current_dialog");
         
         var message_id = utils.randomInteger(0, 10000);
         $('#mail_content_panel').append('<div status=0 id="'+message_id+'" class="message_wrapper"><div class="my_message"><div class="dialog_left_message_wrapper_arrow"></div><div class="dialog_left_message_wrapper">'+$('#message_area').val()+'</div></div><span class="dialog_left_message_timespan">'+time.getHours()+":"+time.getMinutes()+'</span></div>');
         var element = document.getElementById("mail_content_panel");
         element.scrollTop = element.scrollHeight;
         
         $.ajax({
            type: "POST",
            url:post_url,
            data: {
               a: "mail_write",
               b: this.current_dialoguid,
               c: this.current_locutor,
               d: $('#message_area').val(),
               e: message_id
            },
            success: function (result){
               if (result != -1)
                  $("#"+result).attr("status", 1);
               else 
                  $("#"+result).children(".dialog_left_message_timespan").css("color","#ff5052").html("Ошибка!");
            },
            statusCode: {
               0: function(){
                  $('div[status=0]').children(".dialog_left_message_timespan").css("color","#ff5052").html("Ошибка!");
               }
            }
         });
         
         $('#message_area').val("");
      }
      else $('#message_area').focus();
   };
   
   this.writeStatus = function(code){
      console.log(code);
   };
   
   //####################################################### Мои баллы #########################################//
   
   this.credits = function(){
      this.switchSideButton($('#credits_button'));
      $('#content-area').load("./dom/profile_credits.html", function(){profile.getCredits(1)});
   };
   
   this.getCredits = function(page){
      this.page = page;
      
      $('#profile_transaction_table').html('<tr><td class="header_td">Сумма операции</td><td class="header_td">Тип</td><td class="header_td">Дата</td><td class="header_td">Описание</td></tr>');
      
      $.post(post_url,{
         a: "profile_get_credits",
         b: page-1
      },function (result){
         var obj = eval("("+result+")");
         var getCreditType = function(type){
            if (type == 1)
               return "Пополнение";
            else return "Списание";
         };
         var c = 0;
         
         if (obj[obj.length-1].items_count > 0){
            for (var i = 0; i < obj.length; i++)
               if (i != obj.length-1){
                  if (i % 2 != 0)
                     $('#profile_transaction_table').append('<tr><td>'+obj[i].total+' баллов</td><td>'+getCreditType(obj[i].type)+'</td><td>'+utils.getDateTimeForTimestamp(obj[i].timestamp)+'</td><td>'+obj[i].description+'</td></tr>');
                  else 
                     $('#profile_transaction_table').append('<tr><td class="highlighted_td">'+obj[i].total+' баллов</td><td class="highlighted_td">'+getCreditType(obj[i].type)+'</td><td class="highlighted_td">'+utils.getDateTimeForTimestamp(obj[i].timestamp)+'</td><td class="highlighted_td">'+obj[i].description+'</td></tr>');
               }
               else{
                  profile.pages = Math.ceil(obj[i].items_count/10);
                  profile.setPagination("profile.getCredits");
               }
         }
         else{
            $('#content-area').html('<div style="width:100%;margin-top:200px;text-align:center;color:#aaa">Операций с баллами пока нет.<br><a style="width: 200px !important;" onclick="profile.promotions()" class="btn transparent orange fw">Заработать баллы</a></div>');
         }
      });
   };
   
   this.home_city_promotions_set = 0; // переменная не дает обновлять город фильтрации промоушенов при
   
   this.promotions = function(){
      this.home_city_promotions_set = 0;
      $('#content-area').load("./dom/profile_promotions.html", function(){profile.getPromotions(1)});
   };
   
   this.getPromotions = function(page){
      this.page = page;
      
      if (region.current_city != -1 && this.home_city_promotions_set == 0){ 
         region.setSearchCity(region.current_city, region.current_city_name, 'promotions_city_input');
         this.home_city_promotions_set = 1;
      }
      $('#profile_promotions_table').html('<tr><td class="header_td"></td><td class="header_td">Название</td><td class="header_td">Город</td><td class="header_td">Вознаграждение</td><td class="header_td"></td></tr>');
      
      $.post(post_url,{
         a: "profile_get_promotions",
         b: page-1,
         c: category.category,
         d: region.current_city
      },function (result){
         var obj = eval("("+result+")");
         console.log(obj)
         var c = 0;
         
         for (var i = 0; i < obj.length; i++){
            var photo_obj = {1:"camera.png"};
            
            if (obj[i].photo !== "")
               photo_obj =  eval("("+obj[i].photo+")");
               
            if (i != obj.length-1){
               if (i % 2 != 0)
                  $('#profile_promotions_table').append('<tr><td><div class="promotionsphoto-wrapper"><img class="promotionphoto" src="http://1shekel.com/'+(photo_obj[0] != undefined ? "catalog/"+photo_obj[0] : "img/camera.png")+'"></div></td><td><a target="_blank" href="item.php?num='+obj[i].num+'">'+obj[i].title+'</a></td><td>'+obj[i].city+'</td><td>1 балл</td><td><a class="repost_btn" onclick=\'profile.showShareForm('+obj[i].num+', "http://1shekel.com/catalog/'+photo_obj[0]+'", "'+obj[i].title+'")\' href="javascript:void(0)">Перепостить</a></td></tr>');
               else 
                  $('#profile_promotions_table').append('<tr><td class="highlighted_td"><div class="promotionsphoto-wrapper"><img class="promotionphoto" src="http://1shekel.com/'+(photo_obj[0] != undefined ? "catalog/"+photo_obj[0] : "img/camera.png")+'"></div></td><td class="highlighted_td"><a target="_blank" href="item.php?num='+obj[i].num+'">'+obj[i].title+'</a></td><td class="highlighted_td">'+obj[i].city+'</td><td class="highlighted_td">1 балл</td><td class="highlighted_td"><a class="repost_btn" onclick=\'profile.showShareForm('+obj[i].num+', "http://1shekel.com/catalog/'+photo_obj[0]+'", "'+obj[i].title+'")\' href="javascript:void(0)">Перепостить</a></td></tr>');
            }
            else{
               profile.pages = Math.ceil(obj[i].items_count/10);
               profile.setPagination("profile.getPromotions");
            }
         }
      });
   };
   
   this.showShareForm = function(item, image, title){
      form = new Form("../dom/item_share_form.html", 355, 233, this.onShareFormLoad);
      this.promoting_item = item;
      this.promoting_item_image = image;
      this.promoting_item_title = title;
   };
   
   this.onShareFormLoad = function(){
      feedback.getPromotedByUser(profile.promoting_item);
      feedback.getPromoteLimits();
      
      $('#sf_vkontakte').attr({"href": "http://vk.com/share.php?url="+encodeURIComponent("http://1shekel.com/item.php?num="+profile.promoting_item)+"&image="+profile.promoting_item_image, "onclick":"window.open(this.href,this.target,'width=500,height=300,scrollbars=1');feedback.repostBy("+profile.promoting_item+", 1);return false;"});
      $('#sf_odnoklassniki').attr({"href": "http://connect.ok.ru/dk?st.cmd=OAuth2Login&st.layout=w&st.redirect=%252Fdk%253Fcmd%253DWidgetSharePreview%2526amp%253Bst.cmd%253DWidgetSharePreview%2526amp%253Bst.shareUrl%253D"+encodeURIComponent("http://1shekel.com/item.php?num="+profile.promoting_item)+"&st.client_id=-1", "onclick":"window.open(this.href,this.target,'width=600,height=400,scrollbars=1');feedback.repostBy("+profile.promoting_item+", 2);return false;"});
      //$('#sf_facebook').attr({"href": "https://www.facebook.com/sharer/sharer.php?u="+encodeURIComponent("http://1shekel.com/item.php?num="+profile.promoting_item), "onclick":"window.open(this.href,this.target,'width=500,height=300,scrollbars=1');feedback.repostBy("+profile.promoting_item+", 3);return false;"});
      $('#sf_facebook').attr({
          onclick: "form.close(); FB.ui({method: 'feed', link: 'http://1shekel.com/item.php?num="+profile.promoting_item+"',}, function(response){if (response && response.post_id) feedback.repostBy("+profile.promoting_item+", 3);});"
      });
      $('#sf_twitter').attr({"href": "https://twitter.com/intent/tweet?url="+encodeURIComponent("http://1shekel.com/item.php?num="+profile.promoting_item)+"&text="+encodeURIComponent(profile.promoting_item_title), "onclick":"window.open(this.href,this.target,'width=500,height=300,scrollbars=1');feedback.repostBy("+profile.promoting_item+", 4);return false;"});
      $('#sf_moimir').attr({"href": "http://connect.mail.ru/share?url="+encodeURIComponent("http://1shekel.com/item.php?num="+profile.promoting_item)+"&imageurl="+encodeURIComponent(profile.promoting_item_image)+"&title="+encodeURIComponent(profile.promoting_item_title), "onclick":"window.open(this.href,this.target,'width=500,height=300,scrollbars=1');feedback.repostBy("+profile.promoting_item+", 6);return false;"});
      
   };
   
   this.promoteItem = function(item){
      form = new Form("../dom/item_promote_form.html", 500, 519, this.onPromoteFormLoad);
      this.promoting_item = item;
   };
   
   this.onPromoteFormLoad = function(){
      $('#promote_item_button').click(function(){
         $.post(post_url,{
            a: "feedback_promote_item",
            b: profile.promoting_item
         },function (result){
            if (result == -1)
               $('#profile_button_wrapper').html("<span style='line-height: 125px;color:rgb(217,100,100);'>Объявление не поднято! У Вас не хватает баллов на балансе.</span>");
            else $('#profile_button_wrapper').html("<span style='line-height: 125px; color:rgb(0,148,15);'>Объявление успешно поднято.</span>");
         });
      });
      
      $('#promote_item_vip_button').click(function(e){
         $.post(post_url,{
            a: "feedback_vip_item",
            b: profile.promoting_item
         },function (result){
            if (result == 0)
               $('#profile_vip_wrapper').html("Объявлению успешно присовен VIP-статус.").css({"color":"rgb(0,148,15)", "line-height":"75px"});
            else
               $('#profile_vip_wrapper').html("Ошибка! Не хватает баллов для присвоения VIP-статуса.").css({"color":"rgb(217,100,100)", "line-height":"75px"});
         });
      });
      
      $('#hightlight_item_button').click(function(){
         $.post(post_url,{
            a: "feedback_highlight_item",
            b: profile.promoting_item
         },function (result){
            if (result == -1)
               $('#profile_button_wrapper').html("<span style='line-height: 125px;color:rgb(217,100,100);'>Объявление не выделено! У Вас не хватает баллов на балансе.</span>");
            else $('#profile_button_wrapper').html("<span style='line-height: 125px; color:rgb(0,148,15);'>Объявление успешно поднято в топ поиска и выделено цветом.</span>");
         });
      });
   };
   
   this.fillPromotionsCategoryList = function(){
      $('#promotions_category_list').html("");
      $('#promotions_category_list').show();
      category.fillCategoryList('promotions_category_list', 'promotions_category_input');
   };
   
   this.fillPromotionsCityList = function(){
      $('#promotions_city_list').html("");
      $('#promotions_city_list').show();
      region.fillCityList('promotions_city_list', 'promotions_city_input');
   };
   
   this.logout = function(){
      if (this.vk_authorized == 1){
         //VK.Auth.logout(function(){location.href = "login.php?logout";});
        //FB.logout() 
       }
      else location.href = "login.php?logout";
   };
   
   this.prolongItem = function(item_num){
       $.post(post_url,{
            a: "profile_prolong_item",
            b: item_num
         },function (response){
            if (response.error != undefined)
                console.log(response.error.description);
            else{
                profile.items();
            }
         });
   };
   
   this.createPayment = function(){
        if ($('#profile_coins_to_fill_input').val().trim().length === 0){
            $('#profile_coins_to_fill_input').focus();
            return 0;
        }

        $.post(post_url,{
            a: "profile_get_coin_currency"
          },function (response){
                var currency = response;
                var coins = Number($('#profile_coins_to_fill_input').val().trim());
                var money = Number(coins*currency).toFixed(2);

                $.post(post_url,{
                    a: "currency_convert",
                    b: money,
                    c: 3,
                    d: 2
                  },function (response){
                    if (response.error != undefined)
                        console.log(response.error.description);
                    else{
                        $('#ik_am').val(response);
                        $('#payment_form_submit').attr("disabled", false);
                    }
                });

                $('#ik_am').val(money);
                $('#coins_to_fill_span').text(coins);
                $('#money_to_get_span').text(money);

                $.post(post_url,{
                    a: "profile_create_payment",
                    b: coins
                  },function (response){
                    if (response.error != undefined)
                        console.log(response.error.description);
                    else{
                        $('.profile_edit_block_wrapper, .profile_button_wrapper').hide();
                        $('#check_wrapper_div').show();
                        $('#payment_form').show();
                        $('#ik_pm_no').val(response);
                    }
                });
        });
   };
   
   this.fillBalance = function(){
      $('#content-area').load("./dom/fill_balance.html", function(){});
   };
   
   this.getURLParameter = function(parameter){
      var params_string = window.location.href.slice(window.location.href.indexOf('?') + 1);
      var params = params_string.split("&");
      var result = {};
      
      for (var i = 0; i < params.length; i++){
         var tmp = params[i].split("=");
         result[tmp[0]] = tmp[1];
      }
      
      return result[parameter];
   };
}