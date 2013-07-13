<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.6
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */

return array(

	/*
	 * If you don't specify a DB configuration name when you create a connection
	 * the configuration to be used will be determined by the 'active' value
	 */
	'active' => 'default',

        'default' => array(
            'type'           => 'mysql',
            'connection'     => array(
                'hostname'       => 'localhost',
                'port'           => '3306',
                'database'       => 'timetrack_db',
                'username'       => 'ttrack',
                'password'       => 'sonicTrack87',
                'persistent'     => false,
                'compress'       => false,
            ),
            'identifier'   => '`',
            'table_prefix'   => '',
            'charset'        => 'utf8',
            'enable_cache'   => true,
            'profiling'      => false,
        )

);
