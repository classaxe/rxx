// ************************************
// * Browser detection:               *
// ************************************
isNS4 =	(navigator.appName.indexOf("Netscape")>=0 && !document.getElementById) ? true : false;
isIE4 =	(document.all && !document.getElementById) ? true : false;
isIE5 =	(document.getElementById && document.all) ? true : false;
isNS6 =	(document.getElementById && navigator.appName.indexOf("Netscape")>=0 ) ? true : false;
isW3C =	(document.getElementById && 1) ? true : false;



// ************************************
// * Various CSS mouseover functions: *
// ************************************
function sp_itu_over(targ,mode) {
  targ.className=	mode ? 'lookupOn' : 'lookupOff';
  return true;
}

function column_over(obj_cell,int_state) {
  switch (int_state) {
    case 0: obj_cell.className = 'downloadTableHeadings'; break;
    case 1: obj_cell.className = 'downloadTableHeadings_over'; break;
    case 2: obj_cell.className = 'downloadTableHeadings_click'; break;
  }
  return true;
}

function navOver(targ,mode) {
  targ.className=	mode ? 'navOn' : 'navOff';
  return true;
}

function navOver_box(targ,mode) {
  targ.className=	mode ? 'navOn_box' : 'navOff_box';
  return true;
}

function rowOver(targ,mode) {
  targ.className=	mode ? 'rowHighlight' : 'rowNormal';
  return true;
}

