function Item(number){
   this.data = null;
   this.photo_data = null;
   this.qview_mode = 0;
   this.num = number;
   //this.author = null;
   this.title = null;
   this.specifics = null;
   this.promoting_item = null; // для формы шаринга
   this.promoting_item_image = null; // для формы шаринга
   this.promoting_item_title = null; // для формы шаринга
   
   this.get = function(){
      $.post(post_url,{
         a: "gI",
         b: this.num,
         c: utils.getUrlParameter("filter_curr")
      },function (result){
         var item_data = eval("(" + result + ")");
         item.data = item_data;
         item.photo_data = item_data.photo != "" ? JSON.parse(item_data.photo) : ["img/camera.png"];
         
         if (!item.qview_mode){
            category.category = item_data.category_code;
            category.subcategory = item_data.subcategory_code;
            category.advsubcategory = item_data.advsubcategory_code;
            $('#show_catalog_a').attr("href", "catalog.php?page=1&cat="+item_data.category_code+(item_data.subcategory_code != null ? "&sub="+item_data.subcategory_code : ""));
         }
         
         item.title = item_data.title;
         
         if (item_data.email.length == 0){
            item.getAuthor(item_data.author);
         }
         else{
             $('#author_write_a').attr({href: "mailto:"+item_data.email, onclick: ""});
             author = new User();
             author.phone = item_data.phone;
             $('#author_name_div').hide();
         }
         
         item.getBreadcrumbs();
         slider = new Slider(item_data.photo);
         //document.title = "Доска объявлений | "+item_data.title;
         
         $('#article_date_div').html(utils.getDateTimeForTimestamp(item_data.timestamp));
         $('#item_views_count_div').html(item.num);
         $('#article_header').html("<h1>"+item_data.title+"<div class='label-danger'></div></h1>");
         var auction_badge = $('<div />',{id: "auction_badge", text: "торг"});
         var gift_badge = $('<div />',{class: "gift_badge", text: "даром"});
         var exchange_badge = $('<div />',{class: "exchange_badge", text: "обмен"});
         
         $('#article_price').html(item_data.price+" "+item_data.currency);
         
         if (item_data.condition != 0) 
            $('#item_cond_div').append(item_data.condition == 1 ? " новое" : " б/у");
         else $('#item_cond_div').hide();
         
         if (item_data.auction == 1) $('#article_price').append(auction_badge);
         if (item_data.gift == 1) $('#article_price').append(gift_badge);
         if (item_data.exchange == 1) $('#article_price').append(exchange_badge);
         
         if (item.data.city_name != null)
            $('#item_address_div').show().html("<span style='color:#777'>Адрес: </span>"+item.data.city_name+(item_data.address != "" ? ", "+item_data.address : ""));
         else $('#item_address_div').hide();
         
         if (item.qview_mode) $('#article_description').html(item_data.description);
         else $('#article_description').html(item_data.full_description);
         
         if (utils.isset(item_data.specifics)){
            item.specifics = eval("(" + item_data.specifics + ")");
            item.getSpecificsIdioma(item.specifics);
            
            //if (utils.isset(item.specifics.address))
              // $('#item_address_a').text(item.specifics.address);
            //else $('#item_address_div').hide();
            
            var c = 0;
            for (var key in item.specifics){
               if (key != "num" && key != "item"){
                  $('#td'+c+' > #key').attr("id", key);
                  $('#td'+c+' > #value').text(item.specifics[key]);
                  c++;
               }
            }
         }
         else{
            $('#specs_table').hide();
            //$('#item_address_div').hide();
         }
         
         $('#show_share_form_a').attr("onclick","item.showShareForm("+item.num+", 'http://avral.by/catalog/"+item.photo_data[0]+"', '"+item.title+"')");
         item.checkForFavorite();
      });
   };
   
   this.checkForFavorite = function(){
      var favorites = JSON.parse(localStorage.favorites);
      
      for (var i = 0; i < favorites.length; i++)
         if (favorites[i] == item.num){
            $('#add_to_favorites_a').hide();
            $('.sidebar').append("<div style='font-size:2.7em;width:100%;text-align:center;'>Объявление у Вас в Избранном.</div>");
         }
   };
   
   this.getBreadcrumbs = function(){
      if (!this.qview_mode)
         $.post(post_url,{
            a: "item_getcrumbs",
            b: category.category,
            c: category.subcategory,
            d: category.advsubcategory
         },function (result){
            var obj = eval("(" + result + ")");
            var crumbs_str = obj.category;
            
            if (obj.subcategory != null) crumbs_str += " → "+obj.subcategory;
            if (obj.advsubcategory != null) crumbs_str += " → "+obj.advsubcategory;
            $('#breadcrumbs_span').html(crumbs_str);
         });
   };
   
   this.getSpecificsIdioma = function(specifics){
      var a = [];
      for (var key in specifics)
         if (key != "num" && key != "item")
            a.push(key);
      $.post(post_url,{
         a: "idioma_get_variables",
         b: JSON.stringify(a)
      },function (result){
         item.specifics_idioma = eval("("+result+")");
         var c = 0;
         for (var key in item.specifics_idioma){
            $('#td'+c+' > #'+key).text(item.specifics_idioma[key]);
            c++;
         }
      });
   };
   
   this.getAuthor = function(author_id){
      $.post(post_url,{
         a: "user_get",
         b: author_id
      },function (result){
         var author_data = eval("(" + result + ")");
         author = new User();
         author.num = author_data.num;
         author.name = author_data.name;
         author.photo_100 = author_data.photo_100;
         author.phone = author_data.phone;
         author.online = author_data.online;
         author.address = author_data.address;
         author.city = author_data.city;
         author.status = author_data.status;
         
         $('#author_name_a').html(author.name);
         $('#author_name_a').attr("href", location.protocol+"//"+location.hostname+"/user.php?num="+author.num);
         
         if (!utils.isset(author.phone))
            $('#author_phone_div').hide();
            
         $('#author_name_div').append(author.status == 0 ? " (физ. лицо)" : " (юр. лицо)");
      });
   };
   
   this.showPhone = function(object){
      var element = $(object);
      element.text(author.phone);
   };
   
    this.showContactForm = function(){
        $.post(post_url,{
            a: "profile_getbannedstatus"
        },function (result){
            if (result == 1){
                $('#author_write_banned_div').show();
            }
            else{
                form = new Form("../dom/item_contact_form.html", 500, 335, item.onContactFormLoad);
            }
        });
    };
   
   this.onContactFormLoad = function(){
      $('#contact_form_seller_name').html(author.name);
      $('#contact_form_seller_name').attr("href",location.protocol+"//"+location.hostname+"/user.php?num="+author.num);
      $('#contact_form_userpic_img').attr("src",author.photo_100);
      
      if (myself != -1) $('#contact_form_email_input').val(myself.email).attr("disabled", "disabled");
   };
   
   this.contactFormSendMessage = function(){
       if ($('#contact_form_minus_words_error:visible').length > 0){
           return false;
       }
       
      $.post(post_url,{
         a: "mail_send",
         b: author.num,
         c: $('#contact_form_message_area').val(),
         d: $('#contact_form_email_input').val()
      },function (result){
         $('#form').remove();
         var h = $(window).height();
         var w = $(window).width();
         var bg = $("#bg");
         var notification = null;
         
         if (result == 1)
            notification = $("<div/>", {id:"mail_send_notification", text: "Сообщение доставлено автору объявления."});
         else notification = $("<div/>", {id:"mail_send_notification", text: "Ошибка. Сообщение не было доставлено."});
         
         bg.append(notification);
         //notification.offset({top:screen.height/2, left:screen.width/2});
         $('#mail_send_notification').css({padding: "20px", "width":"300px", height:"50px", "border-radius": "5px", opacity: "0.9", "font-size" :"2.3em", "line-height": "11px", "text-align":"left", "marginLeft":"auto", "marginRight":"auto","z-index": 9999, background:"#fff"});
         var top = $(window).height()/2-50;
         $('#mail_send_notification').css("marginTop",top+"px");
         window.setTimeout('$("#bg").animate({opacity:0}, 500, function(){$("#bg").remove();$(document.body).css({height: "auto",overflow: "scroll"})})', 1000);
      });
   }
   
   this.showShareForm = function(item, image, title){
      form = new Form("../dom/item_share_form.html", 355, 233, this.onShareFormLoad);
      this.promoting_item = item;
      this.promoting_item_image = image;
      this.promoting_item_title = title;
   };
   
   this.onShareFormLoad = function(){
      feedback.getPromotedByUser(item.promoting_item);
      feedback.getPromoteLimits();
      
      $('#sf_vkontakte').attr({"href": "http://vk.com/share.php?url="+encodeURIComponent("http://avral.by/item.php?num="+item.promoting_item)+"&image="+item.promoting_item_image, "onclick":"window.open(this.href,this.target,'width=500,height=300,scrollbars=1');feedback.repostBy("+item.promoting_item+", 1);return false;"});
      $('#sf_odnoklassniki').attr({"href": "http://connect.ok.ru/dk?st.cmd=OAuth2Login&st.layout=w&st.redirect=%252Fdk%253Fcmd%253DWidgetSharePreview%2526amp%253Bst.cmd%253DWidgetSharePreview%2526amp%253Bst.shareUrl%253D"+encodeURIComponent("http://avral.by/item.php?num="+item.promoting_item)+"&st.client_id=-1", "onclick":"window.open(this.href,this.target,'width=600,height=400,scrollbars=1');feedback.repostBy("+item.promoting_item+", 2);return false;"});
      $('#sf_facebook').attr({
          onclick: "form.close(); FB.ui({method: 'feed', link: 'http://avral.by/item.php?num="+item.promoting_item+"',}, function(response){if (response && response.post_id) feedback.repostBy("+item.promoting_item+", 3);});"
      });
      $('#sf_twitter').attr({"href": "https://twitter.com/intent/tweet?url="+encodeURIComponent("http://avral.by/item.php?num="+item.promoting_item)+"&text="+encodeURIComponent(item.promoting_item_title), "onclick":"window.open(this.href,this.target,'width=500,height=300,scrollbars=1');feedback.repostBy("+item.promoting_item+", 4);return false;"});
      $('#sf_moimir').attr({"href": "http://connect.mail.ru/share?url="+encodeURIComponent("http://avral.by/item.php?num="+item.promoting_item)+"&imageurl="+encodeURIComponent(item.promoting_item_image)+"&title="+encodeURIComponent(item.promoting_item_title), "onclick":"window.open(this.href,this.target,'width=500,height=300,scrollbars=1');feedback.repostBy("+item.promoting_item+", 6);return false;"});
      
   };
   
   this.showReportForm = function(){
      form = new Form("../dom/item_report_form.html", 355, 330);
   };
   
   this.sendReport = function(){
      $.post(post_url,{
         a: "item_report",
         b: this.num,
         c: $('.radGroup1:checked').val()
      },function (result){
         $('#form').remove();
         var h = $(window).height();
         var w = $(window).width();
         var bg = $("#bg");
         var notification = null;
         
         if (result == 1)
            notification = $("<div/>", {id:"report_send_notification", text: "Жалоба отправлена. Обещаем принять меры."});
         else notification = $("<div/>", {id:"report_send_notification", text: "Ошибка. Жалоба не была отправлена."});
         
         bg.append(notification);
         //notification.offset({top:screen.height/2, left:screen.width/2});
         $('#report_send_notification').css({padding: "20px", "width":"300px", height:"50px", "border-radius": "5px", opacity: "0.9", "font-size" :"2.3em", "line-height": "11px", "text-align":"left", "marginLeft":"auto", "marginRight":"auto","z-index": 9999, background:"#fff"});
         var top = $(window).height()/2-50;
         $('#report_send_notification').css("marginTop",top+"px");
         window.setTimeout('$("#bg").animate({opacity:0}, 500, function(){$("#bg").remove();$(document.body).css({height: "auto",overflow: "scroll"})})', 1000);
      });
   };
   
   this.setBreadcrumbs = function(){
      
   };
   
    this.checkMinusWords = function(input){
        $.post(post_url,{
            a: "item_check_minuswords",
            b: $(input).val().trim()
        },function(result){
            if (result == 1){
                $("#contact_form_minus_words_error").show();
            }
            else{
                $("#contact_form_minus_words_error").hide();
            }
        });
    };
   
   this.get();
}

