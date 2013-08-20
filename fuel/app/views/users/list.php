<div id='user_table_wrapper'>
    <table id='user_table'>
        <tr id='titles'>
            <td class='name'>
                <h1>Name</h1>
            </td>
            <td class='username'>
                <h1>Username</h1>
            </td>
            <td class='type'>
                <h1>Type</h1>
            </td>
            <td class='status'>
                <h1>Status</h1>
            </td>
            <td class='options' colspan='3'>
                <h1>Options</h1>
            </td>
        </tr>
        <?php 
        $flag = 'even';
        foreach($users as $user):?>
        <?php $flag = ($flag == 'even') ? 'odd' : 'even'; ?>
        <tr class='user_row <?php echo $flag?>'>
            <td class='name'>
                <p><?php echo $user->fname.' '.$user->lname?></p>
            </td>
            <td class='username'>
                <p><?php echo $user->username ?></p>
            </td>
            <td class='type'>
                <p><?php if($user->group == 100){echo "Administrator";}else{echo "Standard";}?></p>
            </td>
            <td class='status'>
              <form name='status_frm' method='post' action='<?php echo Uri::create('root/change_status')?>'>
                <input type='hidden' name='id' value='<?php echo $user->id?>'/>
                <a class='<?php echo $user->status_class?>' href='#'><?php echo $user->status ?></a>
              </form>
            </td>
            <td class='options_logs'>
                <a href='<?php echo Uri::create('logs/display?id='.$user->id)?>'>Logs</a>
            </td>
            <?php //if(false):?>
            <td class='options_edit'>
                <a href='<?php echo Uri::create('users/auto_reset_pass?id='.$user->id)?>'>Reset</a>
            </td>
            <?php //endif?>
            <td class='options_remove'>
            <?php if($user->id != $currid):?>
                <a href='<?php echo Uri::create('users/remove?id='.$user->id)?>'>Remove</a>
            <?php else :?>
                <p class='disabled'>Remove</p>
            <?php endif?>
            </td>
            
        </tr>
        <?php endforeach ?>

    </table>
    <div id='add'>
        <a id='add_user' class="black_button" name='add_user' href='<?php echo Uri::create('users/add')?>'>Add User</a>
    </div>
</div>
