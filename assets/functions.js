isNS4 =	(navigator.appName.indexOf("Netscape")>=0 && !document.getElementById) ? true : false;
isIE4 =	(document.all && !document.getElementById) ? true : false;
isIE5 =	(document.getElementById && document.all) ? true : false;
isNS6 =	(document.getElementById && navigator.appName.indexOf("Netscape")>=0 ) ? true : false;
isW3C =	(document.getElementById && 1) ? true : false;

function award(id) {
    $('#awards_requested').val('');
    $('#Award_'+id).val($('#Award_'+id).val()==1 ? 0 : 1);
    toggleDivDisplay(id);
    $('input').each(
        function(key, value){
            if ($(this).attr('id') && $(this).attr('id').substr(0,6) == "Award_" && $(this).val()=='1') {
                $('#awards_requested').val(
                    $('#awards_requested').val() +
                    '  * ' + $(this).attr('id').substr(6).replace(/_/g,' ') +
                    '\n'
                )
            }
        }
    );
    if ($('#awards_requested').val()) {
        $('#awards_requested').val(
            "NDB LIST AWARD REQUEST:\n" +
            "To:    " + $('#awards_coordinator_name').val() + "\n" +
            "From:  " + $('#awards_requester').val() + "\n" +
            "URL:   " + $('#awards_url').val() + "\n\n" +
            "I would like to request the following awards, based upon my "+
            "published logs in RNA / REU / RWW.\n" +
            "I confirm that have not previously applied for these awards.\n\n" +
            $('#awards_requested').val() + "\n\n" +
            "Sincerely,\n" + $('#awards_name').val()
        );
        $('#order').prop('disabled', false);
        return;
    }
    $('#awards_requested').val('(No certificates have been ordered)');
    $('#order').prop('disabled', true);
}

function award_place_order() {
    if ($('#awards_email').val()=='(enter email address)' || $('#awards_email').val()=='') {
        alert('Please enter your email address');
        return;
    }
    if (
        confirm(
            "CONFIRM ORDER\nPlease confirm that you have verified the details in this form, including the\n" +
            "Reply To email address to which the awards will be emailed.\n\n" +
            "* Press 'OK' to send your request now.\n"+
            "* Press 'Cancel' if you wish to go back and check again."
        )
    ) {
        $('#submode').val('send');
        $('#form').submit();
    }
}

function check_for_tabs(frm) {
    if (frm.log_entries.value.search(/	/)!=-1) {
        frm.conv.disabled=0;
        return true;
    }
    frm.conv.disabled=1;
    return false;
}

function get_type(form){
    if (form.type_NDB.checked)    return 0;
    if (form.type_DGPS.checked)   return 1;
    if (form.type_TIME.checked)   return 2;
    if (form.type_NAVTEX.checked) return 3;
    if (form.type_HAMBCN.checked) return 4;
    if (form.type_OTHER.checked)  return 5;
    if (form.type_DSC.checked)    return 6;
    return '';
}

function set_signal_list_types(form, state){
    with (form) {
        type_DGPS.checked=state;
        type_DSC.checked=state;
        type_HAMBCN.checked=state;
        type_NAVTEX.checked=state;
        type_NDB.checked=state;
        type_OTHER.checked=state;
        type_TIME.checked=state;
        type_ALL.checked=state;
    }
}

function clear_signal_list(form){
    with (form) {
        filter_dx_gsq.value="";
        filter_dx_max.value="";
        filter_dx_min.value="";
        filter_sp_itu_clause.value="AND";
        filter_heard_in.value="";
        radio_filter_heard_in_mod_any.checked=1;
        filter_dx_units_km.checked=1;
        form['filter_listener[]'].selectedIndex=0;
        filter_khz_1.value="";
        filter_khz_2.value="";
        filter_sp.value="";
        filter_itu.value="";
        filter_continent.selectedIndex=0;
        filter_channels.selectedIndex=0;
        filter_id.value="";
        filter_heard_in.disabled=0;
        filter_heard_in.className="formField";
        type_DGPS.checked=0;
        type_DSC.checked=0;
        type_HAMBCN.checked=0;
        type_NAVTEX.checked=0;
        type_NDB.checked=1;
        type_OTHER.checked=0;
        type_TIME.checked=0;
        type_ALL.checked=0;
        filter_last_date_1.value="";
        filter_last_date_2.value="";
        if (typeof filter_listener_invert_0 !== 'undefined') {
            filter_listener_invert_0.checked = 1;
        }
        if (typeof region !== 'undefined') {
            region.selectedIndex=0;
        }
        if (typeof show_list !== 'undefined') {
            show_list.checked=1;
        }
        if (typeof filter_logged_date_1 !== 'undefined') {
            filter_logged_date_1.value = "";
            filter_logged_date_2.value = "";
        }
        if (typeof filter_first_date_1 !== 'undefined') {
            filter_first_date_1.value="";
            filter_first_date_2.value="";
        }
        if (typeof chk_filter_active !== 'undefined') {
            chk_filter_active.checked=0;
        }
    }
    set_range(form);
}

