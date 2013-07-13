<?php
/*
 *      Home.php constructs the content for the home page in the
 *      TimeTrack application
 */
?>
<div id='clock_box'>
    <div class='vertical_centered'>
        <form id='clock_form' action='<?php echo $action?>' method='post'>
                <input type='submit' name='activate_clock' value='<?php echo $button_label?>'/>
        </form>
    </div>
</div>

