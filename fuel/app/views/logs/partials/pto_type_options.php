<?php
/*
 * Create the partial view displaying options for timelog types
 */
?>
<?php if(count($types)):?>
    <?php foreach ($types as $type):?>
      <option value='<?php echo $type['type_val']?>' 
        <?php if($type['selected']) echo 'selected'?>>
          <?php echo $type['type_string']?></option>
    <?php endforeach; ?>
<?php else :?>
    <option value=''>- none available -</option>
<?php endif?>