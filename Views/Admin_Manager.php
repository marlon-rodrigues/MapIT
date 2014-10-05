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
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBaB26c-fzIJpvh_zOm9ZINErBOOPQK078&sensor=false"></script>

        <link rel="stylesheet" href="../libraries/css/primary.css" />
        <link rel="stylesheet" href="../libraries/css/map.css" />

        <title>MapIT - Admin Manager</title>

        <script>
            $(document).ready(function() {
                //setup accordion
                $('#accordion').accordion({
                    icons: false
                });

                $('#users_btn').button({
                    icons: {
                        primary: "ui-icon-custom_user"
                    },
                    text: false
                }).click(function() { 
                    if ($('#userstatus').text() != 2) {
                        $('#dialog').dialog({
                            title: 'Notice',
                            buttons: [{
                                    text: 'OK',
                                    click: function() {
                                        $(this).dialog('close');
                                    }
                                }]
                        }).html('User dont have access to this area. Please, contact administrator.');
                    } else {
                        window.location = "../Controllers/User_Controller.php";
                    }
                });

                $('#dig_sites_btn').button({
                    icons: {
                        primary: "ui-icon-custom_cannon"
                    },
                    text: false
                }).click(function() {
                    if ($('#userstatus').text() != 2) {
                        $('#dialog').dialog({
                            title: 'Notice',
                            buttons: [{
                                    text: 'OK',
                                    click: function() {
                                        $(this).dialog('close');
                                    }
                                }]
                        }).html('User dont have access to this area. Please, contact administrator.');
                    } else {
                        window.location = "../Controllers/Dig_List_Manager_Controller.php";
                    }
                });
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

        <label id="userstatus" style="display:none"><?php echo $_SESSION['userstatus'] ?></label>

        <div id="accordion" align="center" style="margin-top: 50px; width: 50%; margin-left: 25%">
            <h3>Admin Manager</h3>
            <div>
                <div align="center" style="padding-top: 10px">
                    <button id="users_btn" class="manager_btn" style="display: inline-block">Manage Users</button>
                    <label style="display: inline-block; margin-left: 10px">Manage Users</label>
                </div>

                <div align="center" style="padding-top: 10px; padding-bottom: 20px; margin-left: 30px">
                    <button id="dig_sites_btn" class="manager_btn" style="display: inline-block">Manage Dig Sites</button>
                    <label style="display: inline-block; margin-left: 10px">Manage Dig Sites</label>
                </div>
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
