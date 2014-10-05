<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!isset($_SESSION)) {
    session_start();
}

//redirects back to main page in case there is no user
if (!isset($_SESSION['userid']) || $_SESSION['userid'] < 0) {
    header('Location: http://www.mocivilwar.org');
}

date_default_timezone_set('UTC');

require_once($_SERVER["DOCUMENT_ROOT"] . '/MapIT/Models/Login_Model.php');

$function_name = $_POST['function_name'];

$userid = (isset($_POST['userid'])) ? $_POST['userid'] : '';
$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = (isset($_POST['password'])) ? $_POST['password'] : '';
$email = (isset($_POST['email'])) ? $_POST['email'] : '';
$super_admin = (isset($_POST['super_admin'])) ? $_POST['super_admin'] : '';

$userid_edit = (isset($_POST['userid_edit'])) ? $_POST['userid_edit'] : '';

$return = array();

switch ($function_name) {
    case 'get_all_users': 
        $return = get_all_users();
        break;
    case 'get_user_info':
        $return = get_user_info($userid_edit);
        break;
    case 'add_update_user':
        $return = add_update_user($userid_edit, $username, $email, $password, $super_admin);
        break;
    case 'delete_user':
        $return = delete_user($userid_edit);
        break;
}

echo json_encode($return);

function get_all_users() {
    $return_func['users'] = true;
    $return_func['message'] = '';

    $login_model = new Login_Model();

    $users = $login_model->get_all_users();

    if (!$users) {
        $return_func['users'] = false;
        $return_func['message'] = 'There was an error retrieving users. Please contact Administrator.';
    } else {   
        $return_func['users'] = $users;
    }

    return $return_func;
}

function get_user_info($user_id){
    $return_func['user'] = true;
    $return_func['message'] = '';

    $login_model = new Login_Model();

    $user = $login_model->get_user_info($user_id);

    if (!$user) {
        $return_func['user'] = false;
        $return_func['message'] = 'There was an error retrieving users. Please contact Administrator.';
    } else {
        $return_func['user'] = $user;
    }

    return $return_func;   
}

function add_update_user($userid, $username, $email, $password, $super_admin){
    $return_func['success'] = true;
    $return_func['message'] = 'Save was successful';

    $login_model = new Login_Model();
    
    //validate fields
    if ($username == '' || $username == NULL || trim($username) == ''){
        $return_func['success'] = false;
        $return_func['message'] = 'Please provide user name.';
        return $return_func;
    }
    
    if ($password == '' || $password == NULL || trim($password) == ''){
        $return_func['success'] = false;
        $return_func['message'] = 'Please provide password.';
        return $return_func;
    }
    
    //if is a new user, verify if already exists
    if ($userid < 0 || $userid == ''){
         if($login_model->validate_user($username)){
             $return_func['success'] = false;
             $return_func['message'] = 'User name alrady exists.';
             return $return_func;
         } 
    }
    
    //encript password
    $psw = md5($password);
    
    //add/update user
    if(!$login_model->add_update_user($username, $email, $psw, $super_admin, $userid)){
        $return_func['success'] = false;
        $return_func['message'] = 'There was an error saving user. Please contact Administrator.';
    }
    
    return $return_func;   
}

function delete_user($userid){
    $return_func['success'] = true;
    $return_func['message'] = 'Delete was successful';

    $login_model = new Login_Model();
    
    $del_user = $login_model->delete_user($userid);
    
    if(!$del_user){
        $return_func['success'] = false;
        $return_func['message'] = 'There was an error deleting user. Please contact Administrator.';
    }
    
    return $return_func; 
}

?>
