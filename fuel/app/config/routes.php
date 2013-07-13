<?php
return array(
	'_root_'  => 'root/index',  // The default route
	'_404_'   => '404',    // The main 404 route
	
	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
);