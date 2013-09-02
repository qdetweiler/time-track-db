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
        
        //setup local javascript variables
        $lmi = 'var log_min_interval = '.\Config::get('timetrack.log_interval').';';
        $this->template->script = View::forge('script',
                array('js' => $lmi));
        
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

        //find out if we are supposed to show older logs
        $show_older = Session::get('show_older');
        $limit = (is_null($show_older) || !$show_older);
        
        //get the set of pay periods available
        $data['period_options'] = $this->forge_period_options('all', $limit);
        
        //if there are no logs for this user, disable the update button
        $data['update_disabled'] = ($this->first_log_clockin('all') == 0) ? true : false;

        //setup users
        $users = Model_User::find('all');
        $data['users'] = $users;

        //setup other variables
        $data['id'] = $id;
        $data['admin'] = true;
        $data['control_form_action'] = Uri::create('logs/logtable3');
        $data['older_logs_label'] = ($limit) ? 'show older logs' : 'hide older logs';
        
        //generate content section
        $this->template->content = View::forge('logs/display', $data);

    }
    
    /**
     * Toggle whether or not older logs should be shown
     */
    public function action_toggle_older(){
      
      //if this operation was not initiated by an administrator, redirect
      //to the home page
      if(!Auth::member(\Config::get('timetrack.admin_group'))){
        Response::redirect('root/home');
      }
      
      $show_older = Session::get('show_older');
      Session::set('show_older', !$show_older);
      
      Response::redirect('logs/display');
      
    }
    
    /**
     * Return the Unix timestamp representing the clockin time of the first
     * log for the specified user or the first overall log if the string
     * 'all' is passed in instead of an ID.  
     * 
     * If "limit" is true, first log is limited by the
     * maximum number of time periods to display (max_periods config option)
     * 
     * @param type mixed - id of user if specified or string 'all' for all users
     * @param type boolean default 'true' - whether or not to limit the oldest
     *                                      log accepted as "first log"
     * @return timestamp of first log
     *         or 0 if there is not
     */
    private function first_log_clockin($id, $limit = true){
      if($limit){
        $time_limit = $this->round_to_period(time()) - 
                ((\Config::get('timetrack.max_periods')-1) 
                * \Config::get('timetrack.period_length_seconds'));
        $time_limit = max(array(\Config::get('timetrack.first_period_start'), $time_limit));
      } else {
        $time_limit = -1;
      }
      
      //get first log for either the given user
      //or all users depending on the id passed in
      if($id == 'all'){
          $first_log = Model_Timelog::find('first', array(
              'where' => array(
                array('clockin', '>=', $time_limit)  
              ),
              'order_by' => array('clockin' => 'asc'),
          ));
      } else {
          $first_log = Model_Timelog::find('first', array(
              'where' => array(
                  array('user_id', $id),
                  array('clockin', '>=', $time_limit)
              ),
              'order_by' => array('clockin' => 'asc'),
          ));
      }
        
      return (is_null($first_log)) ? 0 : $first_log->clockin;
    }
    
    /**
     * Forge select options for available log types
     * @param type $type - type to pre-select (or -1 for blank)
     */
    private function forge_type_options($type){
      
      //create data variable for view
      $data['types'] = array();
      
      //grab types from configuration file
      $types = \Config::get('timetrack.log_types');
      
      //for each type, setup information for the view
      foreach($types as $type_v => $type_s){
        $t['type_val'] = $type_v;
        $t['type_string'] = $type_s;
        $t['selected'] = ($type_v == $type);
        array_push($data['types'], $t);
      }
      
      //setup selected value string (type = -1 blanks the string)
      $data['selected_type'] = ($type === -1) ? '' 
              : \Config::get('timetrack.log_types.'.$type);
      
      return View::forge('logs/partials/type_options', $data);
      
    }
    
    /**
     * Forge select options for displaying PTO logs
     * @param type $type
     */
    private function forge_pto_options($type){
      
      $data['types'] = array();
      $types = Config::get('timetrack.log_types');
      
      //for each type, setup information for the view
      foreach($types as $type_v => $type_s){
        $t['type_val'] = $type_v;
        $t['type_string'] = $type_s;
        $t['selected'] = ($type_v == $type);
        array_push($data['types'], $t);
      }
      
      array_shift($data['types']);//remove '0' entry
      
      //add entry for 'all'
      $t['type_val'] = 'all';
      $t['type_string'] = 'All';
      $t['selected'] = ($t['type_val'] == $type);
      array_unshift($data['types'], $t);
      
      return View::forge('logs/partials/pto_type_options', $data);
      
    }
    
    /**
     * Round the given timestamp back to the start of the containing time period
     * @param type $timestamp
     */
    private function round_to_period($timestamp){
      
      $distance = $timestamp - \Config::get('timetrack.first_period_start');
      $difference = $distance % \Config::get('timetrack.period_length_seconds');
      return $timestamp - $difference;
      
    }
    
    /**
     * Construct the set of pay period options for the specified user
     * or for all users if 'all' is passed instead of an ID.  
     * Note:  The return value of this function is a 
     * formatted HTML partial-page constructed using "View::forge()"
     * @param limit - whether to limit pay periods to number specified in
     *                config as "max_periods"
     */
    private function forge_period_options($id, $limit){
      
      $start_time = $this->first_log_clockin($id, $limit);
      $end_time = time(); //fetch all pay periods from the first to today
      
      //there are no available pay periods for the specified user
      if($start_time == 0){
        $data['periods'] = array();
        return View::forge('logs/partials/period_options');
      }
      
      //get timestamp for monday on the first and last pay periods available
      $first_week = $this->round_to_period($start_time);
      $last_week = $this->round_to_period($end_time);
        
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
        
        //get period information
        $period_start = Input::post('period');

        //set whether or not to round
        $round = (!is_null(Input::post('round'))) ? true : false;
        
        //set whether or not to show type
        $showtype = (!is_null(Input::post('showtype'))) ? true : false;
        
        $display_type = Input::post('display_type');
        switch($display_type){
        case 'all':
        case 'day_totals':
          
          $full = ($display_type === 'all');
          $data['admin'] = Auth::member(\Config::get('timetrack.admin_group'));
          
          //for each user, construct information for
          //the view
          foreach($user_list as $u){

            //create day partial views and get overall total\
            
            list($overall_total, $day_views) 
                    = $this->forge_days($u->id, $period_start, $data['admin']
                            ,$round, $full, $showtype);

            //setup data for view
            $usr['name'] = $u->fname.' '.$u->lname;
            $usr['day_views'] = $day_views;
            $usr['period_total'] = Util::sec2hms($overall_total);
            $users[] = $usr;

          }

          $data['users'] = $users;
          
          $data['full'] = $full;
          $data['showtype'] = $showtype;

          return View::forge('logs/logtable3', $data);

          break;
          
        case 'period_totals':
          
          $data['users'] = array();
          
          foreach($user_list as $u){
            
            $logs_for_period = Model_Timelog::find('all', array(
              'where' => array(
                  array('user_id', $u->id),
                  array('clockin','>=',$period_start),
                  array('clockout', '<=', $this->get_period_end($period_start)),
                  array('clockout', '!=', 0),
              ),
              'order_by' => array('clockin' => 'asc'),
            ));
          
            $total = 0;
            foreach($logs_for_period as $log){
              $parsed = $this->parse_log($log, true, false);
              $total += $parsed->time;
            }
            
            $usr['total'] = Util::sec2hms($total);
            $usr['name'] = $u->fname.' '.$u->lname;
            array_push($data['users'], $usr);
          
          }
          
          return View::forge('logs/logtable2_period_totals', $data);
          
          
          break;
          
        }
      
    }
    
    /**
     * generate the display used for the PTO page
     */
    public function action_pto(){
       
        //make sure user is authenticated
        $id_info = Auth::get_user_id();
        $id = $id_info[1];
        if(!$id){
            Response::redirect('root/home');
        }
        
        $user_id = Input::param('id');
        if(!is_null($user_id)){
            //admin is viewing logs for another user
            $data['selected_id'] = $user_id;
        } else {
            //admin is viewing own logs
            $data['selected_id'] = $id;
        }
        
        //create control form action
        $data['control_form_action'] = Uri::create('logs/pto_table');
        
        //setup info for view
        $data['id'] = $id;
        $data['admin'] = Auth::member(\Config::get('timetrack.admin_group'));
        $data['users'] = Model_User::find('all');
        $data['start_date'] = date(\Config::get('timetrack.range_date_format'),
                \Config::get('timetrack.first_period_start'));
        $data['end_date'] = date(\Config::get('timetrack.range_date_format'),
                time());
        
        $data['type_selection'] = $this->forge_pto_options('all');
        
        //setup css for page
        $this->template->css = array('logs_pto.css','logs_pto_table.css');
        
        //setup javascript for page
        $this->template->js = array('logs_pto.js', 'logs_pto_table.js',
            'jquery-ui-timepicker-addon.js');
        
        //setup title
        $this->template->title = "Paid Time Off";
        $this->template->content = View::forge('logs/pto', $data);
        
      
    }
    
    /**
     * return the partial view that includes the PTO table
     */
    public function action_pto_table(){
      
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
        
        $type = Input::post('type');
        $start_date = Input::post('start_date');
        $end_date = Input::post('end_date');
        $data['show_type'] = $type == 'all'; //only display type if all types are being shown
        $data['users'] = array();
        $data['control_form_action'] = Uri::create('logs/pto_table');
        
        foreach($user_list as $user){
          
          $u['name'] = $user->fname.' '.$user->lname;
          list($total, $views) = 
                  $this->forge_pto_display($user->id, $type, $start_date, $end_date);
          
          $u['total'] = Util::sec2hms($total);
          if($total == 0){
            $u['no_logs_msg'] = "None found.";
          }
          $u['log_views'] = $views;
          array_push($data['users'], $u);
        }
      
        return View::forge('logs/pto_table', $data);
    }
    
    /**
     * Create a set of partial displays and a total for displaying information
     * about PTO logs
     * @param type $user_id - user for whom to show the logs
     * @param type $type - type of logs to display
     * @param type $start_date - first date from which to show logs
     * @param type $end_date - last date from which to show logs
     */
    private function forge_pto_display($user_id, $type, $start_date, $end_date){

      $total = 0;
      
      //setup type rule for getting logs
      $type_rule = ($type == 'all') 
              ? array('type', '>', '0') //even if we want all types, we want to skip standard logs 
              : array('type', '=', $type);
      
      //get the logs
      $logs = Model_Timelog::find('all', array(
          'where' => array(
              array('user_id', $user_id),
              array('clockin', '>=', strtotime($start_date)),
              array('clockout', '<=', (strtotime($end_date)+(86399))),
              $type_rule,
          )
      ));
      
      //create the partial views
      $views = array();
      foreach($logs as $log){
        list($time, $view) = $this->forge_pto_log($log, $type=='all');
        $total += $time;
        array_push($views, $view);
      }
      
      return array($total, $views);
    }
    
    /**
     * Construct the view for a single PTO log based on a timelog object
     * and return the log time and a view
     * @param type $log
     * @param type $showtype - whether or not to display type information
     */
    private function forge_pto_log($log, $showtype){

      $parsed = $this->parse_log($log, true);
      $time = $parsed->time;
      
      $data['day'] = date('m/d/y', $log->clockin);
      $data['range'] = $parsed->clockin_string.' - '.$parsed->clockout_string;
      $data['total'] = Util::sec2hms($time);
      $data['show_type'] = $showtype;
      $data['type'] = \Config::get('timetrack.log_types.'.$log->type);
      
      $view = View::forge('logs/partials/pto_log', $data);
      
      return array($time, $view);
    }
    
    /**
     * Construct the partial view that displays all log information for a
     * given day
     * @param type $user_id - user ID whose data will be displayed
     * @param type $day_start - start of the day (Unix timestamp)
     * @param admin - whether or not the user is an admin
     * @param type $round - whether or not to round time values
     * @param showtype - whether or not to show the log type
     * @return - array(total time for the day, day view)
     */
    private function forge_day_full($user_id, $day_start, $admin, $round, $showtype){
      
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
          $log['type'] = $this->forge_type_options(-1);
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
          $log['type'] = $this->forge_type_options($log_sans_clockout->type);
          $log['controls'] = View::forge('logs/partials/edit_controls');
          array_push($data['logs'], $log);
          
          //construct log display for adding a log
          if($admin){
            $log['id'] = 0;
            $log['range'] 
                    = View::forge('logs/partials/log_range', array('no_logs_msg' => ''));
            $log['time'] = '';
            $log['type'] = $this->forge_type_options(-1);
            $log['controls'] 
                    = View::forge('logs/partials/add_controls', array('disabled' => false));
            array_push($data['logs'], $log);
          }
          
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
            $log['type'] = $this->forge_type_options($log_for_day->type);
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
            $log['type'] = $this->forge_type_options($log_sans_clockout->type);
            $log['controls'] = View::forge('logs/partials/edit_controls');
            array_push($data['logs'], $log);
            
          }
          
          //create the add log entry for after all valid logs
          if($admin){
            $log['id'] = 0;
            $log['range'] 
                    = View::forge('logs/partials/log_range', array('no_logs_msg' => ''));
            $log['time'] = '';
            $log['type'] = $this->forge_type_options(-1);
            $log['controls'] = $this->forge_add_controls($user_id, $day_start);
            array_push($data['logs'], $log);
          }
          
        }
        
        //setup data for the view
        $data['day_start'] = $day_start;
        $data['day_label'] = date(\Config::get('timetrack.log_date_format'), $day_start);
        $data['showtype'] = $showtype;
        $data['log_form_action'] = Uri::create('logs/CRUD');
        $data['user_id'] = $user_id;
        $data['admin'] = Auth::member(\Config::get('timetrack.admin_group'));
          
        return array($day_total, View::forge('logs/partials/day', $data));
        
    }
    
    /**
     * Forge the display required if showing only daily totals for a user
     * @param type $user_id
     * @param type $day_start
     */
    private function forge_day_totals_only($user_id, $day_start){
      
      //end of the day
      $day_end = $day_start + (24*60*60)-1; //one day minus one second in seconds
      $day_total = 0;

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
      
      foreach($logs_for_day as $log){
        $parsed = $this->parse_log($log, true, false);
        $day_total = $parsed->time;
      }
      
      $data['day_label'] = date(\Config::get('timetrack.log_date_format'), $day_start);
      $data['time'] = Util::sec2hms($day_total);
      
      return array($day_total, View::forge('logs/partials/day_totals_only', $data));
      
    }
    
    /**
     * Perform addition, removal, or deletion of a log
     * @return JSON map with data appropriate to the desired operation
     * Note: returned JSON map will always include 'show_msg' (bool) and 'msg' (string) fields 
     */
    public function action_CRUD(){
      
      //if this operation was not initiated by an administrator, redirect
      //to the home page
      if(!Auth::member(\Config::get('timetrack.admin_group'))){
        Response::redirect('root/home');
        
      //if this operation was not submitted properly, redirect to the home page
      } else if(is_null(Input::post('action'))){
        Response::redirect('root/home');
      }
      
      switch(Input::post('action')){
        case 'remove':
          return $this->remove_log(Input::post('log_id'));
          break;
        
        case 'add':
          return $this->add_log();
          break;
        
        case 'edit':
          return $this->edit_log();
          break;
      }
      
      
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
    private function parse_log($log, $round, $partial = false){
      
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
    public function edit_log(){
        
        //fetch the log to be edited
        $log = Model_Timelog::find(Input::post("log_id"));
        
        //find the date of the log
        $date_string = date("M j Y", $log->clockin);
        
        //get the start timestamp
        $clockin_new = strtotime(Input::post('start_time')." ".$date_string);
        
        //get the end timestamp
        $end_time = Input::post('end_time');
        $clockout_new = (preg_match('/^\d\d?:\d\d (am|AM|Am|PM|pm|Pm)$/', $end_time)) 
                ? strtotime($end_time." ".$date_string) : 0;
        
        //make sure edited time is valid
        $invalid_msg = $this->valid_log($clockin_new, $clockout_new, 
                Input::post('user_id'), Input::post('log_id'));
        if(!is_null($invalid_msg)){
          return Response::forge(json_encode(array('success' => false,
                                                   'show_msg' => true,
                                                   'msg' => $invalid_msg)));
        }
        
        //if clockout is 0 but clockout_new is not zero, find the
        //user associated with the log and clock him / her out
        if($log->clockout == 0 && $clockout_new > 0){
          
          $user = Model_User::find($log->user_id);
          $user->clocked_in = false;
          $user->save();
          
        }
        
        //edit log clock time
        $log->clockin = $clockin_new;
        $log->clockout = $clockout_new;
        
        //edit log type
        $type = Input::post('type');
        if(!is_null($type)){
          $log->type = $type;
        }
        
        $log->save();
        
        return Response::forge(json_encode(array('success' => true,
                                                 'show_msg' => false,
                                                 'msg' => 'Log change saved.')));
    }

    private function add_log(){

        //find the date of the log
        $day_start = Input::post('day_start');
        $date_string = date("M j Y", $day_start);
        
        //get the start timestamp
        $clockin = strtotime(Input::post('start_time')." ".$date_string);
        $clockout = strtotime(Input::post('end_time')." ".$date_string);

        //verify that this is a valid log
        $invalid_msg = $this->valid_log($clockin, $clockout, 
                Input::post('user_id'), Input::post('log_id'));
        if(!is_null($invalid_msg)){
          return Response::forge(json_encode(array('success' => false,
                                                   'show_msg' => true,
                                                   'msg' => $invalid_msg)));
        }
        
        $new_log = Model_Timelog::forge();
        $new_log->clockin = $clockin;
        $new_log->clockout = $clockout;
        $new_log->user_id = Input::post('user_id');
        
        $type = Input::post('type');
        if(!is_null($type)){
          $new_log->type = $type;
        } else {
          $new_log->type =0;
        }
        
        $success = $new_log->save();
        
        return Response::forge(json_encode(array('success' => true,
                                                 'show_msg' => false,
                                                 'msg' => 'Log Added.')));
      
    }
    
    /**
     * Determine if a log is valid (does not overlap already existing logs
     * @param type $clockin_new - clockin time for log
     * @param type $clockout_new - clockout time for log
     * @param type $user_id - user ID that will be associated with log
     * @param type $log_id - ID of log (if this is a log being edited)
     * @return type String - error message if log is invalid, null if it is okay
     */
    private function valid_log($clockin_new, $clockout_new, $user_id, $log_id){

      //this is an open log
      if($clockout_new === 0){
        
        //find any logs that start or end after the beginning of this log
        $s_or_e = Model_Timelog::query()
                ->where('id', '!=', $log_id)
                ->where('user_id', '=', $user_id)
                ->and_where_open()
                  ->where('clockin','>', $clockin_new)
                  ->or_where('clockout', '>', $clockin_new)
                ->and_where_close()->get();
        
        if(!empty($s_or_e)){
          return 'Logs cannot overlap';
        } else {
          return null;
        }
        
        
      } else {
      
        //find any logs that start during the range (if any)
        $start_during_range = Model_Timelog::find('first', array(
            'where' => array(
                array('clockin', '>', $clockin_new),
                array('clockin', '<', $clockout_new),
                array('id', '!=', $log_id),
                array('user_id', '=', $user_id),
            )
        ));

        //find any logs that end during the range (if any)
        $end_during_range = Model_Timelog::find('first', array(
            'where' => array(
                array('clockout', '>', $clockin_new),
                array('clockout', '<', $clockout_new),
                array('id', '!=', $log_id),
                array('user_id', '=', $user_id),
            )
        ));

        //find any logs that start before and end after the range (if any)
        $contain_range = Model_Timelog::find('first', array(
            'where' => array(
                array('clockin', '<=', $clockin_new),
                array('clockout', '>=', $clockout_new),
                array('id', '!=', $log_id),
                array('user_id', '=', $user_id),
            )
        ));

        //find any logs that are still open where clockin was prior to the
        //start or end of this log
        $prev_open_log = Model_Timelog::query()
                ->where('clockout', '=', 0)
                ->where('user_id', '=', $user_id)
                ->where('id', '!=', $log_id)
                ->and_where_open()
                  ->where('clockin', '<', $clockin_new)
                  ->or_where('clockin', '<', $clockout_new)
                ->and_where_close()
                ->get();

        if(!is_null($start_during_range) 
                || !is_null($end_during_range) 
                || !is_null($contain_range)){
          return 'Logs cannot overlap';
        } else if (!empty($prev_open_log)){
          return 'A prior log is not clocked out.';
        } else if ($clockout_new > time()){
          return 'Invalid end time.';
        } else {
          return null;
        }
      }
    }
    
    /**
     * remove a log from the database
     */
    private function remove_log($id){
      
      $log = Model_Timelog::find($id);
      $log->delete();

      return Response::forge(json_encode(array('success' => true,
                                               'show_msg' => false,
                                               'msg' => 'Removal Succeeded.')));
      
    }

    /**
     * Create the HTML partial pages for each day in the time period that
     * starts at "period_start"
     * @param type $id - ID of user whose logs are displayed
     * @param type $period_start - timestamp for period start
     * @param admin - whether or not this user is an administrative user
     * @param type $round - whether or not to round times in display
     * @param full - whether to display full information or just day totals
     * @param showtype - whether to display the log type
     */
    private function forge_days($id, $period_start, $admin, $round, $full, $showtype){
      
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
            list($day_total, $day_view) = ($full)
                   ? $this->forge_day_full($id, $curr_day_start, $admin, $round, $showtype)
                   : $this->forge_day_totals_only($id, $curr_day_start);
            
            $overall_total += $day_total;
            $day_views[] = $day_view;
            
            //update control variable
            $curr_day_start += (24*60*60);//add one day in seconds

        }
        
        return array($overall_total, $day_views);
      
    }
    
    
}//end class


?>
