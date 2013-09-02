<?php

/*
 * Construct the view used to display a user's paid time off
 */
?>
<div id="wrapper">
    <div id="controls">
        <form id='control_form' action='
            <?php echo $control_form_action?>' method='post'>
        <input type='hidden' name='id' value='<?php echo $id?>'/>
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
            <span id='start_date'>
              <label>Start Date</label>
              <input type='text' class='date_input' name='start_date' value='<?php echo $start_date?>'/>
            </span>
            <span id='end_date'>
              <label>End Date</label>
              <input type='text' class='date_input' name='end_date' value='<?php echo $end_date?>'/>
            </span>
            <span id='type'>
                <label>Type</label>
                <select name='type'>
                  <?php echo $type_selection?>
                </select>
            </span>
            <span id='button'>
                <input id="update_button" class="white_button" type='submit' 
                       name='submit' value='Update'/>
            </span>
        </form>
    </div>
    <div id="logs">
    </div>
</div>