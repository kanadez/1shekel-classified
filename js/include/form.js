function Form(dom_path, width, height, onloadfunc){
   this.show = function(){
      $(document.body).css({height: "100%",overflow: "hidden"});
      var h = $(window).height();
      var w = $(window).width();
      var bg = $("<div/>", {id:"bg"});
      bg.width(w);
      bg.height(h);
      bg.offset({top:0});
      bg.css({"position":"fixed","z-index": 99999, background:"rgba(0,0,0,0.9)"});
      $(document.body).append(bg);
      
      var form = $("<div/>", {id:"form", class:"form"});
      bg.append(form);
      $('#form').width(width).height(height);
      var top = $(window).height()/2-(height/2);
      $('#form').css("marginTop",top+"px");
      $('#form').load(dom_path, onloadfunc);
   };
   
   this.close = function(){
      $('#bg').remove();
      $(document.body).css({height: "auto",overflow: "scroll"});
   };
   
   this.show();
}