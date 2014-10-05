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

        <title>MapIT - Map</title>

        <script>
            $(document).ready(function() {
                //google map coordinates 
                var mapCenter = new google.maps.LatLng(38.612578, -90.175781);
                var map;
                var userstats = parseInt($('#userstats').text());

                //call function that initializes maps
                map_initialize();

                function map_initialize() {
                    var googleMapOptions = {
                        center: mapCenter, //map center
                        zoom: 5, //zoom level, 0 = earth view to higher value
                        maxZoom: 10, //max zoom level
                        panControl: true, //enable pan control
                        zoomControl: true, //enable zoom control
                        zoomControlOptions: {
                            style: google.maps.ZoomControlStyle.LARGE //zoom control size
                        },
                        scaleControl: false, //enable scale control
                        mapTypeId: google.maps.MapTypeId.ROADMAP, //google map type,
                        streetViewControl: false, //don't allow street view control
                        scrollwheel: false, //disable scrollwheel zoom
                        mapTypeControl: true, //disable map type control
                        navigationControl: false //disable navigation control
                    };

                    map = new google.maps.Map(document.getElementById("google_map"), googleMapOptions);
                    
                    //load maps saved in the db
                    if (userstats > 0){
                        load_marks_admin();
                    } else {
                        load_marks_guest();
                    }

                    //drop a new mark with mouse click for admin users
                    if(userstats > 0) {
                        google.maps.event.addListener(map, 'click', function(event) {
                            //Edit form to be displayed wiht new marked
                            var EditForm = '<p><div class="marker-edit">' +
                                    '<form action="" method="POST" name="saveMarker" id="saveMarker">' +
                                    '<table id="dig_site_tabe">' +
                                    '<tr>' +
                                    '<td><span>Site Name:</span></td>' +
                                    '<td><input type="text" name="mName" class="dig_name" placeHolder="Enter Dig Site Name" maxlength="50" style="width:228px"/></td>' +
                                    '</tr><tr>' +
                                    '<td><span>Site Description:</span></td>' +
                                    '<td><textarea name="mDesc" class="dig_desc" placeHolder="Enter Dig Site Description" maxlength="350" style="width:229px" rows="4" cols="30"></textarea></td>' +
                                    '</tr><tr>' +
                                    '<td><span>Initial Date of Exploration:</span></td>' +
                                    '<td><input type="date" name="mInitialDate" class="dig_init_date" value="' + $.datepicker.formatDate('yy-mm-dd', new Date()) + '"/></td>' +
                                    '</tr><tr>' +
                                    '<td><span>Final Date of Exploration:</span></td>' +
                                    '<td><input type="date" name="mFinalDate" class="dig_final_date" value="' + $.datepicker.formatDate('yy-mm-dd', new Date()) + '"/></td>' +
                                    '</tr><tr>' +
                                    '<td><span>Type of Site:</span></td>' +
                                    '<td><select name="mType" class="dig_type" style="width:125px"><option value="0">War Site</option><option value="1">Other Type</option></select></td>' +
                                    '</tr><tr>' +
                                    '<td><span>Status of Site:</span></td>' +
                                    '<td><select name="mStatus" class="dig_status" style="width:125px"><option value="0">Exploring</option><option value="1">Concluded</option></select></td>' +
                                    '</tr>' +
                                    '</table>' +
                                    '</form>' +
                                    '</div></p>' +
                                    '<hr>' +
                                    '<button name="save_marker" id="save_marker" class="save_marker">Save Marker</button>';

                            //call create marker function
                            create_marker(event.latLng, 'New Site', EditForm, true, true, true, '../libraries/images/museum_war.png');
                        });
                    }
                }

                function create_marker(MapPos, MapTitle, MapDesc, InfoOpenDefault, Dragable, Removable, iconPath) {
                    //new marker
                    var marker = new google.maps.Marker({
                        position: MapPos, //map coordinates where user clicks on map
                        map: map,
                        draggable: Dragable, //set mark draggable
                        animation: google.maps.Animation.DROP, //bounce animation
                        title: MapTitle,
                        icon: iconPath //map pin icon
                    });

                    //action for simple info window
                    //Content structure of info window simple for the markers
                    var contentStringSimple = $('<div style="width:auto; height:auto">' +
                                '<h4>' + MapTitle + '</h4>' +
                                '<br>' +
                                '</div>');

                    var infoWindowSimple = new google.maps.InfoWindow();
                    
                    //set the content of info window simple
                    infoWindowSimple.setContent(contentStringSimple[0]);
                    
                    google.maps.event.addListener(marker, 'mouseover', function() {
                        infoWindowSimple.open(map, this);
                    });

                    // assuming you also want to hide the infowindow when user mouses-out
                    google.maps.event.addListener(marker, 'mouseout', function() {
                        infoWindowSimple.close();
                    });

                    //action for add/remove windows
                    var contentString = '';
                    
                    //if user is a guess only show dig site info
                    if(userstats < 1){
                        //Content structure of info window for the markers
                        contentString = $('<div style="min-width:280px; height:auto">' +
                                '<div><span class="info_content">' +
                                '<h3>' + MapTitle + '</h3>' +
                                MapDesc +     
                                '<br><br><br>' +
                                '</div></div>');
                    } else {
                        //Content structure of info window for the markers
                        contentString = $('<div style="width:auto; height:auto">' +
                                '<div><span class="info_content">' +
                                '<h3>' + MapTitle + '</h3>' +
                                MapDesc +
                                '</span><button name="remove_marker" id="remove_marker" class="remove_marker">Remove Site</button>' +
                                '<br><br><br><br>' +
                                '</div></div>');
                    }
                    
                    //create info window
                    var infoWindow = new google.maps.InfoWindow();

                    //set the content of info window
                    infoWindow.setContent(contentString[0]);

                    //Find remove button in info window
                    var removeBtn = contentString.find('button.remove_marker')[0];

                    //Find save button in info window
                    var saveBtn = contentString.find('button.save_marker')[0];
                    
                    //Find update button in info window
                    var updateBtn = contentString.find('button.update_marker')[0];

                    //add click listener to remove marker button
                    if (typeof removeBtn !== 'undefined') {
                        google.maps.event.addDomListener(removeBtn, "click", function(event) {
                            //call remove marker function
                            remove_marker(marker, infoWindow);
                        });
                    }

                    //continue only when save button is present
                    if (typeof saveBtn !== 'undefined') {
                        //add click listener to save marker
                        google.maps.event.addDomListener(saveBtn, "click", function(event) {
                            //html to be replaced after success 
                            var mName = contentString.find('input.dig_name')[0].value;
                            var mDesc = contentString.find('textarea.dig_desc')[0].value;
                            var mInitialDate = contentString.find('input.dig_init_date')[0].value;
                            var mFinalDate = contentString.find('input.dig_final_date')[0].value;
                            var mType = contentString.find('select.dig_type')[0].value;
                            var mStatus = contentString.find('select.dig_status')[0].value;

                            save_marker(marker, mName, mDesc, mInitialDate, mFinalDate, mType, mStatus, infoWindow);                           
                        });
                    }
                    
                     //continue only when save button is present
                    if (typeof updateBtn !== 'undefined') {
                        //add click listener to save marker
                        google.maps.event.addDomListener(updateBtn, "click", function(event) {
                            //html to be replaced after success 
                            var mName = contentString.find('input.dig_name')[0].value;
                            var mDesc = contentString.find('textarea.dig_desc')[0].value;
                            var mInitialDate = contentString.find('input.dig_init_date')[0].value;
                            var mFinalDate = contentString.find('input.dig_final_date')[0].value;
                            var mType = contentString.find('select.dig_type')[0].value;
                            var mStatus = contentString.find('select.dig_status')[0].value;

                            update_marker(marker, mName, mDesc, mInitialDate, mFinalDate, mType, mStatus, infoWindow);                           
                        });
                    }                   

                    //add click listener to save marker button
                    google.maps.event.addListener(marker, 'click', function() {
                        //click on marker opens info window
                        infoWindow.open(map, marker);
                        infoWindowSimple.close();
                    });

                    //wheter info window should be open by default
                    if (InfoOpenDefault) {
                        infoWindow.open(map, marker);
                    }
                }

                //remove marker function
                function remove_marker(Marker, infoWindow) {
                    //determine wether marker is draggable 
                    //new markers are draggable and saved markers are fixed
                    if(Marker.getDraggable()){
                        //just remove new marker
                        Marker.setMap(null);
                    } else {
                        //get marker position
                        var mLatLang = Marker.getPosition().toUrlValue();
                        var mLatitude = mLatLang.substr(0, mLatLang.indexOf(','));
                        var mLongitude = mLatLang.substr(mLatLang.indexOf(',') + 1);
                        
                        $.ajax({
                            url: '../Controllers/Map_Services.php',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                function_name: 'delete_dig_site_by_latlong',
                                latitude: mLatitude,
                                longitude: mLongitude
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

                                if (data['success']) {
                                    Marker.setMap(null);
                                    infoWindow.close();
                                }
                            }
                        });
                    }
                }

                //save marker function
                function save_marker(Marker, mName, mDesc, mInitialDate, mFinalDate, mType, mStatus, infoWindow) {
                    //get marker position
                    var mLatLang = Marker.getPosition().toUrlValue();
                    var mLatitude = mLatLang.substr(0, mLatLang.indexOf(','));
                    var mLongitude = mLatLang.substr(mLatLang.indexOf(',') + 1);
     
                    $.ajax({
                        url: '../Controllers/Map_Services.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            function_name: 'add_dig_site',
                            name: mName,
                            latitude: mLatitude,
                            longitude: mLongitude,
                            description: mDesc,
                            initial_date: mInitialDate,
                            end_date: mFinalDate,
                            type: mType,
                            status: mStatus
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
                            
                            if (data['success']) {
                                Marker.setDraggable(false);
                                if(mType == '0'){
                                    Marker.setIcon('../libraries/images/museum_war_dark.png');
                                } else {
                                    Marker.setIcon('../libraries/images/map_icon_dark.png');
                                }
                                infoWindow.close();
                            }
                        }
                    });  
                }
                
                //save marker function
                function update_marker(Marker, mName, mDesc, mInitialDate, mFinalDate, mType, mStatus, infoWindow) {
                    //get marker position
                    var mLatLang = Marker.getPosition().toUrlValue();
                    var mLatitude = mLatLang.substr(0, mLatLang.indexOf(','));
                    var mLongitude = mLatLang.substr(mLatLang.indexOf(',') + 1);
     
                    $.ajax({
                        url: '../Controllers/Map_Services.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            function_name: 'update_dig_site_by_latlong',
                            name: mName,
                            latitude: mLatitude,
                            longitude: mLongitude,
                            description: mDesc,
                            initial_date: mInitialDate,
                            end_date: mFinalDate,
                            type: mType,
                            status: mStatus
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
                            
                            if (data['success']) {
                                Marker.setDraggable(false);
                                if(mType == '0'){
                                    Marker.setIcon('../libraries/images/museum_war_dark.png');
                                } else {
                                    Marker.setIcon('../libraries/images/map_icon_dark.png');
                                }
                                infoWindow.close();
                            }
                        }
                    });  
                }                
                
                //load dig sites for admin users
                function load_marks_admin(){
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
                                var EditForm = '';
                                var icon = '';
                                var ini_date = '';
                                var end_date = '';
                                var status_sel_1 = '';
                                var status_sel_2 = '';
                                var type1 = '';
                                var type2 = '';
                                
                                for(i = 0; i < data['digs'].length; i++){ 
                                    ini_date = (data['digs'][i]['initial_date'] == '' || data['digs'][i]['initial_date'] <= 0) ? '' : $.datepicker.formatDate('yy-mm-dd', new Date(1000*data['digs'][i]['initial_date'] + 18000000));
                                    end_date = (data['digs'][i]['end_date'] == '' || data['digs'][i]['end_date'] <= 0) ? '' : $.datepicker.formatDate('yy-mm-dd', new Date(1000*data['digs'][i]['end_date'] + 18000000));                                   
                                    status_sel_1 = (data['digs'][i]['type'] == '0') ? ' selected' : '';
                                    status_sel_2 = (data['digs'][i]['type'] == '1') ? ' selected' : '';
                                    type1 = (data['digs'][i]['status'] == '0') ? ' selected' : '';
                                    type2 = (data['digs'][i]['status'] == '1') ? ' selected' : '';
                                    
                                    EditForm = '<p><div class="marker-edit">' +
                                        '<form action="" method="POST" name="saveMarker" id="saveMarker">' +
                                        '<table id="dig_site_tabe">' +
                                        '<tr>' +
                                        '<td><span>Site Name:</span></td>' +
                                        '<td><input type="text" name="mName" class="dig_name" placeHolder="Enter Dig Site Name" maxlength="50" style="width:228px" value="' + data['digs'][i]['name'] + '"/></td>' +
                                        '</tr><tr>' +
                                        '<td><span>Site Description:</span></td>' +
                                        '<td><textarea name="mDesc" class="dig_desc" placeHolder="Enter Dig Site Description" maxlength="350" style="width:229px" rows="4" cols="30">' + data['digs'][i]['description'] +'</textarea></td>' +
                                        '</tr><tr>' +
                                        '<td><span>Initial Date of Exploration:</span></td>' +
                                        '<td><input type="date" name="mInitialDate" class="dig_init_date" value="' + ini_date + '"/></td>' +
                                        '</tr><tr>' +
                                        '<td><span>Final Date of Exploration:</span></td>' +
                                        '<td><input type="date" name="mFinalDate" class="dig_final_date" value="' + end_date + '"/></td>' +
                                        '</tr><tr>' +
                                        '<td><span>Type of Site:</span></td>' +
                                        '<td><select name="mType" class="dig_type" style="width:125px"><option value="0" ' + status_sel_1 + '>War Site</option>' + 
                                            '<option value="1" ' + status_sel_2 +'>Other Type</option></select></td>' +
                                        '</tr><tr>' +
                                        '<td><span>Status of Site:</span></td>' +
                                        '<td><select name="mStatus" class="dig_status" style="width:125px"><option value="0" ' + type1 + '>Exploring</option>' + 
                                            '<option value="1" ' + type2 + '>Concluded</option></select></td>' +
                                        '</tr>' +
                                        '</table>' +
                                        '</form>' +
                                        '</div></p>' +
                                        '<hr>' +
                                        '<button name="update_marker" id="update_marker" class="update_marker">Update Site</button>';
                                
                                    var pos = new google.maps.LatLng(parseFloat(data['digs'][i]['latitude']), parseFloat(data['digs'][i]['longitude']));
                                
                                    if(data['digs'][i]['type'] == '0'){
                                        icon = '../libraries/images/museum_war_dark.png';
                                    } else {
                                       icon = '../libraries/images/map_icon_dark.png';
                                    }
                                    
                                    create_marker(pos, data['digs'][i]['name'], EditForm, false, false, false, icon);
                                    
                                    EditForm = '';
                                    icon = '';
                                    ini_date = '';
                                    end_date = '';
                                    status_sel_1 = '';
                                    status_sel_2 = '';
                                    type1 = '';
                                    type2 = '';
                                }
                            }
                        }
                    });
                }
                
                //load dig sites for guest users
                function load_marks_guest(){
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
                                var EditForm = '';
                                var icon = '';
                                var ini_date = '';
                                var end_date = '';
                                var status = '';
                                var type = '';
                                
                                for(i = 0; i < data['digs'].length; i++){ 
                                    ini_date = (data['digs'][i]['initial_date'] == '' || data['digs'][i]['initial_date'] <= 0) ? '' : $.datepicker.formatDate('yy-mm-dd', new Date(1000*data['digs'][i]['initial_date'] + 18000000));
                                    end_date = (data['digs'][i]['end_date'] == '' || data['digs'][i]['end_date'] <= 0) ? '' : $.datepicker.formatDate('yy-mm-dd', new Date(1000*data['digs'][i]['end_date'] + 18000000));                                   
                                    status = (data['digs'][i]['type'] == '0') ? ' Civil War Site' : 'Normal Site';
                                    type = (data['digs'][i]['status'] == '0') ? ' Exploring' : 'Concluded';
                                                                      
                                    EditForm = '<p><div class="marker-edit">' +
                                        '<form action="" method="POST" name="saveMarker" id="saveMarker">' +
                                        '<table id="dig_site_tabe">' +
                                        '<tr>' +
                                        '<td><span>Site Name:</span></td>' +
                                        '<td>' +data['digs'][i]['name'] + '</td>' +
                                        '</tr><tr>' +
                                        '<td><span>Site Description:</span></td>' +
                                        '<td><span style="width:250px;word-break:break-word">' + data['digs'][i]['description'] +'</span></td>' +
                                        '</tr><tr>' +
                                        '<td width="150px"><span>Initial Date of Exploration:</span></td>' +
                                        '<td>' + ini_date + '</td>' +
                                        '</tr><tr>' +
                                        '<td><span>Final Date of Exploration:</span></td>' +
                                        '<td>' + end_date + '</td>' +
                                        '</tr><tr>' +
                                        '<td><span>Type of Site:</span></td>' +
                                        '<td>' + status + '</td>' +
                                        '</tr><tr>' +
                                        '<td><span>Status of Site:</span></td>' +
                                        '<td>' + type + '</td>' +
                                        '</tr>' +
                                        '</table>' +
                                        '</form>' +
                                        '</div></p>';
                                
                                    var pos = new google.maps.LatLng(parseFloat(data['digs'][i]['latitude']), parseFloat(data['digs'][i]['longitude']));
                                
                                    if(data['digs'][i]['type'] == '0'){
                                        icon = '../libraries/images/museum_war_dark.png';
                                    } else {
                                       icon = '../libraries/images/map_icon_dark.png';
                                    }
                                    
                                    create_marker(pos, data['digs'][i]['name'], EditForm, false, false, false, icon);
                                    
                                    EditForm = '';
                                    icon = '';
                                    ini_date = '';
                                    end_date = '';
                                    status = '';
                                    type = '';
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
        
        <div id="body" class="body_block">
            <label id="userstats" style="display:none"><?php echo $_SESSION['userstatus'] ?></label>
            <div id="google_map"></div>
        </div>

        <div id="footer" style="margin-top: 40px">
            <div style="border-top: 1px solid gray">
                <p class="text_footer">&copy;&nbsp;Copyright 2014 MapIT Team</p>
            </div>
        </div>

        <div id="dialog"></div>
    </body>
</html>
