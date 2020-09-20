function Preview(number){
   this.data = null;
   this.num = number;
   this.title = null;
   this.specifics = null;
   this.token = utils.getUrlParameter("token");
   
   this.get = function(){
      $.post(post_url,{
         a: "gI",
         b: this.num,
         c: null//utils.getUrlParameter("filter_curr")
      },function (result){
         var item_data = eval("(" + result + ")");
         preview.data = item_data;
         preview.title = item_data.title;
         
         preview.getAuthor(item_data.author);
         slider = new Slider(item_data.photo);
         $('#article_date_div').html(utils.getDateTimeForTimestamp(item_data.timestamp));
         $('#article_header').html("<h1>"+item_data.title+"<div class='label-danger'></div></h1>");
         $('#article_price').html(item_data.price+" "+item_data.currency);
         var auction_badge = $('<div />',{id: "auction_badge", text: "торг"});
         
         if (item_data.auction == 1) $('#article_price').append(auction_badge);
         
         if (item_data.condition != 0) 
            $('#item_cond_div').append(item_data.condition == 1 ? " нового" : " б/у");
         else $('#item_cond_div').hide();
         
         if (preview.data.city_name != null)
            $('#item_address_div').show().html("<span style='color:#777'>Адрес: </span>"+preview.data.city_name+(item_data.address != "" ? ", "+item_data.address : ""));
         else $('#item_address_div').hide();
         
         $('#article_description').html(item_data.full_description);
         
         if (item_data.specifics != "null"){
            preview.specifics = eval("(" + item_data.specifics + ")");
            preview.getSpecificsIdioma(preview.specifics);

            var c = 0;
            for (var key in preview.specifics){
               if (key != "num" && key != "item"){
                  $('#td'+c+' > #key').attr("id", key);
                  $('#td'+c+' > #value').text(preview.specifics[key]);
                  c++;
               }
            }
         }
         else{
            $('#specs_table').hide();
            
         }
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
         preview.specifics_idioma = eval("("+result+")");
         var c = 0;
         for (var key in preview.specifics_idioma){
            $('#td'+c+' > #'+key).text(preview.specifics_idioma[key]);
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
         user = new User();
         user.num = author_data.num;
         user.name = author_data.name;
         user.photo_100 = author_data.photo_100;
         user.phone = author_data.phone;
         user.online = author_data.online;
         user.address = author_data.address;
         user.status = author_data.status;
         
         $('#author_name_a').html(user.name);
         $('#author_name_a').attr("href", location.protocol+"//"+location.hostname+"/user.php?num="+user.num);
         
         if (!utils.isset(user.phone))
            $('#author_phone_div').hide();
            
         $('#author_name_div').append(user.status == 0 ? " (физ. лицо)" : " (юр. лицо)");
      });
   };
   
   this.showPhone = function(object){
      var element = $(object);
      element.text(user.phone);
   };
   
   this.add = function(item){
       $.post(post_url,{
         a: "item_add_from_preview",
         b: item,
         c: utils.getUrlParameter("token") != undefined ? utils.getUrlParameter("token") : -1
      },function (result){
         if (result == 0)
            $('.container').html("<div id='add_result_msg' style='width:100%;text-align:center;font-size:2.3em'>Объявление успешно добавлено. Посмотреть его можно <a href='item.php?num="+item_num+"' target='_blank'>по ссылке</a>.</div>");
         else $('.container').html("<div id='add_result_msg' style='width:100%;text-align:center;font-size:2.3em'>Что-то пошло не так. Приносим извинения за неудобства.</div>.");
         
         $('#add_result_msg').css("line-height",($(document).height()-250)+"px");
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
   };
   
   this.right = function(){
      if (this.key < this.images.length-1)
         this.key++;
      else this.key = 0;
      console.log(this.key);
      $('#main_image_wrapper_div').html(this.images[this.key]);
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