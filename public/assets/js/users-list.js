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
    
    $('.cl_in').click(function(event){
      clockout($(this));
      event.preventDefault();
    });
    $('.cl_out').click(function(event){
      clockin($(this));
      event.preventDefault();
    });
    
});

function clockin(th){
      th.parent().ajaxSubmit(function(data){
        th.removeClass('cl_out');
        th.addClass('cl_in');
        th.val("Clocked In");
        th.unbind();
        th.click(function(event){
          clockout(th);
          event.preventDefault();
        });
      });
      return false;
};
    
var clockout = function(th){
      th.parent().ajaxSubmit(function(data){
        th.removeClass('cl_in');
        th.addClass('cl_out');
        th.val("Clocked Out");
        th.unbind();
        th.click(function(event){
          clockin(th);
          event.preventDefault();
        });
      });
      return false;
};