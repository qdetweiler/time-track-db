/* 
 * Scripts used in the users list page
 */
$(document).ready(function(){
    
    $('.remove_button').click(function(){
        if(!confirm("Are you sure you want to remove this user\n" +
                    "and all associated logs from the database?")){
            return false;
        }
    });
    
    $('.cl_in').click(function(){
      clockout($(this));
    });
    $('.cl_out').click(function(){
      clockin($(this));
    });
    
});

function clockin(th){
      th.parent().ajaxSubmit(function(data){
        th.removeClass('cl_out');
        th.addClass('cl_in');
        th.html("Clocked In");
        th.unbind();
        th.click(function(){
          clockout(th);
        });
      });
      return false;
};
    
var clockout = function(th){
      th.parent().ajaxSubmit(function(data){
        th.removeClass('cl_in');
        th.addClass('cl_out');
        th.html("Clocked Out");
        th.unbind();
        th.click(function(){
          clockin(th);
        });
      });
      return false;
};