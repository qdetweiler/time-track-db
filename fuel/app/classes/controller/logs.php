<?php

/*
 * The logs controller handles all events related to timelogs, including
 * CRUD and displaying log reports
 */

class Controller_Logs extends Controller_Template {

    /**
     * Build the page responsible for enabling user to view logs
     * Note:  Most of the controls here will be handled with ajax
     */
    public function action_display(){
        
        //make sure user is authenticated
        $id_info = Auth::get_user_id();
        $id = $id_info[1];
        if(!$id){
            Response::redirect('root/home');
        }
        
        //build correct view depending on user account type
        if(Auth::member(\Config::get('timetrack.admin_group'))){
            $this->build_admin_display($id);
        } else {
            $this->build_standard_display($id);
        }
        
        //setup css for page
        $this->template->css = array('logs_display.css','logs_logtable.css');
        
        //setup javascript for page
        $this->template->js = array('logs-display.js', 'logs-logtable.js',
            'jquery-ui-timepicker-addon.js');
        
        //setup title
        $this->template->title = "Timelogs";

    }
    
    
    /**
     * Build the log display for a standard user
     * @param type $id
     */
    private function build_standard_display($id){
        
        //setup period <select> options
        $data['period_options'] = $this->forge_period_options($id);
        
        //if there are no logs for this user, disable the update button
        $data['update_disabled'] = ($this->first_log_clockin('all') == 0) ? true : false;
        
        //setup other variables
        $data['admin'] =false;
        $data['id'] = $id;
        $data['control_form_action'] = Uri::create('logs/logtable3');
        
        //setup view
        $this->template->title = "Timelogs";
        $this->template->content = View::forge('logs/display', $data);
        
    }
    
    /**
     * Build the logs display page for an administrative user
     * @param type $id
     */
    private function build_admin_display($id){
        
        //setup selected user
        $user_id = Input::param('id');
        if(!is_null($user_id)){
            //admin is viewing logs for another user
            $data['selected_id'] = $user_id;
        } else {
            //admin is viewing own logs
            $data['selected_id'] = $id;
        }
        
        //get the set of pay periods available
        $data['period_options'] = $this->forge_period_options('all');
        
        //if there are no logs for this user, disable the update button
        $data['update_disabled'] = ($this->first_log_clockin('all') == 0) ? true : false;

        //setup users
        $users = Model_User::find('all');
        $data['users'] = $users;

        //setup other variables
        $data['id'] = $id;
        $data['admin'] = true;
        $data['control_form_action'] = Uri::create('logs/logtable3');
        
        //js variables
//        $log_min_interval = \Config::get('timetrack.log_interval');
//        $valid_log_uri = Uri::create('logs/valid_log');
//        $remove_uri = Uri::create('logs/remove');
        
//        $js = htmlspecialchars("var log_min_interval = \"$log_min_interval\";
//var valid_log_uri = \"$valid_log_uri\";
//var remove_uri = \"$remove_uri;\"");

        //set local javascript into page
//        $this->template->script = View::forge('script', array('js' => $js));
        
        //generate content section
        $this->template->content = View::forge('logs/display', $data);

    }
    
    /**
     * Return the Unix timestamp representing the clockin time of the first
     * log for the specified user or the first overall log if the string
     * 'all' is passed in instead of an ID
     * @param type mixed - id of user if specified or string 'all' for all users
     * @return time of first clockin for the given user, or 0 if the
     *         user never clocked in
     */
    private function first_log_clockin($id){
        
        //get first log for either the given user
        //or all users depending on the id passed in
        if($id == 'all'){
            $first_log = Model_Timelog::find('first', array(
                'order_by' => array('clockin' => 'asc'),
            ));
        } else {
            $first_log = Model_Timelog::find('first', array(
                'where' => array(
                    array('user_id', $id),
                ),
                'order_by' => array('clockin' => 'asc'),
            ));
        }
        
        return (is_null($first_log)) ? 0 : $first_log->clockin;
    }
    
