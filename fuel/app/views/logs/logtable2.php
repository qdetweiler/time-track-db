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
        <span class='log_range'><h3>Logs</h3></span>
        <span class='log_time'><h3>Time</h3></span>
        <span class='controls'><h3>Controls</h3></span>
      </div>
    </div>
    <div class='divider'><hr></div>

    <!--     Loop through days    -->
    <?php foreach($user['days'] as $day):?>
    
    <div class='day_display'>
    
    <!--   First log of the day  -->
    <div class='log_display'>
      <div class='day'><?php echo($day->day_label)?></div>
      <div class='a_log'>
        <span class='log_range'>
            <?php if(is_null($day->first_log)):?>
              <p>None</p>
            <?php else:?>
              <p><?php echo $day->first_log->clockin_string?> - <?php echo $day->first_log->clockout_string?></p>
              <input type='hidden' name='log_id' value='<?php echo $day->first_log->id?>'/>
            <?php endif?>
          </span>
          <span class='log_time'>
            <?php if(is_null($day->first_log)):?>
              <p>N/A</p>
            <?php else:?>
            <p><?php echo $day->first_log->time_string?></p>
            <?php endif?>
          </span>
          <?php if (!is_null($day->first_log)):?>
          <span class='controls'>
            <a href='#'>Edit</a>
            <a href='#'>Remove</a>
          </span>
          <?php elseif(($day->clocked_out == true)&&($day->day_start <= time())):?>
          <span class='controls'>
            <a href="#">Add</a>
          </span>
          <?php else:?>
          <span class='controls'>
            <p class='disabled_link'>Add</p>
          </span>
        <?php endif?>
      </div>
    </div>
    <?php if(false):?>
        <div class='add_log'>
            <a href='#'>Add</a>
        </div>
    <?php endif?>
    
    <!--    Additional Logs   -->
      <?php if(count($day->additional_logs)):?>
        <?php foreach ($day->additional_logs as $a_log): ?>
          <div class='log_display'>
                  <div class='a_log'>
                    <span class='log_range'>
                      <p><?php echo $a_log->clockin_string?> - <?php echo $a_log->clockout_string?></p>
                      <input type='hidden' name='log_id' value='<?php echo $a_log->id?>'/>
                    </span>
                    <span class='log_time'>
                      <p><?php echo $a_log->time_string?></p>
                    </span>
                    <span class='controls'>
                      <a href='#'>Edit</a>
                      <a href='#'>Remove</a>
                    </span>
                </div>
          </div>
        <?php endforeach //end for each additional log?>
    
        <!--   Add if one or more logs exist   -->
        <div class='add_log'>
          <div class='day'></div>
          <div class='a_log'>
            <span class='log_range'></span>
            <span class='log_time'></span>
            <span class='controls'>
              <?php if($day->clocked_out == false):?>
              <p class='disabled_link'>Add</p>
              <?php else:?>
              <a href='#'>Add</a>
              <?php endif?>
            </span>
          </div>
        </div>
    
      <?php endif //end if additional days exist?>
    
   
    </div>
    <?php endforeach //end for each day?>
    
    <div class='total_display'>
      <h3>Total: <?php echo $user['total']?></h3>
    </div>
</div>

<?php endforeach //end for each user?>


