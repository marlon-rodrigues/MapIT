<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!isset($_SESSION)) {
    session_start();
}

date_default_timezone_set('UTC');

require_once($_SERVER["DOCUMENT_ROOT"] . '/MapIT/Models/Login_Model.php');

$function_name = $_POST['function_name'];
$userid = (isset($_POST['userid'])) ? $_POST['userid'] : '';
$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = (isset($_POST['password'])) ? $_POST['password'] : '';

$return = array();

switch ($function_name) {
    case 'validate_user': 
        $return = validate_user($username, $password);
        break;
    case 'logout':
        $return = logout();
        break;
}

echo json_encode($return);

function validate_user($username, $password) {
    $return_func['data'] = true;
    $return_func['message'] = '';

    $login_model = new Login_Model();

    $psw = md5($password);
    
    $user_validate = $login_model->validate_user_for_login($username, $psw);

    if (!$user_validate) {
        $return_func['data'] = false;
        $return_func['message'] = 'User name not found. Please contact Administrator or login in as a Guest.';
    } else {
        $return_func['data'] = $user_validate;
    }

    return $return_func;
}

function logout(){
    //kill section before leaving the page
    unset($_SESSION['userid']);
    unset($_SESSION['username']);
    unset($_SESSION['userstatus']);
    
    session_destroy();
    
    $return_func['data'] = true;
    
    return $return_func;
}

?>