function clear_state_map(form){
    form.filter_listener.selectedIndex=0;
    form.simple.selectedIndex=0;
    form.type_DGPS.checked=0;
    form.type_HAMBCN.checked=0;
    form.type_NAVTEX.checked=0;
    form.type_NDB.checked=1;
    form.type_OTHER.checked=0;
    form.type_TIME.checked=0;
    form.places.selectedIndex=0;
    form.chk_filter_active.checked=0;
    form.hide_placenames.checked=0;
    form.hide_labels.checked=0;
}

function colSort(sortBy, current, desc) {
    if (typeof desc!=='undefined' && desc===1){
        $('#sort_by').val(current==sortBy+'_d' ? sortBy : sortBy+'_d');
    } else {
        $('#sort_by').val(sortBy+(current==sortBy ? '_d' : ''));
    }
    document.form.submit();
}

function column_over(obj_cell,int_state) {
    switch (int_state) {
        case 0: obj_cell.className = 'downloadTableHeadings'; break;
        case 1: obj_cell.className = 'downloadTableHeadings_over'; break;
        case 2: obj_cell.className = 'downloadTableHeadings_click'; break;
    }
    return true;
}

function signalsListChangeSortControl() {
    var option = $('#sort_by_column').val();
    $('#sort_by').val(option + ($('#sort_by_d').is(':checked') ? '_d' : ''));
    if (option=='CLE64' && $('#filter_dx_gsq').val().length!=6) {
        alert('CLE64\nThis option requires that you provide \na GSQ from which to measure distance');
        return;
    }
    document.form.submit();
}

function conv_dd_dddd(form) {
    var dec_lat =	parseFloat(form.lat_dddd.value);
    var dec_lon =	parseFloat(form.lon_dddd.value);
    if (isNaN(dec_lat)) {
        alert("Latitude must be a decimal number");
        return false;
    }
    if (isNaN(dec_lon)) {
        alert("Longitude must be a decimal number");
        return false;
    }
    var lat_h =		(dec_lat>0 ? "N" : "S")
    var lat_abs =	Math.abs(dec_lat);
    var lat_dd =		Math.floor(lat_abs);
    var lat_mm =		lead(Math.floor(60*(lat_abs%1)));
    var lat_ss =		lead(Math.floor((lat_abs-lat_dd-(lat_mm/60))*3600));
    var lon_h =		(dec_lon>0 ? "E" : "W")
    var lon_abs =	Math.abs(dec_lon);
    var lon_dd =		Math.floor(lon_abs);
    var lon_mm =		lead(Math.floor(60*(lon_abs%1)));
    var lon_ss =		lead(Math.floor((lon_abs-lon_dd-(lon_mm/60))*3600));
    form.lat_dd_mm_ss.value =	lat_dd+"."+lat_mm+"."+lat_ss+"."+lat_h
    form.lon_dd_mm_ss.value =	lon_dd+"."+lon_mm+"."+lon_ss+"."+lon_h
    return true;
}

