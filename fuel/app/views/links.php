<?php
/*
 *  Links.php generates the navigation links for the TimeTrack application
 */

    //setup id info for link display
    $id_info = Auth::get_user_id();
    $id = $id_info[1];
    
    if($id){
        $user = Model_User::find($id);
        $admin = Auth::member(\Config::get('timetrack.admin_group'));
        //PHP_Console::log($user);
        //PHP_Console::log($admin);
    }
    
    
?>


<ul>
  
  <?php if(!Agent::is_mobiledevice()):?>
    <?php if($id):?>
        <li><a href='<?php echo Uri::create('root/home')?>'>Home</a></li>
        <li><a href='<?php echo Uri::create('logs/display')?>'>Logs</a></li>
        <li><a href='<?php echo Uri::create('logs/pto')?>'>PTO</a></li>
    <?php endif?>
    <?php if(isset($admin) && $admin):?>
    <li><a href="<?php echo Uri::create('users/list')?>">Users</a></li>
    
    
    <?php endif ?>
  <?php endif?>
    
    <?php if($id):?>
    <li style="position:absolute;right:0px;"><a href='<?php echo Uri::create('root/logout')?>'><?php echo "Logout ($user->username)"?></a></li>
    <?php endif?>
</ul>

