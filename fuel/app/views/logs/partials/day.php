<?php
/*
 * This partial view creates the HTML content for displaying information
 * about all logs recorded on a single day
 */
?>
<div class='day_display'>

  <div class='day_label'><?php echo $day_label?></div>
  
  <?php foreach($logs as $log):?>
  <div class='log_display'>
    <form name='log_form' method='post' action='<?php echo $log_form_action?>'>
      <input name='day_start' type='hidden' value='<?php echo $day_start ?>'/>
      <input name='user_id' type='hidden' value='<?php echo $user_id?>'/>
      <input name='log_id' type='hidden' value='<?php echo $log['id']?>'/>
        <span class='log_range'><?php echo($log['range'])?></span>
        <span class='log_time'><?php echo($log['time'])?></span>
        <?php if($showtype):?>
        <span class='log_type'><?php echo($log['type'])?></span>
        <?php endif?>
        <?php if($admin):?>
        <span class='controls'><?php echo($log['controls'])?></span>
        <?php endif?>
    </form>
  </div>
  <?php endforeach?>  
  
</div>
    