function Slider(image_links_object){
    if (image_links_object !== "")
        this.image_links = eval("(" + image_links_object + ")");
    else{
        $('#main_image_wrapper_div').html("<img src='img/camera.png' />");
        return 0;
    }
    this.images = [];
    this.key = 0;

    this.loadImages = function(){
        for (var i = 0; i < utils.getJSONlength(this.image_links); i++){
            var image = new Image();
            image.id = "item_photo_img";
            image.src = location.protocol+"//"+location.host+"/catalog/"+this.image_links[i];
            image.onload = this.showForm;
            this.images[i] = image;
        }
        
        $('#main_image_wrapper_div').html(this.images[0]);
        
        for (i = 0; i < this.images.length; i++){
            $('#photos_queue_div').append("<div id='cell"+i+"'></div>");
            $('#photos_queue_div > #cell'+i).html('<img onclick="slider.toggleImage('+i+')" src="'+this.images[i].src+'" />');
        }
    };
    
    this.toggleImage = function(key){
        this.key = key;
        $('#main_image_wrapper_div').html(this.images[this.key]);
        $('.photos-wrapper > .arrow')
                .show()
                .height($('.photos-wrapper').height())
                .css("margin-top", "-"+$('.photos-wrapper').height()+"px");
    };

    this.showForm = function(){
        if (item.qview_mode == 1)
            $('#form').children().show();
    };

    this.right = function(){
        if (this.key < this.images.length-1)
            this.key++;
        else this.key = 0;
        
        $('#main_image_wrapper_div').html(this.images[this.key]);
        $('.photos-wrapper > .arrow')
                .show()
                .height($('.photos-wrapper').height())
                .css("margin-top", "-"+$('.photos-wrapper').height()+"px");
        //$(this.images[this.key]).height(420);
    };

    this.left = function(){
        if (this.key > 0)
            this.key--;
        else this.key = this.images.length-1;
        $('#main_image_wrapper_div').html(this.images[this.key]);
    };

    this.loadImages();
}

