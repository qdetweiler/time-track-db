<?php
/*
 * Build the set of HTML <option> elements required for allowing
 * selection of pay periods in the logs display
 */
?>
<?php if(count($periods)):?>
    <?php foreach ($periods as $period):?>
      <option value='<?php echo $period['start']?>'><?php echo $period['string']?></option>
    <?php endforeach; ?>
<?php else :?>
    <option value=''>- none available -</option>
<?php endif?>
