<?php

/*
 * Configuration options for the TimeTrack application
 */
return array(
  
    /**
     * Login related configuration options
     */
    
    //maximum number of wrong login attempts
    'max_attempts' => 3,
    
    //amount of minutes to lock a user account after max_attempts wrong logins
    'lock_time' => 15,
  
    
    /**
     * User related configuration options
     */
    
    //integer assignment for administrators group
    'admin_group' => 100,
    
    
    /*
     * Log related configuration options
     */
    
    //minimum interval allowed for a time log in minutes
    'log_interval' => 15,
    
    //password lifespan
    'password_lifespan' => '3 months',
    
    //day on which to start a period
    'period_start_day' => "Sunday",
    
    //length of a period
    'period_length' => '1 week',
    
    //format for times used in log table
    'log_time_format' => 'g:i a',
    
    //format for dates used in the log table
    'log_date_format' => 'm / d',
    
    
    
);


?>
