var post_url = "./php/post.php";
var register = null;
var utils = null;
var region = null;
var dropdown = null;

$(document).ready(function(){
   register = new Register();
   utils = new Utils();
   region = new Region();
   dropdown = new Dropdown();
   region.current_region = utils.getUrlParameter("region") != undefined && utils.getUrlParameter("region") != "" ? utils.getUrlParameter("region") : -1;

   region.getRegionData();
   region.getCityData();
   
   register.setupDom();
});

function Register(){
   this.errors = 0;
   this.scrolled = 0;
   
   this.setupDom = function(){
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
   };
   
   this.comparePasswd = function(){
      if ($('#pass_input').val() != $('#passagain_input').val()){
         $('#password_alert').show();
         register.errors++;
      }
      
      return this.errors;
   };
   
   this.checkEmpty = function(){
      $('.neccesary_input').each(function(){
         if ($(this).val().length == 0){
            register.errors++;
            register.hlInput($(this));
          
            if(!register.scrolled){
               $('html,body').stop().animate({ scrollTop: $(this).offset().top-100 }, 200);
               register.scrolled++;
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
         register.errors++;
         register.hlText($('label.radGroup1'));
      }
      
      return this.errors;
   };
   
   this.hlText = function(element){
      element.css({color:"red"});
      element.animate({color: "#000"}, 2000);
   };
   
   this.now = function(){
      if (!this.checkEmpty() && !this.checkNotChecked() && !this.comparePasswd())
         $.post(post_url,{
            a: "user_register",
            b: JSON.stringify(this.collectData())
         },function(result){
            if (result != -1 && result != -2)
               $('#content-area').html("<div id='add_result_msg' style='width:100%;text-align:center;'>Вы успешно зарегистрированы. Теперь можете <a href='login.php'>войти на сайт</a>.</div>");
            else if (result == -2) $('#registered_alert').show(); //$('#content-area').html("<div id='add_result_msg' style='width:100%;text-align:center;'>Что-то пошло не так. Приносим извинения за неудобства.</div>.");
            else $('#content-area').html("<div id='add_result_msg' style='width:100%;text-align:center;'>Что-то пошло не так. Приносим извинения за неудобства.</div>.");
            $('#add_result_msg').css("line-height", $('#content-area').height()+"px");
         });
      
      this.scrolled = 0;
      this.errors = 0;
   };
   
   this.collectData = function(){
      var data = {};
      
      data.status = $('.radGroup1:checked').val();
      data.name = $('#name_input').val();
      data.phone = $('#phone_input').val();
      data.phone_hide = $('#hidephone_check:checked').length;
      data.email = $('#email_input').val();
      data.passwd = $('#pass_input').val();
      //data.skype = $('#skype_input').val();
      
      return data;
   };
}