    /**
     * Construct the set of pay period options for the specified user
     * or for all users if 'all' is passed instead of an ID.  
     * Note:  The return value of this function is a 
     * formatted HTML partial-page constructed using "View::forge()"
     */
    private function forge_period_options($id){
      
      $start_time = $this->first_log_clockin($id);
      $end_time = time(); //fetch all pay periods from the first to today
      
      //there are no available pay periods for the specified user
      if($start_time == 0){
        $data['periods'] = array();
        return View::forge('logs/partials/period_options');
      }
      
      //get timestamp for monday on the first and last pay periods available
      $first_week = strtotime("previous ".\Config::get('timetrack.period_start_day'), strtotime("+ 1 day", $start_time));
      $last_week = strtotime("previous ".\Config::get('timetrack.period_start_day'), strtotime("+ 1 day", $end_time));
        
      //construct data for the view
        //add first period
        $end_first = $this->get_period_end($first_week);
        $periods[] = array(
                'string' => $this->period_string($first_week, $end_first),
                'start'  => $first_week,
                'end' => $end_first
        );
        
        //add other periods
        $period_start = $first_week;
        $period_end = $this->get_period_end($period_start);
        while($period_end < $last_week){
            $period_start = strtotime("+ ".\Config::get('timetrack.period_length'), $period_start);
            $period_end = $this->get_period_end($period_start);
            $periods[] = array(
                'string' => $this->period_string($period_start, $period_end),
                'start'  => $period_start,
                'end' => $period_end
            );
        }
      
      rsort($periods);//TODO optimize here
      $data['periods'] = $periods;
      
      //forge the view
      return View::forge('logs/partials/period_options', $data);
        
    }
    
    /**
     * get_period_end returns a timestamp representing the end of a time period
     * based on the timestamp of the beginning of the same period
     * @param type $period_start
     * @return type
     */
    private function get_period_end($period_start){
        return strtotime("+ ".\Config::get('timetrack.period_length')." -1 sec", $period_start);
    }
    
    /**
     * format start and end dates into a string representing the range
     * @param type $start_stamp
     * @param type $end_stamp
     * @return type
     */
    private function period_string($start_stamp, $end_stamp){
        $format = \Config::get('timetrack.range_date_format');
        return date($format, $start_stamp)." - ".date($format, $end_stamp);
    }
    
    /**
     * Construct and return the partial view for displaying timelog information
     */
    public function action_logtable3(){
      
        //retrieve user / users
        $user = Input::post('user');
        if(is_null($user)){
            $id = Input::post('id');
            $user_list[] = Model_User::find($id);
            
        } else if($user == 'All'){
            $user_list = Model_User::find('all');
            
        } else {
            $user_list[] = Model_User::find($user);
        }
        
        //build the appropriate display
        $display_type = Input::post('display_type');
        switch($display_type){
        case 'all':

          //get period information
          $period_start = Input::post('period');

          //set whether or not to round
          $round = (!is_null(Input::post('round'))) ? true : false;

          //for each user, construct information for
          //the view
          foreach($user_list as $u){

            //create day partial views and get overall total
            list($overall_total, $day_views) 
                    = $this->forge_days($u->id, $period_start, $round);

            //setup data for view
            $usr['name'] = $u->fname.' '.$u->lname;
            $usr['day_views'] = $day_views;
            $usr['period_total'] = Util::sec2hms($overall_total);
            $users[] = $usr;

          }

          $data['users'] = $users;
          $data['admin'] = Auth::member(\Config::get('timetrack.admin_group'));

          return View::forge('logs/logtable3', $data);

          break;
        }
      
    }
    