function tabOver(targ,mode) {
  targ.className=	mode ? 'tabOn' : 'tabOff';
  return true;
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




// ************************************
// * award()                          *
// ************************************
// Used to compile list of selected awards in Awards form
function award(form,id) {
  alert();
  form.awards_requested.value="";

  form["Award: "+id].value = (document.form["Award: "+id].value==1 ? 0 : 1)

  var status = form["Award: "+id].value;

  toggleDivDisplay(id);

  for(i in form) {
    if (i.substr(0,7) =="Award: ") {
      if (form[i].value=="1") {
        form.awards_requested.value += "  * " + i.substr(7)+"\n";
      }
    }
  }
  if (form.awards_requested.value!="") {
    form.awards_requested.value =	"NDB LIST AWARD REQUEST:\n" +
					"To:    Andy Robins (Awards Co-ordinator)\n" +
					"From:  " + document.form.awards_requester.value + "\n" +
					"URL:   " + document.form.awards_url.value + "\n\n" +
					"I would like to request the following awards, based upon my "+
					"published logs in RNA / REU / RWW.\n" +
					"I confirm that have not previously applied for these awards.\n\n" +
					document.form.awards_requested.value + "\n\n" +
					"Sincerely,\n" + document.form.awards_name.value;
    form.order.disabled = false;
  }
  else {
    form.awards_requested.value="(No certificates have been ordered)";
    form.order.disabled = true;
  }
}



// ************************************
// * award_place_order()              *
// ************************************
// Used to verify selected awards in Awards form
function award_place_order(form) {
  if (form.awards_email.value=="(enter email address)" || form.awards_email.value=="") {
    alert("Please enter your email address");
    return;
  }
  if (confirm(  "CONFIRM ORDER\nPlease confirm that you have verified the details in this form, including the\n" +
		"Reply To email address to which the awards will be emailed.\n\n" +
		"* Press 'OK' to send your request now.\n"+
		"* Press 'Cancel' if you wish to go back and check again.")) {
    form.submode.value='send';
    form.submit();
  }
}



// ************************************
// * check_for_tabs()                 *
// ************************************
// Used in log import
function check_for_tabs(frm) {
  if (frm.log_entries.value.search(/	/)!=-1) {
    frm.conv.disabled=0;
    return true;
  }
  frm.conv.disabled=1;
  return false;
}


// ************************************
// * clear_signal_list()              *
// ************************************
// Used to clear search criteria
function clear_signal_list(form){
  form.filter_dx_gsq.value="";
  form.filter_dx_max.value="";
  form.filter_dx_min.value="";
  form.filter_heard.value="";
  form.listenerID.selectedIndex=0;
  form.filter_khz_1.value="";
  form.filter_khz_2.value="";
  form.filter_sp.value="";
  form.filter_itu.value="";
  form.filter_id.value="";
  form.filter_heard.disabled=0;
  form.filter_heard.className="formField";
  form.type_DGPS.checked=0;
  form.type_HAMBCN.checked=0;
  form.type_NAVTEX.checked=0;
  form.type_NDB.checked=1;
  form.type_OTHER.checked=0;
  form.type_TIME.checked=0;
  form.filter_date_1.value="";
  form.filter_date_2.value="";
  form.chk_filter_active.checked=0;
  set_range(form);
}



// ************************************
// * clear_signal_list()              *
// ************************************
// Used to clear search criteria in SP detailed map
function clear_state_map(form){
  form.listenerID.selectedIndex=0;
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



// ************************************
// * conv_dd_mm_ss()                  *
// ************************************
// Used on tools page
function conv_dd_mm_ss(form) {
  var rexp =		/([0-9]+)[° \.]*([0-9]+)[' \.]*([0-9]+)*[" \.]*([NS])*/i;
  var a =			form.lat_dd_mm_ss.value.match(rexp);
  if (a==null) {
    alert("ERROR\n\nLatitude must be given in one of these formats:\n  DD°MM'SS\"H\n  DD.MM.SS.H\n  DD MM SS H\n  DD°MM.H\n  DD.MM.H\n  DD MM H\n\n(H is N or S, but defaults to N if not given)");
    return false;
  }
  var deg =		parseFloat(a[1]);
  var min =		parseFloat(a[2]);
  var sec =		(a[3]!="" ? parseFloat(a[3]) : 0);
  var min_d =		min+sec/60;
  var hem =		(a[4]!="" ? (a[4]=="N"||a[4]=="n" ? 1 : -1) : 1);
  var dec_lat =	hem*(deg+(Math.round(((sec/3600)+(min/60))*10000))/10000);

  var rexp =		/([0-9]+)[° \.]*([0-9]+)[' \.]*([0-9]+)*[" \.]*([EW])*/i;
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



// ************************************
// * conv_dd_dddd()                   *
// ************************************
// Used on tools page
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


// ************************************
// * deg_gsq()                        *
// ************************************
// Used on tools page
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


// ************************************
// * DGPS()                           *
// ************************************
// Constructor for DGPS data items in lookup on tools page
function DGPS(id1,id2,ref,khz,bps,qth,sta,cnt,act){
  if (typeof dgps[id1] == 'undefined') {
    dgps[id1] =		new Array();
    dgps[id1][0] =	new Array(ref,khz,bps,qth,sta,cnt,id1,id2,act);
  }
  else {
    dgps[id1][dgps[id1].length] =	new Array(ref,khz,bps,qth,sta,cnt,id1,id2,act);
  }
  if (typeof dgps[id2] == 'undefined') {
    dgps[id2] =		new Array();
    dgps[id2][0] =	new Array(ref,khz,bps,qth,sta,cnt,id1,id2,act);
  }
  else {
    dgps[id2][dgps[id2].length] =	new Array(ref,khz,bps,qth,sta,cnt,id1,id2,act);
  }
}


// ************************************
// * dgps_lookup()                    *
// ************************************
// Used to lookup DGPS data on tools page
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


// ************************************
// * get_cookie()                     *
// ************************************
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
  var result = unescape(cookies.substring(start,end))
  return result;
}


// ************************************
// * gsq_deg()                        *
// ************************************
// Used on tools page
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



// ************************************
// * itu()                            *
// ************************************
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


// ************************************
// * lead()                           *
// ************************************
// Used in number string formatting
function lead(num) {
  return (num.toString().length==1 ? "0"+num : num)
}


// ************************************
// * outputComma()                    *
// ************************************
// Puts commas in numbers to indicate thousands 
function outputComma(number) {
    number = '' + number
    if (number.length > 3) {
        var mod = number.length%3;
        var output = (mod > 0 ? (number.substring(0,mod)) : '');
        for (i=0 ; i < Math.floor(number.length/3) ; i++) {
            if ((mod ==0) && (i ==0))
                output+= number.substring(mod+3*i,mod+3*i+3);
            else
                output+= ',' + number.substring(mod+3*i,mod+3*i+3);
        }
        return (output);
    }
    else return number;
}


// ************************************
// * parse_log()                      *
// ************************************
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

// ************************************
// * popWin()                         *
// ************************************
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
    var theWin = window.open(theURL,winName,features+',width='+windowx+',height='+windowy+',left='+posx+',top='+posy);
  } else {
    var theWin = window.open(theURL,winName,features+',width='+windowx+',height='+windowy+',left=25,top=25');
  }
  theWin.focus();
}


// ************************************
// * set_listener_and_heard_in()      *
// ************************************
// Used to validate Listener and Heard In setting for search criteria
function set_listener_and_heard_in(form) {
  if (form.listenerID.selectedIndex) {
    form.filter_heard.disabled=1;
    form.filter_heard.className="formField_disabled";
    form.filter_heard.value = "";
    form.grp[0].disabled=1;
    form.grp[1].disabled=1;
  }
  else {
    form.filter_heard.disabled=0;
    form.filter_heard.className="formField";
  }
  if (!form.filter_heard.value.length) {
    form.grp[0].disabled=1;
    form.grp[1].disabled=1;
  }
  else {
    form.grp[0].disabled=0;
    form.grp[1].disabled=0;
  }
}


// ************************************
// * set_ICAO_cookies()               *
// ************************************
// Used to set cookie to remember last entered ICAO station in Weather page
function set_ICAO_cookies(form) {
  var nextYear =	new Date();
  nextYear.setFullYear(nextYear.getFullYear()+1);
  document.cookie = "ICAO="+form.ICAO.value+";expires="+nextYear.toGMTString();
  document.cookie = "hours="+form.hours.value+";expires="+nextYear.toGMTString();
}


// ************************************
// * set_range()                      *
// ************************************
// Used to validate distance range setting for search criteria
function set_range(form) {
  if (form.filter_dx_gsq.value.length==6) {
    form.filter_dx_min.disabled=0;
    form.filter_dx_max.disabled=0;
    form.filter_dx_min.className="formField";
    form.filter_dx_max.className="formField";
  }
  else {
    form.filter_dx_min.disabled=1;
    form.filter_dx_max.disabled=1;
    form.filter_dx_min.className="formField_disabled";
    form.filter_dx_max.className="formField_disabled";
  }
  if (form.filter_dx_min.value.length ||form.filter_dx_max.value.length) {
    form.filter_dx_units[0].disabled=0;
    form.filter_dx_units[1].disabled=0;
  }
  else {
    form.filter_dx_units[0].disabled=1;
    form.filter_dx_units[1].disabled=1;
  }
}



// ************************************
// * set_sunrise_cookies()            *
// ************************************
// Used to set cookie to remember last entered lat and lon values in tools form
function set_sunrise_cookies(form) {
  var nextYear =	new Date();
  nextYear.setFullYear(nextYear.getFullYear()+1);
  document.cookie = "sunrise="+form.lat_dddd.value+"|"+form.lon_dddd.value+";expires="+nextYear.toGMTString();
}


// ************************************
// * set_sunrise_form()               *
// ************************************
// Used to set up sunrise form and use cookie if saved
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



// ************************************
// * show_map_info()                  *
// ************************************
// Used in SP Detailed Map page to show info on stations during mouseover
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
  }
  else {
    window.status = "";
    document.form.info_type.value="";
    document.form.info_call.value="";
    document.form.info_QTH.value="";
    document.form.info_khz.value="";
    document.form.info_lat.value="";
    document.form.info_lon.value="";
  }
}


