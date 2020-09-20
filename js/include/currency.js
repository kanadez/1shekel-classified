function Currency(){
   this.data = {};
   
   this.getData = function(){
      $.post(post_url,{
         a: "currency_getlist"
      },function(result){
         currency.data = JSON.parse(result);
      });
   };
   
   this.fillList = function(select){
      for (var i = 0; i < this.data; i++){
         var option = $("<option />", {code: this.data[i].code, text: this.data[i].short_title+" ("+this.data[i].symbol+")"});
         select.append(option);
      }
   };
}