    /**
     * Construct the partial view that displays all log information for a
     * given day
     * @param type $user_id - user ID whose data will be displayed
     * @param type $day_start - start of the day (Unix timestamp)
     * @param type $round - whether or not to round time values
     * @return - array(total time for the day, day view)
     */
    private function forge_day($user_id, $day_start, $round){
      
        //end of the day
        $day_end = $day_start + (24*60*60)-1; //one day minus one second in seconds
        $day_total = 0;
        $data['logs'] = array();
        
        //fetch all logs for the day that have been clocked out
        $logs_for_day = Model_Timelog::find('all', array(
            'where' => array(
                array('user_id', $user_id),
                array('clockin','>=',$day_start),
                array('clockout', '<=', $day_end),
                array('clockout', '!=', 0),
            ),
            'order_by' => array('clockin' => 'asc'),
        ));

        //fetch any logs for the day that have not been clocked out
        $log_sans_clockout = Model_Timelog::find('last', array(
            'where' => array(
                array('user_id', $user_id),
                array('clockin', '>=', $day_start),
                array('clockin', '<=', $day_end),
                array('clockout', 0),
            ),
        ));
        
        //there are no logs at all
        if(empty($logs_for_day) && is_null($log_sans_clockout)){
          
          //construct partial view for a log with an add log entry only
          $log['id'] = 0;
          $log['range'] = View::forge('logs/partials/log_range',
                  array('no_logs_msg' => 'None'));
          $log['time'] = 'N/A';
          $log['controls'] = $this->forge_add_controls($user_id, $day_start);
          array_push($data['logs'],$log);
          
          
        //there is only an open log for this day
        } else if(empty($logs_for_day)){
          
          //construct partial view for an open log
          $log['id'] = $log_sans_clockout->id;
          $parsed = $this->parse_log($log_sans_clockout,$round, true);
          $log['range'] = View::forge('logs/partials/log_range', array(
              'clocked_out' => false,
              'clockin_string' => $parsed->clockin_string,
              'clockout_string' => $parsed->clockout_string
          ));
          $log['time'] = $parsed->time_string;
          $log['controls'] = View::forge('logs/partials/edit_controls');
          array_push($data['logs'], $log);
          
          //construct log display for adding a log
          $log['id'] = 0;
          $log['range'] 
                  = View::forge('logs/partials/log_range', array('no_logs_msg' => ''));
          $log['time'] = '';
          $log['controls'] 
                  = View::forge('logs/partials/add_controls', array('disabled' => false));
          array_push($data['logs'], $log);
          
        //there is at least one closed log for the day
        } else {
          
          foreach($logs_for_day as $log_for_day){
            
            //create log views for each log
            $log['id'] = $log_for_day->id;
            $parsed = $this->parse_log($log_for_day, $round, false);
            $log['range'] = View::forge('logs/partials/log_range', array(
                'clocked_out' => true,
                'clockin_string' => $parsed->clockin_string,
                'clockout_string' => $parsed->clockout_string
            ));
            $log['time'] = $parsed->time_string;
            $log['controls'] = View::forge('logs/partials/edit_controls');
            array_push($data['logs'], $log);
            $day_total += $parsed->time;
            
          }
          
          //if there is also a closed log for this day, create
          //a view for that log
          if(!is_null($log_sans_clockout)){
            
            $log['id'] = $log_sans_clockout->id;
            $parsed = $this->parse_log($log_sans_clockout, $round, true);
            $log['range'] = View::forge('logs/partials/log_range', array(
                'clocked_out' => false,
                'clockin_string' => $parsed->clockin_string,
                'clockout_string' => $parsed->clockout_string
            ));
            $log['time'] = $parsed->time_string;
            $log['controls'] = View::forge('logs/partials/edit_controls');
            array_push($data['logs'], $log);
            
          }
          
          //create the add log entry for after all valid logs
          $log['id'] = 0;
          $log['range'] 
                  = View::forge('logs/partials/log_range', array('no_logs_msg' => ''));
          $log['time'] = '';
          $log['controls'] = $this->forge_add_controls($user_id, $day_start);
          array_push($data['logs'], $log);
          
          
        }
        
        //setup data for the view
        $data['day_start'] = $day_start;
        $data['day_label'] = date(\Config::get('timetrack.log_date_format'), $day_start);
        $data['user_id'] = $user_id;
        $data['admin'] = Auth::member(\Config::get('timetrack.admin_group'));
          
        return array($day_total, View::forge('logs/partials/day', $data));
        
    }

