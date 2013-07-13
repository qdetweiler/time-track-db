<?php

namespace Fuel\Migrations;

class Edit_first_login_field
{
	public function up()
	{
            
            \DBUtil::drop_fields('users', array('first_login'));
            
            \DBUtil::add_fields('users', array(
			'password_expiration' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
            ));
                
	}

	public function down()
	{
		\DBUtil::drop_fields('users',array(
                    'password_expiration'
                ));
                
                \DBUtil::add_fields('users', array(
                   'first_login' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                ));
	}
}
?>
