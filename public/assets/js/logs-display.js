/* 
 * Logs Display enables ajax interactions to display log information based
 * on user specified values
 */
$(document).ready(function(){

  $('#control_form').submit(function(){
      
        $(this).ajaxSubmit(function(data){
            $('#logs').html(data);
            setup_logtable();
        });
        
        return false;
    });
    
    $('#control_form').submit();
    
    
});