// ************************************
// * show_map_place()                 *
// ************************************
// Used in SP Detailed Map page to show info on places during mouseover
function show_map_place(name, population, lat, lon, capital) {
  if (name) {
    population = outputComma(population);
    window.status = ""+name+(capital=='1' ? ' (State Capital)' : '')+" - Population: "+population+" (lat:"+lat+", lon:" + lon + ")";
    document.form.place_name.value=name;
    document.form.place_population.value=population;
    document.form.place_lat.value=lat;
    document.form.place_lon.value=lon;
    document.form.place_type.value=(capital=='1' ? "Capital" : "Town / City")
  }
  else {
    window.status = "";
    document.form.place_name.value="";
    document.form.place_population.value="";
    document.form.place_lat.value="";
    document.form.place_lon.value="";
    document.form.place_type.value="";
  }
}


// ************************************
// * show_name()                      *
// ************************************
// Used in listener maps
function show_name(ID,isOver) {
  // Not for NS4
  var div;
  if (isW3C) div =	document.getElementById(ID);
  if (isIE4) div =	document.all[ID];

  if (isW3C) {
    if (isOver) {
      div.style.backgroundColor = '#ffc000';
    }
    else {
      div.style.backgroundColor = '';
    }
  }
}


// ************************************
// * show_point()                     *
// ************************************
// Used in listener maps
function show_point(x,y) {
  var div;
  if (isW3C) div =	document.getElementById('point_here');
  if (isIE4) div =	document.all['point_here'];
  if (isNS4) div =	document.layers['point_here'];

  if (isNS4) {
    if (x+y) {
      div.moveTo(x+3,y+3);
      div.display = 'inline';
    }
    else {
      div.display = 'none';
    }
    return;
  }
  if (isIE5) {
    if (x+y) {
      div.style.display = 'inline';
      div.style.left = x + 5;
      div.style.top = y + 10;
    }
    else {
      div.style.display = 'none';
    }
    return;
  }
  if (isNS6) {
    if (x+y) {
      div.style.display = 'inline';
      div.style.left = x + 3;
      div.style.top = y + 3;
    }
    else {
      div.style.display = 'none';
    }
  }
}