    /**
     * Construct add controls and enable or disable them according
     * to rules for when a log can be added
     * @param type $user_id
     * @param type $day_start
     * @return type
     */
    private function forge_add_controls($user_id, $day_start){
      
      //determine which set of controls to add to the log
      $open_log = Model_Timelog::find('first', array(
          'where' => array(
              array('user_id', $user_id),
              array('clockout', 0)
          )
      ));

      //there are no open logs or an open log comes after today, and the current
      //day is not a date in the future.
      //Add controls can be enabled
      if((is_null($open_log) || $open_log->clockin > $day_start) && ($day_start < time())){
        $controls = View::forge('logs/partials/add_controls'
                , array('disabled' => false));
      //there is an open log that started before today.
      //Add controls should be disabled
      } else {
        $controls = View::forge('logs/partials/add_controls',
                array('disabled' => true));
      }
      
      return $controls;
    }
    
    /**
     * Create a LogInfo object based off an actual timelog
     * @param type $log - timelog object to parse
     * @param type $round - whether to round time values
     * @param type $partial - whether this is a partial log
     * @return
     */
    private function parse_log($log, $round, $partial){
      
        //get rounded values
        $clockin_rounded 
                = Util::roundToInterval($log->clockin, 
                        \Config::get('timetrack.log_interval')*60);
        $clockout_rounded 
                = Util::roundToInterval($log->clockout, 
                        \Config::get('timetrack.log_interval')*60);

        //set clockin and clockout
        $clockin = ($round) ? $clockin_rounded : $log->clockin;
        $clockout = ($round) ? $clockout_rounded : $log->clockout;

        //store information about the log
        $lg = new stdClass();
        $lg->id = $log->id;
        $lg->clockin_string = date(\Config::get('timetrack.log_time_format'), $clockin);
        $lg->clockout_string = ($partial) ? 'Now' : date(\Config::get('timetrack.log_time_format'), $clockout);
        $lg->time = ($partial) ? 0 : $clockout_rounded - $clockin_rounded;
        $lg->time_string = ($partial) ? 'N/A' : Util::sec2hms($lg->time);
      
        return $lg;
        
    }
    
    
    /**
     * Edit log entry to reflect changes submitted by user
     */
    public function action_edit(){
      
        //make sure user is authenticated and an admin
        if(!Auth::member(\Config::get('timetrack.admin_group'))){
            Response::redirect('root/home');
        }
        
        //fetch the log to be edited
        $log = Model_Timelog::find(Input::post("id"));
        
        //find the date of the log
        $date_string = date("M j Y", $log->clockin);
        
        //get the start timestamp
        $clockin_new = strtotime(Input::post('start_time')." ".$date_string);
        
        //get the end timestamp
        $end_time = Input::post('end_time');
        $clockout_new = (preg_match('/^\d\d?:\d\d (am|AM|Am|PM|pm|Pm)$/', $end_time)) 
                ? strtotime($end_time." ".$date_string) : 0;
        
        //if clockout is 0 but clockout_new is not zero, find the
        //user associated with the log and clock him / her out
        if($log->clockout == 0 && $clockout_new > 0){
          
          $user = Model_User::find($log->user_id);
          $user->clocked_in = false;
          $user->save();
          
        }
        
        //edit log clockin time
        $log->clockin = $clockin_new;
        $log->clockout = $clockout_new;
        $log->save();
        
        return Response::forge(json_encode(array('success' => true)));
    }

