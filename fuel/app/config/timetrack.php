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
    
    //Unix timestamp of the start of the first pay period
    //Note:  make sure "period start day"
    'first_period_start' => 1374984000,
    
    //minimum interval allowed for a time log in minutes
    'log_interval' => 15,
    
    //password lifespan
    'password_lifespan' => '3 months',
    
    //length of a period (make sure these match!)
    'period_length' => '1 week',
    'period_length_seconds' => 604800,
    
    
    //maximum number of periods to display
    'max_periods' => 10,
    
    //format for the display of period start and end dates
    'range_date_format' => 'm/d/y',
    
    //format for times used in log table
    'log_time_format' => 'g:i a',
    
    //format for dates used in the log table
    'log_date_format' => 'n / j',
    
    //log type definitions
    //Note:  -whatever is at value 0 will be the default
    //       -any entries above 0 are considered payed time off (there is
    //            currently no way to specify non-payed time off)
    'log_types' => array(
        0 => 'Standard',//
        1 => 'Vacation',
        2 => 'Sick',
    ),
    
    //auto-break settings
    'auto-break' => array(
      'enable' => 'true',
      'threshold' => 21600,
      'break_length' => 1800,
      'message' => '(AB)' //message appended to time string if auto-break is applied
    )
    
);


?>
