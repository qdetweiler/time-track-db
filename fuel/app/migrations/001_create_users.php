<?php

namespace Fuel\Migrations;

class Create_users
{

    function up()
    {
        // get the tablename
        \Config::load('simpleauth', true);
        $table = \Config::get('simpleauth.table_name', 'users');

        // only do this if it doesn't exist yet
        if ( ! \DBUtil::table_exists($table))
        {
                // table users
                \DBUtil::create_table($table, array(
                        'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
                        'username' => array('type' => 'varchar', 'constraint' => 50),
                        'password' => array('type' => 'varchar', 'constraint' => 255),
                        'group' => array('type' => 'int', 'constraint' => 11, 'default' => 1),
                        'email' => array('type' => 'varchar', 'constraint' => 255),
                        'last_login' => array('type' => 'varchar', 'constraint' => 25),
                        'login_hash' => array('type' => 'varchar', 'constraint' => 255),
                        'profile_fields' => array('type' => 'text'),
                        'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                        'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                        'deleted_at' => array('type' => 'int', 'constraint' => 11,'null'=>true),
                        'account_locked' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                        'last_attempt' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                        'num_attempts' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                        'clocked_in' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
                ), array('id'));

                // add a unique index on username and email
                \DBUtil::create_index($table, array('username', 'email'), 'username', 'UNIQUE');
        }
    }

    function down()
    {
        // get the tablename
        \Config::load('simpleauth', true);
        $table = \Config::get('simpleauth.table_name', 'users');

        // drop the users table
        \DBUtil::drop_table($table);
    }
}
