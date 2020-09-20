function authInfo(response) {
  if (response.session) {
    $('#vk_login_button').css("opacity", "1");
    profile.vk_authorized = 1;
    
    VK.Api.call('users.get', {user_ids: response.session.mid, fields:"photo_100"}, function(r) { 
      if(r.response) {
         var ava = new Image();
         ava.id = "profilephoto";
         ava.src = r.response[0].photo_100;
         ava.onload = function(){$('#profilephoto').width(70).height(70)};
         $('#profilephoto-wrapper').html(ava);
         $('.emailblock').hide();
         $('.passblock').hide();
      }
   }); 
  } else {
    $('#vk_login_button').css("opacity", "0.2");
    profile.vk_authorized = 0;
  }
}

function newAuthInfo(response){
   if (response.session){
      $('#email_input').removeClass('neccesary_input').hide();
      $('#email_input_label').hide();
   }
}

function coauthInfo(response) {
  if (response.session){
    $('#vk_login_button').css("opacity", "1");
    profile.coauth = 1;
  }
  else{
    $('#vk_login_button').css("opacity", "0.2");
    profile.coauth = 0;
  }
}

function loginAuthInfo(response) {
   VK.Api.call('users.get', {user_ids: response.session.user.id, fields:"photo_100, contacts, city", v:"5.8"}, function(r) { 
      if(r.response) {
         var home_phone = r.response[0].home_phone;
         var photo = r.response[0].photo_100;
         var mobile_phone = r.response[0].mobile_phone;
         var city = r.response[0].city.title;
         
         //console.log(r.response[0]);
         $.post(post_url,{
            a: "user_login_vk",
            b: r.response[0].id,
            c: r.response[0].first_name+" "+r.response[0].last_name,
            d: home_phone !== undefined ? home_phone : mobile_phone,
            e: photo,
            f: city
         },function (result){
            if (result == 1)
              location.href = "profile.php";
         });
      }
   });
}

function okLogin(){
   var sign = MD5('application_key=CBAHJGLKEBABABABAmethod=users.getCurrentUser'+MD5(utils.getHashParameter("access_token")+"409DBD50E5000BF26628E9F1"));
   $.get( "http://api.odnoklassniki.ru/fb.do",{ 
      method: "users.getCurrentUser", 
      access_token: utils.getHashParameter("access_token"),
      application_key: "CBAHJGLKEBABABABA",
      sig: sign
   }, function(result){
      var ok_id = result.uid;
      var ok_name = result.first_name+" "+result.last_name;
      var ok_photo = result.pic_3;
      var ok_city = result.location.city;
      
      $.post(post_url,{
         a: "user_update_city_sn",
         b: ok_city
      },function (result){
         $.post(post_url,{
            a: "user_login_ok",
            b: ok_id,
            c: ok_name,
            d: "",
            e: ok_photo,
            f: ok_city
         },function (result){
            if (result == 1)
               location.href = "profile.php";
         });
      });
      
      
   });

}

function fbAuth(response) {
    $.post(post_url,{
       a: "user_login_vk",
       b: response.id,
       c: response.name,
       d: "",
       e: "http://graph.facebook.com/"+response.id+"/picture"
    },function (result){
       if (result == 1)
         location.href = "profile.php"; 
    });
}

function checkEmailExist(input){
    if ($(input).val().trim().length == 0){
        return 0;
    }
    
    $.post(post_url,{
       a: "user_check_email_exist",
       b: $(input).val().trim()
    },function (result){
        if (result == 1){
            $('#email_exits_auth_button, #email_exist_error_span').show();
            $('#submit_buttons_wrapper').hide();
            $('#email_input').css("color", "red");
        }
        else{
            $('#email_exits_auth_button, #email_exist_error_span').hide();
            $('#submit_buttons_wrapper').show();
            $('#email_input').css("color", "black");
        }
    });
    
    
}