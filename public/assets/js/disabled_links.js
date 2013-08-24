/* 
 * Disabled_links simply sets all links (<a> tags) with the "disabled" class
 * to perform no function
 */
$('a.disabled').on('click', function(ev){
  ev.preventDefault();
  return false;
});