// ************************************
// * show_time()                      *
// ************************************
function show_time() {
  var now =		new Date()
  var hhmm =		lead(now.getUTCHours())+":"+lead(now.getUTCMinutes());
  date =			now.getUTCFullYear()+"-"+lead(now.getUTCMonth()+1)+"-"+lead(now.getUTCDate());
  var msg = 		"Current UTC Time: "+hhmm+" Date: "+date+" (accuracy limited to your PC clock)";
  if (window.defaultStatus!=msg) {
    window.defaultStatus = msg;
  }
  setTimeout("show_time()",1000)
}



// ************************************
// * sp()                             *
// ************************************
// Used to set SP and ITU on form from SP selector popup
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


// ************************************
// * send_form()                      *
// ************************************
// Used to validate signals form submission
function send_form(form) {
  var msg = "", err_sp=false, err_itu=false;
  if (form.sortBy_column && form.sortBy_column.options[form.sortBy_column.selectedIndex].value == "CLE64" && form.filter_dx_gsq.value=="") {
    alert("To use this setting you must first specify a value for GSQ from which distances should be calculated.");
    form.filter_dx_gsq.focus();
    return false;
  }
  if (form.sortBy) {
    form.sortBy.value = form.sortBy_column.options[form.sortBy_column.selectedIndex].value + (form.sortBy_d.checked ? form.sortBy_d.value : "");
  }
  if (form.filter_heard && (!validate_alphasp(form.filter_heard.value))) {
    msg += "HEARD IN:\n* List locations separated by spaces, e.g.: ENG FRA ON BC NE NOR\n\n";
  }
  if (form.filter_date_1 && (!validate_YYYYMMDD(form.filter_date_1.value) || !validate_YYYYMMDD(form.filter_date_2.value))) {
    msg += "LAST HEARD:\n* Dates should be given in this format: YYYY-MM-DD\n\n";
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
    if (!validate_GSQ(form.filter_dx_gsq.value) || !validate_number(form.filter_dx_max.value) || !validate_number(form.filter_dx_min.value)) {
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
  if (form.go) {
    form.go.value="Loading...";
    form.go.disabled=1;
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


// ************************************
// * submit_log()                     *
// ************************************
// Used in log import
function submit_log(form){
  document.form.submode.value="submit_log";
  document.form.submit();
}


// ************************************
// * tabs_to_spaces()                 *
// ************************************
// Used in log import
function tabs_to_spaces(frm) {
  var log = 			frm.log_entries.value;
  frm.log_entries.value =	log.replace(/	/ig,"     ");
  frm.conv.disabled=1;
}


// ************************************
// * toggle()                         *
// ************************************
// Used to toggle state of signal type checkboxes in search criteria
function toggle(field) {
  field.checked = ! field.checked;
}
 

// ************************************
// * validate_alpha()                 *
// ************************************
// Generic form field validation 
function validate_alpha(field) {
  if (field=="") {
    return true;
  }
  if (field.match(/[a-zA-Z]*/i) == field) {
    return true;
  }
  return false;
}


// ************************************
// * validate_alphasp()               *
// ************************************
// Generic form field validation 
function validate_alphasp(field) {
  if (field=="") {
    return true;
  }
  if (field.match(/[a-zA-Z ]*/i) == field) {
    return true;
  }
  return false;
}


// ************************************
// * validate_GSQ()                   *
// ************************************
// Generic form field validation 
function validate_GSQ(field) {
  if (field=="") {
    return true;
  }
  if (field.match(/[a-rA-R][a-rA-R][0-9][0-9][a-xA-X][a-xA-X]/i) == field) {
    return true;
  }
  return false;
}


// ************************************
// * validate_number()                *
// ************************************
// Generic form field validation 
function validate_number(field) {
  if (field=="") {
    return true;
  }
  if (field.match(/[0-9.]*/i) == field) {
    return true;
  }
  return false;
}


// ************************************
// * validate_YYYYMMDD()              *
// ************************************
// Generic form field validation 
function validate_YYYYMMDD(field) {
  if (field=="") {
    return true;
  }
  if (field.match(/[0-9]{4}-*[0-9]{2}-*[0-9]{2}/i) == field) {
    return true;
  }
  return false;
}
