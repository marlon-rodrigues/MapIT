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
        <script src="../libraries/DataTables-1.9.4/media/js/jquery.js"></script>
        <script src="../libraries/DataTables-1.9.4/media/js/jquery.dataTables.js"></script>
        <link rel="stylesheet" href="../libraries/jquery-ui-1.9.2.custom/css/redmond/jquery-ui-1.9.2.custom.css" />
        <script src="../libraries/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.js"></script>
        <script src="../libraries/jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.min.js"></script>       
        <script src="../js/DateFormat.js"></script>
        
        <link rel="stylesheet" href="../libraries/css/primary.css" />
        <link rel="stylesheet" href="../libraries/DataTables-1.9.4/media/css/demo_table.css" />
        <link rel="stylesheet" href="../libraries/DataTables-1.9.4/media/css/demo_table_jui.css" />

        <title>MapIT - Manage User</title>
        
        <script>
            $(document).ready(function() {
                //setup datatable
                var oTable = $('#user_table').dataTable({
                    "sDom": 'T<"clear">lfrtip',
                    "bLengthChange": false,
                    "bProcessing": true,
                    "bJQueryUI": true,
                    "sPaginationType": "full_numbers",
                    "sScrollX": "100%",
                    "sScrollY": "200px",
                    "iDisplayLength": 5,
                    "oLanguage": {
                        "sSearch": "Search:"
                    },
                    "bDeferRender": true,
                    "bAutoWidth": false,
                    "bPaginate": true,
                    "aoColumnDefs": [
                        { "asSorting": [ "asc" ], "aTargets": [ 1 ] },
                        { "bSortable": false, "aTargets": [ 5 ] }, 
                        { "bVisible": false, "aTargets": [0] }
                     ],
                    "fnDrawCallback": function(oSettings){
                        $('.edit_user').button({
                            icons: {
                                primary: "ui-icon-pencil"
                            },
                            text:false
                        }).unbind('click').bind('click', function(){
                            $('#userid_edit').val($(this).attr('id')); 
                            $('#username_edit').val($(this).parent().prev().prev().prev().prev().html()); 
                            $('#addedit_form').submit();
                        });
                        
                        $('.delete_user').button({
                            icons: {
                                primary: "ui-icon-close"
                            },
                            text:false
                        }).unbind('click').bind('click', function(){ 
                            var id = $(this).attr('id');
                            var row = $(this).closest('tr').get(0);
                            $('#dialog').dialog({
                                    title: 'Delete',
                                    buttons: [
                                        {
                                            text: 'YES',
                                            click: function() {
                                                $(this).dialog('close');
                                                delete_user(id, row);
                                            }
                                        },
                                        {
                                            text: 'NO',
                                            click: function() {
                                                $(this).dialog('close');                                               
                                            }
                                        }]
                                }).html('Are you sure you want to delete user: ' + $(this).parent().prev().prev().prev().prev().html() + ' ?');
                        });
                    }
                });
                
                //setup buttons
                $('#add_btn').button({
                    icons: {
                        primary: "ui-icon-circle-plus"
                    }
                }).click(function() {
                    $('#userid_edit').val('');
                    $('#username_edit').val(''); 
                    $('#addedit_form').submit();
                });
                
                get_users();
                
                function get_users(){
                    $.ajax({
                        url: '../Controllers/User_Services.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            function_name: 'get_all_users'
                        },
                        success: function(data) { 
                            if (!data['users']) {
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
                                oTable.fnClearTable(); 
                                for(i = 0; i < data['users'].length; i++){ 
                                    oTable.fnAddData([
                                        data['users'][i]['id'], 
                                        data['users'][i]['user_name'], 
                                        (data['users'][i]['super_admin'] == '2') ? 'Yes' : 'No', 
                                        data['users'][i]['email'], 
                                        (data['users'][i]['last_login'] == '' || data['users'][i]['last_login'] <= 0) ? 'Never' : convert_date(data['users'][i]['last_login'], 0),
                                        '<button class="edit_user" id="' + data['users'][i]['id'] + '">Edit User</button>' +
                                        '<button class="delete_user" id="' + data['users'][i]['id'] + '">Delete User</button>'        
                                    ]);
                                }
                            }
                        }
                    });
                }
                
                function delete_user(user_id, row){
                    $.ajax({
                        url: '../Controllers/User_Services.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            function_name: 'delete_user',
                            userid_edit: user_id
                        },
                        success: function(data) { 
                                $('#dialog').dialog({
                                    title: 'Notice',
                                    buttons: [{
                                            text: 'OK',
                                            click: function() {
                                                $(this).dialog('close');
                                            }
                                        }]
                                }).html(data['message']);
                                
                                if(data['success']){
                                    //delete row from the table
                                    oTable.fnDeleteRow(oTable.fnGetPosition(row));
                                } 
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
        
        
        <div id="body" class="body_block_small">
            <div style="margin-top: 10px">
                <label style="font-weight: bold; color: #e17009; margin-left: 350px; font-size: 20px">Users List</label>
                <hr>
            </div>
            
            <div id="table" style="width: 95%; margin-left:20px; margin-top:20px">
                <table id="user_table" class="display">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th width="150px" align="left">Name</th>
                            <th width="90px" align="left">Super Admin</th>
                            <th width="200px" align="left">Email</th>
                            <th width="125px" align="left">Last Login</th>
                            <th width="70px"></th>
                        </tr>
                    </thead>
                </table>
                <div style="clear:both"></div>
            </div>
            
            <div align="center" style="margin-top:15px; font-size: 14px">
                <button id="add_btn">Add New User</button>
            </div>
        </div>

        <div id="footer" style="margin-top: 40px">
            <div style="border-top: 1px solid gray">
                <p class="text_footer">&copy;&nbsp;Copyright 2014 MapIT Team</p>
            </div>
        </div>

        <div id="dialog"></div>
        
        <form name="addedit_form" id="addedit_form" action="../Views/User_Manager_AddEdit.php" method="POST">
            <input type="hidden" name="userid_edit" id="userid_edit" value=-1>
            <input type="hidden" name="username_edit" id="username_edit" value=''>
        </form>
    </body>
</html>