    public function action_add(){
      
      
        //make sure user is authenticated and an admin
        if(!Auth::member(\Config::get('timetrack.admin_group'))){
            Response::redirect('root/home');
        }
        
        //find the date of the log
        $start_stamp = Input::post('start_stamp');
        $date_string = date("M j Y", $start_stamp);
        
        //get the start timestamp
        $clockin = strtotime(Input::post('start_time')." ".$date_string);
        $clockout = strtotime(Input::post('end_time')." ".$date_string);

        $new_log = Model_Timelog::forge();
        $new_log->clockin = $clockin;
        $new_log->clockout = $clockout;
        $new_log->user_id = Input::post('user_id');
        $success = $new_log->save();
        
        return Response::forge(json_encode(array('success' => $success)));
      
    }
    
    /**
     * determine if a log is valid (does not overlap already existing logs
     */
    public function action_valid_log(){
      
      //get date for this log
      $date_string = date("M j Y", Input::post('day_stamp'));
      
      //get log start and end times
      $start = strtotime(Input::post('start')." ".$date_string);
      $end = strtotime(Input::post('end')." ".$date_string);
      
      //get id if one is specified
      $id = is_null(Input::post('id')) ? 0 : Input::post('id');
      $user_id = is_null(Input::post('user_id')) ? 0 : Input::post('user_id');
      
      //find any logs that start during the range (if any)
      $start_during_range = Model_Timelog::find('all', array(
          'where' => array(
              array('clockin', '>', $start),
              array('clockin', '<', $end),
              array('id', '!=', $id),
              array('user_id', '=', $user_id),
          )
      ));
      
      //find any logs that end during the range (if any)
      $end_during_range = Model_Timelog::find('all', array(
          'where' => array(
              array('clockout', '>', $start),
              array('clockout', '<', $end),
              array('id', '!=', $id),
              array('user_id', '=', $user_id),
          )
      ));
      
      //find any logs that start before and end after the range (if any)
      $contain_range = Model_Timelog::find('all', array(
          'where' => array(
              array('clockin', '<', $start),
              array('clockout', '>', $end),
              array('id', '!=', $id),
              array('user_id', '=', $user_id),
          )
      ));
      
      
      if(empty($start_during_range) 
              && empty($end_during_range) 
              && empty($contain_range)){
        return Response::forge(json_encode(true));
      } else {
        return Response::forge(json_encode(false));
      }
      
      
    }
    
    /**
     * remove a log from the database
     */
    public function action_remove(){
      
       //make sure user is authenticated and an admin
      if(!Auth::member(\Config::get('timetrack.admin_group'))){
          Response::redirect('root/home');
      }
      
      $id = Input::post('id');
      
      $log = Model_Timelog::find($id);
      $log->delete();

      return Response::forge(json_encode('true'));
      
    }

    /**
     * Create the HTML partial pages for each day in the time period that
     * starts at "period_start"
     * @param type $id - ID of user whose logs are displayed
     * @param type $period_start - timestamp for period start
     * @param type $round - whether or not to round times in display
     */
    private function forge_days($id, $period_start, $round){
      
        //first day in the period
        $start_day = $period_start;
        
        //last day of the period
        $end_day = strtotime("+ ".\Config::get
                ('timetrack.period_length')." -1 day", $start_day);
        
        $curr_day_start = $start_day;
        $overall_total = 0;
        
        //cycle through days and grab all the logs for the user for that
        //day
        while($curr_day_start <= $end_day){

            //setup data for the view for this day
            list($day_total, $day_view) 
                    = $this->forge_day($id, $curr_day_start, $round);
            
            $overall_total += $day_total;
            $day_views[] = $day_view;
            
            //update control variable
            $curr_day_start += (24*60*60);//add one day in seconds

        }
        
        return array($overall_total, $day_views);
      
    }
    
    
}//end class


?>
