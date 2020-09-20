function Utils(){
   this.getJSONlength = function(object){
      var key, count = 0;
      for(key in object)
         if(object.hasOwnProperty(key))
            count++;
            
      return count;
   };
   
   this.getUrlParams = function(){
      var params_string = window.location.href.slice(window.location.href.indexOf('?') + 1);
      var params = params_string.split("&");
      var result = {};
      for (var i = 0; i < params.length; i++){
         var tmp = params[i].split("=");
         result[tmp[0]] = tmp[1];
      }
      
      return result;
   };
   
   this.getUrlParameter = function(parameter){
      var params_string = window.location.href.slice(window.location.href.indexOf('?') + 1);
      var params = params_string.split("&");
      var result = {};
      for (var i = 0; i < params.length; i++){
         var tmp = params[i].split("=");
         result[tmp[0]] = tmp[1];
      }
      
      return result[parameter];
   };
   
   this.getHashParameter = function(parameter){
      var params_string = window.location.href.slice(window.location.href.indexOf('#') + 1);
      var params = params_string.split("&");
      var result = {};
      
      for (var i = 0; i < params.length; i++){
         var tmp = params[i].split("=");
         result[tmp[0]] = tmp[1];
      }
      
      return result[parameter];
   };
   
   this.isset = function(variable){
      if (variable != "" && variable != null && variable != undefined && variable != "null" && variable != "undefined")
         return 1;
      else return 0;
   };
   
   this.getDaysLeft = function(timestamp, period){
      var now = this.getTimestamFromDate();
      var stamp_left = Number(timestamp)+Number(period)-now;
      if (stamp_left > 0)
         return Math.ceil(stamp_left/86400);
      else return -1;
   };
   
   this.getDateTimeForTimestamp = function(timestamp){
      var a = new Date(timestamp*1000);
      var months = ["января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"];
      var month = months[a.getMonth()];
      var date = a.getDate();
      var hours = a.getHours();
      var minutes = "0" + a.getMinutes();
      var formattedTime = hours + ':' + minutes.substr(minutes.length-2);
      return date+" "+month+", "+formattedTime;
   };
   
   this.getTimeForTimestamp = function(timestamp){
      
      var a = new Date(timestamp*1000);
      var month = "0" + a.getMonth();
      var date = a.getDate();
      var formattedDate = date + '.' + month.substr(month.length-2);
      var hours = a.getHours();
      var minutes = "0" + a.getMinutes();
      var formattedTime = hours + ':' + minutes.substr(minutes.length-2);
      if ((this.getTimestamFromDate()-timestamp) > 86400)
         return formattedDate;
      else return formattedTime;
   };
   
   this.getTimestamFromDate = function(){
      var a = new Date().getTime()/1000;
      return Math.floor(a);
   };
   
   this.hideEmail = function(email){
      var complete = "";
      for (var i = 0; i < email.length; i++){
         if (i < 1 || i > email.indexOf("@")-1)
            complete += email.substr(i, 1);
         else complete += "*";
      }
      return complete;
   };
   
   this.randomInteger = function(min, max) {
      var rand = min + Math.random() * (max - min);
      rand = Math.round(rand);
      return rand;
   };
   
   this.rand = function() {
      return Math.random().toString(36).substr(2); // remove `0.`
   };

   this.token = function() {
      return this.rand() + this.rand(); // to make it longer
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