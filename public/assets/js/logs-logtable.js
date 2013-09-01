var time_format_regex = RegExp("^\\d\\d?:\\d\\d (pm|Pm|PM|Am|am|AM)");
var prev_start = '';
var prev_end = '';
var prev_type = '';

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function setup_logtable(){

  setup_edit_buttons();
  setup_remove_buttons();
  setup_cancel_buttons();
  setup_add_buttons();
  setup_add_submit_buttons();
  setup_edit_submit_buttons();
  
};

/**
 * Setup the interactions on edit buttons
 * @returns {undefined} 
 */
function setup_edit_buttons(){
  
  $('.edit_b').on("click", function(event){
    
    //disable all visible controls on the page
    $('.buttons_1 input').prop('disabled', true);
    
    //hide edit and delete buttons from this form
    $(this).parent().toggleClass('hidden');
    
    //unhide submit and cancel buttons for this form
    $(this).parent().siblings('.buttons_2').toggleClass('hidden');
    
    //swap type elements to allow type selection
    $(this).closest('form').find('.type_display').first().toggleClass('hidden');
    $(this).closest('form').find('.type_select').first().toggleClass('hidden');
    
    //store current type in case cancellation is required
    prev_type = $(this).closest('form').find('.type_select').find(':selected').val();
    
    //setup dates for input elements
    var form_input = $(this).closest('form').find('.time_input');
    
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
        
        //capture current values in case a cancellation is required
        if(index === 0) prev_start = time ; else prev_end = time;
        
        if(time.match(time_format_regex) !== null){

          var date = convert_hhmmtt(time);
          $(this).timepicker("setDate", date);

        } else {
          var date = convert_hhmmtt($(this).closest('form')
                  .find('.time_input').first().val());
          date.setMinutes(date.getMinutes()+log_min_interval);
          $(this).timepicker("setDate", date);
        }

      });
    
    //enable input elements
    form_input.prop('disabled', false);
    
    event.preventDefault();
    return false;
    
  });
}

/**
 * Setup the interactions on the remove button
 * @returns {undefined}
 */
function setup_remove_buttons(){

  $('.remove_b').click(function(event){
      //if user wants to remove the log, submit the form, then
      //refresh the view
      if(confirm('Are you sure you want to remove this log?')){
        $(this).closest('form').ajaxSubmit(
            {
              success:ajax_response,
              data:{action:'remove'}
       });
      }
      event.preventDefault();
      return false;
  });
}


/**
 * Setup the interactions on the cancel buttons
 * @returns {undefined}
 */
function setup_cancel_buttons(){
  
  $('.cancel_b').click(function(event){
    
    //reset input values to their originals and disable them
    var form_input = $(this).closest('form').find('.time_input');
    
      $(form_input).each(function(index){

        //capture current values in case a cancellation is required
        var time = (index === 0) ? prev_start : prev_end;
        
        //this is a valid time
        if(time.match(time_format_regex) !== null){
          var date = convert_hhmmtt(time);
          $(this).timepicker("setDate", date);

        //this is not a valid time
        } else {
          
          var val = $(this).closest('form').find('.time_input').first().val();
          
          //first input field is valid.  Set this field based on it's value.
          if(val.match(time_format_regex)){
            var date = convert_hhmmtt(val);
            date.setMinutes(date.getMinutes()+log_min_interval);
            $(this).timepicker("setDate", date);
          }
          
        }
        
        $(this).val(time);
        $(this).prop('disabled', true);

      });

    //enable all buttons
    $('.buttons_1 input').prop('disabled', false);
    
    //hide the cancel buttons
    $(this).parent().toggleClass('hidden');
    
    //unhide the edit and remove buttons
    $(this).parent().siblings('.buttons_1').toggleClass('hidden');
    
    //swap type elements to hide type selection
    $(this).closest('form').find('.type_display').first().toggleClass('hidden');
    $(this).closest('form').find('.type_select').first().toggleClass('hidden');
    
    //revert selected type
    $(this).closest('form').find('.type_select')
            .find('[value='+prev_type+']').prop('selected', true);
    
    //in case this is a cancellation of an add operation
    var log_range = $(this).closest('form').find('.log_range');
    log_range.children('.no_logs_p').toggleClass('hidden');
    log_range.children('.add_input_spn').toggleClass('hidden');
    
    return false;
    
  });
}

/**
 * Setup interactions on the add buttons
 * @returns {undefined}
 */
