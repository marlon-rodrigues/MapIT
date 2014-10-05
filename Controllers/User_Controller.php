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

//redirects back to main page in case there is no user
if(!isset($_SESSION['userid']) || $_SESSION['userid'] < 0){
    header('Location: http://www.mocivilwar.org');
} else if (!isset($_SESSION['userstatus']) || $_SESSION['userstatus'] != 2) {
    header('Location: ../Views/Access_Denied.php');
} else {
    header('Location: ../Views/User_Manager_List.php');
}

?>