<?php
/*
 * This partial view constructs the HTML that displays controls
 * for adding timelogs to the database
 */
?>
<?php if(!$disabled):?>
<span class='buttons_1'>
  <input type='submit' class='hyperlink add_b' value='Add'/>
</span>
<span class='buttons_2 hidden'>
  <input type='submit' class='hyperlink cancel_b' value='Cancel'/>
  <input type='submit' name='add' class='hyperlink' value='Submit'/>
</span>
<?php else:?>
<span class='buttons_1'>
  <input type='submit' class='hyperlink add_b_disabled' value='Add' disabled/>
</span>
<?php endif; ?>
