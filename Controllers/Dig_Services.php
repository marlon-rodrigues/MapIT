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

require_once($_SERVER["DOCUMENT_ROOT"] . '/MapIT/Models/Dig_Model.php');

$function_name = $_POST['function_name'];

$digid = (isset($_POST['digid'])) ? $_POST['digid'] : '';

$return = array();

switch ($function_name) {
    case 'get_all_digs': 
        $return = get_all_digs();
        break;
    case 'get_dig':
        $return = get_dig($digid);
        break;
}

echo json_encode($return);

function get_all_digs() {
    $return_func['digs'] = true;
    $return_func['message'] = '';

    $dig_model = new Dig_Model();

    $digs = $dig_model->get_all_digs();

    if (!$digs) {
        $return_func['digs'] = false;
        $return_func['message'] = 'There was an error retrieving dig sites. Please contact Administrator.';
    } else {
        $return_func['digs'] = $digs;
    }

    return $return_func;
}

function get_dig($id){
    $return_func['dig'] = true;
    $return_func['message'] = '';

    $dig_model = new Dig_Model();

    $digs = $dig_model->get_dig($id);

    if (!$digs) {
        $return_func['dig'] = false;
        $return_func['message'] = 'There was an error retrieving dig site. Please contact Administrator.';
    } else {
        $return_func['dig'] = $digs;
    }

    return $return_func;
}