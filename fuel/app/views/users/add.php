<?php

/*
 * Add.php creates the content for the user addition form
 */
?>
<pre><?php //echo print_r($error)?></pre>
<div class="content_wrapper">
<div class='rounded_box' id='user_info'>
    <form id='form' action='<?php echo Uri::create('users/add')?>' method='post'>
    <div class='field_set'>
        <div class='label'>
            <label id='fname'>First Name</label>
        </div>
        <div class='field'>
            <input type='text' name='fname' value='<?php if(isset($fname))echo$fname?>'/>
        </div>
        <div class='error'>
            <p><?php if(isset($error['fname']))echo $error['fname']?></p>
        </div>
    </div>
    <div class='field_set'>
        <div class='label'>
            <label id='lname'>Last Name</label>
        </div>
        <div class='field'>
            <input type='text' name='lname' value='<?php if(isset($lname))echo$lname?>'/>
        </div>
        <div class='error'>
            <p><?php if(isset($error['lname']))echo $error['lname']?></p>
        </div>
    </div>
    <div class='field_set'>
        <div class='label'>
            <label id='email'>Email</label>
        </div>
        <div class='field'>
            <input type='text' name='email' value='<?php if(isset($email))echo$email?>'/>
        </div>
        <div class='error'>
            <p><?php if(isset($error['email']))echo $error['email']?></p>
        </div>
    </div>
    <div class='field_set'>
        <div class='label'>
            <label id='username'>Username</label>
        </div>
        <div class='field'>
            <input type='text' name='username' value='<?php if(isset($username))echo$username?>'/>
        </div>
        <div class='error'>
            <p><?php if(isset($error['username']))echo $error['username']?></p>
        </div>
    </div>
        <div class='field_set'>
            <div class='label'>
                <label id='type'>Type</label>
            </div>
            <div class='field'>
                <select name='type'>
                    <option value='admin' <?php if($type=='admin') echo "selected"?>>Administrator</option>
                    <option value='standard' <?php if($type=='standard') echo "selected"?>>Standard</option>
                </select>
            </div>
        </div>
    
    <div >
        <button id='submit' class="black_button">Add</button>
    </div>
    </form>
</div>
<div id="directions">
    <h3>Instructions</h3>
    <p>The following restrictions apply to first and last names</p>
    <ol id="name_rules">
        <li>First and Last name may only consist of letters</li>
        <li>First and Last name must be 2 or more characters</li>
    </ol>
    <p>The following restrictions apply to a username:</p>
    <ol id="username_rules">
        <li>Username may only consist of letters, digits, and underscores</li>
        <li>Username must be at least 3 characters long</li>
        <li>Username must start with a letter</li>
    </ol>
</div>
</div>