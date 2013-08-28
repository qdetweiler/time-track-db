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
  
    //format of clock showed on clockin / clockout page
    'clock_format' => 'g:i a',
    
    //last clocked in/out notification format
    'last_clock_format' => 'M d, g:i a',
    
    /**
     * User related configuration options
     */
    
    //integer assignment for administrators group
    'admin_group' => 100,
    
    
    /*
     * Log related configuration options
     */
    
    //first date of first recorded pay period (date-time)
    'log_start_date' => '2013-06-30',
    
    //minimum interval allowed for a time log in minutes
    'log_interval' => 15,
    
    //password lifespan
    'password_lifespan' => '3 months',
    
    //day on which to start a period
    'period_start_day' => "Sunday",
    
    //length of a period
    'period_length' => '1 week',
    
    //format for the display of period start and end dates
    'range_date_format' => 'm/d/y',
    
    //format for times used in log table
    'log_time_format' => 'g:i a',
    
    //format for dates used in the log table
    'log_date_format' => 'n / j',
    
);


?>
