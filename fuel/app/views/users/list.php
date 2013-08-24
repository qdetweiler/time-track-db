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
    <?php foreach ($users as $user): ?>
      <tr class='user_row'>
        <td class='name'>
          <p><?php echo $user->fname . ' ' . $user->lname ?></p>
        </td>
        <td class='username'>
          <p><?php echo $user->username ?></p>
        </td>
        <td class='type'>
          <p><?php echo $user->type ?></p>
        </td>
        <td class='status'>
          <form name='status_frm' method='post' action='<?php echo Uri::create('root/change_status') ?>'>
            <input type='hidden' name='id' value='<?php echo $user->id ?>'/>
            <input type='submit' class='<?php echo $user->status_class ?> hyperlink' value='<?php echo $user->status ?>'>
          </form>
        </td>
        <td class='options_logs'>
          <a href='<?php echo Uri::create('logs/display?id=' . $user->id) ?>'>Logs</a>
        </td>
        <td class='options_edit'>
          <form name='edit_frm' method='post' action='<?php echo Uri::create('users/auto_reset_pass') ?>'>
            <input type='hidden' name='id' value='<?php echo $user->id ?>'/>
            <input type='submit' class='hyperlink' value='Reset'/>
          </form>
        </td>
        <td class='options_remove'>
          <form name='remove_frm' method='post' action='<?php echo Uri::create('users/remove') ?>'>
            <input type='hidden' name='id' value='<?php echo $user->id ?>'/>
            <input type='submit' class='remove_button hyperlink' value='Remove' <?php if ($user->remove_disabled) echo 'disabled' ?>/> 
          </form>
        </td>
      </tr>
<?php endforeach ?>

  </table>
  <div id='add'>
    <a id='add_user' class="black_button" name='add_user' href='<?php echo Uri::create('users/add') ?>'>Add User</a>
  </div>
</div>
