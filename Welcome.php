<?php
    session_start();
    date_default_timezone_set('UTC');
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <script src="libraries/jquery-1.11.0.js"></script>
        <link rel="stylesheet" href="libraries/jquery-ui-1.9.2.custom/css/redmond/jquery-ui-1.9.2.custom.css" />
        <script src="libraries/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.js"></script>
        <script src="libraries/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js"></script>
        <link rel="stylesheet" href="libraries/css/primary.css" />

        <title>MapIT - Login</title>

        <script>
            $(document).ready(function() {
                //setup accordion
                $('#accordion').accordion({
                    icons: false
                });

                //setup buttons
                $('#cancel_btn').button({
                    icons: {
                        primary: "ui-icon-circle-close"
                    }
                }).click(function() {
                    window.location = "http://www.mocivilwar.org";
                });

                $('#guest_btn').button({
                    icons: {
                        primary: "ui-icon-circle-triangle-e"
                    }
                }).click(function() {
                    $('#userid_validated').val(0);
                    $('#username_validated').val('Guest');
                    $('#input_form').submit();
                });

                $('#login_btn').button({
                    icons: {
                        primary: "ui-icon-circle-check"
                    }
                }).click(function() {
                    $.ajax({
                        url: 'Controllers/Login_Services.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            function_name: 'validate_user',
                            username: $('#username').val(),
                            password: $('#userpsw').val()
                        },
                        success: function(data) {
                            if (!data['data']) {
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
                                $('#userid_validated').val(data['data']);
                                $('#username_validated').val($('#username').val());
                                $('#input_form').submit();
                            }
                        }
                    });
                });
            });
        </script>
    </head> 
    <body>

        <div id="header">
            <div style="width: 100%"><img src="libraries/images/header-blue-bar.gif" id="image_title" width=100%> </div>
            <div style="position: relative"><img src="libraries/images/MapIT_Title.png" id="image_title" class="title_image"></div>
            <div align="right" style="position: relative"><span class="title_text">Missouri's Civil War Dig Sites</span></div>
        </div>  

        <div id="body">
            <div id="accordion" align="center" style="margin-top: 100px; width: 40%; margin-left: 29%">
                <h3>Login</h3>
                <div>
                    <table width="100%">
                        <tr>
                            <td>User Name:</td>
                            <td>
                                <input type="text" name="username" id="username" style="width: 250px">
                            </td>
                        </tr>
                        <tr>
                            <td>Password:</td>
                            <td>
                                <input type="password" name="userpsw" id="userpsw" style="width: 250px">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div align="center" style="padding-top: 80px">
                <button id="cancel_btn">Cancel</button>
                <button id="guest_btn" >Continue as Guest</button>
                <button id="login_btn">Login</button>
            </div>
        </div>

        <div id="footer" style="margin-top: 10%">
            <div style="border-top: 1px solid gray">
                <p class="text_footer">&copy;&nbsp;Copyright 2014 MapIT Team</p>
            </div>
        </div>

        <div id="dialog"></div>

        <form name="input_form" id="input_form" action="Controllers/Map_Main_Controller.php" method="POST">
            <input type="hidden" name="userid_validated" id="userid_validated" value=-1>
            <input type="hidden" name="username_validated" id="username_validated" value="">
        </form>

    </body>
</html>

