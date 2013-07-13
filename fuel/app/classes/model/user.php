<?php

class Model_User extends \Orm\Model
{
	protected static $_properties = array(
            'id',
            'username',
            'password',
            'group',
            'fname',
            'lname',
            'password_expiration',
            'email',
            'last_login',
            'login_hash',
            'profile_fields',
            'created_at',
            'updated_at',
            //'deleted_at',
            'account_locked',
            'last_attempt',
            'num_attempts',
            'clocked_in',
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
        
	protected static $_table_name = 'users';
       
        protected static $_has_many = array(
            'timelogs' => array(
                'key_from' => 'id',
                'model_to' => 'Model_Timelog',
                'key_to' => 'user_id',
                'cascade_save' => true,
                'cascade_delete' => true,
            )
        );

}

?>
