function User(){
   this.num = null;
   this.name = null;
   this.photo_100 = null;
   this.phone = null;
   this.last_seen = null;
   this.email = null;
   this.address = null;
   
   this.get = function(user_num){
      $.post(post_url,{
         a: "user_get",
         b: user_num
      },function (result){
         var user_data = eval("(" + result + ")");
         user.fillUserProfileData(user_data);
      });
   };
   
   this.getForComments = function(users_obj){
      var json_string = JSON.stringify(users_obj);
      $.post(post_url,{
         a: "user_getAlot",
         b: json_string
      },function (result){
         var user_data = eval("(" + result + ")");
         comments.getFromUser(user_data);
      });
   };
   
   this.getLastSeen = function(timestamp){
      if (utils.getTimestamFromDate()-600 < timestamp)
         return 0;
      else return utils.getDateTimeForTimestamp(timestamp);
   };
   
   this.fillUserProfileData = function(object){
      var lastseen = this.getLastSeen(object.last_seen) == 0 ? "Онлайн" : "Последний визит: "+this.getLastSeen(object.last_seen);
      
      this.num = object.num;
      this.name = object.name;
      this.photo_100 = object.photo_100;
      this.phone = object.phone;
      this.last_seen = lastseen;
      this.email = object.email;
      this.address = object.address;
      
      utils.isset(object.photo_100) ? $('#userphoto').attr("src", object.photo_100) : $('#userphoto').attr("src",location.origin+"/user/default_user.png");
      $('#user_name').html(object.name);
      utils.isset(lastseen) ? $('#user_lastseen').html(lastseen) : $('#user_lastseen').hide();
      utils.isset(object.phone) ? $('#user_phone').html("Телефон: "+object.phone) : $('#user_phone').hide();
      utils.isset(object.email) ? $('#user_email').html("Электронная почта: <a class='white_link' href='mailto:"+object.email+"'>"+object.email+"</a>") : $('#user_email').hide();
   };
   
   this.showContactForm = function(){
      form = new Form("../dom/user_contact_form.html", 500, 335, this.onContactFormLoad);
   };
   
   this.onContactFormLoad = function(){
      $('#contact_form_seller_name').html(user.name);
      $('#contact_form_seller_name').attr("href",location.protocol+"//"+location.hostname+"/user.php?num="+user.num);
      $('#contact_form_userpic_img').attr("src",user.photo_100);
      
      if (myself != -1 && myself.email != "") $('#contact_form_email_input').val(myself.email).attr("disabled", "disabled");
   };
   
   this.contactFormShowPhone = function(object){
      var element = $(object);
      element.text(this.phone);
   };
   
   this.contactFormSendMessage = function(){
      $.post(post_url,{
         a: "mail_send",
         b: user.num,
         c: $('#contact_form_message_area').val(),
         d: $('#contact_form_email_input').val()
      },function (result){
         $('#form').remove();
         var h = $(window).height();
         var w = $(window).width();
         var bg = $("#bg");
         var notification = null;
         
         if (result == 1)
            notification = $("<div/>", {id:"mail_send_notification", text: "Сообщение доставлено пользователю."});
         else notification = $("<div/>", {id:"mail_send_notification", text: "Ошибка. Сообщение не было доставлено."});
         
         bg.append(notification);
         //notification.offset({top:screen.height/2, left:screen.width/2});
         $('#mail_send_notification').css({padding: "20px", "width":"300px", height:"50px", "border-radius": "5px", opacity: "0.9", "font-size" :"2.3em", "line-height": "11px", "text-align":"left", "marginLeft":"auto", "marginRight":"auto","z-index": 9999, background:"#fff"});
         var top = $(window).height()/2-50;
         $('#mail_send_notification').css("marginTop",top+"px");
         window.setTimeout('$("#bg").animate({opacity:0}, 500, function(){$("#bg").remove();$(document.body).css({height: "auto",overflow: "scroll"})})', 1000);
      });
   }
}

function getUser(){
   
}

function getMySelf(){ // создает текущего зщалогиненого юзера, получая данные с серва
   $.post(post_url,{
      a: "user_get_myself"
   },function (result){
      if (result != -1){
         var data = JSON.parse(result);
         myself = new User();
         myself.num = data.num;
         myself.name = data.name;
         myself.photo_100 = data.photo_100;
         myself.phone = data.phone;
         myself.email = data.email;
         myself.address = data.address;
         $('.article-comments').append('<div class="comment_block last"><textarea id="comment_input_area" onchange="comments.checkMinusWords(this)" class="input_area" placeholder="Комментировать.."></textarea><a id="write_comment_button" class="btn transparent blue send_button">Отправить</a><div id="comment_write_error_div" style="display:none;" class="error"></div></div>');
         $('#write_comment_button').click(function(){comments.write($('#comment_input_area').val());});
      }
      else{ 
         myself = -1;
         $('.article-comments').append('<div class="comment_block last"><a href="login.php">Войдите</a>, чтобы оставить комментарий.</div>');
      }
   });
}