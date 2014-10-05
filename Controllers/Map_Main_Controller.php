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

$userid = $_POST['userid_validated'];
$username = $_POST['username_validated'];
$password = $_POST['psw'];

$_SESSION['userid'] = $userid;
$_SESSION['username'] = $username;

//redirects back to main page in case there is no user
if(!isset($_SESSION['userid']) || $_SESSION['userid'] < 0){
    header('Location: http://www.mocivilwar.org');
} else {
    $saved_last_login = true;
    
    //status 
    //0 - Guest
    //1 - Admin
    //2 - Super Admin
    if ($userid == 0) {
        $_SESSION['userstatus'] = 0;
    } else {
        //get user status
        $_SESSION['userstatus'] = get_user_status();
        
        //save last login time
        $saved_last_login = save_last_login_time();
    }
    
    if($saved_last_login){
        header('Location: ../Views/Map_Main.php');
    } else {
        echo "FATAL ERROR!";
    }
}

function get_user_status(){
    $login_model = new Login_Model();

    $user_info = $login_model->get_user_info($_SESSION['userid']);
    
    return $user_info['super_admin'];
}

function save_last_login_time(){
    $login_model = new Login_Model();

    return $login_model->save_last_login_time($_SESSION['userid']);
}

?>
