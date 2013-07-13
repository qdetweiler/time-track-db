<?php

namespace Fuel\Migrations;

class Add_user_info
{
	public function up()
	{
		\DBUtil::add_fields('users', array(
			'fname' => array('type' => 'varchar', 'constraint' => 255),
			'lname' => array('type' => 'varchar', 'constraint' => 255),
                        'first_login' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
		));
                
	}

	public function down()
	{
		\DBUtil::drop_fields('users',array(
                    'fname','lname', 'first_login'
                ));
	}
}
?>
