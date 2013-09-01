<?php

namespace Fuel\Migrations;

class Add_timelog_type
{
	public function up()
	{
		\DBUtil::add_fields('timelogs', array(
			'type' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true, 'default' => 0),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('timelogs', array('type'));
	}
}