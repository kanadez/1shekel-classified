function Dropdown(){
   this.opened = null;
   
   this.toggle = function(list_id){
      var list = $('#'+list_id);
      if (utils.isset(this.opened)){
         this.opened.hide();
         if (this.opened.attr("id") == list_id){
            this.opened = null;
         }
         else{
            this.opened = list;
            this.opened.show();
         }
      }
      else{
         this.opened = list;
         list.show();
      }
   };
   
   this.findsub = function(str, sub){
      if (str.toUpperCase().indexOf(sub.toUpperCase()) + 1) return 1; else return 0;
   };
}