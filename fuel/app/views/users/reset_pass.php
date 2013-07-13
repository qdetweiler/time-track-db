<div class="rounded_box">
    <div id='instructions'>
        <p>Your password has expired.  Please enter a new password that meets the following requirements:</p>
        <ol>
            <li>Password must have at least one number and one letter</li>
            <li>Password must be at least 6 characters in length</li>
        </ol>
    </div>
    <div id='reset_box'>
        <form id='pass_reset_form' action="<?php echo Uri::create('users/reset_pass')?>" method='post'>
            <div class='field_set'>
                <label id='oldpass'>Old Password</label>
                <input type='password' name='oldpass' value=''/>
            </div>
            <div class='field_set'>
                <label id='newpass1'>New Password</label>
                <input type='password' name='newpass1' value=''/>
            </div>
            <div class='field_set'>
                <label id='newpass2'>Confirm New Password</label>
                <input type='password' name='newpass2' value=''/>
            </div>
            <?php if(isset($errormsg)):?>
            <div id='error_msg'>
                <p><?php echo $errormsg?></p>
            </div>
            <?php endif ?>
            <div id='button'>
                <input type='submit' value='Submit'/>
            </div>
        </form>
    </div>
</div>