<?php foreach ($users as $user): ?>

  <div class='user_info content_box'>
  <!--------------------- Header ---------------------->
  
  <!--    More than one user    -->
  <?php if (count($users) > 1): ?>
  
    <div class='name'>
      <h1><?php echo $user['name'] ?></h1>
    </div>
  
  <?php endif?>

    <!--     Header     -->
    <div class='head'>
      <div class='day'><h3>Day</h3></div>
      <div class='a_log_head'>
        <span class='log_range'>
          <span class='log_time'><h3>Time</h3></span>
        </span>
      </div>
    </div>
    <div class='divider'><hr></div>

    <!--     Loop through days    -->
    <?php foreach($user['days'] as $day):?>
    
    <div class='day_display'>
    
      <div class='log_display'>
        <div class='day'><?php echo($day->day_label)?></div>
        <span class='log_time'><?php echo $day->total_time_string?></span>
      </div>
      
    </div>
    <?php endforeach //end for each day?>
    
    <div class='total_display'>
      <h3>Total: <?php echo $user['total']?></h3>
    </div>

  </div>

<?php endforeach //end for each user?>
