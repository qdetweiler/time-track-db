<?php
/*
 * Create the partial view displaying options for timelog types
 */
?>
<span class='type_display'><?php echo($selected_type)?></span>
<select name='type' class='type_select hidden'>
<?php if(count($types)):?>
    <?php foreach ($types as $type):?>
      <option value='<?php echo $type['type_val']?>' 
        <?php if($type['selected']) echo 'selected'?>>
          <?php echo $type['type_string']?></option>
    <?php endforeach; ?>
<?php else :?>
    <option value=''>- none available -</option>
<?php endif?>
</select>