function conv_dd_mm_ss(form) {
    var rexp =		/([0-9]+)[� \.]*([0-9]+)[' \.]*([0-9]+)*[" \.]*([NS])*/i;
    var a =			form.lat_dd_mm_ss.value.match(rexp);
    if (a==null) {
        alert("ERROR\n\nLatitude must be given in one of these formats:\n  DD�MM'SS\"H\n  DD.MM.SS.H\n  DD MM SS H\n  DD�MM.H\n  DD.MM.H\n  DD MM H\n\n(H is N or S, but defaults to N if not given)");
        return false;
    }
    var deg =		parseFloat(a[1]);
    var min =		parseFloat(a[2]);
    var sec =		(a[3]!="" ? parseFloat(a[3]) : 0);
    var min_d =		min+sec/60;
    var hem =		(a[4]!="" ? (a[4]=="N"||a[4]=="n" ? 1 : -1) : 1);
    var dec_lat =	hem*(deg+(Math.round(((sec/3600)+(min/60))*10000))/10000);
    var rexp =		/([0-9]+)[� \.]*([0-9]+)[' \.]*([0-9]+)*[" \.]*([EW])*/i;
    var a =			form.lon_dd_mm_ss.value.match(rexp);
    if (a==null) {
        alert("ERROR\n\nLongitude must be given in one of these formats:\n  DD.MM.SS.H\n  DD MM SS H\n  DD.MM.H\n  DD MM H\n\n(H is E or W, but defaults to E if not given)");
        return;
    }
    var deg =		parseFloat(a[1]);
    var min =		parseFloat(a[2]);
    var sec =		(a[3]!="" ? parseFloat(a[3]) : 0);
    var min_d =		min+sec/60;
    var hem =		(a[4]!="" ? (a[4]=="E"||a[4]=="e" ? 1 : -1) : 1);
    var dec_lon =	hem*(deg+(Math.round(((sec/3600)+(min/60))*10000))/10000);
    form.lat_dddd.value=dec_lat;
    form.lon_dddd.value=dec_lon;
    return true;
}

function deg_gsq(form) {
    var letters = "abcdefghijklmnopqrstuvwxyz";
    if (form.lat_dddd.value==""||form.lon_dddd.value=="") {
        return;
    }
    lat =		parseFloat(form.lat_dddd.value)+90;
    var lat_a =	letters.charAt(Math.floor(lat/10)).toUpperCase();
    var lat_b =	Math.floor(lat%10);
    var lat_c =	letters.charAt(Math.floor(24*(lat%1)))
    lon =		(parseFloat(form.lon_dddd.value)+180)/2;
    var lon_a =	letters.charAt(Math.floor(lon/10)).toUpperCase();
    var lon_b =	Math.floor(lon%10);
    var lon_c =	letters.charAt(Math.floor(24*(lon%1)))
    form.GSQ.value= lon_a+lat_a+lon_b+lat_b+lon_c+lat_c;
}

function DGPS(id1,id2,ref,khz,bps,qth,sta,cnt,act){
    if (typeof dgps[id1] == 'undefined') {
        dgps[id1] =		new Array();
        dgps[id1][0] =	new Array(ref,khz,bps,qth,sta,cnt,id1,id2,act);
    } else {
        dgps[id1][dgps[id1].length] =	new Array(ref,khz,bps,qth,sta,cnt,id1,id2,act);
    }
    if (typeof dgps[id2] == 'undefined') {
        dgps[id2] =		new Array();
        dgps[id2][0] =	new Array(ref,khz,bps,qth,sta,cnt,id1,id2,act);
    } else {
        dgps[id2][dgps[id2].length] =	new Array(ref,khz,bps,qth,sta,cnt,id1,id2,act);
    }
}

function dgps_lookup(id) {
    var out = new Array();
        if (typeof dgps[parseFloat(id)] == 'undefined') {
        return "Station not recognised";
    }
    id = parseFloat(id);
    for (var i=0; i < dgps[id].length; i++) {
        var a =	dgps[id][i];
        out[i] =	"Station "+a[0]+(a[8]=='0' ? " (Inactive)": "")+"\n"+a[1]+"KHz "+a[2]+"bps"+"\n"+a[3]+" "+a[4]+" "+a[5]+"\nReference ID(s): "+a[6]+(a[6]!=a[7] ? ", "+a[7] : "");
    }
    if (i>1) {
        return "Multiple matches (" + i + "):\n\n"+out.join("\n\n");
    }
    return out.join("");
}

function export_signallist_excel() {
    if (
        !confirm(
            "EXPORT ENTIRE "+system+" DATABASE TO EXCEL\n"+
            "This can be a time consuming process - typically a minute or more.\n"+
            "Signal types, 'offsets' setting and 'active' status will be taken from the options\n"+
            "you selected in \"Customise Report\" above, other options are ignored.\n"+
            "Continue?"
        )
    ){
        return;
    }
    popWin(
        system_URL+'/export_signallist_excel?'+
        (document.form.filter_active.checked ? '&filter_active=1' : '')+
        (document.form.offsets.options[document.form.offsets.selectedIndex].value!='' ? '&offsets=abs' : '')+
        (document.form.type_DGPS.checked ? '&type_DGPS=1' : '')+
        (document.form.type_DSC.checked ? '&type_DSC=1' : '')+
        (document.form.type_HAMBCN.checked ? '&type_HAMBCN=1' : '')+
        (document.form.type_NAVTEX.checked ? '&type_NAVTEX=1' : '')+
        (document.form.type_NDB.checked ? '&type_NDB=1' : '')+
        (document.form.type_TIME.checked ? '&type_TIME=1' : '')+
        (document.form.type_OTHER.checked ? '&type_OTHER=1' : ''),
        'popExcel','scrollbars=1,toolbar=1,menubar=1,status=1,resizable=1',800,550,'centre'
    );
}

function filtered_signallist_map() {
    popWin(
        system_URL+'/filtered_signallist_map?'+
        (document.form.filter_active.checked ? '&filter_active=1' : '')+
        (document.form.offsets.options[document.form.offsets.selectedIndex].value!='' ? '&offsets=abs' : '')+
        (document.form.type_DGPS.checked ? '&type_DGPS=1' : '')+
        (document.form.type_DSC.checked ? '&type_DSC=1' : '')+
        (document.form.type_HAMBCN.checked ? '&type_HAMBCN=1' : '')+
        (document.form.type_NAVTEX.checked ? '&type_NAVTEX=1' : '')+
        (document.form.type_NDB.checked ? '&type_NDB=1' : '')+
        (document.form.type_TIME.checked ? '&type_TIME=1' : '')+
        (document.form.type_OTHER.checked ? '&type_OTHER=1' : ''),
        'popSignalMap','scrollbars=1,toolbar=1,menubar=1,status=1,resizable=1',1024,768, 'centre'
    );
}

function export_signallist_pdf() {
    if (
        !confirm(
            "EXPORT ENTIRE "+system+" DATABASE TO PDF\n"+
            "This can be a time consuming process - typically a minute or more.\n"+
            "Signal types, 'offsets' setting and 'active' status will be taken from the options\n"+
            "you selected in \"Customise Report\" above, other options are ignored.\n"+
            "Continue?"
        )
    ) {
        return;
    }
    popWin(
        system_URL+'/export_signallist_pdf?'+
        (document.form.filter_active.checked ? '&filter_active=1' : '')+
        (document.form.offsets.options[document.form.offsets.selectedIndex].value!='' ? '&offsets=abs' : '')+
        (document.form.type_DGPS.checked ? '&type_DGPS=1' : '')+
        (document.form.type_DSC.checked ? '&type_DSC=1' : '')+
        (document.form.type_HAMBCN.checked ? '&type_HAMBCN=1' : '')+
        (document.form.type_NAVTEX.checked ? '&type_NAVTEX=1' : '')+
        (document.form.type_NDB.checked ? '&type_NDB=1' : '')+
        (document.form.type_TIME.checked ? '&type_TIME=1' : '')+
        (document.form.type_OTHER.checked ? '&type_OTHER=1' : ''),
        'popPDF','scrollbars=1,toolbar=1,menubar=1,status=1,resizable=1',800,550,'centre'
    );
}

function export_signallist_ilg() {
    if (
        !confirm(
        "EXPORT ENTIRE "+system+" DATABASE TO IRGRadio Database format\n"+
        "All options selected in \"Customise Report\" above will be ignored.\n"+
        "Continue?"
        )
    ) {
        return;
    }
    document.location=system_URL+'/ILGRadio_signallist';
}

function find_ICAO() {
    popWin(
        system_URL+'/find_ICAO',
        'popFindICAO',
        'scrollbars=0,toolbar=0,menubar=0,status=0,resizable=1',
        250,
        180,
        'centre'
    );
}

function get_cookie(which) {
    var cookies =		document.cookie;
    var pos =		cookies.indexOf(which+"=");
    if (pos == -1) {
        return false;
    }
    var start =	pos + which.length+1;
    var end =	cookies.indexOf(";",start);
    if (end == -1) {
        end =	cookies.length;
    }
    return unescape(cookies.substring(start, end));
}

function gsq_deg(form){
    var GSQ = form.GSQ.value.toUpperCase()
    if (GSQ=="") {
        alert("GSQ must be in one of these formats:\nXXnnxx or XXnn");
        return false;
    }
    var offset =	(GSQ.length==6 ? 1/48 : 0);
    if(GSQ.length == 4) {
        GSQ = GSQ+"MM";
    }
    lon_d = GSQ.charCodeAt(0)-65;
    lon_m = parseFloat(GSQ.substr(2,1));
    lon_s = GSQ.charCodeAt(4)-65;
    lat_d = GSQ.charCodeAt(1)-65;
    lat_m = parseFloat(GSQ.substr(3,1));
    lat_s = GSQ.charCodeAt(5)-65;
    lon = Math.round((2 * (lon_d*10 + lon_m + lon_s/24 + offset) - 180)*10000)/10000;
    lat = Math.round((lat_d*10 + lat_m + lat_s/24 + offset - 90)*10000)/10000;
    form.lat_dddd.value= lat
    form.lon_dddd.value= lon
    return true;
}

function itu(ITU) {
    if (window.opener && window.opener.form) {
        if (window.opener.form.ITU) {
            window.opener.form.SP.value = "";
            window.opener.form.ITU.value = ITU;
            window.opener.focus();
        }
        if (window.opener.form.filter_itu) {
            window.opener.form.filter_sp.value = "";
            window.opener.form.filter_itu.value = ITU;
            window.opener.focus();
        }
    }
}

function lead(num) {
    return (num.toString().length==1 ? "0"+num : num)
}

function line_up(frm) {
    var log_arr = frm.log_entries.value.split('\n');
    var line, log, word, words;
    var max_words = 0;
    var word_len_arr = new Array();
    for (idx in log_arr) {
        line = log_arr[idx].replace(/^\s+|\s+$/g,'').replace(/\s+/g,' ');
        words = line.split(' ').length;
        if (words>max_words) {
            max_words = words;
        }
        log_arr[idx] = line;
    }
    for (var i=0; i<max_words; i++) {
        word_len_arr[i] = 0;
    }
    for (idx in log_arr) {
        line = log_arr[idx];
        words = line.split(' ');
        for (word_num in words) {
            word = words[word_num];
            if (word.length>word_len_arr[word_num]) {
                word_len_arr[word_num] = word.length;
            }
        }
    }
    for (idx in log_arr) {
        line = log_arr[idx];
        words = line.split(' ');
        for (word_num in words) {
            word = words[word_num];
            words[word_num] = pad(word,word_len_arr[word_num]+1);
        }
        log_arr[idx] = words.join('');
    }
    log = log_arr.join('\r\n');
    frm.log_entries.value = log;
}

function listener_edit(ID,name){
    popWin(
        system_URL+'/listener_edit/'+ID+(typeof name!=='undefined' ? '?name='+name : ''),
        'popListener',
        'scrollbars=0,resizable=1',
        640,
        420,
        'centre'
    );
}

function listener_log(ID) {
    popWin(
        system_URL+'/listener_log/'+ID,'popListener',
        'scrollbars=0,resizable=1',
        640,
        380,
        'centre'
    );
}

function listener_map(system_ID) {
    switch (system_ID) {
        case 1:
            popWin(
                system_URL+'/listener_map/'+system_ID,
                'popListenerMap1',
                'scrollbars=1,resizable=1',
                960,
                660,
                'centre'
            );
            break;
        case 2:
            popWin(
                system_URL+'/listener_map/'+system_ID,
                'popListenerMap2',
                'scrollbars=1,resizable=1',
                1000,
                700,
                'centre'
            );
            break;
    }
}

function listener_signals(ID){
    popWin(
        system_URL+'/listener_signals/'+ID,
        'popListener',
        'scrollbars=0,resizable=1',
        640,
        380,
        'centre'
    );
}

function log_upload(ID){
    popWin(
        system_URL+'/log_upload?listenerID='+ID,
        'popLogUpload',
        'scrollbars=1,resizable=1',
        1160,
        680,
        'centre'
    );
}

function navOver(targ,over) {
    targ.className=	over ? 'navOn' : 'navOff';
    return true;
}

function navOver_box(targ,over) {
    targ.className=	over ? 'navOn_box' : 'navOff_box';
    return true;
}

function outputComma(number) {
    number = '' + number
    if (number.length <3) {
        return number;
    }
    var mod = number.length%3;
    var output = (mod > 0 ? (number.substring(0,mod)) : '');
    for (i=0 ; i < Math.floor(number.length/3) ; i++) {
        if ((mod ==0) && (i ==0)) {
            output+= number.substring(mod+3*i,mod+3*i+3);
        } else {
            output += ',' + number.substring(mod + 3 * i, mod + 3 * i + 3);
        }
    }
    return (output);
}

function pad(string, len) {
    if (string.length>=len) {
        return string;
    }
    return (string+'        ').substr(0, len);
}

function parse_log(form){
    if (check_for_tabs(form)) {
        alert("ATTENTION\n\nThe log contains tab characters which cannot be interpretted -\npress the Tabs > Spaces button to convert tabs to spaces\nthen check your formatting carefully.");
        return false;
    }
    if (form.log_format.value.search("ID")==-1) {
        alert("ERROR\n\nThis listener's Text Format (the grey shaded box above the log area) should contain at least an ID field to identify each signal.");
        check_for_tabs(form);
        return false;
    }
    if (form.log_format.value=="" || form.log_format.value=="<- Enter the log format here") {
        alert("ERROR\n\nYou should define a log format in the box shown");
        form.log_format.value="<- Enter the log format here";
        form.log_format.focus();
        form.log_format.select();
        check_for_tabs(form);
        return false;
    }
    if (form.log_entries.value=="" || form.log_entries.value=="<- Enter the log entries here") {
        alert("ERROR\n\nYou should paste the logbook entries into the box shown");
        form.log_entries.value="<- Enter the log entries here";
        form.log_entries.focus();
        form.log_entries.select();
        check_for_tabs(form);
        return false;
    }
    if (form.log_dd && (form.log_dd.value=="" || form.log_dd.value=="DD")) {
        alert("ERROR\n\nYour log format doesn't show the day for entries.\nEnter it in the DD box.");
        form.log_dd.value="DD";
        form.log_dd.focus();
        form.log_dd.select();
        check_for_tabs(form);
        return false;
    }
    if (form.log_mm && (form.log_mm.value=="" || form.log_mm.value=="MM")) {
        alert("ERROR\n\nYour log format doesn't show the month for entries.\nEnter it in the MM box.");
        form.log_mm.value="MM";
        form.log_mm.focus();
        form.log_mm.select();
        check_for_tabs(form);
        return false;
    }
    if (form.log_yyyy && (form.log_yyyy.value=="" || form.log_yyyy.value=="YYYY")) {
        alert("ERROR\n\nYour log format doesn't show the year for entries.\nEnter it in the YYYY box.");
        form.log_yyyy.value="YYYY";
        form.log_yyyy.focus();
        form.log_yyyy.select();
        check_for_tabs(form);
        return false;
    }
    return true;
}

function poll_edit(ID){
    popWin(
        system_URL+'/poll_edit?ID='+ID,
        'pollEdit',
        'scrollbars=1,resizable=1',
        600,
        340,
        'centre'
    );
}

function popWin(theURL,winName,features,windowx,windowy,centre) {
    if (centre == "centre") {
        var availx;
        var availy;
        var posx;
        var posy;
        availx = screen.availWidth;
        availy = screen.availHeight;
        posx = (availx - windowx)/2;
        posy = (availy - windowy)/2;
        var theWin = window.open(
            theURL,
            winName,
            features+',' +
            'width='+windowx+',' +
            'height='+windowy+',' +
            'left='+posx+',' +
            'top='+posy
        );
    }
    else {
        var theWin = window.open(
            theURL,
            winName,
            features+',' +
            'width='+windowx+',' +
            'height='+windowy+',' +
            'left=25,' +
            'top=25'
        );
    }
    theWin.focus();
}

function popup_mapquestmap(call,lat,lon){
    popWin(
        'http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude='+lat+'&longitude='+lon+'&size=big&zoom=5',
        'popMap',
        'scrollbars=1,resizable=1',
        800,
        600,
        'centre'
    );
}

function popup_map(call,lat,lon){
    popWin(
        'http://maps.google.com/maps?ll='+lat+','+lon+'&spn=0.005223,0.009438&t=h&hl=en',
        'popMap',
        'scrollbars=1,resizable=1',
        1024,
        800,
        'centre'
    );
}

function rowOver(targ,over) {
    targ.className=	over ? 'rowHighlight' : 'rowNormal';
    return true;
}

function set_listener_and_heard_in(form) {
    if (form['filter_listener[]'].selectedIndex) {
        form.filter_heard_in.disabled=1;
        form.filter_heard_in.className="formField_disabled";
        form.filter_heard_in.value = "";
        form.filter_heard_in_mod[0].disabled=1;
        form.filter_heard_in_mod[1].disabled=1;
    } else {
        form.filter_heard_in.disabled=0;
        form.filter_heard_in.className="formField";
    }
    if (!form.filter_heard_in.value.length) {
        form.filter_heard_in_mod[0].disabled=1;
        form.filter_heard_in_mod[1].disabled=1;
    } else {
        form.filter_heard_in_mod[0].disabled=0;
        form.filter_heard_in_mod[1].disabled=0;
    }
}

function set_ICAO_cookies(form) {
    var nextYear =	new Date();
    nextYear.setFullYear(nextYear.getFullYear()+1);
    document.cookie = "ICAO="+form.ICAO.value+";expires="+nextYear.toGMTString();
    document.cookie = "hours="+form.hours.value+";expires="+nextYear.toGMTString();
}

function set_range(form) {
    if (form.filter_dx_gsq.value.length==6) {
        form.filter_dx_gsq.value = form.filter_dx_gsq.value.substr(0,2).toUpperCase()+form.filter_dx_gsq.value.substr(2);
        if (!validate_GSQ(form.filter_dx_gsq.value)) {
            lastVal = form.filter_dx_gsq.value
            form.filter_dx_gsq.disabled = 1;
            alert("Sorry, "+lastVal+" doen't look like a valid GSQ value.\nValid GSQ values look like this: FN03gv");
            form.filter_dx_gsq.disabled = 0;
            return;
        }
        form.filter_dx_min.disabled=0;
        form.filter_dx_max.disabled=0;
        form.filter_dx_min.className="formField";
        form.filter_dx_max.className="formField";
    } else {
        form.filter_dx_min.disabled=1;
        form.filter_dx_max.disabled=1;
        form.filter_dx_min.className="formField_disabled";
        form.filter_dx_max.className="formField_disabled";
    }
    if (form.filter_dx_min.value.length ||form.filter_dx_max.value.length) {
        form.filter_dx_units[0].disabled=0;
        form.filter_dx_units[1].disabled=0;
    } else {
        form.filter_dx_units[0].disabled=1;
        form.filter_dx_units[1].disabled=1;
    }
}

function set_sunrise_cookies(form) {
    var nextYear =	new Date();
    nextYear.setFullYear(nextYear.getFullYear()+1);
    document.cookie = "sunrise="+form.lat_dddd.value+"|"+form.lon_dddd.value+";expires="+nextYear.toGMTString();
}

function set_sunrise_form(form) {
    var now = new Date();
    form.inpYear.value=now.getUTCFullYear();
    form.inpMonth.value=now.getUTCMonth()+1;
    form.inpDay.value=now.getUTCDate();
    if (get_cookie('sunrise')) {
        var txt_options = get_cookie("sunrise").split("|");
        form.lat_dddd.value =		txt_options[0];
        form.lon_dddd.value =		txt_options[1];
    }
}

function show_ILG() {
    document.location=system_URL+'/ILGRadio_signallist';
}

function show_itu(){
    popWin(
        system_URL+'/show_itu'+(arguments.length ? '?region=' + arguments[0] : ''),
        'popITU',
        'scrollbars=1,toolbar=0,menubar=0,status=0,resizable=1',
        560,
        550,
        'centre'
    );
}

function show_map_info(khz, cs, type, QTH, lat, lon, active) {
    var strType;
    switch (type) {
        case "0":
            strType = "NDB";
            break;
        case "1":
            strType = "DGPS";
            break;
        case "2":
            strType = "TIME";
            break;
        case "3":
            strType = "NAVTEX";
            break;
        case "4":
            strType = "HAMBCN";
            break;
        case "5":
            strType = "OTHER";
            break;
    }
    if (khz) {
        window.status = ""+strType+": "+khz+"-"+cs+" " + QTH + " (lat:"+lat+", lon:" + lon + (active=="1" ? "" : " - inactive") + ")";
        document.form.info_type.value=strType;
        document.form.info_call.value=cs;
        document.form.info_khz.value=khz;
        document.form.info_QTH.value=QTH;
        document.form.info_lat.value=lat;
        document.form.info_lon.value=lon;
    } else {
        window.status = "";
        document.form.info_type.value="";
        document.form.info_call.value="";
        document.form.info_QTH.value="";
        document.form.info_khz.value="";
        document.form.info_lat.value="";
        document.form.info_lon.value="";
    }
}

function show_map_place(name, population, lat, lon, capital) {
    if (name) {
        population = outputComma(population);
        window.status = ""+name+(capital=='1' ? ' (State Capital)' : '')+" - Population: "+population+" (lat:"+lat+", lon:" + lon + ")";
        document.form.place_name.value=name;
        document.form.place_population.value=population;
        document.form.place_lat.value=lat;
        document.form.place_lon.value=lon;
        document.form.place_type.value=(capital=='1' ? "Capital" : "Town / City")
    } else {
        window.status = "";
        document.form.place_name.value="";
        document.form.place_population.value="";
        document.form.place_lat.value="";
        document.form.place_lon.value="";
        document.form.place_type.value="";
    }
}

function show_name(ID,isOver) {
    var div;
    if (isW3C) div =	document.getElementById(ID);
    if (isIE4) div =	document.all[ID];
    if (isW3C) {
        if (isOver) {
            div.style.backgroundColor = '#ffc000';
        } else {
            div.style.backgroundColor = '';
        }
    }
}

function show_point(x,y) {
    var div;
    if (isW3C) div =	document.getElementById('point_here');
    if (isIE4) div =	document.all['point_here'];
    if (isNS4) div =	document.layers['point_here'];
    if (isNS4) {
        if (x+y) {
            div.moveTo(x+3,y+3);
            div.display = 'inline';
        } else {
            div.display = 'none';
        }
        return;
    }
    if (isIE5) {
        if (x+y) {
            div.style.display = 'inline';
            div.style.left = x + 5;
            div.style.top = y + 10;
        } else {
            div.style.display = 'none';
        }
        return;
    }
    if (isNS6) {
        if (x+y) {
            div.style.display = 'inline';
            div.style.left = x + 3;
            div.style.top = y + 3;
        } else {
            div.style.display = 'none';
        }
    }
}

function show_sp(){
    popWin(
        system_URL+'/show_sp',
        'popSP',
        'scrollbars=1,toolbar=0,menubar=0,status=0,resizable=1',
        560,
        550,
        'centre'
    );
}

function show_time() {
    var now =	    new Date()
    var hhmm =	lead(now.getUTCHours())+":"+lead(now.getUTCMinutes());
    date =		now.getUTCFullYear()+"-"+lead(now.getUTCMonth()+1)+"-"+lead(now.getUTCDate());
    var msg = 	"Current UTC Time: "+hhmm+" Date: "+date+" (accuracy limited to your PC clock)";
    if (window.defaultStatus!=msg) {
        window.defaultStatus = msg;
    }
    setTimeout("show_time()",1000)
}

function signal_add(call,khz,GSQ,QTH,SP,ITU,PWR,type){
    popWin(
        system_URL+'/signal_info?call='+call.replace(/#/,"%23")+'&khz='+khz+'&GSQ='+GSQ+'&QTH='+QTH+'&SP='+SP+'&ITU='+ITU+'&pwr='+PWR+'&type='+type,
        'popsignalAdd',
        'scrollbars=0,resizable=1,status=1',
        640,
        380,
        'centre'
    );
}

function signal_info(ID){
    popWin(
        system_URL+'/signal_info/'+ID,
        'popsignal',
        'scrollbars=0,resizable=1',
        640,
        380,
        'centre'
    );
}

function signal_log(ID){
    popWin(
        system_URL+'/signal_log/'+ID,
        'popsignal',
        'scrollbars=0,resizable=1',
        640,
        380,
        'centre'
    );
}

function signal_map_eu(ID){
    popWin(
        system_URL+'/signal_map_eu/'+ID,
        'popsignalMapEU',
        'scrollbars=0,resizable=1',
        870,
        690,
        'centre'
    );
    return false;
}

function signal_map_na(ID){
    popWin(
        system_URL+'/signal_map_na/'+ID,
        'popsignalMapNA',
        'scrollbars=0,resizable=1',
        674,
        640,
        'centre'
    );
    return false;
}

function signal_merge(ID){
    popWin(
        system_URL+'/signal_merge?ID='+ID,
        'popsignalMove',
        'scrollbars=0,resizable=1,status=1',
        640,
        380,
        'centre'
    );
}

function sp(SP,ITU) {
    if (window.opener && window.opener.form) {
        if (window.opener.form.SP) {
            window.opener.form.SP.value = SP;
            window.opener.form.ITU.value = ITU;
            window.opener.focus();
        }
        if (window.opener.form.filter_sp) {
            window.opener.form.filter_sp.value = SP;
            window.opener.form.filter_itu.value = ITU;
            window.opener.focus();
        }
    }
}

function sp_itu_over(targ,mode) {
    targ.className=	mode ? 'lookupOn' : 'lookupOff';
    return true;
}

function send_form(form) {
  if (validate_form(form)) {
      if (form.go) {
        form.go.value="Loading...";
        form.go.disabled=1;
      }
      if (form.map) {
        form.map.disabled=1;
      }
      if (form.clear) {
        form.clear.disabled=1;
      }
      if (form.previous) {
        form.previous.disabled=1;
      }
      if (form.next) {
        form.next.disabled=1;
      }
      form.submit();
  }
}

function validate_form(form) {
    var msg = "", err_sp=false, err_itu=false;
    if (form.filter_heard_in && form.filter_heard_in.value!="(All States and Countries)" && (!validate_alphasp(form.filter_heard_in.value))) {
        msg += "HEARD IN:\n* List locations separated by spaces, e.g.: ENG FRA ON BC NE NOR\n\n";
    }
    if (form.filter_logged_date_1 && (!validate_YYYYMMDD(form.filter_logged_date_1.value) || !validate_YYYYMMDD(form.filter_logged_date_2.value))) {
        msg += "LOGGED BETWEEN:\n* Dates should be given in this format: YYYY-MM-DD\n\n";
    }
    if (form.filter_first_date_1 && (!validate_YYYYMMDD(form.filter_first_date_1.value) || !validate_YYYYMMDD(form.filter_first_date_2.value))) {
        msg += "FIRST LOGGED:\n* Dates should be given in this format: YYYY-MM-DD\n\n";
    }
    if (form.filter_last_date_1 && (!validate_YYYYMMDD(form.filter_last_date_1.value) || !validate_YYYYMMDD(form.filter_last_date_2.value))) {
        msg += "LAST LOGGED:\n* Dates should be given in this format: YYYY-MM-DD\n\n";
    }
    if (form.filter_khz_1 && (!validate_number(form.filter_khz_1.value) || !validate_number(form.filter_khz_2.value))) {
        msg += "FREQUENCIES:\n* Should be given as numbers: e.g. 200 or 245.9\n\n";
    }
    if (form.filter_sp && !validate_alphasp(form.filter_sp.value)) {
        msg += "STATE / PROV:\n* Use 2-character State or Province codes separated by spaces.\n\n";
        err_sp=true;
    }
    if (form.filter_itu && !validate_alphasp(form.filter_itu.value)) {
        msg += "COUNTRY:\n* Use a 3-character Country codes separated by spaces.\n\n";
        err_itu=true;
    }
    if (form.filter_dx_gsq) {
        if (!validate_GSQ(form.filter_dx_gsq.value) ||
            !validate_number(form.filter_dx_max.value) ||
            !validate_number(form.filter_dx_min.value)
        ) {
            msg += "DISTANCE FILTER:\n";
            if (!validate_GSQ(form.filter_dx_gsq.value)) {
                msg+= "* GSQ values look like this: FN03gv\n";
            }
            if (!validate_number(form.filter_dx_max.value) || !validate_number(form.filter_dx_min.value)) {
                msg += "* Distances given should be numbers\n";
            }
            msg += "\n";
        }
    }
    if (msg) {
        alert("Please correct the following errors:\n\n" + msg);
        if (err_sp) {
            show_sp();
        }
        if (err_itu) {
            show_itu();
        }
        return false;
    }
    return false;
    return true;
}

function submit_log(form){
    document.form.submode.value="submit_log";
    document.form.submit();
}

function tabOver(targ,over) {
    targ.className=	over ? 'tabOn' : 'tabOff';
    return true;
}

function tabs_to_spaces(frm) {
    var log = 			frm.log_entries.value;
    frm.log_entries.value =	log.replace(/	/ig,"     ");
    frm.conv.disabled=1;
}

function toggle(field) {
    field.checked = ! field.checked;
}

function toggleDivDisplay(targDiv) {
    if (isW3C) { // DOM3 = IE5, NS6
        var icon_hide = document.getElementById(targDiv+"_icon_hide");
        var icon_show = document.getElementById(targDiv+"_icon_show");
        if (icon_show.style.display == "none") {
            icon_hide.style.display = "none";
            icon_show.style.display = "";
        } else {
            icon_hide.style.display = "";
            icon_show.style.display = "none";
        }
    }
}

function toggleDivDisplaySimple(targDiv) {
    if (isW3C) { // DOM3 = IE5, NS6
        var div = document.getElementById(targDiv);
        if (div.style.display == "none") {
            div.style.display = "";
        } else {
            div.style.display = "none";
        }
    }
}

function validate_alpha(field) {
    if (field=="") {
        return true;
    }
    if (field.match(/[a-zA-Z]*/i) == field) {
        return true;
    }
    return false;
}

function validate_alphasp(field) {
    if (field=="") {
        return true;
    }
    if (field.match(/[a-zA-Z\* ]*/i) == field) {
        return true;
    }
    return false;
}

function validate_GSQ(field) {
    if (field=="") {
        return true;
    }
    if (field.match(/[a-rA-R][a-rA-R][0-9][0-9][a-xA-X][a-xA-X]/i) == field) {
        return true;
    }
    return false;
}

function validate_number(field) {
    if (field=="") {
        return true;
    }
    if (field.match(/[0-9.]*/i) == field) {
        return true;
    }
    return false;
}

function validate_YYYYMMDD(field) {
    if (field=="") {
        return true;
    }
    if (field.match(/[0-9]{4}-*[0-9]{2}-*[0-9]{2}/i) == field) {
        return true;
    }
    return false;
}