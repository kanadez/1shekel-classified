function Feedback(){
   this.promoted_by_user = {};
   this.promote_limits = null;
   
   this.repostBy = function(item, to){
       $('[to='+to+']').css("opacity","0.3").attr({
               "href":"javascript:void(0)",
               "onclick":"",
               "target":"_self",
               "title": "Лимит перепостов исчерпан"
            }).unbind("click");
       
      $.post(post_url,{
         a: "feedback_repost_by",
         b: item,
         c: to
      },function(result){
         console.log(result);
         
      });
   };
   
   this.repostFrom = function(author_num, href_string){
      $.post(post_url,{
         a: "feedback_repost_from",
         b: author_num,
         c: href_string
      },function(result){
         console.log(result);
      });
   };
   
   this.getPromotedByUser = function(item){
      $.post(post_url,{
         a: "feedback_get_promoted_by_user",
         b: item
      },function(result){
         feedback.promoted_by_user = JSON.parse(result);
         
         for (var i = 0; i < feedback.promoted_by_user.length; i++)
            $('[to='+feedback.promoted_by_user[i].to+']').css("opacity","0.3").attr({
               "href":"javascript:void(0)",
               "onclick":"",
               "target":"_self",
               "title": "Лимит перепостов исчерпан"
            }).unbind("click");
      });
   };
   
   this.getPromoteLimits = function(){
      $.post(post_url,{
         a: "feedback_get_promote_limits"
      },function(result){
         feedback.promote_limits = JSON.parse(result);
         var b = 0; // 5 limit
         
         for (var i = 0; i < feedback.promote_limits.length; i++){
             if (feedback.promote_limits[i].to == 3){
                 if (b < 4){
                     b++;
                 }
                 else{
                     $('[to=3]').css("opacity","0.3").attr({
                        "href":"javascript:void(0)",
                        "onclick":"",
                        "target":"_self",
                        "title": "Лимит перепостов исчерпан"
                     }).unbind("click");
                 }
             }
             else{
                $('[to='+feedback.promote_limits[i].to+']').css("opacity","0.3").attr({
                   "href":"javascript:void(0)",
                   "onclick":"",
                   "target":"_self",
                   "title": "Лимит перепостов исчерпан"
                }).unbind("click");
             }
         }
         
         /*for (var i = 0; i < feedback.promoted_by_user.length; i++)
            $('[to='+feedback.promoted_by_user[i].to+']').css("opacity","0.3").attr({
               "href":"javascript:void(0)",
               "onclick":"",
               "target":"_self"
            }).unbind("click");*/
      });
   };
}