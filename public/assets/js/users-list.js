/* 
 * Scripts used in the users list page
 */
$(document).ready(function(){
    
    $('.options_remove').click(function(){
        if(!confirm("Are you sure you want to remove this user\n" +
                    "and all associated logs from the database?")){
            return false;
        }
    });
    
})

