/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {
//setup buttons
    $('#logout_btn').button({
        icons: {
            primary: "ui-icon-custom_logout"
        },
        text: false
    }).click(function() {
        $.ajax({
            url: '../Controllers/Login_Services.php',
            type: 'POST',
            dataType: 'json',
            data: {
                function_name: 'logout',
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
                    }).html(data['Problem on logout. Please, contact administrator.']);
                } else {
                    window.location = "http://www.mocivilwar.org";
                }
            }
        });
    });

    $('#dig_list_btn').button({
        icons: {
            primary: "ui-icon-custom_diglist"
        },
        text: false
    }).click(function() {
        window.location = "../Controllers/Dig_List_Controller.php";
    });

    $('#admin_manager_btn').button({
        icons: {
            primary: "ui-icon-custom_admin"
        },
        text: false
    }).click(function() {
        window.location = "../Controllers/Admin_Manager_Controller.php";
    });


    $('#map_view_btn').button({
        icons: {
            primary: "ui-icon-custom_map"
        },
        text: false
    }).click(function() {
        window.location = "../Views/Map_Main.php";
    });
});


