<div id='success_message' class="rounded_box">
    <h1>Success!</h1>
    <p>The password for the following user has been successfully reset.</p>
    <div id='user_info' class='grey_box'>
        <h3><?php echo $username?></h3>
    </div>
    <p>The following password can be used to authenticate once.  
            After login, user will be required to change his/her password.</p>
    <div class='grey_box'>
        <h3><?php echo $temp_password?></h3>
    </div>
</div>
