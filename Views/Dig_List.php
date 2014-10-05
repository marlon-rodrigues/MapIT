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
                var oTable = $('#dig_table').dataTable({
                    "sDom": 'T<"clear">lfrtip',
                    "bLengthChange": false,
                    "bProcessing": true,
                    "bJQueryUI": true,
                    "sPaginationType": "full_numbers",
                    "sScrollX": "100%",
                    "sScrollXInner": "150%",
                    //"bScrollCollapse": true,
                    "sScrollY": "270px",
                    "iDisplayLength": 10,
                    "oLanguage": {
                        "sSearch": "Search:"
                    },
                    "bDeferRender": true,
                    "bAutoWidth": false,
                    "bPaginate": true,
                    "aoColumnDefs": [
                        { "asSorting": [ "asc" ], "aTargets": [ 0 ] }
                     ],
                    "fnDrawCallback": function(oSettings){}
                });
                
                get_dig_sites();
                
                function get_dig_sites(){
                    $.ajax({
                        url: '../Controllers/Dig_Services.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            function_name: 'get_all_digs'
                        },
                        success: function(data) { 
                            if (!data['digs']) {
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
                                for(i = 0; i < data['digs'].length; i++){ 
                                    oTable.fnAddData([
                                        data['digs'][i]['name'], 
                                        '<span style="width:300px;word-break:break-word">' + data['digs'][i]['description'] + '</span>', 
                                        (data['digs'][i]['type'] == '0') ? 'Civil War Site' : 'Normal Site', 
                                        (data['digs'][i]['status'] == '0') ? 'Exploring' : 'Concluded',    
                                        (data['digs'][i]['initial_date'] == '' || data['digs'][i]['initial_date'] <= 0) ? ' - ' : convert_date(data['digs'][i]['initial_date'], 1),
                                        (data['digs'][i]['end_date'] == '' || data['digs'][i]['end_date'] <= 0) ? ' - ' : convert_date(data['digs'][i]['end_date'], 1)
                                    ]);
                                }
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
                <label style="font-weight: bold; color: #e17009; margin-left: 340px; font-size: 20px">Dig Sites List</label>
                <hr>
            </div>
            
            <div id="table" style="width: 95%; margin-left:20px; margin-top:20px">
                <table id="dig_table" class="display" style='word-wrap:break-word !important;'>
                    <thead>
                        <tr>
                            <th width="130px" align="left">Name</th>
                            <th width="300px" align="left">Description</th>
                            <th width="70px" align="left">Type</th>
                            <th width="70px" align="left">Status</th>
                            <th width="100px" align="left">Initial Date</th>
                            <th width="100px" align="left">End Date</th>
                        </tr>
                    </thead>
                </table>
                <div style="clear:both"></div>
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


