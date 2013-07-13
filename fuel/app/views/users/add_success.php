<div id='success_message' class="rounded_box">
    <h1>Success!</h1>
    <p>The following user has been successfully added to the TimeTrack system.</p>
    <div id='user_info'>
        <table>
            <tr>
                <td class='label'>First Name</td>
                <td><?php echo $fname?></td>
            </tr>
            <tr>
                <td class='label'>Last Name</td>
                <td><?php echo $lname?></td>
            </tr>
            <tr>
                <td class='label'>Email</td>
                <td><?php echo $email?></td>
            </tr>
            <tr>
                <td class='label'>Username</td>
                <td><?php echo $username?></td>
            </tr>
            <tr>
                <td class='label'>Type</td>
                <td><?php echo $type?></td>
            </tr>
        </table>
    </div>
    <p>The following password can be used to authenticate the first time.  
            After initial login, user will be required to change his/her password.</p>
    <div id='password_info'>
        <h3><?php echo $temp_password?></h3>
    </div>
</div>
