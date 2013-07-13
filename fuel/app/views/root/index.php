<?php
/**
 *    index.php serves as the landing view for the TimeTrack application
 */
?>
<div id='login_box'>
    <form id='login_form' action='<?php echo Uri::create('root/authenticate')?>' method='post'>
        <div id='username'>
            <label id='username_label'>Username</label>
            <input class='field' type='text' name='username' 
                   value='<?php if(isset($username)) echo $username?>'/>
        </div>
        <div id='password'>
            <label id='password_label'>Password</label>
            <input class='field' type='password' name='password' 
                   value=''/>
        </div>
        <?php if(isset($error)):?>
            <div id='error'>
                <p class='error_msg'><?php echo $error?></p>
            </div>
        <?php endif?>
        <button id='submit' type='submit' name='submit'>Login</button>
    </form>
</div>