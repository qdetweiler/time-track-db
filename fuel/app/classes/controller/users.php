<?php

/*
 * Model_Users controls interactions involving users
 */

class Controller_Users extends Controller_Template {
    
    
    /**
     * Build the users list display for administration involving users
     */
    public function action_list(){
        
        //user is not an admin
        if(!Auth::member(\Config::get('timetrack.admin_group'))){
            Response::redirect('root/home');
        }
        
        //get list of users
        $users = Model_User::find('all');
        
        //for each user, set the status and status_class properties
        foreach($users as $user){
            $user->status = ($user->clocked_in) ? "Clocked In" : "Clocked Out";
            $user->status_class = ($user->clocked_in) ? "cl_in" : "cl_out";
        }
        
        $data['users'] = $users;
        $id_info = Auth::get_user_id();
        $id = $id_info[1];
        $data['currid'] = $id;
        
        //create view
        $this->template->title = 'Users List';
        $this->template->page_css = array('users_list.css');
        $this->template->page_js = array('users-list.js');
        $this->template->content = View::forge('users/list', $data);
        
    }
    
    /**
     * Create the view needed to add a user to the database
     */
    public function action_add(){
        
        //user is not an admin
        if(!Auth::member(\Config::get('timetrack.admin_group'))){
            Response::redirect('root/home');
        }
        
        //this is a re-entry
        if(count(Input::all())){
            
            $validator = $this->getValidator();
            
            //if validator passes, add user and display success message
            if($validator->run()){
                
                $fname = ucfirst($validator->validated('fname'));
                $lname = ucfirst($validator->validated('lname'));
                $username = ucfirst($validator->validated('username'));
                $email = $validator->validated('email');
                $type = $validator->input('type');
                $temp_password = \Str::random('alnum', 8);
                
                $id = Auth::create_user($username, $temp_password, $email);
                $user = Model_User::find($id);
                $user->first_login = true;
                $user->fname = $fname;
                $user->lname = $lname;
                $user->group = ($type=='admin') ? 100 : 1;
                $user->save();
                
                $data['fname'] = $fname;
                $data['lname'] = $lname;
                $data['email'] = $email;
                $data['username'] = $username;
                $data['type'] = ($type=='admin') ? "Administrator" : "Standard";
                $data['temp_password'] = $temp_password;
                
                $this->template->title = "Success!";
                $this->template->page_css = array('users_add_success.css');
                $this->template->content = View::forge('users/add_success', $data);
                return;
                
            //validation failed
            } else {
                
                $fname = trim($validator->input('fname'));
                $lname = trim($validator->input('lname'));
                $username = trim($validator->input('username'));
                $email = trim($validator->input('email'));
                $type = $validator->input('type');
                $error = $validator->error();
                
            }
            
        //this is not a re-entry
        } else {
            $fname = "";
            $lname = "";
            $username = "";
            $email = "";
            $type="standard";
        }
        
        $data['fname'] = $fname;
        $data['lname'] = $lname;
        $data['username'] = $username;
        $data['email'] = $email;
        $data['type'] = $type;
        $data['error'] = isset($error) ? $error : array();
        
        //setup view
        $this->template->title = "Add User";
        $this->template->page_css = array('user_add.css');
        $this->template->content = View::forge('users/add', $data);
        
    }
    
    
    public function action_remove(){
        
        //user is not an admin
        if(!Auth::member(\Config::get('timetrack.admin_group'))){
            Response::redirect('root/home');
        }
        
        //delete the user account
        $id = Input::param('id');
        $user = Model_User::find($id);
        $user->delete();
        
        Response::redirect('users/list');
    }
    
