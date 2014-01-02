<?php

/**
 * Controller_Root handles interactions with core functionality for
 * the TimeTrack application, including authentication and clocking in and out
 * as well as building the home page
 */
class Controller_Root extends Controller_Template {

  /**
   * Homepage
   * 
   * The homepage provides a form for logging into the system.  Because
   * every clock interaction requires authentication, the homepage is 
   * used for logging in
   */
  public function action_index() {

    //if user is already logged in, direct to homepage
    $user_id = Auth::get_user_id();
    if ($user_id[1] != 0) {
      Response::redirect('root/home');
    }

    //otherwise, build login page
    $this->template->content = View::forge('root/index');
  }

  /**
   * Authenticate a user based on submission of the login form
   * found on the homepage
   *
   * Options in config file for authentication:
   * max_attempts -> amount of failed logins to allow
   * lock_time -> amount of time to lock an account after max_attempts failed logins
   * password_lifespan -> number of days until password must be changed 
   */
  public function action_authenticate() {

    //protect from direct access
    //only allow script to execute if reached by post
    //and 'submit' is set
    if (is_null(Input::post('submit'))) {
      Response::redirect('404');
    }

    //check account status
    $username = Input::post('username');
    $user = Model_User::find('first', array(
                'where' => array(
                    //or used here because Simpleauth allows logins
                    //based on either username or email
                    array('username', $username),
                    'or' => array('email', $username),
                ),
    ));

    $locked = false; //flag
    //if this account appears in the database, check whether
    //it is locked
    if (!is_null($user)) {
      $time = time();

      //if account was locked, determine whether it should be unlocked
      //note account_locked == 0 indicates account is NOT locked
      if ($user->account_locked != 0) {

        //account is not supposed to be unlocked yet
        if ($user->account_locked > $time) {

          $locked = true;

          //account lock has expired
        } else {

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
        if (($user->last_attempt != 0) &&
                (($user->last_attempt + 60 * \Config::get('timetrack.lock_time')) < $time)) {
          $user->last_attempt = 0;
          $user->num_attempts = 0;
          $user->save();
        }
      }
    }

    $password = Input::post('password');


    //both fields life blank
    if ($password == '' && $username == '') {

      $data['username'] = $username;
      $data['error'] = "Enter your credentials";

      //only password left blank
    } else if ($password == '') {

      $data['username'] = $username;
      $data['error'] = "Enter your password";

      //user account is not locked and authentication succeeded
    } else if (!$locked && Auth::login()) {

      //password requires a change
      if ($user->password_expiration == 0 || $user->password_expiration > strtotime("+ " . \Config::get
                              ('timetrack.password_lifespan'), time())) {

        //logout to prevent user from switching pages
        //to avoid password change
        Auth::logout();
        Session::set('reset_id', $user->id);
        Response::redirect('users/reset_pass');
      }


      //reset user authentication data
      $user->last_attempt = 0;
      $user->num_attempts = 0;
      $user->save();

      //redirect to home page
      Response::redirect('root/home');

      //user account is not locked, but authentication failed
    } else if (!$locked) {

      //user account exists
      if (!is_null($user)) {

        //if this is the third invalid attempt, lock
        //the account for LOCK_TIME minutes
        if ($user->num_attempts == \Config::get('timetrack.max_attempts')) {
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

      $data['error'] = "Your account has been locked" .
              " due to too many failed login attempts.";
    }

    //setup the view
    $data = (isset($data)) ? $data : array();
    $this->template->title = 'Login';
    $this->template->content = View::forge('root/index', $data);
  }

  /**
   * Home Page
   * 
   * The home page displays the button used to clock in and out
   * as well as the current time on the server and the last time
   * the logged in user clocked in or out
   */
  public function action_home() {

    //if there is no authenticated user, redirect to login page
    $id_info = Auth::get_user_id();
    $id = $id_info[1];
    if ($id == 0) {
      Response::redirect('root/index');
    }

    //there is a valid user
    //fetch last timelog for testing
    $last_log = Model_Timelog::find('first', array(
                'where' => array(
                    array('user_id', $id),
                ),
                //ordering required here because default ordering may be
                //incorrect if logs for previous dates are added manually
                'order_by' => array('clockin' => 'desc')
    ));


    //user is new
    if(is_null($last_log)){
        
        $data['button_label'] = 'Clock In';
        $data['action'] = Uri::create('root/clock_in');
        $data['last_clock_s'] = 'N/A';
        
        
    //user is clocked in
    } else if ($last_log->clockout == 0) {

      $data['button_label'] = 'Clock Out';
      $data['action'] = Uri::create('root/clock_out');
      $data['last_clock_s'] = 'Last Clocked In:  '
              . date(\Config::get('timetrack.last_clock_format'), $last_log->clockin);

    //user is clocked out
    } else {

      $data['button_label'] = 'Clock In';
      $data['action'] = Uri::create('root/clock_in');
      $data['last_clock_s'] = 'Last Clocked Out:  '
              . date(\Config::get('timetrack.last_clock_format'), $last_log->clockout);
    }
    
    $data['time'] = date(\Config::get('timetrack.clock_format'), time());
    
    //setup local javascript variables
    $time_uri = Uri::create('root/time');
    $js = "var time_uri = \"$time_uri\";";
    $this->template->script = View::forge("script", array('js' => $js), false);

    //build view
    $this->template->title = 'Home';
    $this->template->css = array('home.css');
    $this->template->js = array('root-home.js');
    $this->template->content = View::forge('root/home', $data);
  }

  /**
   * Clock in the user by creating a timestamp in the database at the
   * current time.  This public function is a convenience function which
   * clocks in the currently logged in user
   */
  public function action_clock_in() {

    //protect from direct access
    //only allow script to execute if reached by post
    //and 'active_clock' is set
    if (is_null(Input::post('activate_clock'))) {
      Response::redirect('404');
    }

    //create a new clockin log for the current user
    $id_info = Auth::get_user_id();
    $id = $id_info[1];

    $this->perform_clockin($id);

    //redirect to home
    Response::redirect('root/home');
  }

  /**
   * Perform the process of clocking in a user based on ID and current time.
   * This method can be used to clock in any user regardless of login status;
   * however, it is private and should only be accessed by scripts protecting
   * direct and non-admin access.
   * @param type $id
   */
  private function perform_clockin($id) {

    //fetch last log for the user with the given ID
    $last_log = Model_Timelog::find('last', array(
                'where' => array(
                    array('user_id' => $id),
                ),
    ));

    $time = time();
    $interval = 60 * \Config::get('timetrack.log_interval');

    //less than a rounded time period has passed
    if (!is_null($last_log) && Util::roundToInterval($last_log->clockout, $interval) == Util::roundToInterval($time, $interval)) {

      //extend the previous timelog rather than creating a new one
      $last_log->clockout = 0;
      $last_log->save();
    } else {

      $log = Model_Timelog::forge();
      $log->user_id = $id;
      $log->clockin = time();
      $log->clockout = 0;
      $log->type = 0;
      $log->save();
    }

    //set user to clocked in
    $user = Model_User::find($id);
    $user->clocked_in = 1;
    $user->save();
  }

  /**
   * Clock out the specified user
   * @param type $id
   */
  private function perform_clockout($id) {

    //pull the last log for the user
    $log = Model_Timelog::find('first', array(
      'where' => array(
          array('user_id', $id),
      ),
      'order_by' => array('clockin' => 'desc')
    ));

    $time = time();

    //if it has been less than LOG_INTERVAL minutes since clockin, just
    //delete the record to clear out the interval
    if ($log->clockin + (60 * \Config::get('timetrack.log_interval')) > $time) {

      $log->delete();

      //at least one LOG_INTERVAL has passed
    } else {

       $type = $log->type;
        
      //if this log's clockout is on a different day than it's clockin, split
      //it into multiple days
      while (date('d/m/y', $time) != date('d/m/y', $log->clockin)) {

        $prev_day = $log->clockin;
        $log->clockout = strtotime('tomorrow - 1 sec', $log->clockin);
        $log->save();
        $log = Model_Timelog::forge();
        $log->user_id = $id;
        $log->clockin = strtotime('tomorrow', $prev_day);
        $log->type = $type;
      }

      $log->clockout = $time;
      $log->save();
    }

    //update and save user clocked_in value
    $user = Model_User::find($id);
    $user->clocked_in = 0;
    $user->save();
  }

  /**
   * Change the status of a specified user (clocked in or clocked out)
   * This method should only work if the current logged in user
   * is an admin.  The purpose of this method is to allow an
   * admin to "force" a clockin or clockout for a specified user
   */
  public function action_change_status() {

    //make sure user is authenticated and an admin
    if (!Auth::member(\Config::get('timetrack.admin_group'))) {
      Response::redirect('root/home');
    }

    $id = Input::post('id');
    $user = Model_User::find($id);

    if ($user->clocked_in) {
      $this->perform_clockout($id);
    } else {
      $this->perform_clockin($id);
    }

    return Response::forge(json_encode(true));
  }

  /**
   * Clock out the current user.  This public function is a convenience
   * function that only clocks out the current user
   */
  public function action_clock_out() {

    //protect from direct access
    //only allow script to execute if reached by post
    //and 'active_clock' is set
    if (is_null(Input::post('activate_clock'))) {
      Response::redirect('404');
    }

    //load the most recent time log for this user (clocked out will be 0)
    $id_info = Auth::get_user_id();
    $id = $id_info[1];

    $this->perform_clockout($id);

    Response::redirect('root/home');
  }

  /**
   * Log current user out of the system
   */
  public function action_logout() {

    //logout
    Auth::logout();

    //redirect to login page
    Response::redirect('root/index');
  }

  /**
   * return server's current time as a json object
   * time: formatted string for displaying current time
   * hours: hour (24 hour w/out leading 0)
   * minutes: minute (w/ leading 0)
   * seconds: seconds past the hour
   */
  public function action_time() {

    $time = time();
    $time_s = date(\Config::get('timetrack.clock_format'), $time);
    return Response::forge(json_encode(
            array('time' => $time_s,
                  'hours' => date('G',$time),
                  'minutes' => date('i', $time),
                  'seconds' => date('s', $time))));
    
  }

}

?>
