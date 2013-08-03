<div id="wrapper">
    <div id="controls">
        <form id='control_form' action='<?php echo Uri::create('logs/logtable2')?>' method='post'>
        <input type='hidden' name='id' value='<?php echo $id?>'/>
            <span id='range_selection'>
                <label>Period</label>
                <select name='period'>
                    <?php if(count($range)):?>
                        <?php for($i = (count($range)-1); $i >= 0; $i--):?>
                        <option value='<?php echo $range[$i]['start']?>'><?php echo $range[$i]['string']?></option>
                        <?php endfor; ?>
                    <?php else :?>
                        <option value=''>- none available -</option>
                    <?php endif?>
                </select>
            </span>
            <?php if($admin):?>
                <span id='user'>
                    <label>User</label>
                    <select name='user'>
                        <option value='All' selected>All</option>
                        <?php foreach($users as $user):?>
                        <option value='<?php echo $user->id?>' <?php if($user->id == $selected_id)echo"selected"?>><?php echo $user->fname." ".$user->lname?></option>
                        <?php endforeach?>
                    </select>
                </span>
            <?php endif?>
            <span id='round'>
                <input type='checkbox' name='round' value='true' checked/>
                <label>Round Times</label>
            </span>
            <span id='round'>
                <label>Display</label>
                <select name='display_type'>
                    <option value='all' selected>Logs</option>
                    <?php if(false):?><option value='day_totals'>Daily Totals</option><?php endif?>
                    <option value='period_totals'>Period Totals</option>
                </select>
            </span>
            <span id='button'>
                <input id="update_button" class="white_button" type='submit' name='submit' value='Update' <?php if(!count($range)) echo "disabled"?>/>
            </span>
        </form>
    </div>
    <div id="logs">
    </div>
</div>