    /**
     * getValidator returns a validator object used to validate the form
     * allowing input of user information
     * @return type
     */
    private function getValidator(){
        
        $validator = Validation::forge();
        $username_regex = "/^[[:alpha:]][[:alpha:]\d_]{2,}$/";
        $name_regex = "/^[[:alpha:]]{2,}$/";
        
        //custom rules
        $username_format = function($val) use ($username_regex){
            return (bool) preg_match($username_regex, $val);
        };
        
        $name_format = function($val) use ($name_regex){
            return (bool) preg_match($name_regex, $val);
        };
        
        $username_unique = function($val){
            $user = Model_User::find('first', array(
                'where' => array(
                    array('username',$val)
                ))
            );
            if(is_null($user)){
                return true;
            } else {
                return false;
            }
        };
        
        $email_unique = function($val){
            $user = Model_User::find('first', array(
                'where' => array(
                    array('email', $val)
                ))
            );
            return (is_null($user)) ? true : false;
        };
        
        //name validation
        $validator->add('fname', 'First Name')
               ->add_rule('trim')
                ->add_rule('required')
                ->add_rule(array('name_requirements' => $name_format));
        
        $validator->add('lname', 'Last Name')
               ->add_rule('trim')
                ->add_rule('required')
                ->add_rule(array('name_requirements' => $name_format));
        
        //username validation
        $validator->add('username','Username')
                ->add_rule('trim')
                ->add_rule('required')
                ->add_rule(array('uname_requirements' => $username_format))
                ->add_rule(array('uname_unique' => $username_unique));
        
        //email validation
        $validator->add('email', 'Email')
                ->add_rule('trim')
                ->add_rule('required')
                ->add_rule('valid_email')
                ->add_rule(array('unique_email' => $email_unique));
        
        //validator messages
        $validator->set_message('required',':label is required');
        $validator->set_message('uname_requirements', 'Invalid username.  See rules.');
        $validator->set_message('uname_unique', 'Username already taken.');
        $validator->set_message('unique_email', 'This email account has already been used.');
        $validator->set_message('valid_email', 'Invalid email address.');
        $validator->set_message('name_requirements', ':label is invalid');
        
        return $validator;
        
    }
    
    /**
     * Allow user to reset password
     */
    public function action_reset_pass(){
        
        //make sure user is authenticated
        $id_info = Auth::get_user_id();
        $id = $id_info[1];
        if(!$id){
            Response::redirect('root/home');
        }
        
        $user = Model_User::find($id);
        
        //this is a re-entry
        if(count(Input::all())){
            
            $valid = true;
            $oldpass = trim(Input::post('oldpass'));
            $newpass1 = trim(Input::post('newpass1'));
            $newpass2 = trim(Input::post('newpass2'));
            //PHP_Console::log($oldpass);
            //PHP_Console::log($newpass1);
            //PHP_Console::log(empty($oldpass));
            
            //make sure fields are filled out
            if(empty($oldpass) || empty($newpass1) || empty($newpass2)){
                $valid = false;
                $errormsg = "All fields required";
            //make sure old password is correct
            } else if(!Auth::validate_user($user->username, $oldpass)){
                $valid = false;
                $errormsg = "Old Password Incorrect";
                
            //make sure new password isn't old password    
            } else if($newpass1 == $oldpass){
                $valid = false;
                $errormsg = "New password cannot match old password";
            
            //make sure complexity requirements are met
            } else if(!preg_match('/[a-zA-Z]/', $newpass1) 
                    || !preg_match('/\d/', $newpass1) 
                    || !preg_match('/^.{6,}$/', $newpass1)){
            
                $valid = false;
                $errormsg = "Password does not meet complexity requirements";
                
            //make sure both passwords match
            } else if($newpass1 != $newpass2){
                $valid = false;
                $errormsg = "Passwords did not match";
            }
            
            if($valid){
                Auth::change_password($oldpass, $newpass1);
                Auth::login($user->username, $newpass1);
                $user->password_expiration = strtotime("+ ".\Config::get('timetrack.password_lifespan'), time());
                $user->save();
                Response::redirect('root/home');
            } else {
                $data['errormsg'] = $errormsg;
            }
        }
        
        $data = isset($data) ? $data : array();
        
        $this->template->title ="Password Reset";
        $this->template->page_css = array('user_reset_pass.css');
        $this->template->content = View::forge('users/reset_pass', $data);
        
    }
    
    /**
     * auto_reset_pass automatically resets a user's password to a
     * random value and displays the new password
     */
    public function action_auto_reset_pass(){
        
        //user is not an admin
        if(!Auth::member(\Config::get('timetrack.admin_group'))){
            Response::redirect('root/home');
        }
        
        //reset password
        $id = Input::param('id');
        $user = Model_User::find($id);
        $new_pass = Auth::reset_password($user->username);
        
        //set password expiration
        $user->password_expiration = 0;
        $user->save();
        
        //setup variables for the view
        $data['username'] = $user->username;
        $data['temp_password'] = $new_pass;
        
        //create the view
        $this->template->title = "Success!";
        $this->template->page_css = array('users_add_success.css');
        $this->template->content = View::forge('users/reset_success', $data);
        
    }
    
}




?>
