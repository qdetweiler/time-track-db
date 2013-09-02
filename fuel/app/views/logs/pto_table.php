<?php

/*
 * Build the HTML necessary for the PTO table which displays information about
 * PTO logs
 */
?>
<?php foreach ($users as $user): ?>
<div class='user_info content_box'>

  <?php //if there is more than one user, add a header with
        //the user's name to the display ?>
  <?php if (count($users) > 1): ?>
    <div class='name'>
      <h1><?php echo $user['name'] ?></h1>
    </div>
  <?php endif?>

  <?php //*************    'table' header    ***********************?>
  <?php if(isset($user['no_logs_msg'])):?>
    <h3><?php echo($user['no_logs_msg'])?></h3>
  <?php else:?>
    <div class='head'>
        <span class='day'><h3>Day</h3></span>
        <span class='log_range'><h3>Range</h3></span>
        <span class='log_time'><h3>Total</h3></span>
        <?php if($show_type):?>
          <span class='log_type'><h3>Type</h3></span>
        <?php endif?>
    </div>
    <div class='divider'><hr></div>

    <?php //Insert HTML content for displaying info about each day
        foreach($user['log_views'] as $log_view){
          echo $log_view;
        }
    ?>

    <div class='total_display'>
      <h3>Total: <?php echo $user['total']?></h3>
    </div>
    <?php endif?>
  </div>

<?php endforeach //end for each user?>

