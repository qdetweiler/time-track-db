<?php

/*
 * The logs controller handles all events related to timelogs, including
 * CRUD and displaying log reports
 */

class Controller_Logs extends Controller_Template {

    /**
     * Build the page responsible for enabling user to view logs
     * 
     * Note:  Most of the controls here will be handled with ajax
     */
    public function action_display(){
        
        //make sure user is authenticated
        $id_info = Auth::get_user_id();
        $id = $id_info[1];
        if(!$id){
            Response::redirect('root/home');
        }
        
        //set admin data for view
        if(Auth::member(\Config::get('timetrack.admin_group'))){
            $this->admin_display($id);
        } else {
            $this->standard_display($id);
        }
        
        //setup css for page
        $this->template->page_css = array('logs_display.css','logs_logtable.css'
            , 'jquery-ui-1.10.3.custom.min.css');
        
        //setup javascript for page
        $this->template->page_js = array('logs-display.js', 'jquery.form.min.js'
            ,'jquery-ui-1.10.3.custom.min.js','jquery-ui-timepicker-addon.js'
            ,'logs-logtable.js');
    }
    
    /**
     * Build the logs display page for an administrative user
     * @param type $id
     */
    private function admin_display($id){
        
        //setup selected user
        $user_id = Input::param('id');
        if(!is_null($user_id)){
            //admin is viewing logs for another user
            $data['selected_id'] = $user_id;
        } else {
            //admin is viewing own logs
            $data['selected_id'] = $id;
        }
        
        //get data range
        //$data['range'] = $this->get_range($data['selected_id']); //get range for only specified user
        $data['range'] = $this->get_range('all');

        //setup users
        $users = Model_User::find('all');
        $data['users'] = $users;

        //setup other variables
        $data['id'] = $id;
        $data['admin'] = true;

        //setup view
        $this->template->title = "Timelogs";
        $this->template->content = View::forge('logs/display', $data);
        
    }
    
    /**
     * Return a map representing the set of pay periods available for
     * the given user or for all users
     * @param type mixed - id of user if specified or string 'all' for all users
     * @return type map of pay period ranges
     */
    private function get_range($id){
        
        //get all the appropriate logs for either the given user
        //or all users depending on the id passed in
        if($id == 'all'){
            $first_log = Model_Timelog::find('first', array(
                'order_by' => array('clockin' => 'asc'),
            ));
//            $last_log = Model_Timelog::find('first', array(
//                'order_by' => array('clockin' => 'desc'),
//            ));
        } else {
            $first_log = Model_Timelog::find('first', array(
                'where' => array(
                    array('user_id', $id),
                ),
                'order_by' => array('clockin' => 'asc'),
            ));
//            $last_log = Model_Timelog::find('first', array(
//                'where' => array(
//                    array('user_id', $id),
//                ),
//                'order_by' => array('clockin' => 'desc'),
//            ));
        }
        
        //there are no logs for this user
        if(is_null($first_log)){
            $range = array();
            
        //there are logs for this user
        } else {
//            $end_log = $last_log->clockout == null ? $last_log->clockin : $last_log->clockout;
          
            //range should end today
            $end_time = time();
            $range = $this->get_date_range($first_log->clockin, $end_time);
        }
        
        return $range;
        
    }
    
    /**
     * Build the log display for a standard user
     * @param type $id
     */
    private function standard_display($id){
        
        //setup range
        $data['range'] = $this->get_range($id);
        
        //setup other variables
        $data['admin'] =false;
        $data['id'] = $id;
        
        //setup view
        $this->template->title = "Timelogs";
        $this->template->content = View::forge('logs/display', $data);
        
    }
    
    
    public function action_test(){
      
        $id = 3;
      
        $timelog = Model_Timelog::forge();
        $timelog->user_id = $id;
        $timelog->clockin = strtotime("last Monday 8:23am");
        $timelog->clockout = strtotime("last Monday 12:23pm");
        $timelog->save();
        
                $timelog = Model_Timelog::forge();
        $timelog->user_id = $id;
        $timelog->clockin = strtotime("last Monday 1:38pm");
        $timelog->clockout = strtotime("last Monday 5:03pm");
        $timelog->save();
        
                $timelog = Model_Timelog::forge();
        $timelog->user_id = $id;
        $timelog->clockin = strtotime("last Tuesday 8:02am");
        $timelog->clockout = strtotime("last Tuesday 9:16pm");
        $timelog->save();
        
                $timelog = Model_Timelog::forge();
        $timelog->user_id = $id;
        $timelog->clockin = strtotime("last Thursday 8:29am");
        $timelog->clockout = strtotime("last Thursday 1:18pm");
        $timelog->save();
        
                $timelog = Model_Timelog::forge();
        $timelog->user_id = $id;
        $timelog->clockin = strtotime("last Thursday 1:45pm");
        $timelog->clockout = strtotime("last Thursday 3:30pm");
        $timelog->save();
        
                $timelog = Model_Timelog::forge();
        $timelog->user_id = $id;
        $timelog->clockin = strtotime("last Thursday 4:15pm");
        $timelog->clockout = strtotime("last Thursday 6:23pm");
        $timelog->save();
        
                $timelog = Model_Timelog::forge();
        $timelog->user_id = $id;
        $timelog->clockin = strtotime("last Saturday 8:23am");
        $timelog->clockout = strtotime("last Saturday 12:23pm");
        $timelog->save();
        
                        $timelog = Model_Timelog::forge();
        $timelog->user_id = $id;
        $timelog->clockin = strtotime("last Saturday 2:27am");
        $timelog->clockout = 0;
        $timelog->save();
        
        $d['timelogs'] = Model_Timelog::find('all', array(
            'where' => array(
                array('user_id', $id),
                array('clockout','!=', 0),
            ),
            'order_by' => array('clockin'=>'asc'),
        ));
        $d['first'] = Model_Timelog::find('first', array(
            'where' => array(
                array('user_id', $id),
            ),
            'order_by' => array('clockin'=>'asc'),
        ));
        $d['last'] = Model_Timelog::find('first', array(
            'where' => array(
                array('user_id', $id),
            ),
            'order_by' => array('clockin' => 'desc'),
        ));
        
        foreach($d['timelogs'] as $timelog){
            $formatted[] = date('m/d g:i:s a',$timelog->clockin).", ".date('m/d g:i:s a',$timelog->clockout);
        }
        $d['formatted'] = $formatted;
        
        $data['data_set'] = $d;
        
        $this->template->content = View::forge('root/test',$data);

    }

