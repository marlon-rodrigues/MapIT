<?php
if (!isset($_SESSION)) {
    session_start();
}

//redirects back to main page in case there is no user
if (!isset($_SESSION['userid']) || $_SESSION['userid'] < 0) {
    header('Location: http://www.mocivilwar.org');
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
        <script src="../js/DateFormat.js"></script>
   
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
                    update_dig_site();
                });
                
                //setup buttons
                $('#cancel_btn').button({
                    icons: {
                        primary: "ui-icon-circle-close"
                    }
                }).click(function() {
                    window.location = "../Controllers/Dig_List_Manager_Controller.php";
                });
                
                $('#dig_type').buttonset();
                $('#dig_status').buttonset();
                $('#initial_date').datepicker();
                $('#final_date').datepicker();
                
                get_dig_site();
                
                function get_dig_site(){ 
                    $.ajax({
                        url: '../Controllers/Dig_Services.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            function_name: 'get_dig',
                            digid: $('#digid_edit').text()
                        },
                        success: function(data) { 
                            if (!data['dig']) {
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
                                $('#name').val(data['dig']['name']);
                                $('#description').val(data['dig']['description']);
                                $("input:radio[name='dig_type'][value='" + data['dig']['type'] + "']").attr('checked', 'checked');
                                $('#dig_type').buttonset('refresh');
                                $("input:radio[name='dig_status'][value='" + data['dig']['status'] + "']").attr('checked', 'checked');
                                $('#dig_status').buttonset('refresh');
                                $('#initial_date').val(convert_date_datepicker(data['dig']['initial_date']));
                                $('#final_date').val(convert_date_datepicker(data['dig']['end_date']));
                            }
                        }
                    });
                }
                
                function update_dig_site(){
                    $.ajax({
                        url: '../Controllers/Map_Services.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            function_name: 'update_dig_site_by_id',
                            id: $('#digid_edit').text(),
                            name: $('#name').val(),
                            description: $('#description').val(),
                            initial_date: $('#initial_date').val(),
                            end_date: $('#final_date').val(),
                            type: $('input:radio[name="dig_type"]:checked').val(),
                            status: $('input:radio[name="dig_status"]:checked').val(),
                        },
                        success: function(data) { 
                             $('#dialog').dialog({
                                    title: 'Notice',
                                    buttons: [{
                                            text: 'OK',
                                            click: function() {
                                                $(this).dialog('close');
                                                
                                                if (data['success']){
                                                    window.location = "../Controllers/Dig_List_Manager_Controller.php";
                                                }
                                            }
                                        }]
                             }).html(data['message']);
                        }
                    });
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
            <label id="digid_edit" style="display:none"><?php echo $_POST['digid_edit'] ?></label>
            <div id="accordion" align="center" style="margin-top: 40px; width: 50%; margin-left: 25%">
                <h3>Dig Site Name: <?php echo $_POST['digname_edit'] ?></h3>
                <div>
                    <table width="100%">
                        <tr>
                            <td>Name:</td>
                            <td>
                                <input type="text" name="name" id="name" style="width: 300px">
                            </td>
                        </tr>
                        <tr>
                            <td>Description:</td>
                            <td>
                                <textarea name="description" id="description" maxlength="350" style="width:300px" rows="4" cols="30"></textarea>
                            </td>
                        </tr>                       
                        <tr>
                            <td>Initial Date:</td>
                            <td>
                                <input type="text" name="initial_date" id="initial_date" style="width: 300px">
                            </td>
                        </tr>
                        <tr>
                            <td>Final Date:</td>
                            <td>
                                <input type="text" name="final_date" id="final_date" style="width: 300px">
                            </td>
                        </tr>
                        <tr>
                            <td>Type:</td>
                            <td>
                                <div id="dig_type" style="font-size: 15px">
                                <input type="radio" id="dig_type_war" name="dig_type" value="0" checked="checked"><label for="dig_type_war">Civil War</label>
                                <input type="radio" id="dig_type_normal" name="dig_type" value="1"><label for="dig_type_normal">Normal Site</label>
                              </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Status:</td>
                            <td>
                                <div id="dig_status" style="font-size: 15px">
                                <input type="radio" id="dig_status_expl" name="dig_status" value="0" checked="checked"><label for="dig_status_expl">Exploring</label>
                                <input type="radio" id="dig_status_conc" name="dig_status" value="1"><label for="dig_status_conc">Concluded</label>
                              </div>
                            </td>
                        </tr>                        
                    </table>
                </div>
            </div>

            <div align="center" style="margin-top:30px; font-size: 14px">
                <button id="cancel_btn">Cancel</button>
                <button id="add_btn">Update Dig Site</button>
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





