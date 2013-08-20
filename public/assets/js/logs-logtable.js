var time_format_regex = RegExp("^\\d\\d?:\\d\\d (pm|Pm|PM|Am|am|AM)");

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function setup_logtable(){

  /********************  Edit Button Interactions  *****************/
  $('.log_edit_button').on("click",function(){
    
    //hide all edit and remove buttons
    $('.buttons1').addClass('hidden');
    
    //unhide all edit and remove disabled links
    $('.disabled_buttons1').removeClass('hidden');
    
    //hide disabled links for this log
    $(this).closest('.controls').find('.disabled_buttons1').addClass('hidden');
    
    //hide add buttons
    $('.add_log_enabled').addClass('hidden');
    $('.add_button_disabled').removeClass('hidden');
    
    //unhide submit and cancel buttons for this log
    $(this).closest('.controls').find('.buttons2').removeClass('hidden');

    //hide log text span
    $(this).closest('.a_log').find('.log_text_spn').addClass('hidden');
    
    //setup dates for log text form
    var form_input = $(this).closest('.a_log').find('.time_input');
    
      //are we supposed to round?
      var round = $('#round').children('input').is(':checked');
      var step_min = (round === true) ? log_min_interval : 1;
    
    $(form_input).each(function(index){
      
      $(this).timepicker({
        timeFormat:'hh:mm tt',
        controlType: 'slider',
        stepMinute: step_min
      });
      $(this).timepicker("refresh");
      
      //parse date string
      //input is in the format hh:mm tt, hours do not have leading zero
      var time = $(this).val();

      if(time.match(time_format_regex) !== null){
      
        var date = convert_hhmmtt(time);
        $(this).timepicker("setDate", date);

      } else {
        var date = convert_hhmmtt($(this).closest('.a_log')
                .find('.log_form_frm').find('.time_input').first().val());
        date.setMinutes(date.getMinutes()+log_min_interval);
        $(this).timepicker("setDate", date);
      }
      
    });
    
    //unhide log text form
    $(this).closest('.a_log').find('.log_form_spn').removeClass('hidden');
    
    return false;
    
  });
  
  /***********************  Cancel Button **********************/
  $('.log_cancel_button').on("click",function(){
    
    //show log text span
    $(this).closest('.a_log').find('.log_text_spn').removeClass('hidden');
    
    //unhide log text form
    $(this).closest('.a_log').find('.log_form_spn').addClass('hidden');
    
    //hide submit and cancel buttons for this log
    $(this).closest('.controls').find('.buttons2').addClass('hidden');
    
    //show all edit and remove buttons
    $('.buttons1').removeClass('hidden');
    
    //hide all edit and remove disabled links
    $('.disabled_buttons1').addClass('hidden');
    
    //show add buttons
    $('.add_log_enabled').removeClass('hidden');
    $('.add_button_disabled').addClass('hidden');
    
    //replace fields with old values
    var log_text_spn = $(this).closest('.a_log').find('.log_text_spn');
    var text = log_text_spn.children('p').html();
    //console.log(text);
    var split = text.split(' - ');
    //console.log(split);
    
    var log_form_spn = log_text_spn.siblings('.log_form_spn');
    //console.log(log_form_spn);
    var start_input = log_form_spn.find('.time_input').first();
    //console.log(start_input);
    var end_input = log_form_spn.find('.time_input').last();
    //console.log(end_input);
    
    start_input.val(split[0]);
    end_input.val(split[1]);

    return false;
    
  });
  
  /************************  Add Log Cancel Button  ********************/
  $('.add_log_cancel_button').on("click",function(){
    
    //show log text span
    $(this).closest('.a_log').find('.no_logs_p').removeClass('hidden');
    
    //hide submit and cancel buttons for this log
    $(this).closest('.controls').find('.buttons2').addClass('hidden');
    
    //show all edit and remove buttons
    $('.buttons1').removeClass('hidden');
    
    //hide all edit and remove disabled links
    $('.disabled_buttons1').addClass('hidden');
    
    //show add buttons
    $('.add_log_enabled').removeClass('hidden');
    $('.add_button_disabled').addClass('hidden');
    
    $(this).parent().siblings('.add_button').removeClass('hidden');
    
    //hide form
    $(this).closest('.a_log').find('.add_log_form_spn').addClass('hidden');
    
    //reset form values to blank
    var time_inputs = $(this).closest('.a_log').find('.add_log_form_frm').children('.time_input');
    time_inputs.val("");
    
    return false;
    
  });
  
  
  /************************  Submit Button for Edited Logs  *******************/
  $('.log_submit_button').on("click",function(){
    
    //day stamp needed for verifying that logs don't overlap
    var day_stamp = $(this).closest('.day_display').children('.day_start').val();
    
    //make sure start time comes before end time
    var time_selectors = $(this).closest('.a_log')
            .find('.log_form_frm').children('.time_input');
    var start_time_s = time_selectors.first().val();
    var end_time_s = time_selectors.last().val();
    
    var form = $(this).closest('.a_log').find('.log_form_frm');
    var clocked_out = form.children('[name=clocked_out]').first().val();
    var id = form.children('[name=id]').first().val();
    var user_id = form.children('[name=user_id]').first().val();
    
    //invalid start time
    if(start_time_s.match(time_format_regex) === null){
      
      alert('Invalid starting time');
      return false;
      
    }
    
    //invalid end time
    if(clocked_out === 'true' && end_time_s.match(time_format_regex) === null ){
      
      alert('Invalid ending time');
      return false;
      
    }
    
    //clocked out
    if(clocked_out === 'true' || end_time_s.match(time_format_regex) !== null){
      var start_time_d = convert_hhmmtt(start_time_s);
      var end_time_d = convert_hhmmtt(end_time_s);
      
      if(start_time_d < end_time_d){
      
        var edit_form = $(this).closest('.a_log').find('.log_form_frm');
        var control_form = $("#control_form");

        //make sure this timeframe does not overlap with other logs
        $.ajax({
              type: "POST",
              url:valid_log,
              data:{
                day_stamp:day_stamp,
                start:start_time_s,
                end:end_time_s,
                id:id,
                user_id:user_id
              },
              success:function(data){
                if(data){
                    edit_form.ajaxSubmit(function(){control_form.submit();});
                } else {
                    alert('Timelogs cannot overlap.');
                }
              },
              dataType:"json"
        });

      } else {

        alert("Start time must come before end time.");

      }
      
    } else {
      
      $(this).closest('.a_log').find('.log_form_frm')
              .ajaxSubmit(function(data){
                console.log(data);
                $('#control_form').submit();
              });
    }
    
    

    return false;
    
  });
  
  $('.add_log_submit_button').click(function(){
    
    var day_stamp = $(this).closest('.day_display').children('.day_start').val();
    
    //make sure start time comes before end time
    var time_selectors = $(this).closest('.a_log')
            .find('.add_log_form_frm').children('.time_input');
    var start_time_s = time_selectors.first().val();
    var end_time_s = time_selectors.last().val();
    
    //invalid start time
    if(start_time_s.match(time_format_regex) === null){
      
      alert('Invalid starting time');
      return false;
      
    }
    
    //invalid end time
    if(end_time_s.match(time_format_regex) === null ){
      
      alert('Invalid ending time');
      return false;
      
    }
    
    //clocked out
    var start_time_d = convert_hhmmtt(start_time_s);
    var end_time_d = convert_hhmmtt(end_time_s);

    if(start_time_d < end_time_d){

        var add_form = $(this).closest('.a_log').find('.add_log_form_frm');
        var control_form = $("#control_form");

        //make sure this timeframe does not overlap with other logs
        $.ajax({
              type: "POST",
              url:valid_log,
              data:{
                day_stamp:day_stamp,
                start:start_time_s,
                end:end_time_s
              },
              success:function(data){
                if(data){
                    add_form.ajaxSubmit(function(){control_form.submit();});
                } else {
                    alert('Timelogs cannot overlap.');
                }
              },
              dataType:"json"
        });

      } else {
        alert("Start time must come before end time.");
      }
      
      return false;
      
  });
  
  
  /*********************  Add Button   ******************/
  $('.add_log_enabled a').click(function(){
    
    var a_log = $(this).closest('.a_log');
    
    //setup timepickers on hidden sliders

      //are we supposed to round?
      var round = $('#round').children('input').is(':checked');
      var step_min = (round === true) ? log_min_interval : 1;
      
    var time_selectors = a_log.find('.add_log_form_frm').children('.time_input');

    time_selectors.timepicker({
        timeFormat:'hh:mm tt',
        controlType: 'slider',
        stepMinute: step_min,
        defaultValue:'12:00 am'
      });
    $(this).timepicker("refresh");
    
    
    //hide the logs paragraph label
    a_log.find('.no_logs_p').addClass('hidden');
    
    //hide the add button
    $(this).closest('.add_button').addClass('hidden');
    
    //hide all the other controls
        //hide all edit and remove buttons
        $('.buttons1').addClass('hidden');
        
        //unhide all edit and remove disabled links
        $('.disabled_buttons1').removeClass('hidden');

        //hide add buttons
        $('.add_log_enabled').addClass('hidden');
        $('.add_button_disabled').removeClass('hidden');
    
    //unhide the form
    a_log.find('.add_log_form_spn').removeClass('hidden');
    
    //show the submit and cancel buttons
    $(this).closest('.add_button').siblings('.buttons2').removeClass('hidden');
    
    
    return false;
    
    
  });
  
  /**********************  Remove Button Interactions  *****************/
  $('.log_remove_button').on('click', function(){
    
    if(confirm('Are you sure you want to remove this log from the database?')){
      
      var id = $(this).closest('.a_log').find('.log_form_frm').find('[name=id]').val();
      $.post(remove, {id:id}, function(data){$('#control_form').submit();}, 'json');
      
    }
    
  });
  
};


/**
 * Convert string in the form "hh:mm tt" into a date object where the
 * date is fixed but the time is accurate
 * @returns {undefined}
 */
function convert_hhmmtt(time){
  
  var time_split = time.split(':');
  var hours = parseInt(time_split[0]);
  var mins = time_split[1].substr(0,2);
  var ampm = time_split[1].substr(3,2);

  if(ampm.toUpperCase() === 'PM'){
    if(hours != '12'){
      hours += 12;
    }
  } else if (ampm.toUpperCase() === 'AM' && hours == '12'){
    hours = 0;
  }

  var date = new Date(2013,8,6,hours, mins, 0, 0);
  return date;
  
}