    /**
     * Return JSON encoded range information
     * This function is primarily designed to be used with ajax
     * @return type
     */
    public function action_date_range(){
        $id = Input::post('id');
        return Response::forge(json_encode($this->get_range($id)));
    }
    
    /**
     * Return an array of date range strings mapped to pairs of
     * timestamp values representing each week a user has timelogs in the
     * system
     * @param type $id of user to get ranges for
     */
    private function get_date_range($first_timelog, $last_timelog){
                
        //get timestamp for first and last monday
        $first_week = strtotime("previous ".\Config::get('timetrack.period_start_day'), strtotime("+ 1 day", $first_timelog));
        $last_week = strtotime("previous ".\Config::get('timetrack.period_start_day'), strtotime("+ 1 day", $last_timelog));
        
        //add first period
        $end_first = $this->get_period_end($first_week);
        $range[] = array(
                'string' => $this->date_range_string($first_week, $end_first),
                'start'  => $first_week,
                'end' => $end_first
        );
        
        //add other periods
        $period_start = $first_week;
        $period_end = $this->get_period_end($period_start);
        while($period_end < $last_week){
            $period_start = strtotime("+ ".\Config::get('timetrack.period_length'), $period_start);
            $period_end = $this->get_period_end($period_start);
            $range[] = array(
                'string' => $this->date_range_string($period_start, $period_end),
                'start'  => $period_start,
                'end' => $period_end
            );
        }
        
        return $range;
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
    private function date_range_string($start_stamp, $end_stamp){
        $format = \Config::get('timetrack.range_date_format');
        return date($format, $start_stamp)." - ".date($format, $end_stamp);
    }
    
    public function action_logtable2(){
        
        //get period information
        $period_start = Input::post('period');
        
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
        
        //set whether or not to round
        $round = (!is_null(Input::post('round'))) ? true : false;
        
        //for each user, construct information for
        //the view
        foreach($user_list as $u){
            
            //retrieve logs for this user
            list($days, $overall_total) = $this->fetch_logs_for_period($u->id, $round, $period_start);
            
            //setup data for user
            $usr['days'] = $days;
            $usr['id'] = $u->id;
            $usr['total'] = Util::sec2hms($overall_total);
            $usr['name'] = $u->fname." ".$u->lname;
            $users[] = $usr;
            
        }
        
        $data['admin'] = Auth::member(\Config::get('timetrack.admin_group'));
        $data['users'] = $users;
        
        $display_type = Input::post('display_type');
        
        //return the appropriate view based on the selected display type
        
        if($display_type == 'all'){
          return new Response(View::forge('logs/logtable2', $data));
        } else if($display_type == 'day_totals'){
          return new Response(View::forge('logs/logtable2_day_totals', $data));
        } else if($display_type == 'period_totals'){
          return new Response(View::forge('logs/logtable2_period_totals', $data));
        }
        
    }
    
    /**
     * Fetch an array of formatted log data containing all the logs for the
     * specified user split into days and the total amount of time logged
     * 
     * Data is returned in the following format:
     * array(*array of DayLogContainer objects*, *total time recorded*)
     * 
     * @param type $id - id of the user
     * @param type $round - true to round values, false to leave un-rounded
     * @param type $start - 12am on the first day of the period
     */
    private function fetch_logs_for_period($id, $round, $start){
        
        //first day in the period
        $start_day = $start;
        
        //last day of the period
        $end_day = strtotime("+ ".\Config::get
                ('timetrack.period_length')." -1 day", $start_day);
        
        $overall_total = 0;
        $curr_day_start = $start_day;
        
        //cycle through days and grab all the logs for the user for that
        //day
        while($curr_day_start <= $end_day){
            
            //fetch logs for the day
            $l = $this->fetch_logs_for_day($id, $round, $curr_day_start);
            $day_logs[] = $l;
            $overall_total += $l->total_time;
            
            //if the user was still clocked in on the day fetched, consume the
            //rest of the days
            if($l->clocked_out == false){
                
                //move to the next day
                $curr_day_start += (24*60*60);//add one day in seconds
                
                while($curr_day_start <= $end_day){
                    
                    $dlc = new DayLogContainer();
                    $dlc->clocked_out = false;
                    $dlc->day_start = $curr_day_start;
                    $dlc->day_label = date(\Config::get
                        ('timetrack.log_date_format'), $curr_day_start);
                    $dlc->total_time_string = "N/A";
                    $day_logs[] = $dlc;
                    $curr_day_start += (24*60*60);//add one day in seconds
                    
                }
            }
            
            $curr_day_start += (24*60*60);//add one day in seconds
        }
        
        return array($day_logs, $overall_total);
    }
    
    /**
     * Fetch all the logs for the given user that fit on the day specified
     * @param type $id - id of the user
     * @param type $round - whether or not to round logs
     * @param type $day_start - timestamp that equals 12am on the desired day
     * @return type
     */
    private function fetch_logs_for_day($id, $round, $day_start){
        
        //end of the day
        $day_end = $day_start + (24*60*60)-1; //one day minus one second in seconds
        
        //variable to hold the logs
        $day_log = new DayLogContainer();
        $day_log->day_start = $day_start;
        $day_log->day_label = date(\Config::get
                ('timetrack.log_date_format'), $day_start);
            
        //fetch all logs for the day that have been clocked out
        $logs_for_day = Model_Timelog::find('all', array(
            'where' => array(
                array('user_id', $id),
                array('clockin','>=',$day_start),
                array('clockout', '<=', $day_end),
                array('clockout', '!=', 0),
            ),
            'order_by' => array('clockin' => 'asc'),
        ));

        //fetch any logs for the day that have not been clocked out
        $log_sans_clockout = Model_Timelog::find('last', array(
            'where' => array(
                array('user_id', $id),
                array('clockin', '>=', $day_start),
                array('clockin', '<=', $day_end),
                array('clockout', 0),
            ),
        ));
        
        //there are full logs for this day
        if(!empty($logs_for_day)){

            $first = true;
            foreach($logs_for_day as $log){

                $lg = $this->parse_log($log, $round, false);
              
                //first log for the day
                if($first){

                    $day_log->first_log = $lg;
                    $first = false;

                //any additional logs
                } else {
                    $day_log->additional_logs[] = $lg;
                }
                
                //add log time to the total time
                $day_log->total_time += $lg->time;
                
            }
            
            //one last partial log exists for this day
            if(!is_null($log_sans_clockout)){
              
              $day_log->additional_logs[] = $this->parse_log($log_sans_clockout, $round, true);
              
            }
            

        //there is only a partial log for this day
        } else if(!is_null($log_sans_clockout)){
          
          $day_log->first_log = $this->parse_log($log_sans_clockout, $round, true);
          
        }

        //set string representing total of full logs
        $day_log->total_time_string = Util::sec2hms($day_log->total_time);
        
       
        return $day_log;
    }
    
    /**
     * Create a LogInfo object based off an actual timelog
     * @param type $log - timelog object to parse
     * @param type $round - whether to round time values
     * @param type $partial - whether this is a partial log
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
        $lg = new LogInfo();
        $lg->id = $log->id;
        $lg->clockin = $clockin;
        $lg->clockout = ($partial) ? 0 : $clockout;
        $lg->clockin_string = date(\Config::get('timetrack.log_time_format'), $clockin);
        $lg->clockout_string = ($partial) ? 'Not Clocked Out' : date(\Config::get('timetrack.log_time_format'), $clockout);
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
    
    
}//end class





class DayLogContainer{
    
    //data contained in a LogInfo object
    public $day_start = 0;          //timestamp equaling 12am on the day
    public $day_label = '';         //label for the day
    public $first_log = null;       //first log of the day
    public $additional_logs = array();    //any additional logs for the day
    public $total_time = 0;         //total amount of time recorded on the day
    public $total_time_string = ''; //string representing total time recorded
    public $clocked_out = true;     //true unless the user is still logged in
    
}

class LogInfo{
  
    //data tracked for a log
    public $id = 0;                 //id of the log
    public $clockin = 0;            //log clockin timestamp
    public $clockout = 0;           //log clockout timestamp
    public $clockin_string = '';    //string representing log clockin
    public $clockout_string = '';   //string representing log clockout
    public $time = 0;               //lenth of time represented by log
    public $time_string = '';       //string representing log time
}
?>
