<?php

namespace Fuel\Migrations;

class Create_timelogs
{
	public function up()
	{
		\DBUtil::create_table('timelogs', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'clockin' => array('constraint' => 11, 'type' => 'int'),
			'clockout' => array('constraint' => 11, 'type' => 'int', 'null' => true),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'default' => 0),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'default' => 0),
                        'deleted_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('timelogs');
	}
}