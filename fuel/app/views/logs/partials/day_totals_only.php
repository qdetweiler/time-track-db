<?php
/*
 * This partial view creates the HTML content for displaying information
 * about all logs recorded on a single day
 */
?>
<div class='day_display'>

    <div class='day_label'><?php echo $day_label ?></div>
    <?php if ($totals): ?>
        <?php foreach ($totals as $label => $time): ?>
            <div class='log_display'>
                <span class='log_time'><?php echo($label) ?>:</span>
                <span class='log_time'><?php echo $time ?></span>
            </div>
        <?php endforeach ?>
    <?php else : ?>
        <div class='log_display'>
            <p class='log_time grey'>None</p>
        </div>
    <?php endif ?>


</div>
