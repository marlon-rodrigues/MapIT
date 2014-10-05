<?php
if (!isset($_SESSION)) {
    session_start();
}

//redirects back to main page in case there is no user
if (!isset($_SESSION['userid']) || $_SESSION['userid'] < 0) {
    header('Location: http://www.mocivilwar.org');
}

if (!isset($_SESSION['userstatus']) || $_SESSION['userstatus'] != 2) {
    header('Location: ../Views/Access_Denied.php');
    exit;
}

date_default_timezone_set('UTC');

?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <script src="../libraries/jquery-1.11.0.js"></script>
        <link rel="stylesheet" href="../libraries/jquery-ui-1.9.2.custom/css/redmond/jquery-ui-1.9.2.custom.css" />
        <script src="../libraries/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.js"></script>
        <script src="../libraries/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js"></script>
   
        <link rel="stylesheet" href="../libraries/css/primary.css" />

        <title>MapIT - Manage User</title>
        
        <script>
            $(document).ready(function() {
                //setup accordion
                $('#accordion').accordion({
                    icons: false,
                    heightStyle: "content" 
                });
                
                //setup buttons
                $('#add_btn').button({
                    icons: {
                        primary: "ui-icon-circle-plus"
                    }
                }).click(function() {
                    if(validate_password()){
                        add_user();
                    }
                });
                
                //setup buttons
                $('#cancel_btn').button({
                    icons: {
                        primary: "ui-icon-circle-close"
                    }
                }).click(function() {
                    window.location = "../Controllers/User_Controller.php";
                });
                
                $('#user_type').buttonset();
                
                //get user info if call is coming from edit user
                if($('#userid_edit').text() != ''){
                    get_user();
                }
                
                function get_user(){ 
                    $.ajax({
                        url: '../Controllers/User_Services.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            function_name: 'get_user_info',
                            userid_edit: $('#userid_edit').text()
                        },
                        success: function(data) { 
                            if (!data['user']) {
                                $('#dialog').dialog({
                                    title: 'Notice',
                                    buttons: [{
                                            text: 'OK',
                                            click: function() {
                                                $(this).dialog('close');
                                            }
                                        }]
                                }).html(data['message']);
                            } else { 
                                $('#username').val(data['user']['user_name']);
                                $('#useremail').val(data['user']['email']);
                                if(data['user']['super_admin'] == 1){ 
                                    $("input:radio[name='user_type'][value='1']").attr('checked', 'checked');
                                    $('#user_type').buttonset('refresh');
                                }
                            }
                        }
                    });
                }
                
                function add_user(){
                    $.ajax({
                        url: '../Controllers/User_Services.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            function_name: 'add_update_user',
                            userid_edit: $('#userid_edit').text(),
                            username: $('#username').val(),
                            email: $('#useremail').val(),
                            password: $('#userpsw').val(),
                            super_admin: $('input:radio[name="user_type"]:checked').val()
                        },
                        success: function(data) { 
                             $('#dialog').dialog({
                                    title: 'Notice',
                                    buttons: [{
                                            text: 'OK',
                                            click: function() {
                                                $(this).dialog('close');
                                                
                                                if (data['success']){
                                                    window.location = "../Controllers/User_Controller.php";
                                                }
                                            }
                                        }]
                             }).html(data['message']);
                        }
                    });
                }
                
                function validate_password(){
                    if($('#userpsw').val() != $('#userpsw_confirm').val()){
                        $('#dialog').dialog({
                                    title: 'Notice',
                                    buttons: [{
                                            text: 'OK',
                                            click: function() {
                                                $(this).dialog('close');
                                            }
                                        }]
                         }).html('Passwords do not match');
                         return false;
                     } else {
                         return true;
                     }      
                }
            });
        </script>
        
        <script src="../js/Main.js"></script>
    </head>

    <body>
        <div id="header">
            <div style="width: 100%"><img src="../libraries/images/header-blue-bar.gif" id="image_title" width=100%> </div>
            <div style="position: relative"><img src="../libraries/images/MapIT_Title.png" id="image_title" class="title_image"></div>
            <div align="right" style="position: relative">
                <span class="title_text">Missouri's Civil War Dig Sites <br> 
                    User Name: <?php echo $_SESSION['username']; ?> - Date: <?php echo date('m/d/Y', time() - 5 * 60 * 60); ?>
                </span>
            </div>
        </div> 

        <div align="right" id="admin_head">  
            <div id="admin_manager_div" class="admin_head_child" style="padding-right: 15px">
                <button id="map_view_btn" class="menu_btn">Map</button>
                <div class="menu_labels" style="margin-left: 7px">
                    <label>Dig Sites</label><br>
                    <label>Map</label>
                </div>
            </div> 
            <div id="admin_manager_div" class="admin_head_child" style="padding-right: 15px <?php echo ($_SESSION['userstatus'] < 1) ? ';display:none' : '' ?>" >
                <button id="admin_manager_btn" class="menu_btn">Admin Manager</button>
                <div class="menu_labels" style="margin-left: 7px">
                    <label>Admin</label><br>
                    <label>Manager</label>
                </div>
            </div>           
            <div id="dig_list_div" class="admin_head_child" style="padding-right: 15px">
                <button id="dig_list_btn" class="menu_btn">Dig Sites Archive</button>
                <div class="menu_labels" style="margin-left: 7px">
                    <label>Dig Sites</label><br>
                    <label>Archive</label>
                </div>
                
            </div>
            <div id="logout_div" class="admin_head_child" style="<?php echo ($_SESSION['userstatus'] < 1) ? 'display:none' : '' ?>">
                <button id="logout_btn" class="menu_btn">Logout</button>
                <div class="menu_labels" style="margin-left: 11px">
                    <label>Logout</label>
                </div>
            </div>
        </div>
        
        <div style="border-top: 1px solid gray; margin-top: 100px"></div>
        
        
        <div id="body">   
            <label id="userid_edit" style="display:none"><?php echo $_POST['userid_edit'] ?></label>
            <div id="accordion" align="center" style="margin-top: 40px; width: 50%; margin-left: 25%">
                <h3><?php echo ($_POST['userid_edit'] == '') ? 'Add New User' : 'User Name: ' . $_POST['username_edit'] ?></h3>
                <div>
                    <table width="100%">
                        <tr>
                            <td>Name:</td>
                            <td>
                                <input type="text" name="username" id="username" style="width: 300px">
                            </td>
                        </tr>
                        <tr>
                            <td>Email:</td>
                            <td>
                                <input type="text" name="useremail" id="useremail" style="width: 300px">
                            </td>
                        </tr>                       
                        <tr>
                            <td>Password:</td>
                            <td>
                                <input type="password" name="userpsw" id="userpsw" style="width: 300px">
                            </td>
                        </tr>
                        <tr>
                            <td>Confirm Password:</td>
                            <td>
                                <input type="password" name="userpsw_confirm" id="userpsw_confirm" style="width: 300px">
                            </td>
                        </tr>
                        <tr>
                            <td>Super Admin:</td>
                            <td>
                                <div id="user_type" style="font-size: 15px">
                                <input type="radio" id="user_type_yes" name="user_type" value="2"><label for="user_type_yes">Yes</label>
                                <input type="radio" id="user_type_no" name="user_type" value="1" checked="checked"><label for="user_type_no">No</label>
                              </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div align="center" style="margin-top:30px; font-size: 14px">
                <button id="cancel_btn">Cancel</button>
                <button id="add_btn"><?php echo ($_POST['userid_edit'] == '') ? 'Add User' : 'Update User' ?></button>
            </div>
        </div>

        <div id="footer" style="margin-top: 40px">
            <div style="border-top: 1px solid gray">
                <p class="text_footer">&copy;&nbsp;Copyright 2014 MapIT Team</p>
            </div>
        </div>

        <div id="dialog"></div>
    </body>
</html>



