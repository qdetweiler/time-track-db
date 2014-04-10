<div id="wrapper">
    <div id="controls">
        <form id='control_form' action='
            <?php echo $control_form_action?>' method='post'>
        <input type='hidden' name='id' value='<?php echo $id?>'/>
            <span id='range_selection'>
                <label>Period</label>
                <select name='period'>
                  <?php echo $period_options?>
                </select>
            </span>
            <?php if($admin):?>
                <span id='user'>
                    <label>User</label>
                    <select name='user'>
                        <option value='All' selected>All</option>
                        <?php foreach($users as $user):?>
                        <option value='<?php echo $user->id?>' 
                          <?php if($user->id == $selected_id)echo"selected"?>>
                          <?php echo $user->fname." ".$user->lname?>
                        </option>
                        <?php endforeach?>
                    </select>
                </span>
            <?php endif?>
            <span id='round'>
                <input type='checkbox' name='round' value='true' checked/>
                <label>Round Times</label>
            </span>
            <span id='showtype'>
                <input type='checkbox' name='showtype' value='true' checked/>
                <label>Show Type</label>
            </span>
            <span id='round'>
                <label>Display</label>
                <select name='display_type'>
                    <option value='all' selected>Logs</option>
                    <option value='day_totals'>Daily Totals</option>
                    <option value='period_totals'>Period Totals</option>
                </select>
            </span>
            <span id='button'>
                <input id="update_button" class="white_button" type='submit' 
                       name='submit' value='Update'
                         <?php if($update_disabled) echo "disabled"?>/>
            </span>
        </form>
      <div id='show_older'>
        <form name='show_older' action='<?php echo Uri::create('logs/toggle_older')?>' method='post'>
          <input type='submit' class='hyperlink' name='show_old' value='<?php echo $older_logs_label?>'/>
        </form>
      </div>
    </div>
    <div id="logs">
    </div>
</div>