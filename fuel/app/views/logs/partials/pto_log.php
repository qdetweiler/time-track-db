<?php

/*
 * Build display for a single PTO log
 */
?>
<div class='pto_log'>
      <span class='day'><?php echo $day?></span>
      <span class='log_range'><?php echo $range?></span>
      <span class='log_time'><?php echo $total?></span>
      <?php if($show_type):?>
        <span class='log_type'><?php echo $type?></span>
      <?php endif?>
</div>