function setup_add_buttons(){
  
  $('.add_b').click(function(event){
    
     //disable all other controls
     $('.buttons_1 input').prop('disabled', true);

     //hide the add button
     $(this).parent().toggleClass('hidden');

     //reveal the submit and cancel buttons
     $(this).parent().siblings('.buttons_2').toggleClass('hidden');
     
    //swap type elements to allow type selection
    $(this).closest('form').find('.type_display').first().toggleClass('hidden');
    $(this).closest('form').find('.type_select').first().toggleClass('hidden');
    
    //store current type in case cancellation is required
    prev_type = $(this).closest('form').find('.type_select').find(':selected').val();

     //setup the input fields
     var time_inputs = $(this).closest('form').find('.time_input');

        //setup timepickers on hidden sliders

        //are we supposed to round?
        var round = $('#round').children('input').is(':checked');
        var step_min = (round === true) ? log_min_interval : 1;

        time_inputs.timepicker({
            timeFormat:'hh:mm tt',
            controlType: 'slider',
            stepMinute: step_min,
            defaultValue:'12:00 am'
          });
        $(this).timepicker("refresh");
        
        time_inputs.val('12:00 am');

      //hide no_logs_p
      var log_range = $(this).closest('form').find('.log_range');
      log_range.children('.no_logs_p').toggleClass('hidden');

      //unhide the input fields
      $(this).closest('form').find('.time_input').prop('disabled', false);
      log_range.children('.add_input_spn').toggleClass('hidden');

      return false;
    
  });
}


/**
 * Evaluates the information in the given JQuery input elements to determine
 * whether the values meet base requirements.
 * @param {JQuery} time_inputs
 * @param {boolean} clocked_out - whether or not the log was originally clocked out
 * @returns boolean - null if valid, string error message if not valid
 */
function validate_times(time_inputs, clocked_out){
  
    var valid = null;
  
    //make sure start time comes before end time
    var start_time_s = time_inputs.first().val();
    var end_time_s = time_inputs.last().val();
    
    //invalid start time
    if(start_time_s.match(time_format_regex) === null){
      return 'Invalid starting time';
    }
    
    //invalid end time
    if(clocked_out === true && end_time_s.match(time_format_regex) === null ){
      return 'Invalid ending time';
    }
    
    //clocked out
    if(clocked_out === true || end_time_s.match(time_format_regex) !== null){
      
    var start_time_d = convert_hhmmtt(start_time_s);
      var end_time_d = convert_hhmmtt(end_time_s);
      
      if(start_time_d < end_time_d){
        valid = null;
      } else {
        return "Start time must come before end time.";
      }
    
    //not clocked out
    } else {
      //do nothing.  start time is valid, and end time does not matter
    }

    return valid;
  
}

/**
 * Setup interactions on buttons that submit form for the addition of a new
 * timelog entry
 * @returns {undefined}
 */
function setup_add_submit_buttons(){
  
  $('.add_submit_b').click(function(event){
    
    //validate values in the time fields
    var time_inputs = $(this).closest('form').find('.time_input');
      //log was originally open if the value of the end time was an invalid time
      //and the log id was not 0
    var clocked_out = !($(this).closest('form').find('[name=log_id]').val() !== '0' &&
                       prev_end.match(time_format_regex) === null);
    
    var validation_msg = validate_times(time_inputs, clocked_out);
    if(validation_msg === null){
      
      $(this).closest('form').ajaxSubmit({
              success:ajax_response,
              data:{action:'add'}
      });
      
    } else {
      alert(validation_msg);
    }
    
    return false;
    
  });
  
}

/**
 * Setup interactions for buttons submitting edited logs
 * @returns {undefined}
 */
function setup_edit_submit_buttons(){
  
  $('.edit_submit_b').click(function(event){
    
    //validate values in the time fields
    var time_inputs = $(this).closest('form').find('.time_input');
      //log was originally open if the value of the end time was an invalid time
      //and the log id was not 0
    var clocked_out = !($(this).closest('form').find('[name=log_id]').val() !== '0' &&
                       prev_end.match(time_format_regex) === null);
    
    var validation_msg = validate_times(time_inputs, clocked_out);
    if(validation_msg === null){
      
      $(this).closest('form').ajaxSubmit({
          success:ajax_response,
          data:{action:'edit'}
      });
      
    } else {
      alert(validation_msg);
    }
    
    return false;
    
  });
  
}

/**
 * Standard method used in response to a CRUD call
 * @param {type} data
 * @returns {undefined}
 */
var ajax_response = function (data){
    var json = $.parseJSON(data);
    //if we are supposed to display a message, do it!
    if(json.show_msg){
      alert(json.msg);
    }
    //if json call succeeded, refresh the view
    if(json.success){
      $('#control_form').submit();
    }
}

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