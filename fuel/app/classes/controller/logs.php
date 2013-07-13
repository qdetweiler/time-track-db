<?php

/*
 * The logs controller handles all events related to timelogs, including
 * CRUD and displaying log reports
 */

class Controller_Logs extends Controller_Template {

    const DATE_FORMAT = 'm/d/y';

    /**
     * Build the page responsible for enabling user to view logs
     * 
     * Note:  Most of the controls here will be handled with ajax
     */
    public function action_display(){
        
        //make sure there is an authenticated user
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
        
        //setup css for control section
        $this->template->page_css = array('logs_display.css','logs_logtable.css');
        $this->template->page_js = array('logs-display.js', 'jquery.form.min.js');
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
        $data['range'] = $this->get_range($data['selected_id']);

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
     * Return range data
     * @param type $id
     * @return type
     */
    private function get_range($id){
        
        if($id == 'all'){
            $first_log = Model_Timelog::find('first', array(
                'order_by' => array('clockin' => 'asc'),
            ));
            $last_log = Model_Timelog::find('first', array(
                'order_by' => array('clockin' => 'desc'),
            ));
        } else {
            $first_log = Model_Timelog::find('first', array(
                'where' => array(
                    array('user_id', $id),
                ),
                'order_by' => array('clockin' => 'asc'),
            ));
            $last_log = Model_Timelog::find('first', array(
                'where' => array(
                    array('user_id', $id),
                ),
                'order_by' => array('clockin' => 'desc'),
            ));
        }
        
        //there are no logs for this user
        if(is_null($first_log)){
            $range = array();
            
        //there are logs for this user
        } else {
            $end_log = $last_log->clockout == null ? $last_log->clockin : $last_log->clockout;
            $range = $this->get_date_range($first_log->clockin, $end_log);
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
        
//        $timelog = Model_Timelog::forge();
//        $timelog->user_id = 1;
//        $timelog->clockin = strtotime("Monday 8:23am");
//        $timelog->clockout = strtotime("Monday 12:23pm");
//        $timelog->save();
//        
//                $timelog = Model_Timelog::forge();
//        $timelog->user_id = 1;
//        $timelog->clockin = strtotime("Monday 1:38pm");
//        $timelog->clockout = strtotime("Monday 5:03pm");
//        $timelog->save();
//        
//                $timelog = Model_Timelog::forge();
//        $timelog->user_id = 1;
//        $timelog->clockin = strtotime("Tuesday 8:02am");
//        $timelog->clockout = strtotime("Tuesday 9:16pm");
//        $timelog->save();
//        
//                $timelog = Model_Timelog::forge();
//        $timelog->user_id = 1;
//        $timelog->clockin = strtotime("Thursday 8:29am");
//        $timelog->clockout = strtotime("Thursday 1:18pm");
//        $timelog->save();
//        
//                $timelog = Model_Timelog::forge();
//        $timelog->user_id = 1;
//        $timelog->clockin = strtotime("Thursday 1:45pm");
//        $timelog->clockout = strtotime("Thursday 3:30pm");
//        $timelog->save();
//        
//                $timelog = Model_Timelog::forge();
//        $timelog->user_id = 1;
//        $timelog->clockin = strtotime("Thursday 4:15pm");
//        $timelog->clockout = strtotime("Thursday 6:23pm");
//        $timelog->save();
//        
//                $timelog = Model_Timelog::forge();
//        $timelog->user_id = 1;
//        $timelog->clockin = strtotime("Saturday 8:23am");
//        $timelog->clockout = strtotime("Saturday 12:23pm");
//        $timelog->save();
//        
//                        $timelog = Model_Timelog::forge();
//        $timelog->user_id = 1;
//        $timelog->clockin = strtotime("Saturday 2:27am");
//        $timelog->save();
        
        $d['timelogs'] = Model_Timelog::find('all', array(
            'where' => array(
                array('user_id', 14),
            ),
            'order_by' => array('clockin'=>'asc'),
        ));
        $d['first'] = Model_Timelog::find('first', array(
            'where' => array(
                array('user_id', 14),
            ),
            'order_by' => array('clockin'=>'asc'),
        ));
        $d['last'] = Model_Timelog::find('first', array(
            'where' => array(
                array('user_id', 14),
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
        return date(self::DATE_FORMAT, $start_stamp)." - ".date(self::DATE_FORMAT, $end_stamp);
    }
    
    /**
     * logtable returns the partial view containing tabular information
     * about logs
     */
    public function action_logtable(){
        
        //PHP_Console::log(Input::all());
        
        //get period information
        $period_start = Input::post('period');
        $period_end = $this->get_period_end($period_start);
        
        //retrieve user / users
        $user = Input::post('user');
        //PHP_Console::log($user);
        if(is_null($user)){
            $id = Input::post('id');
            $user_list[] = Model_User::find($id);
            
        } else if($user == 'All'){
            $user_list = Model_User::find('all');
            
        } else {
            $user_list[] = Model_User::find($user);
        }
        
        //PHP_Console::log($user_list);
        //PHP_Console::log(count($user_list));
        
        //set whether or not to round
        $round = (!is_null(Input::post('round'))) ? true : false;
        
        //for each user, construct information for
        //the view
        foreach($user_list as $u){
            
            //fetch logs for the current period
            $timelogs = Model_Timelog::find('all', array(
                'where' => array(
                    array('user_id', $u->id),
                    array('clockin','>=',$period_start),
                    array('clockout', '<=', $period_end),
                ),
                'order_by' => array('clockin' => 'asc'),
            ));
            
            //the above will not include a log with a null end time
            //we need to grab such a record ourselves
            $last_log = Model_Timelog::find('last', array(
                'where' => array(
                    array('clockin', '<=', $period_end),
                    array('user_id', $u->id),
                ),
            ));
            
            //there is a last log
            if(!is_null($last_log)){
                
                //there are no logs except one with a null clockout
                if(empty($timelogs)){
                    array_push($timelogs, $last_log);
                    
                //there is at least one log which may be the same
                //as the last log we fetched
                } else {
                    $last_element = end($timelogs);
                    reset($timelogs);
                    
                    //the last log in the timelogs list is not the same
                    //as the last log we fetched. This means there was
                    //a log with a null clockout
                    if($last_log->id != $last_element->id){
                        array_push($timelogs, $last_log);
                    }
                }
                
            }
            
            //PHP_Console::log(count($timelogs));
            //PHP_Console::log($timelogs);
            
            //split timelogs by days
            list($days, $overall_total) = $this->split_into_days($period_start, $timelogs, $round);
            
            //setup data for user
            $usr['days'] = $days;
            $usr['num_logs'] = count($timelogs);
            $usr['total'] = $overall_total;
            $usr['name'] = $u->fname." ".$u->lname;
            $users[] = $usr;
        }
        
        $data['users'] = $users;
        $data['display_type'] = Input::post('display_type');
        
        //return the view
        return new Response(View::forge('logs/logtable', $data));
    }
    
    /**
     * Split a set of timelogs into days
     * 
     * Returns an array with the following format
     * 
     * [0] => [0] =>    'string' => Sunday-Monday,
     *                  'logs'      => [0]  => 'start' => timestamp,
     *                                      => 'end'   => timestamp
     *                  'total'  => Total time
     * [1] => overall_total
     * 
     * 
     * @param type $start_day
     * @param type $timelogs
     * @param type $round
     */
    private function split_into_days($start_day, $timelogs, $round){
        
        //PHP_Console::log(date('m/d', $start_day));
        
        //end day
        $end_day = strtotime("+ ".\Config::get('timetrack.period_length')." -1 day", $start_day);
        //PHP_Console::log(date('m/d', $end_day));
        
        //setup control variables
        $curr_day = $start_day;//12am on first day
        $curr_day_end = strtotime("+ 1 day - 1 second",$curr_day);//11:59:59pm of first day

        //track overall total for the time period
        $overall_total = 0;
        
        //setup first timelog
        $curr_timelog = array_shift($timelogs);

        //loop through all the days in the time period
        while($curr_day <= $end_day){
            
            //PHP_Console::log(date("m/d/y", $curr_day));
            
            //setup the day
            $day = array();
            $day['string'] = date(\Config::get('timetrack.log_date_format'),$curr_day);
            $day['logs'] = array();
            $day['total'] = 0;

            while($curr_timelog != null //we haven't reached the end of the logs
                    && !is_null($curr_timelog->clockout) //timelog has a clockout time
                    && $curr_timelog->clockout <= $curr_day_end){  //only include timelogs that finish on the current day
                
                //if current log belongs to the current day, add an entry to
                //to the day's logs array
                
                //get rounded values
                $clockin_rounded = Util::roundToInterval($curr_timelog->clockin, \Config::get('timetrack.log_interval')*60);
                $clockout_rounded = Util::roundToInterval($curr_timelog->clockout, \Config::get('timetrack.log_interval')*60);
                
                //we are not rounding
                if(!$round){
                    array_push($day['logs'],array(
                        'start' => date(\Config::get('timetrack.log_time_format'),$curr_timelog->clockin),
                        'end' => date(\Config::get('timetrack.log_time_format'),$curr_timelog->clockout),
                    ));
                    
                //we are rounding
                } else {
                    array_push($day['logs'],array(
                        'start' => date(\Config::get('timetrack.log_time_format'),$clockin_rounded),
                        'end' => date(\Config::get('timetrack.log_time_format'),$clockout_rounded),
                    ));
                }
                
                //add to the total
                $day['total']+= $clockout_rounded - $clockin_rounded;
                $overall_total += $clockout_rounded - $clockin_rounded;
                
                //go on to next timelog
                $curr_timelog = array_shift($timelogs);
            }
            
            //if curr timelog has a null clockout, add a special entry to
            //the logs
            if(!is_null($curr_timelog) //there is still another log
                    && $curr_timelog->clockin >= $curr_day && $curr_timelog->clockin <= $curr_day_end //the current log belongs to the current day
                    && is_null($curr_timelog['clockout'])){ //the current log does not have a clockout time
                
                if($round){
                    $clockin_rounded = Util::roundToInterval($curr_timelog->clockin, \Config::get('timetrack.log_interval')*60);
                    array_push($day['logs'], array(
                       'start' => date(\Config::get('timetrack.log_time_format'), $clockin_rounded),
                       'end' => "still clocked in",
                    ));
                } else {
                    array_push($day['logs'], array(
                       'start' => date(\Config::get('timetrack.log_time_format'), $curr_timelog->clockin),
                        'end' => 'still clocked in',
                    ));
                }
            } else if (!is_null($curr_timelog) //there is still another log
                    && $curr_timelog->clockin < $curr_day //the current log was started on a previous day
                    && is_null($curr_timelog['clockout'])){ //the current log does not have a clockout time
                
                array_push($day['logs'], array(
                    'start' => 'still clocked in',
                    'end' => 'still clocked in',
                ));
                
            }
        
            //format day total
            $day['total'] = ($day['total']==0) ? "0" : Util::sec2hms($day['total']);
            
            //add day to days
            $days[] = $day;
            
            //increment control variables
            $curr_day = strtotime("+ 1 day", $curr_day);
            $curr_day_end = strtotime(" + 1 day - 1 second", $curr_day);
            
        }
        
        //format overall total
        $overall_total = ($overall_total == 0) ? '' : Util::sec2hms($overall_total);
        
        //PHP_Console::log($days);
        return array($days, $overall_total);
    }
    
}
?>
