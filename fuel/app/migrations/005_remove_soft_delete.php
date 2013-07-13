<?php

namespace Fuel\Migrations;

class Remove_soft_delete
{
	public function up()
	{
            
            \DBUtil::drop_fields('users', array('deleted_at'));
            \DBUtil::drop_fields('timelogs', array('deleted_at'));
                
	}

	public function down()
	{
                \DBUtil::add_fields('users', array(
                   'deleted_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                ));
                \DBUtil::add_fields('timelogs', array(
                   'deleted_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                ));
	}
}
?>