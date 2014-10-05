<?php
/* 
 * Describes Login Model
 */
date_default_timezone_set('UTC');

require_once($_SERVER["DOCUMENT_ROOT"] . '/MapIT/Models/DBConn.php');

class Dig_Model extends DBConn {
    private $name;
    private $latitude;
    private $longitude;
    private $description;
    private $initial_date;
    private $end_date;
    private $type;
    private $status;
    
    function __construct() {
        parent::__construct();
    }
    
    function get_all_digs(){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "SELECT * FROM dig_sites ORDER by name";
       
        $result = $conn->query($sql);
       
        if($result){
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
    
    function get_dig($id){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "SELECT * FROM dig_sites WHERE id = " . $id;
       
        $result = $conn->query($sql);
       
        if($result){
            return mysqli_fetch_array($result, MYSQLI_ASSOC);
        } else {
            return false;
        }
    }    
    
    function add_dig_site($name, $desc, $latitude, $longitude, $initial_date, $end_date, $type, $status){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "INSERT INTO dig_sites (name, description, latitude, longitude, initial_date, end_date, type, status, last_modified)
                VALUES ('" . $name . "','" . $desc . "'," . $latitude . "," . $longitude . "," . $initial_date . "," . $end_date . "," . $type . "," . $status . "," . time() . ")"; 
           
        $result = $conn->query($sql);
       
        return $result;
    }
    
    function validate_dig_site($name){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "SELECT * FROM dig_sites WHERE name = '" . $name . "' LIMIT 1";
       
        $result = $conn->query($sql);
       
        if($result){
            if($result->num_rows > 0){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    function delete_dig_site_by_id($dig_site_id){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "DELETE FROM dig_sites WHERE id = " . $dig_site_id;
       
        $result = $conn->query($sql);
       
        if($result){
            return true;
        } else {
            return false;
        }
    } 
    
    function delete_dig_site_by_latlong($lat, $long){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "DELETE FROM dig_sites WHERE latitude = " . $lat . " AND longitude = " . $long;
       
        $result = $conn->query($sql);
       
        if($result){
            return true;
        } else {
            return false;
        }
    }
    
    function update_dig_site_by_id($id, $name, $desc, $initial_date, $end_date, $type, $status){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "UPDATE dig_sites SET name = '" .$name . "', description = '" . $desc . "', initial_date = " . $initial_date . ", end_date = " . $end_date . 
                       ", type = " . $type . ", status = " . $status . ", last_modified = " . time() . " WHERE id = " . $id;
               
        $result = $conn->query($sql);
       
        return $result;
    }  
    
    function update_dig_site_by_latlong($name, $desc, $lat, $long, $initial_date, $end_date, $type, $status){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "UPDATE dig_sites SET name = '" .$name . "', description = '" . $desc . "', initial_date = " . $initial_date . ", end_date = " . $end_date . 
                       ", type = " . $type . ", status = " . $status . ", last_modified = " . time() . " WHERE latitude = " . $lat . " AND longitude = " . $long;
               
        $result = $conn->query($sql);
       
        return $result;
    }      
    
}

?>


