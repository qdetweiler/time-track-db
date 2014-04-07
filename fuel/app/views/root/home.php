<?php
/*
 *      Home.php constructs the content for the home page in the
 *      TimeTrack application
 */
?>
<div class='content_box' id='clock_box'>
  
  <div id='clock_wrapper' class='grey_box rounded'>
    <h3>Current Time: </h3><h3 id="clock"><?php echo $time?></h3>
  </div>
  
  <div id='button_wrapper'>
      <form id='clock_form' action='<?php echo $action?>' method='post'>
          <input id='clock_button' class='black_button rounded' 
                 type='submit' name='activate_clock' value='<?php echo $button_label?>'/>
          
          <?php if($clockin):?>
            <select id="log_type" name="type">
                <?php foreach($type_options as $type_value => $type_string):?>
                <option value="<?php echo $type_value?>"><?php echo $type_string?></option>
                <?php endforeach?>
            </select>
          <?php endif?>
          
      </form>
  </div>
    
  <div id="last_log_notification" class='grey_box rounded'><?php echo $last_clock_s ?></div>
  
</div>

