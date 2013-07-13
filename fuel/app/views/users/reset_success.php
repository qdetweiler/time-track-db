<div id='success_message'>
    <h1>Success!</h1>
    <p>The password for the following user has been successfully reset.</p>
    <div id='user_info'>
        <p>Username:</p>
        <p><?php echo $username?></p>
    </div>
    <p>The following password can be used to authenticate once.  
            After login, user will be required to change his/her password.</p>
    <div id='password_info'>
        <h3><?php echo $temp_password?></h3>
    </div>
</div>
