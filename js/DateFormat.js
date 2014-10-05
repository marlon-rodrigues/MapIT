function convert_date(epoch_time, time_inc) {
    //add 5 hours, assumig user is in GTM -5
    var d = '';
    if(time_inc == 0){
        d = new Date(1000 * epoch_time); 
    } else {
        d = new Date((1000 * epoch_time) + 18000000); 
    }

    var m_names = new Array("Jan", "Feb", "Mar",
            "Apr", "May", "Jun", "Jul", "Aug", "Sep",
            "Oct", "Nov", "Dec");

    var a_p = "";        
    var month = d.getMonth();
    var day = d.getDate();
    var hour = d.getHours();
    var minute = d.getMinutes();
    var second = d.getSeconds();
    
    if (hour < 12) {
       a_p = "AM";
    } else {
       a_p = "PM";
    }
   
    if (hour == 0) {
       hour = 12;
    }
    
    if (hour > 12) {
       hour = hour - 12;
    }

    /*var output = d.getFullYear() + '-' +
     ((''+month).length<2 ? '0' : '') + month + '-' +
     ((''+day).length<2 ? '0' : '') + day + ' ' +
     ((''+hour).length<2 ? '0' :'') + hour + ':' +
     ((''+minute).length<2 ? '0' :'') + minute + ':' +
     ((''+second).length<2 ? '0' :'') + second;*/
    
    var output = '';
    
    if(time_inc == 0){
        output = 
                m_names[month] + ', ' +
                (('' + day).length < 2 ? '0' : '') + day + ' - ' +
                d.getFullYear() + ' ' +
                (('' + hour).length < 2 ? '0' : '') + hour + ':' +
                (('' + minute).length < 2 ? '0' : '') + minute + ':' +
                (('' + second).length < 2 ? '0' : '') + second + ' ' +
                a_p;
    } else {
        output = 
                m_names[month] + ', ' +
                (('' + day).length < 2 ? '0' : '') + day + ' - ' +
                d.getFullYear() + ' ';   
    }
    
    return output;
}

function convert_date_datepicker(epoch_time) {
    //add 5 hours, assumig user is in GTM -5
    var d = new Date((1000 * epoch_time) + 18000000); 

    var month = d.getMonth() + 1;
    var day = d.getDate();
    

    var output = ((''+month).length<2 ? '0' : '') + month + '/' +
     ((''+day).length<2 ? '0' : '') + day + '/' +
     d.getFullYear();
   
    return output;
}
