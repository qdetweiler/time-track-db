<?php

/*
 * This partial view creates the HTML content for displaying information
 * about a single log
 */
?>
<?php if(isset($no_logs_msg)):?>
  <p class='no_logs_p'><?php echo($no_logs_msg)?></p>
  <span class="input_spn hidden">
    <input type="text" name="start_time" class="time_input" disabled/>
    <span class='hyphen'> - </span>
    <input type="text" name="end_time" class="time_input" disabled/>
  </span>
<?php else:?>
  <span class="input_spn">
    <input type="hidden" name='clocked_out' value='<?php echo ($clocked_out)?>'/>
    <input type="text" name="start_time" class="time_input" 
           value="<?php echo $clockin_string?>" disabled/>
    <span class='hyphen'> - </span>
    <input type="text" name="end_time" class="time_input" 
           value="<?php echo $clockout_string?>" disabled/>
  </span>
<?php endif?>