<?php

/**
 * Controller_Root handles interactions with core functionality for
 * the TimeTrack application, including authentication and clocking in and out
 */
class Controller_Root extends Controller_Template{
    
    /**
     * Homepage
     * 
     * The homepage provides a form for logging into the system.  Because
     * every clock interaction requires authentication, the homepage is 
     * used for logging in
     */
    public function action_index(){
        
        //if user is already logged in, direct to homepage
        $user_id = Auth::get_user_id();
        //PHP_Console::log($user_id);
        if($user_id[1] != 0){
            Response::redirect('root/home');
        }
        
        //otherwise, build login page
        $this->template->content = View::forge('root/index');
        
    }
    
    /**
     * Perform authentication
     */
    public function action_authenticate(){

        //protect from direct access
        //only allow script to execute if reached by post
        //and 'submit' is set
        if(is_null(Input::post('submit'))){
            Response::redirect('404');
        }
        
        //check account status
        $username = Input::post('username');
        $user = Model_User::find('first', array(
            'where' => array(
                
                //or used here because Simpleauth allows logins
                //based on either username or email
                array('username',$username),
                'or' => array('email', $username),
            ),
        ));
        
//        PHP_Console::log($username);
//        PHP_Console::log($user);
        
        $locked = false; //flag
        
        //if this account appears in the database, check whether
        //it is locked
        if(!is_null($user)){
            $time = time();
            
            //if account was locked, determine whether it should be unlocked
            if($user->account_locked != 0){
                
                if($user->account_locked > $time){
                
                    //account should stay locked
                    $locked = true;
                
                } else {
                    //account should not be locked anymore
                    //reset all lock-related fields
                    $user->account_locked = 0;
                    $user->last_attempt = 0;
                    $user->num_attempts = 0;
                    $user->save();
                }
                
            //if account was not locked, check last login time
            //to determine if num_attempts should be reset
            } else {
                
                //if last login attempt was a while ago, reset attempt counter
                if(($user->last_attempt != 0) &&(($user->last_attempt + 60*\Config::get('timetrack.lock_time')) < $time)){
                    //PHP_Console::log("last attempt a while ago");
                    $user->last_attempt = 0;
                    $user->num_attempts = 0;
                    $user->save();
                }
            }
        }
        
        $password = Input::post('password');
        
        
        //password field was left blank
        if($password == '' && $username == ''){
        
            $data['username'] = $username;
            $data['error'] = "Enter your credentials";
            
        } else if($password == ''){
            
            $data['username'] = $username;
            $data['error'] = "Enter your password";
            
        //user account is not locked and authentication succeeded
        } else if(!$locked && Auth::login()){
            
            //password requires a change
            if($user->password_expiration == 0 
                    || $user->password_expiration > strtotime("+ ".\Config::get('timetrack.password_lifespan'),time())){
                Response::redirect('users/reset_pass');
            }
            
            
            //reset user authentication data
            $user->last_attempt = 0;
            $user->num_attempts = 0;
            $user->save();
            
            //redirect to home page
            Response::redirect('root/home');
            
        
            
        //user account is not locked, but authentication failed
        } else if(!$locked){

            //user account exists
            if(!is_null($user)){
            
                //if this is the third invalid attempt, lock
                //the account for LOCK_TIME minutes
                if($user->num_attempts == \Config::get('timetrack.max_attempts')){
                    $user->account_locked = time() + (60 * \Config::get('timetrack.lock_time'));
                    $user->save();
                } else {
                    $user->last_attempt = time();
                    $user->num_attempts++;
                    $user->save();
                }
                
                $data['username'] = $username;
            
            //there is no such user
            } else {
                $data['username'] = '';
            }
            
            $data['error'] = "Invalid username or password.";
         
        } else {
            
            $data['error'] = "Your account has been locked due to too many failed login attempts.";
            
        }
        
        //setup the view
        $data = (isset($data)) ? $data : array();
        $this->template->title = 'Login';
        $this->template->content = View::forge('root/index', $data);
        
    }
    
