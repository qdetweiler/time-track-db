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
        <?php if($admin):?>
        <span class='controls'><h3>Controls</h3></span>
        <?php endif?>
      </div>
    </div>
    <div class='divider'><hr></div>

    <!--     Loop through days    -->
    <?php foreach($user['days'] as $day):?>
    
    <div class='day_display'>
      <input class="day_start" type='hidden' value='<?php echo $day->day_start?>'/>
    
    <!--   First log of the day  -->
    <div class='log_display'>
      <div class='day'><?php echo($day->day_label)?></div>
      <div class='a_log'>
          <span class='log_range'>
              <?php if(is_null($day->first_log)):?>
                <p class='no_logs_p'>None</p>
                <span class="add_log_form_spn hidden">
                    <form class="add_log_form_frm" method="post" action="<?php echo Uri::create('logs/add')?>">
                      <input type="hidden" name='start_stamp' value='<?php echo $day->day_start ?>'/>
                      <input type='hidden' name='user_id' value='<?php echo $user['id']?>'/>
                      <input type="text" name="start_time" class="time_input" value=""/>
                      <p> - </p>
                      <input type="text" name="end_time" class="time_input" value=""/>
                    </form>
                </span>
              <?php else:?>
                <span class="log_text_spn">
                  <p><?php echo $day->first_log->clockin_string?> - <?php echo $day->first_log->clockout_string?></p>
                </span>
                <span class="log_form_spn hidden">
                    <form class="log_form_frm" method="post" action="<?php echo Uri::create('logs/edit')?>">
                      <input type="hidden" name="id" value="<?php echo $day->first_log->id?>"/>
                      <input type="hidden" name="user_id" value="<?php echo $user['id']?>"/>
                      <input type="hidden" name='clocked_out' value='<?php echo ($day->first_log->clockout == 0) ? 'false' : 'true';?>'/>
                      <input type="text" name="start_time" class="time_input" value="<?php echo $day->first_log->clockin_string?>"/>
                      <p> - </p>
                      <input type="text" name="end_time" class="time_input" value="<?php echo $day->first_log->clockout_string?>"/>
                    </form>
                </span>
              <?php endif?>
          </span>
          <span class='log_time'>
            <?php if(is_null($day->first_log)):?>
              <p></p>
            <?php else:?>
            <p><?php echo $day->first_log->time_string?></p>
            <?php endif?>
          </span>
        
          <?php if($admin):?>
          <?php if (!is_null($day->first_log)):?>
          <span class='controls'>
            <span class="buttons1">
              <a class="log_edit_button" href='#'>Edit</a>
              <a class="log_remove_button" href="#">Remove</a>  
            </span>
            <span class="disabled_buttons1 hidden">
              <p class="disabled_link">Edit</p>
              <p class="disabled_link">Remove</p>  
            </span>
            <span class="buttons2 hidden">
              <a class="log_cancel_button" href="#">Cancel</a>
              <a class="log_submit_button" href="#">Submit</a>  
            </span>
          </span>
          <?php elseif($day->day_start <= time()):?>
          <span class='controls'>
              <span class="add_button">
                <?php if($day->clocked_out == true): //user clocked out?>
                  <span class="add_button_disabled hidden">
                    <p class='disabled_link'>Add</p>
                  </span>
                  <span class='add_log_enabled'>
                    <a href='#'>Add</a>
                  </span>
                <?php else: //user not clocked out?>
                  <span class="add_button_disabled_p invisible">
                    <p class='disabled_link'>Add</p>
                  </span>
                <?php endif?>
              </span>                  
              <span class="buttons2 hidden">
                  <a class="add_log_cancel_button" href="#">Cancel</a>
                  <a class="add_log_submit_button" href="#">Submit</a>  
              </span>
          </span>
          <?php else:?>
        <span class="controls">
          <span class="add_button">
            <p class="disabled_link">Add</p>
          </span>
        </span>
        <?php endif?>
        <?php endif //if admin?>
      </div>
    </div>
    
    <!--    Additional Logs   -->
      <?php if(count($day->additional_logs)):?>
        <?php foreach ($day->additional_logs as $a_log): ?>
          <div class='log_display'>
                  <div class='a_log'>
                    <span class='log_range'>
                      <span class="log_text_spn">
                        <p><?php echo $a_log->clockin_string?> - <?php echo $a_log->clockout_string?></p>
                      </span>
                      <span class="log_form_spn hidden">
                          <form class="log_form_frm" method="post" action="<?php echo Uri::create('logs/edit')?>">
                            <input type="hidden" name="id" value="<?php echo $a_log->id?>"/>
                            <input type="hidden" name="user_id" value="<?php echo $user['id']?>"/>
                            <input type="hidden" name='clocked_out' value='<?php echo ($a_log->clockout == 0) ? 'false' : 'true';?>'/>
                            <input type="text" name="start_time" class="time_input" value="<?php echo $a_log->clockin_string?>"/>
                            <p> - </p>
                            <input type="text" name="end_time" class="time_input" value="<?php echo $a_log->clockout_string?>"/>
                          </form>
                      </span>
                    </span>
                    <span class='log_time'>
                      <p><?php echo $a_log->time_string?></p>
                    </span>
                    
                    <?php if($admin):?>
                    <span class='controls'>
                        <span class="buttons1">
                          <a class="log_edit_button" href='#'>Edit</a>
                          <a class="log_remove_button" href="#">Remove</a>  
                        </span>
                        <span class="disabled_buttons1 hidden">
                          <p class="disabled_link">Edit</p>
                          <p class="disabled_link">Remove</p>  
                        </span>
                        <span class="buttons2 hidden">
                          <a class="log_cancel_button" href="#">Cancel</a>
                          <a class="log_submit_button" href="#">Submit</a>  
                        </span>
                    </span>
                    <?php endif //if user admin?>
                </div>
          </div>
         <?php endforeach //end for each additional log?>
        <?php endif //end if additional days exist?>
        
        <?php if($admin && !is_null($day->first_log)):?>
        <!--   Add if one or more logs exist   -->
        <div class='add_log'>
          <div class='day'></div>
          <div class='a_log'>
            <span class='log_range'>
              <span class="add_log_form_spn hidden">
                    <form class="add_log_form_frm" method="post" action="<?php echo Uri::create('logs/add')?>">
                      <input type="hidden" name='start_stamp' value='<?php echo $day->day_start ?>'/>
                      <input type='hidden' name='user_id' value='<?php echo $user['id']?>'/>
                      <input type="text" name="start_time" class="time_input" value=""/>
                      <p> - </p>
                      <input type="text" name="end_time" class="time_input" value=""/>
                    </form>
              </span>
            </span>
            <span class='log_time'></span>
            
            <?php if($admin):?>
            <span class='controls'>
              <span class="add_button">
                <?php if($day->clocked_out == true): //user clocked out?>
                  <span class="add_button_disabled hidden">
                    <p class='disabled_link'>Add</p>
                  </span>
                  <span class="add_log_enabled">
                    <a href='#'>Add</a>
                  </span>
                <?php else: //user not clocked out?>
                  <span class="add_button_disabled_p invisible">
                    <p class='disabled_link'>Add</p>
                  </span>
                <?php endif?>
              </span>
              <span class="buttons2 hidden">
                  <a class="add_log_cancel_button" href="#">Cancel</a>
                  <a class="add_log_submit_button" href="#">Submit</a>  
              </span>
            </span>
            <?php endif //if user is an admin?>
            
          </div>
        </div>
        <?php endif?>

    
   
    </div>
    <?php endforeach //end for each day?>
    
    <div class='total_display'>
      <h3>Total: <?php echo $user['total']?></h3>
    </div>
</div>

<?php endforeach //end for each user?>