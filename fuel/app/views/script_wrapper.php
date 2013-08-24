<?php

/*
 * This view document is designed to allow compositing a local script
 * generated using the "script" view and a separate PHP view.
 */
?>
<?php echo View::forge('script', $js)?>
<?php echo View::forge($page_name, $page_data)?>