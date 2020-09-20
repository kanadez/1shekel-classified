function VipSlider(){
   this.step = $('.gallery_element_box_img_wrapper').outerWidth()+4;;
   this.w = $('.gallery_element_box').outerWidth()+4;
   this.h = $('.gallery_element_box').height();
   this.finish_margin = $('.gallery_element_box').length*this.w-$('#vip_content_wrapper_div').width();
   this.start_margin = 0;
   this.move_block = 0;
   
   this.init = function(){
      $('#vip_pageblock_div').outerHeight(this.h*1.67);
      $('#vip_slider_left_arrow_img').height(this.h/3.64).css("marginTop",this.h/1.8);
      $('#vip_slider_right_arrow_img').height(this.h/3.64).css("marginTop",this.h/1.8);
      this.truncateTitles();
   };

   //$('#first_gallery_box_div').animate({"marginLeft":-slider_finish_margin},300);
   
   this.truncateTitles = function(){
      for (var i = 0; i < 15; i++){
         var str = $('#vip_title_'+i).text();
         
         if (str.length > 20)
         $('#vip_title_'+i).html(str.substring(0,20)+"..");
      }
   };
   
   this.showDescription = function(description_div_element){
      var elm = $(description_div_element);
      elm.children('.vip_desc_div').animate({"marginTop":"-100px"},200);
   };
   
   this.hideDescription = function(description_div_element){
      var elm = $(description_div_element);
      elm.children('.vip_desc_div').animate({"marginTop":"0"},200);
   };
   
   this.right = function(){
      if (this.move_block == 0){
         this.move_block = 1;
         var margin = $('#first_gallery_box_div').margin().left;
         
         if (-margin <= this.finish_margin)
            $('#first_gallery_box_div').animate({"marginLeft":"-="+this.step},50, function(){vipslider.move_block = 0;});
         else vipslider.move_block = 0;
      }
   };
   
   this.left = function(){
      if (this.move_block == 0){
         var margin = $('#first_gallery_box_div').margin().left;
         
         if (margin <= this.start_margin)
            $('#first_gallery_box_div').animate({"marginLeft":"+="+this.step},50, function(){vipslider.move_block = 0;});
         else vipslider.move_block = 0;
      }
   };
   
   this.init();
}