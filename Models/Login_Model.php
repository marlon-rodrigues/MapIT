<?php
/* 
 * Describes Login Model
 */
date_default_timezone_set('UTC');

require_once($_SERVER["DOCUMENT_ROOT"] . '/MapIT/Models/DBConn.php');

class Login_Model extends DBConn {
    private $username;
    private $password;
    private $super_admin;
    private $last_login;
    
    function __construct() {
        parent::__construct();
    }
    
    function validate_user_for_login($username, $password){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "SELECT * FROM users WHERE user_name = '" . $username . "' AND password = '" . $password . "' LIMIT 1";
       
        $result = $conn->query($sql);
       
        if($result){
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            return $row['id'];
        } else {
            return false;
        }
    }
    
    function save_last_login_time($userid){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "UPDATE users SET last_login = " . time() . " WHERE id = " . $userid;
       
        $result = $conn->query($sql);
       
        if($result){
            return true;
        } else {
            return false;
        }
    }
    
    function get_all_users(){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "SELECT * FROM users ORDER by user_name";
       
        $result = $conn->query($sql);
       
        if($result){
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
    
    function get_user_info($user_id){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "SELECT * FROM users WHERE id = " . $user_id;
       
        $result = $conn->query($sql);
       
        if($result){
            return mysqli_fetch_array($result, MYSQLI_ASSOC);
        } else {
            return false;
        }
    }
    
    function add_update_user($username, $email, $password, $super_admin, $userid_edit){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        if($userid_edit < 0 || $userid_edit == ''){
            $sql = "INSERT INTO users (user_name, super_admin, password, email)
                    VALUES ('" . $username . "'," . $super_admin . ",'" . $password . "','" . $email . "')"; 
        } else {
            $sql = "UPDATE users SET user_name = '" . $username . "', super_admin = " . $super_admin . ", password = '" . $password . "', email = '" . $email . 
                   "' WHERE id = " . $userid_edit;         
        }
        
        $result = $conn->query($sql);
       
        return $result;
    }
    
    function validate_user($username){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "SELECT * FROM users WHERE user_name = '" . $username . "' LIMIT 1";
       
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
    
    function delete_user($user_id){
        $conn_status = $this->open_connection();
        $conn = $this->get_dbconn();
    
        if($conn_status['status'] == '1'){
            return false;
        }
        
        $sql = "DELETE FROM users WHERE id = " . $user_id;
       
        $result = $conn->query($sql);
       
        if($result){
            return true;
        } else {
            return false;
        }
    }
    
}

?>