    /**
     * Home Page
     * 
     * The home page displays links for performing various program
     * functions according to user's group (admin, standard) and the 
     * buttons used to clock in and out
     */
    public function action_home(){
        
        //if there is no authenticated user, redirect to login page
        $id_info = Auth::get_user_id();
        $id = $id_info[1];
        if($id == 0){
            Response::redirect('root/index');
        }
        //PHP_Console::log($id);
        
        //there is a valid user
        $clocked_in = Model_User::find($id)->clocked_in;
        if($clocked_in){
            $data['button_label'] = 'Clock Out';
            $data['action'] = Uri::create('root/clock_out');
        } else {
            $data['button_label'] = 'Clock In';
            $data['action'] = Uri::create('root/clock_in');
        }
        
        $this->template->title = 'Home';
        $this->template->page_css = array('home.css');
        $this->template->content = View::forge('root/home', $data);
        
    }
    
    /**
     * Clock in the user by creating a timestamp in the database at the
     * current time
     */
    public function action_clock_in(){
        
        //protect from direct access
        //only allow script to execute if reached by post
        //and 'active_clock' is set
        if(is_null(Input::post('activate_clock'))){
            Response::redirect('404');
        }
        
        //create a new clock_in lock for the current user
        $id_info = Auth::get_user_id();
        $id = $id_info[1];
        $log = Model_Timelog::forge();
        $log->user_id = $id;
        $log->clockin = time();
        //PHP_Console::log(date('ga i', $log->clockin));
        $log->save();
        
        //set user to clocked in
        $user = Model_User::find($id);
        $user->clocked_in = 1;
        $user->save();
        
        //redirect to home
        Response::redirect('root/home');
        
    }
    
    
    /**
     * Clock out the current user by either adding a clock out time to the
     * current time log or removing the time log.
     */
    public function action_clock_out(){
        
        //protect from direct access
        //only allow script to execute if reached by post
        //and 'active_clock' is set
        if(is_null(Input::post('activate_clock'))){
            Response::redirect('404');
        }
        
        //load the most recent time log for this user (clocked out will be null)
        $id_info = Auth::get_user_id();
        $id = $id_info[1];
        $log = Model_Timelog::find('last', array(
                'where' => array(
                    array('user_id', $id),
                    array('clockout', null),
                )
        ));
        
        

        $time = time();
        //PHP_Console::log(date('ga i', $time));
        
        //if it has been less than LOG_INTERVAL minutes since clockin, just
        //delete the record to clear out the interval
        if($log->clockin+(60*\Config::get('timetrack.log_interval')) > $time){
            $log->delete();
            
        //at least one LOG_INTERVAL has passed
        } else {

            //if this log is on a different day than the previous one,
            //split it into multiple logs
            while(date('d/m/y', $time) != date('d/m/y', $log->clockin)){
                PHP_Console::log(date('d/m/y', $time));
                PHP_Console::log(date('d/m/y', $log->clockin));
                
                $prev_day = $log->clockin;
                $log->clockout = strtotime('tomorrow - 1 sec', $log->clockin);
                $log->save();
                $log = Model_Timelog::forge();
                $log->user_id = $id;
                $log->clockin = strtotime('tomorrow', $prev_day);
            }
            
            $log->clockout = $time;
            $log->save();
        }

        //update and save user clocked_in value
        $user = Model_User::find($id);
        $user->clocked_in = 0;
        $user->save();
        Response::redirect('root/home');
        
    }
    
//    public function action_test(){
//        
//        $time = time()+(20*60);
//        $d['initial_time'] = $time;
//        $d['initial_time_formatted'] = date('ga i',$time);
//        $d['rounded_time'] = $this->roundToInterval($time, 15*60);
//        $d['rounded_time_formatted'] = date('ga i',$d['rounded_time']);
//        
//        $data['data_set'] = $d;
//        $this->template->content = View::forge('root/test',$data);
//    }
    
    
    /**
     * Log current user out of the system
     */
    public function action_logout(){
        
        //logout
        Auth::logout();
        
        //redirect to login page
        Response::redirect('root/index');
        
    }
    
    
    
    
    
    
}


?>