function Comments(){
   this.get = function(){
      $.post(post_url,{
         a: "comments_get",
         b: item.num
      },function(result){
         var comments_data = JSON.parse(result);
         
         if (utils.isset(comments_data)){
            $('#comments_caption_div').show();
            $('.article-comments').html("");
            
            for (var i = 0; i < comments_data.length; i++)
               $('.article-comments').append('<div class="comment_block '+(i == comments_data.length-1 ? "last" : "")+'"><table class="comment_block_table"><tr><td class="ava" valign="top"><div class="image_circled"><a href="user.php?num='+comments_data[i].user_num+'"><img class="userpic" src="'+(comments_data[i].user_photo != null && comments_data[i].user_photo != "" ? comments_data[i].user_photo : "http://avral.by/user/default_user.png")+'" /></a></div></td><td class="text" valign="top"><span class="username">'+(comments_data[i].user_name != null ? '<a href="user.php?num='+comments_data[i].user_num+'">'+comments_data[i].user_name+'</a>' : "Гость")+'</span><div class="usertext">'+comments_data[i].comment_text+'</div></td></tr></table></div>');
         }
         else{
            $('#comments_caption_div').hide();
            $('.article-comments').html("<div style='margin:20px 0; padding-top:20px'>Комментариев к объявлению пока нет. Вы будете первым.</div>");
         }
      });
   };
   
    this.write = function(comment_body){
        if(comment_body && $('#comment_write_error_div:visible').length === 0){
            $.post(post_url,{
                a: "comments_write",
                b: comment_body,
                c: item.num
            },function(result){
                var obj = JSON.parse(result);
                
                if (obj.error != undefined){
                    if (obj.error.code == 501){
                        $('#comment_write_error_div').text("Вы забанены и не можете написать комментарий.").show();
                    }
                }
                else{
                    comments.get();
                }
            });
       }
    };
    
    this.checkMinusWords = function(input){
        $.post(post_url,{
            a: "item_check_minuswords",
            b: $(input).val().trim()
        },function(result){
            if (result == 1){
                $("#comment_write_error_div").show().text("Комментарий не должен содержать нецензурные выражения, ссылки, и быть на русском языке.");
            }
            else{
                $("#comment_write_error_div").hide().text("");
            }
        });
    };
}

function Crosslinks(){
   this.get = function(){
      $.post(post_url,{
         a: "crosslinks_get",
         b: item.num
      },function(result){
         var crosslinks_data = eval("(" + result + ")");
         
         if (crosslinks_data != 0){
            $('.article-crosslinks').html("");
            for (var i = 0; i < crosslinks_data.length; i++){
               var photos = eval("(" + crosslinks_data[i].photo + ")");
               $('.article-crosslinks').append('<a href="item.php?num='+crosslinks_data[i].num+'"><div class="box"><div class="img_wrapper"><img class="img" src="catalog/'+photos[0]+'"></div><div class="desc_div">'+crosslinks_data[i].title+'<p></p><p><span class="price">'+crosslinks_data[i].price+' '+crosslinks_data[i].currency_short_title+'</span></p></div></div></a>');
            }
         }else $('#crosslinks_caption_div').hide();
      });
   };
}