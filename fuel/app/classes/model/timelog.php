
<?php

class Model_Timelog extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'user_id',
		'clockin',
		'clockout',
		'created_at',
		'updated_at',
        //'deleted_at',
	);

	protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
	);
        
	protected static $_table_name = 'timelogs';
        
        protected static $_belongs_to = array(
            'user' => array(
                'key_from' => 'user_id',
                'model_to' => 'Model_User',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        );
        
//        /**
//         * hard delete a log entry from the database
//         * @param type $id
//         */
//        public function hard_delete(){
//            DB::delete('timelogs')->where('id', $this->id)->execute();
//        }
       

}
