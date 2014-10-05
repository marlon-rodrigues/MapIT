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

$id = (isset($_POST['id'])) ? $_POST['id'] : '';
$name = (isset($_POST['name'])) ? $_POST['name'] : '';
$latitude = (isset($_POST['latitude'])) ? $_POST['latitude'] : '';
$longitude = (isset($_POST['longitude'])) ? $_POST['longitude'] : '';
$description = (isset($_POST['description'])) ? $_POST['description'] : '';
$initial_date = (isset($_POST['initial_date'])) ? $_POST['initial_date'] : '';
$end_date = (isset($_POST['end_date'])) ? $_POST['end_date'] : '';
$type = (isset($_POST['type'])) ? $_POST['type'] : '';
$status = (isset($_POST['status'])) ? $_POST['status'] : '';
$last_modified = (isset($_POST['last_modified'])) ? $_POST['last_modified'] : '';

$return = array();

switch ($function_name) {
    case 'add_dig_site': 
        $return = add_dig_site($name, $latitude, $longitude, $description, $initial_date, $end_date, $type, $status);
        break;
    case 'delete_dig_site_by_id': 
        $return = delete_dig_site_by_id($id);
        break;
    case 'delete_dig_site_by_latlong': 
        $return = delete_dig_site_by_latlong($latitude, $longitude);
        break;
    case 'update_dig_site_by_id':
        $return = update_dig_site_by_id($id, $name, $description, $initial_date, $end_date, $type, $status);
        break;
    case 'update_dig_site_by_latlong':
        $return = update_dig_site_by_latlong($name, $description, $latitude, $longitude, $initial_date, $end_date, $type, $status);
        break;    
}

echo json_encode($return);

function add_dig_site($name, $latitude, $longitude, $description, $initial_date, $end_date, $type, $status){
    $return_func['success'] = true;
    $return_func['message'] = 'Save was successful';

    $dig_model = new Dig_Model();
    
    //validate fields
    if ($name == '' || $name == NULL || trim($name) == ''){
        $return_func['success'] = false;
        $return_func['message'] = 'Please provide a name for the dig site.';
        return $return_func;
    }
    
    //verify if dig site already exists
    if ($dig_model->validate_dig_site($name)) {
        $return_func['success'] = false;
        $return_func['message'] = 'Dig site name alrady exists.';
        return $return_func;
    }
    
    //prepare date fields
    if(isset($initial_date) && $initial_date > 0){
        $epoch_ini_date = strtotime($initial_date);
    } else {
        $epoch_ini_date = 0;
    }

    if(isset($end_date) && $end_date > 0){
        $epoch_end_date = strtotime($end_date);
    } else {
        $epoch_end_date = 0;
    }
    
    //add dig site
    if(!$dig_model->add_dig_site($name, $description, $latitude, $longitude, $epoch_ini_date, $epoch_end_date, $type, $status)){
        $return_func['success'] = false;
        $return_func['message'] = 'There was an error saving dig site. Please contact Administrator.';
    }
    
    return $return_func;   
}

function delete_dig_site_by_id($dig_site_id){
    $return_func['success'] = true;
    $return_func['message'] = 'Delete was successful';

    $dig_model = new Dig_Model();
    
    $del_dig_site = $dig_model->delete_dig_site_by_id($dig_site_id);
    
    if(!$del_dig_site){
        $return_func['success'] = false;
        $return_func['message'] = 'There was an error deleting dig site. Please contact Administrator.';
    }
    
    return $return_func; 
}


function delete_dig_site_by_latlong($lat, $long){
    $return_func['success'] = true;
    $return_func['message'] = 'Delete was successful';

    $dig_model = new Dig_Model();
    
    $del_dig_site = $dig_model->delete_dig_site_by_latlong($lat, $long);
    
    if(!$del_dig_site){
        $return_func['success'] = false;
        $return_func['message'] = 'There was an error deleting dig site. Please contact Administrator.';
    }
    
    return $return_func; 
}

function update_dig_site_by_id($id, $name, $description, $initial_date, $end_date, $type, $status){
    $return_func['success'] = true;
    $return_func['message'] = 'Save was successful';

    $dig_model = new Dig_Model();
    
    //validate fields
    if ($name == '' || $name == NULL || trim($name) == ''){
        $return_func['success'] = false;
        $return_func['message'] = 'Please provide a name for the dig site.';
        return $return_func;
    }

    if ($initial_date == '' || $initial_date == NULL || trim($initial_date) == ''){
        $return_func['success'] = false;
        $return_func['message'] = 'Please provide a initial date for the dig site.';
        return $return_func;
    }
    
    //prepare date fields
    if(isset($initial_date) && $initial_date > 0){
        $epoch_ini_date = strtotime($initial_date);
    } else {
        $epoch_ini_date = 0;
    }

    if(isset($end_date) && $end_date > 0){
        $epoch_end_date = strtotime($end_date);
    } else {
        $epoch_end_date = 0;
    }
    
    //update dig site
    if(!$dig_model->update_dig_site_by_id($id, $name, $description, $epoch_ini_date, $epoch_end_date, $type, $status)){
        $return_func['success'] = false;
        $return_func['message'] = 'There was an error saving dig site. Please contact Administrator.';
    }
    
    return $return_func;   
}


function update_dig_site_by_latlong($name, $description, $lat, $long, $initial_date, $end_date, $type, $status){
    $return_func['success'] = true;
    $return_func['message'] = 'Save was successful';

    $dig_model = new Dig_Model();
    
    //validate fields
    if ($name == '' || $name == NULL || trim($name) == ''){
        $return_func['success'] = false;
        $return_func['message'] = 'Please provide a name for the dig site.';
        return $return_func;
    }

    if ($initial_date == '' || $initial_date == NULL || trim($initial_date) == ''){
        $return_func['success'] = false;
        $return_func['message'] = 'Please provide a initial date for the dig site.';
        return $return_func;
    }
    
    //prepare date fields
    if(isset($initial_date) && $initial_date > 0){
        $epoch_ini_date = strtotime($initial_date);
    } else {
        $epoch_ini_date = 0;
    }

    if(isset($end_date) && $end_date > 0){
        $epoch_end_date = strtotime($end_date);
    } else {
        $epoch_end_date = 0;
    }
    
    //update dig site
    if(!$dig_model->update_dig_site_by_latlong($name, $description, $lat, $long, $epoch_ini_date, $epoch_end_date, $type, $status)){
        $return_func['success'] = false;
        $return_func['message'] = 'There was an error saving dig site. Please contact Administrator.';
    }
    
    return $return_func;   
}