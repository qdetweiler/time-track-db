$(document).ready(function(){

  start_timer();
  
});

function start_timer(){

  //get time from server
  $.post(time_uri, function(data){
    
    var json = $.parseJSON(data);
    $('#clock').html(json.time);
    var pause = (60-json.seconds) * 1000;
    //console.log(json);
    //console.log(pause);
    setTimeout(function(){start_timer();}, pause);
    
  });
  
}

