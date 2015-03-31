// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   NDB Web Log                  *
// * Filename:  functions.js                 *
// * Created:   01/09/2003 (MF)              *
// * Revised:   15/02/2004 (MF)              *
// *******************************************
version =	"1.1.16";

// ###########################################
// # Inline code:                            #
// ###########################################
// Notes:
// In this file, the order of code shown is as follows:
// 1) Inline code
// 2) Object constructors in alphabetical order of object name
// 3) Functions in alphabetical order of function name


// ++++++++++++++++++++++++++++++++++
// + Open Progress Indicator window +
// ++++++++++++++++++++++++++++++++++
function progress() {
  progress_hd =	  		window.open('','progress', 'width=260,height=100,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=0');
  progress_hd.focus();
  progress_hd.document.write("<html><head><title>NDB WebLog "+version+"</title><style type='text/css'>h2 { font-family: Arial, sans-serif; }</style></head><body bgcolor='#ffffd8'><h2>Working...<br><small>Please wait</small></h2></body></html>");
  progress_hd.document.close();
}

// ++++++++++++++++++++++++++++++++++
// + Find values for user selection +
// ++++++++++++++++++++++++++++++++++
cookie =				new Array();
var now = 			new Date();
var cur_yyyy =			now.getFullYear();				// Default value for year on entry
var cur_mm =			("0"+(now.getMonth()+1));		// Default value for month (requires a leading zero)
cur_mm =				cur_mm.substr((cur_mm.length-2),2)

var list_selected =			false
if (get_cookie('list_selected')) {
  var txt_options = get_cookie("list_selected").split("|");
  var i=0;
  cookie['sel_mm'] =		txt_options[i++];
  cookie['sel_sort'] =		txt_options[i++];
  cookie['sel_yyyy'] =		txt_options[i++];
}

sel_mm =		(cookie['sel_mm'] ? cookie['sel_mm'] : cur_mm);
sel_sort =	(cookie['sel_sort'] ? cookie['sel_sort'] : "khz");
sel_yyyy =	(cookie['sel_yyyy'] ? cookie['sel_yyyy'] : cur_yyyy);

var list_options =			false;
if (get_cookie("list_options")) {
  var txt_options = get_cookie("list_options").split("|");
  var i=0;
  cookie['format'] =		txt_options[i++];
  cookie['h_dxw'] =			txt_options[i++];
  cookie['h_gsq'] =			txt_options[i++];
  cookie['h_ident'] =		txt_options[i++];
  cookie['h_lifelist'] =		txt_options[i++];
  cookie['h_notes'] =		txt_options[i++];
  cookie['map_zoom'] =		txt_options[i++];
  cookie['units'] =			txt_options[i++];
  // Add new options in chronological order for forward compatability
  cookie['mod_abs'] =		txt_options[i++];
}

cookie['format'] =		(cookie["format"] ? cookie["format"] : "yyyymmdd");
cookie['units'] =		(cookie["units"] ? cookie["units"] : "km");
cookie['map_zoom'] =	(cookie["map_zoom"] ? cookie["map_zoom"] : "5");

		
// ++++++++++++++++++++++++++++++++++
// + Establish daylight for location+
// ++++++++++++++++++++++++++++++++++
utc_daylight =			10+utc_offset;
utc_daylight_array =	new Array();

for (var i=utc_daylight; i<utc_daylight+4; i++) {
  utc_daylight_array[i-utc_daylight] =	lead(i);
}

// ++++++++++++++++++++++++++++++++++
// + Initialse data container arrays+
// ++++++++++++++++++++++++++++++++++
stats =				new Array();	// Global variable hold stats data.
station =				new Array();	// Global variable to hold station data
logbook =				new Array();	// Used when printing out text listing
unregistered_countries =	new Array();	// Used if countries are logged but not defined in countries.js
unregistered_stations =	new Array();	// Used if stations are logged but not defined in stations.js
cnt_arr =				new Array();	// Used to hold details on countries
rgn_arr =				new Array();	// Used to hold details on regions
sta_arr =				new Array();	// Holds data on states (Australia, US and Canada only)

// ++++++++++++++++++++++++++++++++++
// + Initialise month name arrays   +
// ++++++++++++++++++++++++++++++++++
months = 		new Array('January','February','March','April','May','June','July','August','September','October','November','December');
//months = 		new Array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre');
//months = 		new Array('Januar','Februar','März','April','Mag','Juni','Juli','August','September','Oktober','November','Dezember');
//months = 		new Array('Enero','Febrero','Marcha','Abril','Puede','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
mm_arr = 		new Array('01','02','03','04','05','06','07','08','09','10','11','12');


// ###########################################
// # Object Constructors:                    #
// ###########################################
// ************************************
// * COUNTRY Constructor:             *
// ************************************
function COUNTRY(cnt,name,rgn) {
  var a = new Array();
  a.name =	name;
  a.rgn =		rgn;
  cnt_arr[cnt] = a;
}


// ************************************
// * LOG Constructor:                 *
// ************************************
function LOG(khz,call,yyyymmdd,hhmm,notes) {
  // ++++++++++++++++++++++++++++++++++
  // + Extract variables              +
  // ++++++++++++++++++++++++++++++++++
  var id =				khz+"-"+call;
  stats.last_date =			yyyymmdd;		// Assume last entry in log is the last entry
  stats.last_time =			hhmm;
  var yyyy =				yyyymmdd.substr(0,4);
  var mm =				yyyymmdd.substr(4,2);
  var dd =				yyyymmdd.substr(6,2);
  var hh =				hhmm.substr(0,2);


  // ++++++++++++++++++++++++++++++++++
  // + Write logbook                  +
  // ++++++++++++++++++++++++++++++++++
  // logbook used for text output mode
  if (!logbook[id]) {
    logbook[id] =			new Array();
    logbook[id]['entry'] =	new Array();
  }
  var entry =				new Array();
  entry['yyyymmddhhmm'] =	yyyymmdd+""+hhmm;
  entry['notes'] =			notes;
  logbook[id]['entry'][logbook[id]['entry'].length] =	entry;


  // ++++++++++++++++++++++++++++++++++
  // + Station registered?            +
  // ++++++++++++++++++++++++++++++++++
  if (!station[id]) {
    unregistered_stations[unregistered_stations.length] = id;
    return;
  }


  // ++++++++++++++++++++++++++++++++++
  // + Prepare stats counts:          +
  // ++++++++++++++++++++++++++++++++++
  // Stats counters used in Statistics output
  // Prepare for global stats counts:
  if (!stats.all) {
    stats.all =				new Array();	// Tally Stations (lifetime log)
    stats.year =			new Array();	// Tally Stations (monthly log)
  }

  // Prepare for annual stats counts:
  if (!stats.year[yyyy]) {
    stats.year[yyyy] =			new Array();	// Initialise year
    stats.year[yyyy].id =		new Array();	// Tally stations
    stats.year[yyyy].cnt =		new Array();	// Tally countries (with states)
    stats.year[yyyy].rgn =		new Array();	// Tally countries (with states)
    for (i in rgn_arr) {
      stats.year[yyyy].rgn[i] =	0;	// Count 
    }
    stats.year[yyyy].n60 =		0;			// Count Beacons North of 60
    stats.year[yyyy].new_beacon =	0;			// Count new Beacons
    stats.year[yyyy].max_day =	0;			// Log furthest day-time DX
    stats.year[yyyy].max_night =	0;			// Log furthest night-time DX
    stats.year[yyyy].max_dxw =	0;			// Log furthest DX/W
  }

  // Prepare for monthly stats counts
  if (!stats.year[yyyy][mm]) {
    stats.year[yyyy][mm] =		new Array();	// Prepare structure
    stats.year[yyyy][mm].id =		new Array();	// Tally stations
    stats.year[yyyy][mm].cnt =	new Array();	// Tally countries (with states)
    stats.year[yyyy][mm].rgn =	new Array();	// Tally countries (with states)
    for (i in rgn_arr) {   
      stats.year[yyyy][mm].rgn[i] =	0;	// Count 
    }
    stats.year[yyyy][mm].dx_d =		new Array();
    stats.year[yyyy][mm].dx_n =		new Array();
    stats.year[yyyy][mm].dx_x =		new Array();
    stats.year[yyyy][mm].new_beacon =	0;		// Count new beacons
    stats.year[yyyy][mm].n60 =		0;		// Count Beacons North of 60
    stats.year[yyyy][mm].max_day =		0;		// Log furthest day-time DX
    stats.year[yyyy][mm].max_night =	0;		// Log furthest night-time DX
    stats.year[yyyy][mm].max_dxw =		0;		// Log furthest DX/W
  }

  // ++++++++++++++++++++++++++++++++++
  // + Prepare station[id].log counts +
  // ++++++++++++++++++++++++++++++++++
  // station[id].log used for main listing output
  if (!station[id].all_date) {
    station[id].all_yyyymmddhhmm =	yyyymmdd+""+hhmm;
    switch (cookie['format']) {
      case "dd.mm.yyyy": station[id].all_date = dd+"."+mm+"."+yyyy; break;
      case "ddmmyyyy":   station[id].all_date = dd+"/"+mm+"/"+yyyy; break;
      case "mmddyyyy":   station[id].all_date = mm+"/"+dd+"/"+yyyy; break;
      default:           station[id].all_date = yyyy+""+mm+""+dd;   break;
    }
    station[id].all_time =	hhmm;
    station[id].all_notes =	new Array();
    stats.all[id] =		true;	// used in lifetime log stats
  }

  if (!station[id].log[yyyy]) {
    station[id].log[yyyy] =		new Array();
    station[id].log[yyyy].rx =	new Array();
  }
  if (!station[id].log[yyyy][mm]) {
    station[id].log[yyyy][mm] = new Array();
    station[id].log[yyyy][mm].notes = new Array();
  }


  // ++++++++++++++++++++++++++++++++++
  // + Input station[id].log counts   +
  // ++++++++++++++++++++++++++++++++++
  // Station ever logged before?
  // Input latest logging for the month
  // Daytime or nighttime logging?
  if (utc_daylight_array[0] == hh || utc_daylight_array[1] == hh || utc_daylight_array[2] == hh || utc_daylight_array[3] == hh) {
    station[id].log[yyyy][mm].day_hhmm = hhmm;
    station[id].log[yyyy][mm].day_dd = dd;
    station[id].log[yyyy].day_dd = dd;				// Put a daytime value in if you have one - doesn't matter which one
    notes = ((notes)?(((monthly)?("D:"):(""))+notes):(""))
    if (station[id].dx > stats.year[yyyy].max_day) {
      stats.year[yyyy].max_day = station[id].dx;
    }
    if (station[id].dx > stats.year[yyyy][mm].max_day) {
      stats.year[yyyy][mm].max_day = station[id].dx;
    }
  }
  else {
    station[id].log[yyyy][mm].night_hhmm = hhmm;
    station[id].log[yyyy][mm].night_dd = dd;
    station[id].log[yyyy].night_dd = dd;	// Put a nighttime value in if you have one - doesn't matter which one
    notes = ((notes)?(((monthly)?("N:"):(""))+notes):(""))
    if (station[id].dx > stats.year[yyyy].max_night) {
      stats.year[yyyy].max_night = station[id].dx;
    }
    if (station[id].dx > stats.year[yyyy][mm].max_night) {
      stats.year[yyyy][mm].max_night = station[id].dx;
    }
  }


  // Don't count unknowns in country reports
  if (!(station[id].cnt=="?" || station[id].sta=="?")) {
    var cnt =	station[id].cnt+"_"+station[id].sta;

  // ++++++++++++++++++++++++++++++++++
  // + Check yearly totals            +
  // ++++++++++++++++++++++++++++++++++
    if (!stats.year[yyyy].id[id]) {			// Has station been logged this year?
      stats.year[yyyy].id[id] =	1;			// No, so log it now
      stats.year[yyyy].new_beacon++;			// and add it to the new beacons count
      stats.year[yyyy][mm].new_beacon++;		// and add it to the new beacons count, and add it to this month's total also.
      if (station[id].dxw>stats.year[yyyy].max_dxw) {
        stats.year[yyyy].max_dxw = station[id].dxw;
      }
      if (station[id].lat >= 60) {				// If North of 60 degrees,
        stats.year[yyyy].n60++;				// Add it to N of 60 count.
      }
      if (!stats.year[yyyy].cnt[cnt]) {			// ... and check if the country has been logged
        stats.year[yyyy].cnt[cnt] = new Array();
        stats.year[yyyy].cnt[cnt].count =	1;	// No, Record the country
        stats.year[yyyy].cnt[cnt].best_ndb_dx = 0;
        stats.year[yyyy].cnt[cnt].best_dgps_dx = 0;
        if (station[id].call.substr(0,1) != "#") {
          stats.year[yyyy].cnt[cnt].best_ndb_dx =	station[id].dx;	// Record DX as best DX from this country
          stats.year[yyyy].cnt[cnt].best_ndb_id =	id;	// Record the ID as the best DX from this country
        }
        else {
          stats.year[yyyy].cnt[cnt].best_dgps_dx =	station[id].dx;	// Record DX as best DX from this country
          stats.year[yyyy].cnt[cnt].best_dgps_id =	id;	// Record the ID as the best DX from this country
        }
        if (station[id].rgn) {
          stats.year[yyyy].rgn[station[id].rgn]++;			// And increment the counter for that region
        }
        else {
          alert(	"NDB WEBLOG ERROR\n\nStation "+id+" is not defined as belonging to any country.\n\n"+
				"Please check the stations data file and enter a valid ITU\ncountry code.");
        }
      }
      else {											// Else country has already been logged
        stats.year[yyyy].cnt[cnt].count++;					// Add another station to it
        if (station[id].call.substr(0,1) != "#") {
          if (station[id].dx>stats.year[yyyy].cnt[cnt].best_ndb_dx) {
            stats.year[yyyy].cnt[cnt].best_ndb_dx = station[id].dx;	// Record DX as best DX from this country
            stats.year[yyyy].cnt[cnt].best_ndb_id = id;				// Record the ID as the best DX from this country
          }
        }
        else {
          if (station[id].dx>stats.year[yyyy].cnt[cnt].best_dgps_dx) {
            stats.year[yyyy].cnt[cnt].best_dgps_dx = station[id].dx;	// Record DX as best DX from this country
            stats.year[yyyy].cnt[cnt].best_dgps_id = id;				// Record the ID as the best DX from this country
          }
        }
      }
    }

  // ++++++++++++++++++++++++++++++++++
  // + Check monthly totals           +
  // ++++++++++++++++++++++++++++++++++
    if (!stats.year[yyyy][mm].id[id]) {			// Has station been logged this month?
      stats.year[yyyy][mm].id[id] =	1;		// No, so log it now
      if (station[id].dxw>stats.year[yyyy][mm].max_dxw) {
        stats.year[yyyy][mm].max_dxw = station[id].dxw;
      }
      if (station[id].lat >=60) {				// If North of 60 degrees,
        stats.year[yyyy][mm].n60++				// Add it to N of 60 count.
      }
      if (!stats.year[yyyy][mm].cnt[cnt]) {		// ... and check if the country has been logged
        stats.year[yyyy][mm].cnt[cnt] =	1;		// No, Record the country
        stats.year[yyyy][mm].rgn[station[id].rgn]++			// And increment the counter for that region
      }
      else {								// Else country has already been logged
        stats.year[yyyy][mm].cnt[cnt]++			// Add another station to it
      }
    }
  }

  // Used in stats output and main listing
  if (station[id].log[yyyy][mm].night_dd && station[id].log[yyyy][mm].day_dd) {
    station[id].log[yyyy].rx[mm] =	"X";
  }
  else if (station[id].log[yyyy][mm].night_dd) {
    station[id].log[yyyy].rx[mm] =	"N";
  }
  else if (station[id].log[yyyy][mm].day_dd) {
    station[id].log[yyyy].rx[mm] =	"D";
  }

  // Used in stats report
  if (station[id].log[yyyy].night_dd && station[id].log[yyyy].day_dd) {
    station[id].log[yyyy].rx_x =	"X";
  }
  else if (station[id].log[yyyy].night_dd) {
    station[id].log[yyyy].rx_n =	"N";
  }
  else if (station[id].log[yyyy].day_dd) {
    station[id].log[yyyy].rx_d =	"D";
  }

  if (notes!="") {
    station[id].all_notes[station[id].all_notes.length] =	notes;
    station[id].log[yyyy][mm].notes[station[id].log[yyyy][mm].notes.length] =	notes;
  }
}


// ************************************
// * REGION Constructor:              *
// ************************************
function REGION(rgn,name) {
  rgn_arr[rgn] = name;
}



// ************************************
// * STATE Constructor:               *
// ************************************
function STATE(sta,name,cnt) {
  if (!sta_arr[cnt]) {
    sta_arr[cnt] = new Array();
  }
  sta_arr[cnt][sta] = name;
}

// ************************************
// * STATION Constructor:             *
// ************************************
function STATION(khz,call,qth,sta,cnt,cyc,daid,lsb,usb,pwr,lat,lon,notes) {
  if (typeof(cnt_arr[cnt]) == "undefined") {
    unregistered_countries[unregistered_countries.length] = khz+"-"+call+" - code given was "+cnt;
    return;
  }
  var a =		new Array()
  a.khz =		khz;
  a.call =	call;
  a.qth =		qth;
  a.sta =		sta;
  a.cnt =		cnt;
  a.rgn =		cnt_arr[cnt].rgn
  a.cyc =		cyc;
  a.daid =	daid;
  if (cookie['mod_abs']=='1') {
    a.lsb = (lsb ? format_3sf(parseInt(khz)-(lsb/1000)) : 0)
    a.usb = (usb ? format_3sf(parseInt(khz)+(usb/1000)) : 0)
  }
  else {
    a.lsb =		lsb;
    a.usb =		usb;
  }

  a.pwr =		pwr;
  a.lat =		lat;
  a.lon =		lon;
  a.ident =	get_ident(call);
  a.notes =	notes;
  if (lat+lon) {
    var n =	get_range_bearing(qth_lat,qth_lon,lat,lon,cookie['units'])
    a.dir =	n[0];
    a.dx =	n[1];
    a.dxw =	pwr ? Math.round(a.dx*10/a.pwr)/10 : 0;
    a.gsq =	get_gsq(lat,lon);
  }
  else {
    a.dir =	-1;
    a.dx =	-1;
    a.dxw =	0;
    a.gsq =	"";
  }
  a.log =	new Array();
  station[khz+"-"+call] =	a;
}


// ************************************
// * TEXT Constructor:                *
// ************************************
// Used in text output
function TEXT(yyyymmddhhmm,date,hhmm,khz,call,lsb,usb,pwr,dx,dxw,nu,qth,sta,cnt) {
  this.yyyymmddhhmm =	yyyymmddhhmm;
  this.date =	date;
  this.hhmm =	hhmm;
  this.khz =	khz;
  this.call =	call;
  this.lsb =	lsb;
  this.usb =	usb;
  this.pwr =	pwr;
  this.dx =	dx;
  this.dxw =	dxw;
  this.nu =	nu;	// Can't say 'new' as this is a reserved word
  this.qth =	qth;
  this.sta =	sta;
  this.cnt =	cnt;
}






// ###########################################
// # Functions:                              #
// ###########################################
// ************************************
// * format_3sf()                     *
// ************************************
function format_3sf(X) {	// Returns decimals formatted to three significant figures
  // Based on work by J.Stockton - http://www.merlyn.demon.co.uk/js-round.htm#OGC
  return String((Math.round(X*1000) + (X<0 ? -0.1 : +0.1)) / 1000 ).replace(/(.*\.\d\d\d)\d*/,'$1');
}



// ************************************
// * format_date()                    *
// ************************************
function format_date(yyyymmdd) {
  var yyyy =		yyyymmdd.substr(0,4);
  var mm =		yyyymmdd.substr(4,2);
  var dd =		yyyymmdd.substr(6,2);
  switch (cookie['format']) {
    case "dd.mm.yyyy": return dd+"."+mm+"."+yyyy;	// For German users (suggested by Udo Deutscher)
    case "ddmmyyyy":   return dd+"/"+mm+"/"+yyyy;
    case "mmddyyyy":   return mm+"/"+dd+"/"+yyyy;
    case "yyyy-mm-dd":   return yyyy+"-"+mm+"-"+dd;
    default:           return yyyymmdd;
  }
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
// * get_graph_color:                 *
// ************************************
function get_graph_color(number,max) {
  var chars =	"0123456789abcdef";
  var value =	255-((number/max)*255)
  var lsb =	value % 16;
  var msb =	(value - lsb) / 16;
  var hex =	"" + chars.charAt(msb) + chars.charAt(lsb)
  var result =	"#ff"+hex+hex
  return	result;
}


// ************************************
// * get_gsq(lat,lon)                 *
// ************************************
function get_gsq(lat,lon) {
  var letters =		"ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  var gsq_lon =		(parseFloat(lon)+180)/20;
  var gsq_lon_deg =	parseInt(gsq_lon);
  var gsq_lon_mmss =	(gsq_lon-gsq_lon_deg)*10;
  var gsq_1 =		letters.charAt(gsq_lon_deg);
  var gsq_3 =		Math.floor(gsq_lon_mmss);
  var gsq_5 =		letters.charAt(parseInt((gsq_lon_mmss-gsq_3)*24));

  var gsq_lat =		(parseFloat(lat)+90)/10;
  var gsq_lat_deg =	parseInt(gsq_lat);
  var gsq_lat_mmss =	(gsq_lat-gsq_lat_deg)*10;
  var gsq_2 =		letters.charAt(gsq_lat_deg);
  var gsq_4 =		parseInt(gsq_lat_mmss);
  var gsq_6 =		letters.charAt(parseInt((gsq_lat_mmss-gsq_4)*24));
  return (gsq_1+gsq_2+gsq_3+gsq_4+gsq_5+gsq_6);
}


// ************************************
// * get_ident()                      *
// ************************************
function get_ident(cal) {
  if (cal.substr(0,1) == "#") {		// Detects DGPS Idents
    return "DGPS "+cal;
  }
  morse =		new Array();
  morse['A'] =	".-";
  morse['Ä'] =	".-.-";  // German
  morse['Æ'] =	".-.-";  // Scandanavian
  morse['Á'] =	".--.-"; // Scandanavian or Spanish
  morse['Å'] =	".--.-"; // Scandanavian or Spanish
  morse['B'] =	"-...";
  morse['C'] =	"-.-.";
  morse['D'] =	"-..";
  morse['E'] =	".";
  morse['É'] =	"..-.."; // Finish or French
  morse['F'] =	"..-.";
  morse['G'] =	"--.";
  morse['H'] =	"....";
  morse['I'] =	"..";
  morse['J'] =	".---";
  morse['K'] =	"-.-";
  morse['L'] =	".-..";
  morse['M'] =	"--";
  morse['N'] =	"-.";
  morse['Ñ'] =	"--.--";	// Spanish
  morse['O'] =	"---";
  morse['Ö'] =	"---.";  // German or Scandanavian
  morse['Ø'] =	"---.";  // Scandanavian
  morse['P'] =	".--.";
  morse['Q'] =	"--.-";
  morse['R'] =	".-.";
  morse['S'] =	"...";
  morse['T'] =	"-";
  morse['U'] =	"..-";
  morse['Ü'] =	"..--";  // Finish or German
  morse['V'] =	"...-";
  morse['W'] =	".--";
  morse['X'] =	"-..-";
  morse['Y'] =	"-.--";
  morse['Z'] =	"--..";
  morse['1'] =	".----";
  morse['2'] =	"..---";
  morse['3'] =	"...--";
  morse['4'] =	"....-";
  morse['5'] =	".....";
  morse['6'] =	"-....";
  morse['7'] =	"--...";
  morse['8'] =	"---..";
  morse['9'] =	"----.";
  morse['0'] =	"-----";

  var out =	new Array;
  var n =	0;
  for (var a=0; a<cal.length; a++) {
    out[n++] = morse[cal.substr(a,1)];
  }
  return (out.join("/")+"&nbsp;");	// fixes letter spacing problem in IE6 - otherwise last set of dashes are not spaced.
}



// ************************************
// * get_range_bearing()              *
// ************************************
function get_range_bearing(qth_lat,qth_lon,dx_lat,dx_lon,units) {
// Check for same point:
  if (qth_lat == dx_lat && qth_lon==dx_lon) {
    return new Array(0,0);
  }
  var dlon = (dx_lon - qth_lon)
  if (Math.abs(dlon) > 180) {
    dlon = (360 - Math.abs(dlon))*(0-(dlon/Math.abs(dlon)));
  }
  var rinlat =		qth_lat*0.01745;	// convert to radians
  var rinlon =		qth_lon*0.01745;
  var rfnlat =		dx_lat*0.01745;
  var rdlon =		dlon*0.01745;
  var rgcdist =	Math.acos(Math.sin(rinlat)*Math.sin(rfnlat)+Math.cos(rinlat)*Math.cos(rfnlat)*Math.cos(rdlon));

  var rincourse =	(Math.sin(rfnlat)-Math.cos(rgcdist)*Math.sin(rinlat))/(Math.sin(rgcdist)*Math.cos(rinlat));
  rincourse =		Math.acos(rincourse);
  incourse =		rincourse*57.3;
  if (dlon < 0) {
    incourse =		360 - incourse;
  }
  switch (units) {
    case "mi": return new Array(Math.round(incourse),Math.round(Math.abs(rgcdist)*3958.284)); break;
    case "nm": return new Array(Math.round(incourse),Math.round(Math.abs(rgcdist)*3439.719)); break;
    default:   return new Array(Math.round(incourse),Math.round(Math.abs(rgcdist)*6370.614)); break;
  }
}



// ************************************
// * get_region(country)              *
// ************************************
// See countries.js



// ************************************
// * get_units()                      *
// ************************************
function get_units() {
  switch(cookie['units'])	{
    case "mi":	return "Miles"; break;
    case "nm":	return "Naut.M"; break;
    default:	return "KM"; break;
  }
}


// ************************************
// * goto_page()                      *
// ************************************
function goto_page(yyyy,mm,sort) {
  document.cookie =		"list_selected="+(mm ? mm : sel_mm)+"|"+(sort ? sort : sel_sort)+"|"+(yyyy ? yyyy : sel_yyyy);
  sel_yyyy =	yyyy
  sel_mm =	mm
  sel_sort =	sort
  progress()
  list()
}



// ************************************
// * keydown()                        *
// ************************************
function keydown(win,e) {
  switch (win) {
    case "main":
      if (e.ctrlKey && e.keyCode == 49) {	// CTRL+1
        popup_stats();		return false;
      }
      if (e.ctrlKey && e.keyCode == 50) {	// CTRL+2
        popup_prefs();		return false;
      }
      if (e.ctrlKey && e.keyCode == 51) {	// CTRL+3
        popup_search();		return false;
      }
      if (e.ctrlKey && e.keyCode == 52) {	// CTRL+4
        popup_text_options();		return false;
      }
      if (e.ctrlKey && e.keyCode == 53) {	// CTRL+5
        popup_help();		return false;
      }
      if (e.ctrlKey && e.keyCode == 54) {	// CTRL+6
        popup_home();		return false;
      }
    break;
    case "popup_details":
      if (e.keyCode == 27) {
        details_h.close();		return false;
      }
    break;
    case "popup_prefs":
      if (e.keyCode == 27) {
        pref_h.close();			return false;
      }
    break;
    case "popup_help":
      if (e.keyCode == 27) {
        help_h.close();			return false;
      }
    break;
    case "popup_home":
      if (e.keyCode == 27) {
        home_h.close();			return false;
      }
    break;
    case "popup_search":
      if (e.keyCode == 27) {
        search_h.close();		return false;
      }
    break;
    case "popup_stats":
      if (e.keyCode == 27) {
        stat_h.close();			return false;
      }
    break;
    case "search":
      if (e.keyCode == 27) {
        result_h.close();		return false;
      }
    break;
    case "popup_text_options":
      if (e.keyCode == 27) {
        text_options_h.close();	return false;
      }
    break;
  }
  return true
}

 
// ************************************
// * lead()                           *
// ************************************
function lead(num) {
  return (num.toString().length==1 ? "0"+num : num)
}


// ************************************
// * list()                           *
// ************************************
function list(yyyy,mm) {
  var out =		new Array();
  var n =			0;

  // Check that system has been correctly set up:
  // First check config.js variables are set:

  if ((typeof qth_lat == "undefined") || (typeof qth_lon == "undefined") || (typeof qth_name == "undefined") || (typeof qth_home == "undefined") ||
      (typeof qth_email == "undefined") || (typeof monthly == "undefined") || (typeof utc_offset == "undefined")) {
    if (progress_hd) {
      progress_hd.close();
    }
    return (	"<h1>Error</h1><p>The <b>config.js</b> file should reside in the same directory as your other NDB WebLog files.<br>"+
			"It must include these definitions in order for this system to function correctly:</p><ul>"+
			"<li><b>qtl_lat</b> - decimal value for latitude.</li>"+
			"<li><b>qtl_lon</b> - decimal value for longitude.</li>"+
			"<li><b>qtl_name</b> - a \"string\" containing the log owner's name.</li>"+
			"<li><b>qtl_home</b> - a \"string\" containing URL of the log owner's web site, or \"\" to disable.</li>"+
			"<li><b>qtl_email</b> - a \"string\" containing email address of the log owner's web site, or \"\" to disable.</li>"+
			"<li><b>monthly</b> - 1 if you want to use the montly log format, 0 if you want a lifetime log format.</li>"+
			"<li><b>utc_offset</b> - the number of hours that standard time in your time zone (winter time) differs from UTC."+
			"<br>For example, for <b>EST</b> this value is <b>5</b>, for <b>GMT</b> this value is <b>0</b></li>");
  }

  // Now check stations.js:
  var i=0;
  for (a in station) { i++ }
  if (!i) {
    if (progress_hd) {
      progress_hd.close();
    }
    return (	"<h1>Error</h1><p>There is a problem reading data in the <b>stations.js</b> file. This file should reside in the same directory as your other NDB WebLog files.<br>"+
			"It should contain at least one station, and contain no errors (e.g. strings not enclosed between \"quotes\") in order for this system to function correctly</p>");
  }
  // Now check log.js:
  i=0;
  for (a in logbook) { i++ }
  if (!i) {
    if (progress_hd) {
      progress_hd.close();
    }
    return (	"<h1>Error</h1><p>There is a problem reading data in the <b>log.js</b> file. This file should reside in the same directory as your other NDB WebLog files.<br>"+
			"It should contain at least one log entry, and contain no errors (e.g. strings not enclosed between \"quotes\") in order for this system to function correctly.</p>");
  }


  var msg =		status_msg("Sort by this column (ascending)")
  var msg_d =		status_msg("Sort by this column (descending)")
  var msg_s =		status_msg("Show details for this month");
  var msg_m =		status_msg("Show map for this location");
  var msg_e =		status_msg("Send an email");
  var msg_dl =		status_msg("Download your copy today!");
  var msg_utc =	status_msg("View World Times");

  // If monthly = 1 then monthly columns are available, otherwise you get first date and time received only
  // (for people who don't keep such detailed logs)

  if ((cookie['h_gsq']=='1' && sel_sort=="gsq") || (cookie['h_ident']=='1' && sel_sort=="ident") ||
      (cookie['h_notes']=='1' && sel_sort=="notes") || (cookie['h_notes']=='1' && sel_sort=="dxw")) {
    sel_sort="khz";  // If user just hid the sorted column, change order back to default.
  }

  out[n++] =	"<head><link TITLE='new' REL='stylesheet' HREF='ndbweblog.css' type='text/css'>\n";
  out[n++] =	"<style type='text/css'>\n";
  out[n++] =	".01 { border-right: 1px solid RGB(128,128,128); background-color: RGB("+((sel_mm=="01")?("200,200,128"):("236,236,236"))+");}\n";
  out[n++] =	".02 { border-right: 1px solid RGB(128,128,128); background-color: RGB("+((sel_mm=="02")?("200,200,128"):("255,255,255"))+");}\n";
  out[n++] =	".03 { border-right: 1px solid RGB(128,128,128); background-color: RGB("+((sel_mm=="03")?("200,200,128"):("236,236,236"))+");}\n";
  out[n++] =	".04 { border-right: 1px solid RGB(128,128,128); background-color: RGB("+((sel_mm=="04")?("200,200,128"):("255,255,255"))+");}\n";
  out[n++] =	".05 { border-right: 1px solid RGB(128,128,128); background-color: RGB("+((sel_mm=="05")?("200,200,128"):("236,236,236"))+");}\n";
  out[n++] =	".06 { border-right: 1px solid RGB(128,128,128); background-color: RGB("+((sel_mm=="06")?("200,200,128"):("255,255,255"))+");}\n";
  out[n++] =	".07 { border-right: 1px solid RGB(128,128,128); background-color: RGB("+((sel_mm=="07")?("200,200,128"):("236,236,236"))+");}\n";
  out[n++] =	".08 { border-right: 1px solid RGB(128,128,128); background-color: RGB("+((sel_mm=="08")?("200,200,128"):("255,255,255"))+");}\n";
  out[n++] =	".09 { border-right: 1px solid RGB(128,128,128); background-color: RGB("+((sel_mm=="09")?("200,200,128"):("236,236,236"))+");}\n";
  out[n++] =	".10 { border-right: 1px solid RGB(128,128,128); background-color: RGB("+((sel_mm=="10")?("200,200,128"):("255,255,255"))+");}\n";
  out[n++] =	".11 { border-right: 1px solid RGB(128,128,128); background-color: RGB("+((sel_mm=="11")?("200,200,128"):("236,236,236"))+");}\n";
  out[n++] =	".12 { border-right: 1px solid RGB(0,0,0); background-color: RGB("+((sel_mm=="12")?("200,200,128"):("255,255,255"))+");}\n";
  out[n++] =	"</style>\n";
  out[n++] =	"<title>NDB WebLog "+version+" for "+qth_name+" > Main Listing > " + months[sel_mm-1] + " " + sel_yyyy + "</title>\n";
  out[n++] =	"</head>\n";
  out[n++] =	"<BODY LINK='#0000ff' VLINK='#800080' onload='' onkeydown='top.keydown(\"main\",event)'>\n";
  out[n++] =	"<form name='form' action='./'>\n";
  out[n++] =	"<p><b>"+((qth_email)?("<a href='mailto:"+qth_email+"?subject="+qth_name+" NDB%20WebLog'"+msg_e+" title='Send me an email!'>"+qth_name+"</a>"):(qth_name));
  out[n++] =	((monthly)?(" - "+months[sel_mm-1]+" "+sel_yyyy):(""))+"<br>\n";
  out[n++] =	"<a href='javascript:void(top.popup_map(qth_lat,qth_lon))'"+msg_m+" title='Click to see a map of this location'>Location</a> "+qth_lat+", "+qth_lon+"</b><br>\n";
  out[n++] =	"<small>NDB WebLog Software: "+version+" by <a href='mailto:martin@classaxe.com' title='Bug reports? Suggestions? Contact the programmer'><b>Martin Francis</b></a>, &copy; 2003, 2004<br>\n"
  out[n++] =	"This <a href='http://www.classaxe.com/dx' target='_blank'"+msg_dl+" title='NDB WebLog is free - get it here!'><b>NDB WebLog</b></a> is configured as a ";
  out[n++] =	"<b>"+((monthly)?("Monthly"):("Lifetime"))+" list</b>, "
  out[n++] =	"all <b>times</b> are in <a href='http://www.timeanddate.com/worldclock' target='_blank'"+msg_utc+" title='View world clock'><b>UTC</b></a>, ";
  out[n++] =	"and the <b>last log entry</b> was recorded on <b>"+format_date(stats.last_date)+" at "+stats.last_time+" UTC</b><br>";

  // Add year selection buttons
  if (monthly) {	// Find out if more than 1 years log results are recorded - if so, put year selector links on page:
    var years =	new Array();
    var i =		0;
    for (link_yyyy in stats.year) {
      years[i++] =	link_yyyy;
    }
    years.sort();
    var years_out =	new Array();
    for (var i=0; i<years.length; i++) {
      years_out[i] = (years[i] == sel_yyyy ? "<font color='red'>"+years[i]+"</font>" : "<a href='javascript:top.goto_page(\""+years[i]+"\",\""+sel_mm+"\",\""+sel_sort+"\")'>"+years[i]+"</a>");
    }
    if (years.length>1 || (years.length == 1 && years[0] != sel_yyyy)) {
      out[n++] =	"<p align='center'><b>Year: [ " +years_out.join(" | ") + " ]\n\n";
    }
  }
  out[n++] =	"<p align='center'><input type='button' class='120px' value='Statistics' onclick='void top.popup_stats()' title='[CTRL]+1'>";
  out[n++] =	"<input type='button' class='120px' value='Preferences' onclick='void top.popup_prefs()' title='[CTRL]+2'>"
  out[n++] =	"<input type='button' class='120px' value='Search' onclick='void top.popup_search()' title='[CTRL]+3'>"
  out[n++] =	"<input type='button' class='120px' value='Text List' onclick='void top.popup_text_options()' title='[CTRL]+4'>"
  out[n++] =	"<input type='button' class='120px' value='Help' onclick='top.popup_help(); return true;' title='[CTRL]+5'>"
  out[n++] =	((qth_home)?("<input type='button' class='120px' value='Home Page' onclick='void top.popup_home()' title='[CTRL]+6'>"):(""))
  out[n++] =	"</p>\n\n"

  rows = ((monthly) ? "'3'" : ((cookie['h_lifelist']=='1')? "'1'" : "'2'"))

  var href =	((monthly)?("href='javascript:top.goto_page(\""+sel_yyyy+"\",\""+sel_mm+"\",\""):("href='javascript:top.goto_page(\"\",\"\",\""))

  // Begin main output (apply borders if NS is in use)
  out[n++] =	"<table border='"+((document.all)?("0"):("1"))+"' cellspacing='0' cellpadding='0' border='0'>\n";

  // Table Headings:
  out[n++] =	"<tr>\n";
  out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="khz"||sel_sort=="khz_d"   ? " class='khz_sort" : " class='khz'")+(sel_sort=="khz_d" ? "_d" : "")+"'>";
  out[n++] =	"<a "+href+"khz"+ (sel_sort=="khz"  ? "_d" : "")+"\")'"+(sel_sort=="khz"  ? msg_d : msg)+" title='Frequency in Kilohertz'>KHz</a></th>\n";

  out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="call"||sel_sort=="call_d" ? " class='sort" : "")+(sel_sort=="call_d" ? "_d" : "")+"'>";
  out[n++] =	"<a "+href+"call"+(sel_sort=="call" ? "_d" : "")+"\")'"+(sel_sort=="call" ? msg_d : msg)+" title='Callsign or DGPS Station Number'>Call</a></th>\n";

  out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="qth" ||sel_sort=="qth_d"  ? " class='sort" : "")+(sel_sort=="qth_d"  ? "_d" : "")+"'>";
  out[n++] =	"<a "+href+"qth"+ (sel_sort=="qth"  ? "_d" : "")+"\")'"+(sel_sort=="qth"  ? msg_d : msg)+" title='Location (where known)'>Location</a></th>\n";

  out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="sta" ||sel_sort=="sta_d"  ? " class='sort" : "")+(sel_sort=="sta_d"  ? "_d" : "")+"'>";
  out[n++] =	"<a "+href+"sta"+ (sel_sort=="sta"  ? "_d" : "")+"\")'"+(sel_sort=="sta"  ? msg_d : msg)+" title='State or province (for USA, Canada and Australia)'>S<br>t<br>a<br>t<br>e</a></th>\n";

  out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="cnt" ||sel_sort=="cnt_d"  ? " class='sort" : "")+(sel_sort=="cnt_d"  ? "_d" : "")+"'>";
  out[n++] =	"<a "+href+"cnt"+ (sel_sort=="cnt"  ? "_d" : "")+"\")'"+(sel_sort=="cnt"  ? msg_d : msg)+" title='NDB List approved Country Code'>C<br>o<br>u<br>n<br>t<br>r<br>y</a></th>\n";

  if (cookie['h_gsq']!='1') {
    out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="gsq" || sel_sort=="gsq_d" ? " class='sort" : "")+(sel_sort=="gsq_d"  ? "_d" : "")+"'>";
    out[n++] =	"<a "+href+"gsq"+ (sel_sort=="gsq"  ? "_d" : "")+"\")'"+(sel_sort=="gsq"  ? msg_d : msg)+" title='ITU Grid Square locator'>GSQ</a></th>\n";
  }
  if (cookie['h_ident']!='1') {
    out[n++] =	"<th ROWSPAN="+rows+">Morse / DGPS ID</th>\n";
  }
  out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="daid"||sel_sort=="daid_d" ? " class='sort" : "")+(sel_sort=="daid_d" ? "_d" : "")+"'>";
  out[n++] =	"<a "+href+"daid"+(sel_sort=="daid" ? "_d" : "")+"\")'"+(sel_sort=="daid" ? msg_d : msg)+" title='Dash After Identification?'>D<br>A<br>I<br>D</a></th>\n";

  out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="cyc" || sel_sort=="cyc_d" ? " class='sort" : "")+(sel_sort=="cyc_d"  ? "_d" : "")+"'>";
  out[n++] =	"<a "+href+"cyc"+ (sel_sort=="cyc"  ? "_d" : "")+"\")'"+(sel_sort=="cyc"  ? msg_d : msg)+" title='Period in seconds OR format of repetitions'>Cycle</a></th>\n";

  if (cookie['mod_abs'] == '1') {
    out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="lsb" ||sel_sort=="lsb_d"  ? " class='sort" : "")+(sel_sort=="lsb_d"  ? "_d" : "")+"'>";
    out[n++] =	"<a "+href+"lsb"+ (sel_sort=="lsb"  ? "_d" : "")+"\")'"+(sel_sort=="lsb"  ? msg_d : msg)+" title='Negative Mod Offset'>LSB<br>KHz<br>(Abs)</a></th>\n";

    out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="usb" ||sel_sort=="usb_d"  ? " class='sort" : "")+(sel_sort=="usb_d"  ? "_d" : "")+"'>";
    out[n++] =	"<a "+href+"usb"+ (sel_sort=="usb"  ? "_d" : "")+"\")'"+(sel_sort=="usb"  ? msg_d : msg)+" title='Positive Mod Offset'>USB<br>KHz<br>(Abs)</a></th>\n";
  }
  else {
    out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="lsb"  ||sel_sort=="lsb_d" ? " class='sort" : "")+(sel_sort=="lsb_d"  ? "_d" : "")+"'>";
    out[n++] =	"<a "+href+"lsb"+ (sel_sort=="lsb"  ? "_d" : "")+"\")'"+(sel_sort=="lsb"  ? msg_d : msg)+" title='Negative Mod Frequency'>LSB<br>Hz<br>(Rel)</a></th>\n";

    out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="usb"  ||sel_sort=="usb_d" ? " class='sort" : "")+(sel_sort=="usb_d"  ? "_d" : "")+"'>";
    out[n++] =	"<a "+href+"usb"+ (sel_sort=="usb"  ? "_d" : "")+"\")'"+(sel_sort=="usb"  ? msg_d : msg)+" title='Positive Mod Frequency'>USB<br>Hz<br>(Rel)</a></th>\n";
  }

  out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="pwr"  ||sel_sort=="pwr_d" ? " class='sort" : "")+(sel_sort=="pwr_d"  ? "_d" : "")+"'>";
  out[n++] =	"<a "+href+"pwr"+ (sel_sort=="pwr"  ? "_d" : "")+"\")'"+(sel_sort=="pwr"  ? msg_d : msg)+" title='Power in Watts (where known)'>Pwr</a></th>\n";

  out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="dir"  ||sel_sort=="dir_d" ? " class='sort" : "")+(sel_sort=="dir_d"  ? "_d" : "")+"'>";
  out[n++] =	"<a "+href+"dir"+ (sel_sort=="dir"  ? "_d" : "")+"\")'"+(sel_sort=="dir"  ? msg_d : msg)+" title='Bearing in degrees from reception location'>Deg</a></th>\n";

  out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="dx"   ||sel_sort=="dx_d"  ? " class='sort" : "")+(sel_sort=="dx_d"   ? "_d" : "")+"'>";
  out[n++] =	"<a "+href+"dx"+  (sel_sort=="dx"   ? "_d" : "")+"\")'"+(sel_sort=="dx"   ? msg_d : msg)+" title='Distance to beacon from reception location'>"
  out[n++] =	get_units()+"</a></th>\n";

  if (cookie['h_dxw']!='1') {
    out[n++] =	"<th ROWSPAN="+rows+(sel_sort=="dxw"  ||sel_sort=="dxw_d"  ? " class='sort" : "")+(sel_sort=="dxw_d"  ? "_d" : "")+"'>";
    out[n++] =	"<a "+href+"dxw"+ (sel_sort=="dxw"  ? "_d" : "")+"\")'"+(sel_sort=="dxw"  ? msg_d : msg)+" title='Distance covered by a single Watt (where DX and power are both known)'>"
    out[n++] =	get_units()+"<br>per<br>W</a></th>\n";
  }
  if (monthly) {
    out[n++] =	"<th class='sel' ROWSPAN=1 COLSPAN=12>&nbsp;"+sel_yyyy+" Summary<br>&nbsp; N&#9;Nighttime<br>&nbsp; D&#9;Daytime<br>&nbsp; X&#9;Both</th>\n";
    out[n++] =	"<th colspan='4' class='sel'>Details for<br>"+months[sel_mm-1]+" "+sel_yyyy+"<br><br>(UTC)<br></th>\n";
  }
  if (cookie['h_lifelist']!='1' && monthly) {
    out[n++] =	"<th ROWSPAN='2' colspan='2' class='all_log'>First<br>\nReceived</th>\n";
  }

  if (cookie['h_lifelist']!='1' && !monthly) {
    out[n++] =	"<th ROWSPAN='1' colspan='2' class='all_log'>First RXed</th>\n";
  }
  if (cookie['h_notes']!='1') {
    out[n++] =	"<th ROWSPAN="+rows+" class='notes'>Notes"+(monthly ? " <span class='normal'>(D:Day, N: Night)</span>" : "")+"</th>\n";
  }
  out[n++] =	"</tr>\n\n";


  if (monthly) {

  // Month summary headings:
  // If none of the months is already selected:
  //   classes of each month = mm_arr[i] except for mmclass=((mm==mm_arr[i])?(selected):(months[i].subst(0,2)))
  //   click changes month but leaves sort order as it is.
  // If month already selected and sort order is NOT set to the month
  //   class=((mm==mm_arr[i])?(sort):(mm_abr[i]))
  //   click changes sort order to month
  // If month already selected and sort order IS set to the month
  //   class=((mm==mm_arr[i])?(sorted_d):(mm_abr[i]))
  //   click reverses sort order in month

    out[n++] =	"<TR>\n";
    var sorting_months =	false;
    for (var i=0; i<12; i++) {
      if (sel_sort==mm_arr[i] || sel_sort==mm_arr[i]+"_d") {
        sorting_months =	true;
      }
    }
    
    for (var i=0; i<12; i++) {
      if (!sorting_months){
        out[n++] =	"<th rowspan='2'>";
        out[n++] =	"<a href='javascript:top.goto_page(\""+sel_yyyy+"\",\""+mm_arr[i]+"\",\""+(sel_mm==mm_arr[i] ? mm_arr[i] : sel_sort)+"\")' "
        out[n++] =	(mm_arr[i]!=sel_mm ? msg_s : msg)+" title='"+months[i]+"'>";
      }
      else {
        if (sel_mm==mm_arr[i]){
          // The current month is selected already
          if (sel_sort==mm_arr[i]) {
            // Sorting on this month already, so next time sort decending:
            out[n++] =	"<th rowspan='2' class='sort'>";
            out[n++] =	"<a href='javascript:top.goto_page(\""+sel_yyyy+"\",\""+mm_arr[i]+"\",\""+mm_arr[i]+"_d\")'"+msg_d+" title='"+months[i]+"'>";
          }
          else {
            // Not sorting on this month already, so next time sort ascending:
            out[n++] =	"<th rowspan='2' class='sort_d'>";
            out[n++] =	"<a href='javascript:top.goto_page(\""+sel_yyyy+"\",\""+mm_arr[i]+"\",\""+mm_arr[i]+"\")'"+msg+" title='"+months[i]+"'>";
          }
        }
        else {
          out[n++] =	"<th rowspan='2' class='"+mm_arr[i]+"'>";
          out[n++] =	"<a href='javascript:top.goto_page(\""+sel_yyyy+"\",\""+mm_arr[i]+"\",\""+mm_arr[i]+"\")'"
          out[n++] =	(mm_arr[i]!=sel_mm ? msg_s : msg)+" title='"+months[i]+"'>";
        }
      }
      out[n++] =	months[i].substr(0,1)+"</a></th>\n";
    }
    out[n++] =	"<th COLSPAN=2>Night<br>"+lead((utc_daylight+4)%24)+":00-<br>"+lead((utc_daylight-1)%24)+":59</th>\n";
    out[n++] =	"<th COLSPAN=2>Day<br>"+lead(utc_daylight)+":00-<br>"+lead((utc_daylight+3)%24)+":59</th>\n";
    out[n++] =	"</TR>\n\n";

    out[n++] =	"<TR>\n";

    out[n++] =	"<TH"+((sel_sort=="ndd")?(" class='sort'"):(""))+((sel_sort=="ndd_d")?(" class='sort_d'"):(""));
    out[n++] =	"><a "+href+"ndd"+((sel_sort=="ndd")?("_d"):(""))+"\")'"+((sel_sort=="ndd")?(msg_d):(msg))+" title='Day of Month'>D</th>\n";

    out[n++] =	"<TH"+((sel_sort=="nhhmm")?(" class='sort'"):(""))+((sel_sort=="nhhmm_d")?(" class='sort_d'"):(""));
    out[n++] =	"><a "+href+"nhhmm"+((sel_sort=="nhhmm")?("_d"):(""))+"\")'"+((sel_sort=="nhhmm")?(msg_d):(msg))+" title='Time (UTC)'>UTC</th>\n";

    out[n++] =	"<TH"+((sel_sort=="ddd")?(" class='sort'"):(""))+((sel_sort=="ddd_d")?(" class='sort_d'"):(""));
    out[n++] =	"><a "+href+"ddd"+((sel_sort=="ddd")?("_d"):(""))+"\")'"+((sel_sort=="ddd")?(msg_d):(msg))+" title='Day of Month'>D</th>\n";

    out[n++] =	"<TH"+((sel_sort=="dhhmm")?(" class='sort'"):(""))+((sel_sort=="dhhmm_d")?(" class='sort_d'"):(""));
    out[n++] =	"><a "+href+"dhhmm"+((sel_sort=="dhhmm")?("_d"):(""))+"\")'"+((sel_sort=="dhhmm")?(msg_d):(msg))+" title='Time (UTC)'>UTC</th>\n";
  }

  if (cookie['h_lifelist']!='1') {
    out[n++] =	"<th "+((sel_sort=="all_date")?(" class='sort'"):(""))+((sel_sort=="all_date_d")?(" class='sort_d'"):(""));
    out[n++] =	"><a "+href+"all_date"+((sel_sort=="all_date")?("_d"):(""))+"\")'"+((sel_sort=="all_date")?(msg_d):(msg))+" title='Date (in format shown in column)'>"
    out[n++] = show_date_heading();
    out[n++] =	"</th>\n<th "+((sel_sort=="all_time")?(" class='sort'"):(""))+((sel_sort=="all_time_d")?(" class='sort_d'"):(""));
    out[n++] =	"><a "+href+"all_time"+((sel_sort=="all_time")?("_d"):(""))+"\")'"+((sel_sort=="all_time")?(msg_d):(msg))+" title='Time (UTC)'>UTC</th>\n";
  }

  out[n++] =	"</tr>\n\n";

  // Process data - convert from associative array into linear to allow sorting by column headings:
  var sorted = new Array(); var i=0;

  if (sel_sort) {
    switch(sel_sort) {
      case "01": case "02": case "03": case "04": case "05": case "06":
      case "07": case "08": case "09": case "10": case "11": case "12":
      case "01_d": case "02_d": case "03_d": case "04_d": case "05_d": case "06_d":
      case "07_d": case "08_d": case "09_d": case "10_d": case "11_d": case "12_d":
      case "ddd": case "dhhmm": case "ndd": case "nhhmm":
      case "ddd_d": case "dhhmm_d": case "ndd_d": case "nhhmm_d":
        for (a in station) {
          sorted[i] =	station[a];
          sorted[i].temp =		"";	// For sorting of XND columns
          sorted[i].temp_mm =		"";	// For sorting of time or date columns
          if (station[a].log && station[a].log[sel_yyyy] && station[a].log[sel_yyyy][sel_mm]) {
            sorted[i].temp_mm = station[a].log[sel_yyyy].rx[sel_mm];
            if ((sel_sort=="ddd"||sel_sort=="ddd_d")&&(station[a].log[sel_yyyy][sel_mm].day_dd)) {
              sorted[i].temp =	station[a].log[sel_yyyy][sel_mm].day_dd;
            }
            if ((sel_sort=="dhhmm"||sel_sort=="dhhmm_d")&&(station[a].log[sel_yyyy][sel_mm].day_dd)) {
              sorted[i].temp =	station[a].log[sel_yyyy][sel_mm].day_hhmm;
            }
            if ((sel_sort=="ndd"||sel_sort=="ndd_d")&&(station[a].log[sel_yyyy][sel_mm].night_dd)) {
              sorted[i].temp =	station[a].log[sel_yyyy][sel_mm].night_dd;
            }
            if ((sel_sort=="nhhmm"||sel_sort=="nhhmm_d")&&(station[a].log[sel_yyyy][sel_mm].night_dd)) {
              sorted[i].temp =	station[a].log[sel_yyyy][sel_mm].night_hhmm;
            }
          }
          i++;
        }
      break;

      default:
        for (a in station) {
          sorted[i++] =	station[a];
        }
      break;
    }

    switch(sel_sort) {
      case "01": case "02": case "03": case "04": case "05": case "06":
      case "07": case "08": case "09": case "10": case "11": case "12":
                        sorted.sort(sortBy_mm);        break;
      case "01_d": case "02_d": case "03_d": case "04_d": case "05_d": case "06_d":
      case "07_d": case "08_d": case "09_d": case "10_d": case "11_d": case "12_d":
                        sorted.sort(sortBy_mm_d);      break;
      case "ddd": case "dhhmm": case "ndd": case "nhhmm":
        sorted.sort(sortBy_temp); break;
      case "ddd_d": case "dhhmm_d": case "ndd_d": case "nhhmm_d":
        sorted.sort(sortBy_temp_d); break;
      case "cnt":
        sorted.sort(sortBy_sta).sort(sortBy_cnt); break;
      case "sta":
        sorted.sort(sortBy_cnt).sort(sortBy_sta); break;
      default:          sorted.sort(eval("sortBy_"+sel_sort));   break;
    }
  }


  // Output data
  var total_ever =	0;
  var total =	0;
  var total_month = 0;
  for (var a=0; a<sorted.length; a++) {
    if (sorted[a].all_date) {
      total_ever++;
      if (sorted[a].log[sel_yyyy]) {
        total++
      }
      out[n++] =	"<tr>\n";
      out[n++] =	"<td class='khz'><a name='"+	sorted[a].khz+"-"+sorted[a].call+"'></a>"+sorted[a].khz+"</td>\n";
      out[n++] =	"<td class='call'><a title='Display details for this beacon' href='javascript:void top.popup_details(\""+	sorted[a].khz+'-'+sorted[a].call+"\")'>"+sorted[a].call+"</a></td>\n";
      out[n++] =	"<td class='qth'>";
      out[n++] =	((sorted[a].lat + sorted[a].lon !="")?
				 ("<a href='javascript:top.popup_map(\""+sorted[a].lat+"\",\""+sorted[a].lon+"\")'"+msg_m+" title='Click to show a map of this location'>"+sorted[a].qth+"</a>"):
				 (((sorted[a].qth!="")?(sorted[a].qth):("&nbsp;"))));
      out[n++] =	"</td>\n";
      out[n++] =	"<td class='sta'>";
      out[n++] =	((sorted[a].sta)?
				 ("<a class='info' href='javascript:void 0' "+status_msg("State = "+sta_arr[sorted[a].cnt][sorted[a].sta]) + " title='"+sta_arr[sorted[a].cnt][sorted[a].sta]+"'>"+sorted[a].sta+"</a>"):
				 ("&nbsp;"));
      out[n++] =	"</td>\n";
      out[n++] =	"<td class='cnt'>";
      out[n++] =	((sorted[a].cnt)?
				 ("<a class='info' href='javascript:void 0' "+status_msg("Country = "+cnt_arr[sorted[a].cnt].name) + " title='"+cnt_arr[sorted[a].cnt].name+"'>"+sorted[a].cnt+"</a>"):
				 ("&nbsp;"));
      out[n++] =	"</td>\n";
      if (cookie['h_gsq']!='1') {
        out[n++] =	"<td class='gsq'>"+	((sorted[a].gsq)?(sorted[a].gsq):("&nbsp;"))+"</td>\n";
      }
      if (cookie['h_ident']!='1') {
        out[n++] =	"<td class='ident'>"+	sorted[a].ident+"</td>\n";
      }
      out[n++] =	"<td class='daid'>"+	((sorted[a].daid!="")?(sorted[a].daid):("&nbsp;"))+"</td>\n";
      out[n++] =	"<td class='cyc'>"+	((sorted[a].cyc!="")?(sorted[a].cyc):("&nbsp;"))+"</td>\n";
      out[n++] =	"<td class='lsb'>"+	((sorted[a].lsb!="")?(sorted[a].lsb):("&nbsp;"))+"</td>\n";
      out[n++] =	"<td class='usb'>"+	((sorted[a].usb!="")?(sorted[a].usb):("&nbsp;"))+"</td>\n";
      out[n++] =	"<td class='pwr'>"+	((sorted[a].pwr!="")?(sorted[a].pwr):("&nbsp;"))+"</td>\n";
      out[n++] =	"<td class='dir'>"+	((sorted[a].dir!=-1)?(sorted[a].dir):("&nbsp;"))+"</td>\n";
      out[n++] =	"<td class='dx'>"+	((sorted[a].dx!=-1)?(sorted[a].dx):("&nbsp;"))+"</td>\n";
      if (cookie['h_dxw']!='1') {
        out[n++] =	"<td class='dxw'>"+	((sorted[a].dxw!="")?(sorted[a].dxw):("&nbsp;"))+"</td>\n";
      }
      if (monthly) {
        for (var b=0; b<12; b++) {
          out[n++] =	"<td class='"+mm_arr[b]+"'>"+((sorted[a].log[sel_yyyy] && sorted[a].log[sel_yyyy].rx[mm_arr[b]])?(sorted[a].log[sel_yyyy].rx[mm_arr[b]]):("&nbsp;"))+"</td>\n";
        }
        if (sorted[a].log[sel_yyyy] && sorted[a].log[sel_yyyy].rx[sel_mm]) {
          total_month++;
        }
        if (sorted[a].log[sel_yyyy] && sorted[a].log[sel_yyyy][sel_mm]) {
          out[n++] =	"<td class='n_yymmdd'>"+((sorted[a].log[sel_yyyy][sel_mm].night_dd)?(parseFloat(sorted[a].log[sel_yyyy][sel_mm].night_dd)):("&nbsp;"))+"</td>\n";
          out[n++] =	"<td class='n_hhmm'>"+((sorted[a].log[sel_yyyy][sel_mm].night_hhmm)?(sorted[a].log[sel_yyyy][sel_mm].night_hhmm):("&nbsp;"))+"</td>\n";
          out[n++] =	"<td class='d_yymmdd'>"+((sorted[a].log[sel_yyyy][sel_mm].day_dd)?(parseFloat(sorted[a].log[sel_yyyy][sel_mm].day_dd)):("&nbsp;"))+"</td>\n";
          out[n++] =	"<td class='d_hhmm'>"+((sorted[a].log[sel_yyyy][sel_mm].day_hhmm)?(sorted[a].log[sel_yyyy][sel_mm].day_hhmm):("&nbsp;"))+"</td>\n";
        }
        else {
          out[n++] =	"<td class='n_yymmdd'>&nbsp;</td>\n";
          out[n++] =	"<td class='n_hhmm'>&nbsp;</td>\n";
          out[n++] =	"<td class='d_yymmdd'>&nbsp;</td>\n";
          out[n++] =	"<td class='d_hhmm'>&nbsp;</td>\n";
        }
      }
      if(cookie['h_lifelist']!='1') {
        out[n++] =	"<td class='all_log'>"+((sorted[a].all_date)?(sorted[a].all_date):("&nbsp;"))+"</td>\n";
        out[n++] =	"<td class='all_log'>"+((sorted[a].all_time)?(sorted[a].all_time):("&nbsp;"))+"</td>\n";
      }
      if (cookie['h_notes']!='1') {
        out[n++] =	"<td class='notes'>"+((sorted[a].notes)?("<b>"+sorted[a].notes+"</b> "):(""));
        if (monthly) {
          out[n++] =	((sorted[a].log && sorted[a].log[sel_yyyy] && sorted[a].log[sel_yyyy][sel_mm] &&
                           sorted[a].log[sel_yyyy][sel_mm].notes && sorted[a].log[sel_yyyy][sel_mm].notes.length)?
			 		 (sorted[a].log[sel_yyyy][sel_mm].notes.join(", ")):
					 ("&nbsp;"))
        }
        else {
          out[n++] =	((sorted[a].all_notes.length)?(sorted[a].all_notes):("&nbsp;"));
        }
        out[n++] =	"</td>\n";
      }
      out[n++] =	"</tr>\n\n";
    }
  }
  out[n++] =	"</table>\n";
  out[n++] =	"<b><a name='bottom'></a>"+total_ever+" stations received in total, with " + total+" stations"+(monthly ? " in "+sel_yyyy : "")+(monthly ? " and "+total_month+" stations in " + months[sel_mm-1] + " " + sel_yyyy : "")+".</b><br><br>\n";

  if (unregistered_stations.length) {
    var error_page =	"<html><head><title>NDB WebLog > Error > Unregistered Stations</title></head><body><h1>Unregistered Stations</h1>"+
    					"<p>The following "+ unregistered_stations.length + " stations were logged but do not appear in the stations.js file:</p>"+
    					"<ol><li>"+unregistered_stations.join("</li>\n<li>")+"</li></ol></body></html>";

    error_h1 =		window.open('','errorViewer', 'width=350,height=600,status=1,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
    error_h1.focus(); error_h1.document.write(error_page); error_h1.document.close();
  }
  if (unregistered_countries.length) {
    var error_page =	"<html><head><title>NDB WebLog > Error > Unregistered Countries</title></head><body><h1>Unregistered Countries</h1>"+
    					"<p>The following "+ unregistered_countries.length + " stations have country codes which do not appear in the countries.js file:</p>"+
    					"<ol><li>"+unregistered_countries.join("</li>\n<li>")+"</li></ol></body></html>";

    error_h2 =		window.open('','errorViewer', 'width=350,height=600,status=1,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
    error_h2.focus(); error_h2.document.write(error_page); error_h2.document.close();
  }

  out[n++] =		"<script language='javascript' type='text/javascript'>top.show_time()</script>"
  if (progress_hd) {
    progress_hd.close();
  }
  top.main.document.write(out.join(""));
  top.main.document.close();
  top.main.document.focus();
}



// ************************************
// * pad()                            *
// ************************************
function pad(txt,len) {
  return ((txt+"        ").substr(0,len));
}


// ************************************
// * popup_details()                  *
// ************************************
function popup_details(id) {
  out = new Array(); n=0
  var km =	get_range_bearing(qth_lat,qth_lon,station[id].lat,station[id].lon,'km');
  var miles =	get_range_bearing(qth_lat,qth_lon,station[id].lat,station[id].lon,'mi');
  if (cookie['mod_abs']=='1') {
    var lsb =	Math.round((station[id].khz - station[id].lsb)*1000);
    var usb =	Math.round((station[id].usb - station[id].khz)*1000);
  }
  else {
    var lsb =	station[id].lsb;
    var usb =	station[id].usb;
  }
  var map=	((station[id].lat + station[id].lon !="")?
			 ("<a href='javascript:void window.opener.popup_map(\""+station[id].lat+"\",\""+station[id].lon+"\")' title='Click to show a map of this location'>Location</a>"):
			 ("&nbsp;"));
  out[n++] =	"<html><title>NDB WebLog > Details > "+id+"</title>";
  out[n++] =	"<link TITLE='new' REL='stylesheet' HREF='ndbweblog.css' type='text/css'>";
  out[n++] =	"<script language='javascript' type='text/javascript'>\n";
  out[n++] =	"function version_check(){\n";
  out[n++] =	"  ver_h =	window.open('http://www.classaxe.com/dx/ndb/log/changelog/?current="+version+"','versionPage',\n"
  out[n++] =	"'width=280,height=220,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');\n";
  out[n++] =	"  ver_h.focus();\n";
  out[n++] =	"}\n</script>\n";
  out[n++] =   "</head>";
  out[n++] =   "<body bgcolor='#ffffff' onload=\"document.body.focus();\" onkeydown=\"window.opener.keydown('popup_details',event)\">";
  out[n++] =	"<h3>Details for "+id+"</h3>";
  out[n++] =	"<table border='"+((document.all)?("0"):("1"))+"' cellspacing='0' cellpadding='1' border='0' width='100%'>\n";
  out[n++] =	"<tr>\n";
  out[n++] =	"  <th rowspan='2' class='l_edge' width='100'>Technical<br>Details</th>\n";
  out[n++] =	"  <th>KHz</th>\n";
  out[n++] =	"  <th>Call</th>\n";
  out[n++] =	"  <th>Morse / DGPS ID</th>\n";
  out[n++] =	"  <th>DAID</th>\n";
  out[n++] =	"  <th>Cycle</th>\n";
  out[n++] =	"  <th>LSB</th>\n";
  out[n++] =	"  <th>USB</th>\n";
  out[n++] =	"  <th>Pwr</th>\n";
  out[n++] =	"</tr>\n";
  out[n++] =	"<tr>\n";
  out[n++] =	"  <td>"+station[id].khz+"</td>\n";
  out[n++] =	"  <td>"+station[id].call+"</td>\n";
  out[n++] =	"  <td nowrap>"+station[id].ident+"</td>\n";
  out[n++] =	"  <td class='c'>"+(station[id].daid ? station[id].daid : "&nbsp;") +"</td>\n";
  out[n++] =	"  <td class='c'>"+(station[id].cyc ? station[id].cyc : "&nbsp;") + "</td>\n";
  out[n++] =	"  <td>"+(lsb ? lsb : "&nbsp;")+"</td>\n";
  out[n++] =	"  <td>"+(usb ? usb : "&nbsp;")+"</td>\n";
  out[n++] =	"  <td>"+(station[id].pwr ? station[id].pwr : "&nbsp;") + "</td>\n";
  out[n++] =	"</tr>\n";
  if (station[id].notes.length) {
    out[n++] =	"<tr>\n";
    out[n++] =	"  <td colspan='9' class='l_edge'>"+station[id].notes+"</td>\n";
    out[n++] =	"</tr>\n";
  }
  out[n++] =	"</table><br>\n";
  out[n++] =	"<table border='"+((document.all)?("0"):("1"))+"' cellspacing='0' cellpadding='1' border='0' width='100%'>\n";
  out[n++] =	"<tr>\n";
  out[n++] =	"  <th rowspan='2' class='l_edge' width='100'>Location<br>Details</th>\n";
  out[n++] =	"  <th>"+map+"</th>\n";
  out[n++] =	"  <th>Lat</th>\n";
  out[n++] =	"  <th>Lon</th>\n";
  out[n++] =	"  <th>GSQ</th>\n";
  out[n++] =	"</tr>\n";
  out[n++] =	"<tr>\n";
  out[n++] =	"  <td>"+station[id].qth+(station[id].sta ? ", "+sta_arr[station[id].cnt][station[id].sta] : "")+", "+cnt_arr[station[id].cnt].name+"</td>\n";
  out[n++] =	"  <td nowrap>"+station[id].lat+"</td>\n";
  out[n++] =	"  <td nowrap>"+station[id].lon+"</td>\n";
  out[n++] =	"  <td>"+station[id].gsq+"</td>\n";
  out[n++] =	"</tr>\n";
  out[n++] =	"</table><br>\n";

  out[n++] =	"<table border='"+((document.all)?("0"):("1"))+"' cellspacing='0' cellpadding='1' border='0' width='100%'>\n";
  out[n++] =	"<tr>\n";
  out[n++] =	"  <th rowspan='2' class='l_edge' width='100'>DX<br>Details</th>\n";
  out[n++] =	"  <th>Deg</th>\n";
  out[n++] =	"  <th>KM</th>\n";
  out[n++] =	"  <th>Miles</th>\n";
  if (station[id].pwr) {
    out[n++] =	"  <th>KM DX/W</th>\n";
    out[n++] =	"  <th>Miles DX/W</th>\n";
  }
  out[n++] =	"</tr>\n";
  out[n++] =	"<tr>\n";
  out[n++] =	"  <td class='c'>"+station[id].dir+"</td>\n";
  out[n++] =	"  <td class='c'>"+km[1]+"</td>\n";
  out[n++] =	"  <td class='c'>"+miles[1]+"</td>\n";
  if (station[id].pwr) {
    out[n++] =	"  <td class='c'>"+Math.round(km[1]*100/station[id].pwr)/100+"</td>\n";
    out[n++] =	"  <td class='c'>"+Math.round(miles[1]*100/station[id].pwr)/100+"</td>\n";
  }
  out[n++] =	"</tr>\n";
  out[n++] =	"</table><br>\n";

  if (logbook[id]) {
    out[n++] =	"<h3>Reception Details for "+id+"</h3>";
    out[n++] =	"<table border='"+((document.all)?("0"):("1"))+"' cellspacing='0' cellpadding='1' border='0'>\n";
    out[n++] =	"<tr>\n";
    out[n++] =	"  <th class='l_edge'>"+show_date_heading()+"</th>\n";
    out[n++] =	"  <th>UTC</th>\n";
    out[n++] =	"  <th>Day</th>\n";
    out[n++] =	"  <th>Notes</th>\n";
    out[n++] =	"</tr>"
    for (var b in logbook[id]['entry']) {
      var log_yyyymmdd =  logbook[id]['entry'][b]['yyyymmddhhmm'].substr(0,8);
      var log_HH =    logbook[id]['entry'][b]['yyyymmddhhmm'].substr(8,2);
      var log_MM =    logbook[id]['entry'][b]['yyyymmddhhmm'].substr(10,2);
      var log_notes = logbook[id]['entry'][b]['notes']
      var log_isDay = (utc_daylight_array[0] == log_HH || utc_daylight_array[1] == log_HH || utc_daylight_array[2] == log_HH || utc_daylight_array[3] == log_HH);
      out[n++] =	"<tr>\n";
      out[n++] =	"  <td class='l_edge'>"+format_date(log_yyyymmdd)+"</td>\n";
      out[n++] =	"  <td>"+log_HH+":"+log_MM+"</td>\n";
      out[n++] =	"  <td class='c'>"+(log_isDay ? "Y" : "&nbsp;")+ "</td>\n";
      out[n++] =	"  <td>"+(log_notes!="" ? log_notes : "&nbsp;") +"</td>\n";
      out[n++] =	"</tr>"
    }
    out[n++] =	"</table>";
  }
  out[n++] =	"</body></html>";
  details_h = window.open('','', 'width=600,height=500,status=1,resizable=1,menubar=0,location=0,toolbar=1,scrollbars=1');
  details_h.focus(); details_h.document.write(out.join("")); details_h.document.close();
}



// ************************************
// * popup_help()                     *
// ************************************
function popup_help() {
  out = new Array(); n=0
  help_h = window.open('help.html','helpViewer', 'width=800,height=400,status=1,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
  help_h.focus();
}

// ************************************
// * popup_home()                     *
// ************************************
function popup_home() {
  out = new Array(); n=0
  home_h = window.open(qth_home,'homePage', 'width=800,height=400,status=1,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
  home_h.focus();
}

function version_check(){
  ver_h =	window.open('http://www.classaxe.com/dx/ndb/log/changelog/?current='+version,'versionPage','width=280,height=220,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
  ver_h.focus();
}

// ************************************
// * popup_map()                      *
// ************************************
function popup_map(lat,lon) {
  var map_hd =	window.open(	"http://www.mapquest.com/maps/map.adp?latlongtype=decimal&latitude="+lat+"&longitude="+lon+
						"&size=big&zoom="+cookie['map_zoom'],"map",
						"width=800,height=600,resizable=1,scrollbars=1,status=0,menubar=0,location=1,toolbar=0");
  map_hd.focus();
}



// ************************************
// * popup_prefs()                    *
// ************************************
function popup_prefs() {
  var out =	new Array();
  var n =	0;
  out[n++] =	"<html><head><title>NDB WebLog > Preferences</title>\n";
  out[n++] =	"<link TITLE=\"new\" REL=\"stylesheet\" HREF=\"ndbweblog.css\" type=\"text/css\">\n";
  out[n++] =	"<script language='JavaScript' type='text/javascript'>\n"
  out[n++] =	"function set_cookies(form) {\n";
  out[n++] =	"  var format =	form.format.options[form.format.selectedIndex].value;\n";
  out[n++] =	"  var h_dxw =		form.h_dxw.options[form.h_dxw.selectedIndex].value;\n";
  out[n++] =	"  var h_gsq =		form.h_gsq.options[form.h_gsq.selectedIndex].value;\n";
  out[n++] =	"  var h_ident =	form.h_ident.options[form.h_ident.selectedIndex].value;\n";
  out[n++] =	"  var h_lifelist =	form.h_lifelist.options[form.h_lifelist.selectedIndex].value;\n";
  out[n++] =	"  var h_notes =	form.h_notes.options[form.h_notes.selectedIndex].value;\n";
  out[n++] =	"  var map_zoom =	form.map_zoom.options[form.map_zoom.selectedIndex].value;\n";
  out[n++] =	"  var units =		form.units.options[form.units.selectedIndex].value;\n";
  out[n++] =	"  var mod_abs =	form.mod_abs.options[form.mod_abs.selectedIndex].value;\n";


  // Add new options in chronological order for forward compatability
  out[n++] =	"  var expires =	new Date();\n";
  out[n++] =	"  expires.setFullYear(expires.getFullYear()+1);\n";
  out[n++] =	"  expires =		expires.toGMTString();\n";
  out[n++] =	"  var D =		\"|\"\n";
  out[n++] =	"  var cookies = \"list_options=\"+format+D+h_dxw+D+h_gsq+D+h_ident+D+h_lifelist+D+"
  out[n++] =	"                h_notes+D+map_zoom+D+units+D+mod_abs+\";expires=\"+expires;\n";

  out[n++] =	"  document.cookie = cookies;\n";
  out[n++] =	"}\n";
  out[n++] =	"</script>\n";
  out[n++] =	"</head>\n";
  out[n++] =	"<body onload=\"document.body.focus();\" onkeydown=\"window.opener.keydown('popup_prefs',event)\">";

  out[n++] =	"<form name='form'>";
  out[n++] =	"<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n";
  out[n++] =	"  <tr>\n";
  out[n++] =	"    <td class='plain'><table cellpadding='1' cellspacing='0' border='1' class='r' width='100%'>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <th colspan='2'>Preferences > Options</th>";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td>Distances&nbsp;</td>\n";
  out[n++] =	"        <td><select name='units'>\n";
  out[n++] =	"    <option value='km'"+((cookie['units']=='km')?(" selected"):(""))+">KM</option>\n"
  out[n++] =	"    <option value='nm'"+((cookie['units']=='nm')?(" selected"):(""))+">Naut.Mi.</option>\n"
  out[n++] =	"    <option value='mi'"+((cookie['units']=='mi')?(" selected"):(""))+">Miles</option>\n"
  out[n++] =	"    </select>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td>Mod Values&nbsp;</td>\n";
  out[n++] =	"        <td><select name='mod_abs'>\n";
  out[n++] =	"    <option value='0'"+((cookie['mod_abs']=='0')?(" selected"):(""))+">Relative</option>\n"
  out[n++] =	"    <option value='1'"+((cookie['mod_abs']=='1')?(" selected"):(""))+">Absolute</option>\n"
  out[n++] =	"    </select>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td>Dates&nbsp;</td>\n";
  out[n++] =	"        <td><select name='format'>\n";
  out[n++] =	"    <option value='ddmmyyyy'"+((cookie['format']=='ddmmyyyy')?(" selected"):(""))+">DD/MM/YYYY</option>\n"
  out[n++] =	"    <option value='dd.mm.yyyy'"+((cookie['format']=='dd.mm.yyyy')?(" selected"):(""))+">DD.MM.YYYY</option>\n"
  out[n++] =	"    <option value='mmddyyyy'"+((cookie['format']=='mmddyyyy')?(" selected"):(""))+">MM/DD/YYYY</option>\n"
  out[n++] =	"    <option value='yyyy-mm-dd'"+((cookie['format']=='yyyy-mm-dd')?(" selected"):(""))+">YYYY-MM-DD</option>\n"
  out[n++] =	"    <option value='yyyymmdd'"+((cookie['format']=='yyyymmdd')?(" selected"):(""))+">YYYYMMDD</option>\n"
  out[n++] =	"    </select>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td>Map Zoom&nbsp;</td>\n";
  out[n++] =	"        <td><select name='map_zoom'>\n";
  for (var i=0; i<10; i++) {
    out[n++] =	"    <option value='"+i+"'"+((cookie['map_zoom']==i)?(" selected"):(""))+">"+(1+i)+"</option>\n"
  }
  out[n++] =	"    </select>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"    </table></td>\n";
  out[n++] =	"  </tr>\n";
  out[n++] =	"  <tr>\n";
  out[n++] =	"    <td class='plain'>&nbsp;</td>\n";
  out[n++] =	"  </tr>\n";
  out[n++] =	"  <tr>\n";
  out[n++] =	"    <td class='plain'><table cellpadding='1' cellspacing='0' border='1' class='r' width='100%'>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <th colspan='2'>Preferences > Columns</th>";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td>Morse / DGPS ID&nbsp;</td>";
  out[n++] =	"        <td><select name='h_ident'>";
  out[n++] =	"    <option value='0'"+((cookie['h_ident']=='1')?(""):(" selected"))+">Show</option>\n"
  out[n++] =	"    <option value='1'"+((cookie['h_ident']=='1')?(" selected"):(""))+">Hide</option>\n"
  out[n++] =	"    </select>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td>GSQ&nbsp;</td>";
  out[n++] =	"        <td><select name='h_gsq'>";
  out[n++] =	"    <option value='0'"+((cookie['h_gsq']=='1')?(""):(" selected"))+">Show</option>\n"
  out[n++] =	"    <option value='1'"+((cookie['h_gsq']=='1')?(" selected"):(""))+">Hide</option>\n"
  out[n++] =	"    </select>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td>DX per Watt&nbsp;</td>";
  out[n++] =	"        <td><select name='h_dxw'>";
  out[n++] =	"    <option value='0'"+((cookie['h_dxw']=='1')?(""):(" selected"))+">Show</option>\n"
  out[n++] =	"    <option value='1'"+((cookie['h_dxw']=='1')?(" selected"):(""))+">Hide</option>\n"
  out[n++] =	"    </select>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td>First Received&nbsp;</td>";
  out[n++] =	"        <td><select name='h_lifelist'>";
  out[n++] =	"    <option value='0'"+((cookie['h_lifelist']=='1')?(""):(" selected"))+">Show</option>\n"
  out[n++] =	"    <option value='1'"+((cookie['h_lifelist']=='1')?(" selected"):(""))+">Hide</option>\n"
  out[n++] =	"    </select>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td>Notes&nbsp;</td>";
  out[n++] =	"        <td><select name='h_notes'>";
  out[n++] =	"    <option value='0'"+((cookie['h_notes']=='1')?(""):(" selected"))+">Show</option>\n"
  out[n++] =	"    <option value='1'"+((cookie['h_notes']=='1')?(" selected"):(""))+">Hide</option>\n"
  out[n++] =	"    </select>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <th colspan='2'><input type='button' value='Submit' onclick='set_cookies(document.form);window.opener.location.reload(1);window.close()'>";
  out[n++] =	"     <input type='button' value='Cancel' onclick='window.close()'></th>";
  out[n++] =	"      </tr>\n";
  out[n++] =	"    </table></td>\n";
  out[n++] =	"  </tr>\n";
  out[n++] =	"</table>\n";
  out[n++] =	"</form>"
  out[n++] =	"</body></html>\n"
  pref_h =	window.open('','prefsPage', 'width=250,height=360,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=0');
  pref_h.focus(); pref_h.document.write(out.join("")); pref_h.document.close();
}


// ************************************
// * popup_search()                   *
// ************************************
function popup_search() {
  var out =	new Array();
  var n =	0;
  out[n++] =	"<html><head><title>NDB WebLog > Search</title>\n";
  out[n++] =	"<link TITLE=\"new\" REL=\"stylesheet\" HREF=\"ndbweblog.css\" type=\"text/css\">\n";
  out[n++] =	"</head>\n";
  out[n++] =	"<body onload='document.form.term.focus()' onkeydown=\"window.opener.keydown('popup_search',event)\"><form name='form' onsubmit='window.opener.search(document.form);return false'>";
  out[n++] =	"<table cellpadding='1' cellspacing='0' border='1' class='r' align='center'>\n";
  out[n++] =	"  <tr>\n";
  out[n++] =	"    <th colspan='2'>Search</th>";
  out[n++] =	"  </tr>\n";
  out[n++] =	"  <tr>\n";
  out[n++] =	"    <td nowrap>KHz / Call&nbsp;</td>";
  out[n++] =	"    <td><input name='term'>\n";
  out[n++] =	"  </tr>\n";
  out[n++] =	"  <tr>\n";
  out[n++] =	"    <th colspan='2'><input type='button' value='Submit' onclick='window.opener.search(document.form);'>";
  out[n++] =	"     <input type='button' value='Cancel' onclick='window.close()'></th>";
  out[n++] =	"  </tr>\n";
  out[n++] =	"</table>";
  out[n++] =	"</form>";
  out[n++] =	"</body></html>\n"
  search_h =	window.open('','searchPage', 'width=280,height=100,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=0');
  search_h.focus(); search_h.document.write(out.join("")); search_h.document.close();
}


// ************************************
// * search()                         *
// ************************************
function search(form) {
  var term =		form.term.value.toUpperCase();
  var result = new Array()
  if (term) {
    for (var i in station) {
      if (station[i].khz == term) {
        result[result.length] = i
      }
    }
    for (var i in station) {
      if (station[i].call == term) {
        result[result.length] = i
      }
    }
    if (!result.length) {
      alert("No stations matched entered criteria")
      search_h.focus()
    }
    else {
      var out =	new Array();
      var n =		0;
      out[n++] =	"<!doctype html public '-//W3C//DTD HTML 4.01 Transitional//EN'>\n";
      out[n++] =	"<html><head><title>NDB WebLog > Search > Results</title>\n";
      out[n++] =	"<meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=windows-1252\">\n";
      out[n++] =	"<link TITLE=\"new\" REL=\"stylesheet\" HREF=\"ndbweblog.css\" type=\"text/css\">\n";
      out[n++] =	"</head><body onload='document.body.focus()' onkeydown=\"window.opener.keydown('search',event)\"><h1>Results</h1>\n"
      out[n++] =	"<table border='1' cellspacing='0' cellpadding='0' border='0'>\n"
      out[n++] =	"  <tr>\n"
      out[n++] =	"    <th>KHz</th><th>Call</th><th>Location</th><th>DX("+get_units()+")</th><th>LSB</th><th>USB</th><th colspan='2'>Last RX</th><th>Logs</th>\n"
      out[n++] =	"  </tr>\n"
      for (var k in result) {
        var id =	result[k];
        out[n++] =	"  <tr title='Notes: " + station[id].notes + "'>\n"
        out[n++] =	"    <td>" + station[id].khz + "</td>\n"
        out[n++] =	"    <td><a href='javascript:void window.opener.popup_details(\"" + id + "\");'>" + station[id].call + "</a></td>\n" 
        out[n++] =	"    <td>"+station[id].qth+(station[id].sta ? ", "+sta_arr[station[id].cnt][station[id].sta] : "")+", "+cnt_arr[station[id].cnt].name+"</td>\n";
        out[n++] =	"    <td>" + (station[id].dx!=-1 ? station[id].dx : "&nbsp;") + "</td>\n"
        out[n++] =	"    <td>" + (station[id].lsb ? station[id].lsb : "&nbsp;") + "</td>\n"
        out[n++] =	"    <td>" + (station[id].usb ? station[id].usb : "&nbsp;") + "</td>\n"

        if (logbook[id]) {
          var log_yyyymmdd =  logbook[id]['entry'][logbook[id]['entry'].length-1]['yyyymmddhhmm'].substr(0,8);
          var log_HH =    logbook[id]['entry'][logbook[id]['entry'].length-1]['yyyymmddhhmm'].substr(8,2);
          var log_MM =    logbook[id]['entry'][logbook[id]['entry'].length-1]['yyyymmddhhmm'].substr(10,2);

          out[n++] =	"  <td>"+format_date(log_yyyymmdd)+"</td>\n";
          out[n++] =	"  <td>"+log_HH+":"+log_MM+"</td>\n";
          out[n++] =	"  <td>"+logbook[id]['entry'].length+"</td>\n";
        }
        else {
          out[n++] =	"    <td>&nbsp;</th>\n"
          out[n++] =	"    <td>&nbsp;</th>\n"
          out[n++] =	"    <td>&nbsp;</th>\n"
        }
        out[n++] =	"  </tr>\n"
      }
      out[n++] =	"</table></body></html>"
      result_h =	window.open('','resultSelector', 'width=600,height=300,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=1');
      result_h.focus();
      result_h.document.write(out.join(""));
      result_h.document.close();
    }
  }
}



// ************************************
// * popup_stats()                    *
// ************************************
function popup_stats() {
  progress();
  var units_long =	get_units();
  var dx_step =	(cookie['units']=="mi" ? 100 : 200)

  var max_dx =		20000;	// Maximum possible DX in KM (smallest unit)
  var rexp_country =	/([A-Z\?]*)\_([A-Z\?]*)/
  var yyyy =	0

  var out =	new Array();
  var n =	0;
  out[n++] =	"<!doctype html public '-//W3C//DTD HTML 4.01 Transitional//EN'>\n"
  out[n++] =	"<html><head>\n";
  out[n++] =	"<title>NDB WebLog for "+qth_name+" > Statistics</title>\n";
  out[n++] =	"<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=windows-1252'>"
  out[n++] =	"<link TITLE=\"new\" REL=\"stylesheet\" HREF=\"ndbweblog.css\" type=\"text/css\">\n";
  out[n++] =	"</head>\n";
  out[n++] =	"<body onload='document.body.focus()' onkeydown=\"window.opener.keydown('popup_stats',event)\"><h2 align='center'><a name='top'></a><u>Statistics and Awards Qualifications Page</u></h2>\n";

  if (monthly) {
    // +++++++++++++++++++
    // + Monthly Report  +
    // +++++++++++++++++++
    var max_count =	new Array();		// Used to calculate contrast settings for colour graduated boxes
    max_count['br'] =	0;
    max_count['dx'] =	0; 
    max_count['cnt'] =	0;
    max_count['new'] =	0;
    max_count['rgn'] =	0;
    max_count['n60'] =	0;

    // +++++++++++++++++++
    // + links:          +
    // +++++++++++++++++++
    var link_top =		" <span class='links'><small>[ <a href='#top' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Top</a> ]</small></span>"
    // Repeat for each available year:
    var years = new Array();
    for (yyyy in stats.year) {
      yyyy = "" + yyyy
      // Quick links:
      var quicklinks =		new Array();
      var qk_i =		0;
      quicklinks[qk_i++] =	"<a href='#"+yyyy+"br' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Beacons</a>";
      if (stats.year[yyyy].new_beacon) {
        quicklinks[qk_i++] =	"<a href='#"+yyyy+"new' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>New Beacons</a>";
      }
      quicklinks[qk_i++] =	"<a href='#"+yyyy+"dx_d' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Day-time DX</a>";
      quicklinks[qk_i++] =	"<a href='#"+yyyy+"dx_n' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Night-time DX</a>";
      quicklinks[qk_i++] =	"<a href='#"+yyyy+"dx_w' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>DX per Watt</a>";
      quicklinks[qk_i++] =	"<a href='#"+yyyy+"cr' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Countries</a>";
      quicklinks[qk_i++] =	"<a href='#"+yyyy+"rr' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>Regions</a>";
      if (stats.year[yyyy].n60) {
        quicklinks[qk_i++] =	"<a href='#"+yyyy+"n60' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>North of 60</a>";
      }
      out[n++] =		"<p align='center' class='links'><small><a href='#year"+yyyy+"' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'><b>Reports for "+yyyy+"</b></a><br>[ "+quicklinks.join(" | ")+" ]</small></p>\n";
    }
    out[n++] =		"<p align='center' class='links'><small>[ <a href='#awards' onclick='return window.opener.checkScrollNecessary(\"stat_h\", this)'>About NDB Awards...</a> ]</small></p>\n";

    out[n++] =	"<hr width='75%' align='center'>\n\n"

    // +++++++++++++++++++
    // + clear old stats:+
    // +++++++++++++++++++
    for (yyyy in stats.year) {
      stats.year[yyyy].rx_x =		0;	// Clear out old stats
      stats.year[yyyy].rx_d =		0;
      stats.year[yyyy].rx_n =		0;
      stats.year[yyyy].dx_d =		new Array();
      stats.year[yyyy].dx_n =		new Array();
      stats.year[yyyy].dx_x =		new Array();

      for (var dx=0; dx<(max_dx/dx_step); dx++) {
        stats.year[yyyy].dx_d[dx] =	0;
        stats.year[yyyy].dx_n[dx] =	0;
        stats.year[yyyy].dx_x[dx] =	0;
      }

      var count =			0;
      var dx_max_day =		0;
      var dx_max_night =	0;
      // +++++++++++++++++++
      // + Count for year: +
      // +++++++++++++++++++
      for (c in station) {
        for (var b=0; b<12; b++) {
          var mm = mm_arr[b];
          if (stats.year[yyyy][mm]) {
            stats.year[yyyy][mm].rx_n = 0;		// Clear the monthly stat counts
            stats.year[yyyy][mm].rx_d = 0;		// Clear the monthly stat counts
            stats.year[yyyy][mm].rx_x = 0;		// Clear the monthly stat counts
            for (dx=0; dx<(max_dx/dx_step); dx++) {
              stats.year[yyyy][mm].dx_d[dx] =	0;
              stats.year[yyyy][mm].dx_n[dx] =	0;
              stats.year[yyyy][mm].dx_x[dx] =	0;
            }
          }
        }

        if (station[c].log[yyyy]) {
          count++;					// Now count how many stations were logged this year
          var dx_range = Math.floor(station[c].dx/dx_step)
          if (station[c].log[yyyy].rx_x) {
            if (station[c].dx > dx_max_day) {
              dx_max_day =	station[c].dx;
            }
            if (station[c].dx > dx_max_night) {
              dx_max_night =	station[c].dx;
            }
            stats.year[yyyy].dx_x[dx_range]++;
            stats.year[yyyy].rx_x++;
          }
          else if (station[c].log[yyyy].rx_n) {
            if (station[c].dx > dx_max_night) {
              dx_max_night =	station[c].dx;
            }
            stats.year[yyyy].dx_n[dx_range]++;
            stats.year[yyyy].rx_n++;
          }
          else if (station[c].log[yyyy].rx_d) {
            if (station[c].dx > dx_max_day) {
              dx_max_day =	station[c].dx;
            }
            stats.year[yyyy].dx_d[dx_range]++;
            stats.year[yyyy].rx_d++;
          }
        }
      }

      // +++++++++++++++++++
      // + Count for month:+
      // +++++++++++++++++++

      for (var b=0; b<12; b++) {
        var mm = mm_arr[b];
        for (c in station) {
          if (station[c].log[yyyy] && station[c].log[yyyy].rx[mm]) {
            var dx_range = Math.floor(station[c].dx/dx_step)
            switch(station[c].log[yyyy].rx[mm]) {
              case "N": stats.year[yyyy][mm].rx_n++; stats.year[yyyy][mm].dx_n[dx_range]++; break;
              case "D": stats.year[yyyy][mm].rx_d++; stats.year[yyyy][mm].dx_d[dx_range]++; break;
              case "X": stats.year[yyyy][mm].rx_x++; stats.year[yyyy][mm].dx_x[dx_range]++; break;
            }

            // has max_count['br'] been exceeded?
            if (stats.year[yyyy][mm].rx_n>max_count['br']) {
              max_count['br'] = stats.year[yyyy][mm].rx_n;
            }
            if (stats.year[yyyy][mm].rx_d>max_count['br']) {
              max_count['br'] = stats.year[yyyy][mm].rx_d;
            }
            if (stats.year[yyyy][mm].rx_x>max_count['br']) {
              max_count['br'] = stats.year[yyyy][mm].rx_x;
            }

            // has max_count['dx'] been exceeded?
            if (stats.year[yyyy][mm].dx_d[dx_range]+stats.year[yyyy][mm].dx_x[dx_range]>max_count['dx']) {
              max_count['dx'] = stats.year[yyyy][mm].dx_d[dx_range]+stats.year[yyyy][mm].dx_x[dx_range];
            }
            if (stats.year[yyyy][mm].dx_n[dx_range]+stats.year[yyyy][mm].dx_x[dx_range]>max_count['dx']) {
              max_count['dx'] = stats.year[yyyy][mm].dx_n[dx_range]+stats.year[yyyy][mm].dx_x[dx_range];
            }
          }
        }
      }

      out[n++] =	"<h2 align='center'><u><a name='year"+yyyy+"'></a>Reports for "+yyyy+"</u></h2>\n";


      // +++++++++++++++++++
      // + Beacons Report: +
      // +++++++++++++++++++
      // Day + Night beacons report:
      out[n++] =	"<big><a name='"+yyyy+"br'></a>"+yyyy+" All Beacons Report (inc UNIDs)"+link_top+"</big><br>\n";
      out[n++] =	"<small>Daytime: "+lead(utc_daylight)+":00-"+lead((utc_daylight+3)%24)+":59, ";
      out[n++] =	"Night: "+lead((utc_daylight+4)%24)+":00-"+lead((utc_daylight-1)%24)+":59</small>\n\n";
      out[n++] =	"<table cellpadding='1' cellspacing='0' border='1'>\n";
      // +++++++++++++++++++
      // + label columns:  +
      // +++++++++++++++++++
      out[n++] =	"  <tr>\n";
      out[n++] =	"    <th width='120'>&nbsp;</th>\n";
      for (var b=0; b<12; b++) {
        out[n++] =	"    <th width='30'>"+months[b].substr(0,3)+"</th>\n";
      }
      out[n++] =	"    <th>"+yyyy+"</th>\n";

      out[n++] =	"  </tr>\n";

      // +++++++++++++++++++
      // + Day:            +
      // +++++++++++++++++++
      out[n++] =	"  <tr>\n"
      out[n++] =	"    <th class='l' nowrap>Day only</th>\n";
      for (var b=0; b<12; b++) {
        var mm = mm_arr[b];
        var val =	(stats.year[yyyy][mm] ? stats.year[yyyy][mm].rx_d : 0)
        out[n++] =	"    <td bgcolor='"+get_graph_color(val,max_count['br'])+"' class='r'>"+((val)?(val):("&nbsp;"))+"</td>\n";
      }
      out[n++] =	"    <th class='r'>"+stats.year[yyyy].rx_d+"</th>\n";
      out[n++] =	"  </tr>\n"

      // +++++++++++++++++++
      // + Day / Night:    +
      // +++++++++++++++++++
      out[n++] =	"  <tr>\n"
      out[n++] =	"    <th class='l' nowrap>Day and Night</th>\n"
      for (var b=0; b<12; b++) {
        var mm = mm_arr[b];
        var val =		(stats.year[yyyy][mm] ? stats.year[yyyy][mm].rx_x : 0)
        out[n++] =	"    <td bgcolor='"+get_graph_color(val,max_count['br'])+"' class='r'>"+((val)?(val):("&nbsp;"))+"</td>\n";
      }
      out[n++] =	"    <th class='r'>"+stats.year[yyyy].rx_x+"</th>\n";
      out[n++] =	"  </tr>\n"

      // +++++++++++++++++++
      // + Night:          +
      // +++++++++++++++++++
      out[n++] =	"  <tr>\n"
      out[n++] =	"    <th class='l' nowrap>Night only</th>\n"
      for (var b=0; b<12; b++) {
        var mm = mm_arr[b];
        var val =	(stats.year[yyyy][mm] ? stats.year[yyyy][mm].rx_n : 0)
        out[n++] =	"    <td bgcolor='"+get_graph_color(val,max_count['br'])+"' class='r'>"+((val)?(val):("&nbsp;"))+"</td>\n";
      }
      out[n++] =	"    <th class='r'>"+stats.year[yyyy].rx_n+"</th>\n";
      out[n++] =	"  </tr>\n"

      // +++++++++++++++++++
      // + Total results:  +
      // +++++++++++++++++++
      out[n++] =	"  <tr>\n"
      out[n++] =	"    <th class='l'>Total</th>\n"
      for (var b=0; b<12; b++) {
        var mm = mm_arr[b];
        out[n++] =	"    <th class='r'>"
        out[n++] =	((stats.year[yyyy][mm])?
			 (stats.year[yyyy][mm].rx_d+stats.year[yyyy][mm].rx_x+stats.year[yyyy][mm].rx_n):
			 ("&nbsp;"))+"</th>\n";
      }
      out[n++] =	"    <th class='r'>"+count+"</th>\n"
      out[n++] =	"  </tr>\n"
      out[n++] =	"</table>\n<p> </p>\n\n"

      // +++++++++++++++++++++++
      // + New Beacons Report: +
      // +++++++++++++++++++++++
      if (stats.year[yyyy].new_beacon) {
        var new_beacons =	new Array();

      // +++++++++++++++++++
      // + Count for month:+
      // +++++++++++++++++++
        for (var c=0; c<12; c++) {
          var mm =	mm_arr[c];
          var val =	((stats.year[yyyy][mm] && stats.year[yyyy][mm].new_beacon)?(stats.year[yyyy][mm].new_beacon):(0))
          new_beacons[c] =	val
          if (val>max_count['new']) {
            max_count['new'] = val;
          }
        }

        // +++++++++++++++++++
        // + Show Results:   +
        // +++++++++++++++++++
        var total_ever =	0;
        for (var c in station) {
          if (station[c].all_date) {
            total_ever++;
          }
        }
        out[n++] =	"<big><a name='"+yyyy+"new'></a>"+yyyy+" New Beacons Report (no UNIDs)"+link_top+"</big><br>\n";
        out[n++] =	"<small>Total for all time: " + total_ever + "</small>\n"
        out[n++] =	"<table cellpadding='1' cellspacing='0' border='1' class='r'>\n"
        out[n++] =	"  <tr>\n"
        out[n++] = 	"    <th width='120'>&nbsp;</th>\n"
        for (var b=0; b<12; b++) {
          out[n++] ="    <th width='30'>"+months[b].substr(0,3)+"</th>\n";
        }
        out[n++] =	"    <th>"+yyyy+"</th>\n";
        out[n++] =	"  </tr>\n"    
        out[n++] =	"  <tr>\n"
        out[n++] =	"    <th class='l'>New for " + yyyy + "</th>\n";
        for (var c=0; c<12; c++) {
          var mm = mm_arr[c];
          out[n++] ="    <td bgcolor='"+get_graph_color(new_beacons[c],max_count['new'])+"' class='r'>"+((new_beacons[c])?(new_beacons[c]):("&nbsp;"))+"</td>\n";
        }
        out[n++] =	"    <th class='r'>"+stats.year[yyyy].new_beacon+"</th>\n";
        out[n++] =	"  </tr>\n";
        out[n++] =	"</table>\n<p> </p>\n"
      }

      // +++++++++++++++++++++++++++++++++
      // + Daytime DX Beacons Report:    +
      // +++++++++++++++++++++++++++++++++
      out[n++] =	"<big><a name='"+yyyy+"dx_d'></a>"+yyyy+" Day-time Distances Report ("+units_long+") "+link_top+"</big><br>\n"
      out[n++] =	"<small>Daytime: "+lead(utc_daylight)+":00-"+lead((utc_daylight+3)%24)+":59</small>"
      out[n++] =	"<table cellpadding='1' cellspacing='0' border='1' class='r'>"
      out[n++] =	"  <tr>\n"
      out[n++] = "<th width='120' class='r'>"+units_long+"</th>\n"
      for (var b=0; b<12; b++) {
        out[n++] =	"<th width='30'>"+months[b].substr(0,3)+"</th>\n";
      }
      out[n++] =	"<th>"+yyyy+"</th>";
      out[n++] =	"  </tr>\n"
      for (var dx=0; dx<(dx_max_day/dx_step); dx++) {
      out[n++] =	"  <tr>\n"
      out[n++] =	"    <th class='r' nowrap>"+(dx*dx_step)+" - "+((dx_step*(dx+1))-1)+"</th>\n";
        for (var b=0; b<12; b++) {
          var mm = mm_arr[b]
          var val = 0;
          if (stats.year[yyyy][mm]) {
            val =	(stats.year[yyyy][mm].dx_d[dx] ? stats.year[yyyy][mm].dx_d[dx] : 0) +
				(stats.year[yyyy][mm].dx_x[dx] ? stats.year[yyyy][mm].dx_x[dx] : 0);
          }
          out[n++] ="    <td bgcolor='"+get_graph_color(val,max_count['dx'])+"' class='r'>"+((val)?(val):("&nbsp;"))+"</td>\n";
        }
        dx_d =		(stats.year[yyyy].dx_d[dx] ? stats.year[yyyy].dx_d[dx] : 0) +
				(stats.year[yyyy].dx_x[dx] ? stats.year[yyyy].dx_x[dx] : 0);
        out[n++] =	"<th class='r'>"+((dx_d)?(dx_d):("&nbsp;"))+"</th>\n";
        out[n++] =	"  </tr>\n"
      }
      out[n++] =	"  <tr>\n"
      out[n++] =	"    <th class='r'>Max "+units_long+"</th>\n"
      for (var b=0; b<12; b++) {
        var mm = mm_arr[b]
        out[n++] =	"    <th class='r'>"+((stats.year[yyyy] && stats.year[yyyy][mm] && stats.year[yyyy][mm].max_day)?(stats.year[yyyy][mm].max_day):("&nbsp;"))+"</th>\n";
      }
      out[n++] =	"    <th>"+((stats.year[yyyy] && stats.year[yyyy].max_day)?(stats.year[yyyy].max_day):("&nbsp;"))+"</th>\n"
      out[n++] =	"  </tr>\n"
      out[n++] =	"</table>\n<p> </p>\n\n"

      // +++++++++++++++++++++++++++++++++
      // + Nighttime DX Beacons Report:  +
      // +++++++++++++++++++++++++++++++++
      out[n++] =	"<big><a name='"+yyyy+"dx_n'></a>"+yyyy+" Night-time Distances Report ("+units_long+") "+link_top+"</big><br>"
      out[n++] =	"<small>Night: "+lead((utc_daylight+4)%24)+":00-"+lead((utc_daylight-1)%24)+":59</small>"
      out[n++] =	"<table cellpadding='1' cellspacing='0' border='1' class='r'>"
      out[n++] =	"  <tr>\n"
      out[n++] = "<th width='120' class='r'>"+units_long+"</th>"
      for (var b=0; b<12; b++) {
        out[n++] =	"<th width='30'>"+months[b].substr(0,3)+"</th>";
      }
      out[n++] =	"<th>"+yyyy+"</th>";
      out[n++] =	"  </tr>\n"
      for (var dx=0; dx<(dx_max_night/dx_step); dx++) {
      out[n++] =	"  <tr>\n"
      out[n++] =	"  <th class='r' nowrap>"+(dx*dx_step)+" - "+((dx_step*(dx+1))-1)+"</th>";
        for (var b=0; b<12; b++) {
          var mm = mm_arr[b]
          var val = 0;
          if (stats.year[yyyy][mm]) {
            val =	(stats.year[yyyy][mm].dx_n[dx] ? stats.year[yyyy][mm].dx_n[dx] : 0) +
				(stats.year[yyyy][mm].dx_x[dx] ? stats.year[yyyy][mm].dx_x[dx] : 0);
          }
          out[n++] =	"<td bgcolor='"+get_graph_color(val, max_count['dx'])+"' class='r'>"+((val)?(val):("&nbsp;"))+"</td>";
        }
        dx_n =		(stats.year[yyyy].dx_n[dx] ? stats.year[yyyy].dx_n[dx] : 0) +
				(stats.year[yyyy].dx_x[dx] ? stats.year[yyyy].dx_x[dx] : 0);
        out[n++] =	"<th class='r'>"+((dx_n)?(dx_n):("&nbsp;"))+"</td>\n";
        out[n++] =	"  </tr>\n"
      }
      out[n++] =	"  </tr>\n"

      out[n++] =	"  <tr><th class='r'>Max "+units_long+"</th>\n"
      for (var b=0; b<12; b++) {
        var mm = mm_arr[b]
        out[n++] =	"<th>"+((stats.year[yyyy] && stats.year[yyyy][mm] && stats.year[yyyy][mm].max_night)?(stats.year[yyyy][mm].max_night):("&nbsp;"))+"</th>";
      }
      out[n++] =	"<th>"+((stats.year[yyyy] && stats.year[yyyy].max_night)?(stats.year[yyyy].max_night):("&nbsp;"))+"</th>"
      out[n++] =	"  </tr>\n"
      out[n++] =	"</table>\n<p> </p>\n\n"

      // +++++++++++++++++
      // + DX/W Report:  +
      // +++++++++++++++++
      var dx_w =	new Array();
      max_count['dxw'] =	0
      for (var c=0; c<12; c++) {
        var mm =	mm_arr[c];
        var val =	((stats.year[yyyy][mm] && stats.year[yyyy][mm].max_dxw)?(stats.year[yyyy][mm].max_dxw):(0))
        dx_w[c] =	val;
        if (val>max_count['dxw']) {
          max_count['dxw'] = val;
        }
      }
      out[n++] =	"<big><a name='"+yyyy+"dx_w'></a>"+yyyy+" DX per Watt "+units_long+link_top+"</big></br>\n";
      out[n++] =	"<table cellpadding='1' cellspacing='0' border='1' class='r'>"
      out[n++] =	" <tr>\n"
      out[n++] = 	"<th width='120'>&nbsp;</th>"
      for (var b=0; b<12; b++) {
        out[n++] =	"<th width='30'>"+months[b].substr(0,3)+"</th>";
      }
      out[n++] =	"<th>"+yyyy+"</th>";
      out[n++] =	"</tr>\n"    
      out[n++] =	"<tr><th class='l'>Beacons</th>";
      for (var c=0; c<12; c++) {
        val =		dx_w[c]
        out[n++] =	"<td bgcolor='"+get_graph_color(val,max_count['dxw'])+"'>"+((val)?(val):("&nbsp;"))+"</td>";
      }
      out[n++] =	"<th class='r'>"+stats.year[yyyy].max_dxw+"</th>";
      out[n++] =	"</tr>\n";
      out[n++] =	"</table>\n<p> </p>\n\n"

      // ++++++++++++++++++++++
      // + Countries Report:  +
      // ++++++++++++++++++++++
      var sorted_cnt =	new Array();
      var s_c =		0;
      for (b in stats.year[yyyy].cnt) {
        sorted_cnt[s_c++] = b;
      }
      sorted_cnt.sort();

      var year_count =	0;
      var year_best_dgps_dx = 0;
      var year_best_dgps_id = "";
      var year_best_ndb_dx = 0;
      var year_best_ndb_id = "";
      var countries =		new Array()
      for (b=0; b<sorted_cnt.length; b++) {
        countries[b] =		new Array()
        var name =			sorted_cnt[b].match(rexp_country);
        countries[b].full =	cnt_arr[name[1]].name + ((name[2])?(" ("+sta_arr[name[1]][name[2]]+")"):(""))
        countries[b].qth =	name[1] + ((name[2])?(" ("+name[2]+")"):(""))
        countries[b].mm =	new Array()
        for (var c=0; c<12; c++) {
          var mm = mm_arr[c];
          countries[b].mm[c] =	((stats.year[yyyy][mm] && stats.year[yyyy][mm].cnt[sorted_cnt[b]])?(stats.year[yyyy][mm].cnt[sorted_cnt[b]]):(0))
          if (countries[b].mm[c]>max_count['cnt']) {
            max_count['cnt'] =	countries[b].mm[c]
          }
        }
      }

      out[n++] =	"<big><a name='"+yyyy+"cr'></a>"+yyyy+" Countries Report"+link_top+"</big></br>\n";
      out[n++] =	"<table cellpadding='1' cellspacing='0' border='1' class='r'>";
      out[n++] =	" <tr>\n";
      out[n++] = "<th width='120' nowrap>Country (Sta)</th>";
      for (var b=0; b<12; b++) {
        out[n++] =	"<th width='30'>"+months[b].substr(0,3)+"</th>";
      }
      out[n++] =	"<th>"+yyyy+"</th>";
      out[n++] =	"<th colspan='2'>Best NDB<br>("+units_long+")</th>";
      out[n++] =	"<th colspan='2'>Best DGPS<br>("+units_long+")</th>";
      out[n++] =	"</tr>\n"

      for (b=0; b<countries.length; b++) {
        out[n++] =	"<tr>\n"
        out[n++] =	"<th class='l'><a class='info' href='javascript:void 0' "
        out[n++] =	"onmouseover='window.status=\""+countries[b].full+"\";return true;' onmouseout='window.status=\"\";return true;' "
        out[n++] =	"title='"+countries[b].full+"'>"+countries[b].qth+"</th>\n"
        for (var c=0; c<12; c++) {
          val =		countries[b].mm[c]
          out[n++] =	"<td bgcolor='"+get_graph_color(val,max_count['cnt'])+"' class='r'>"+((val)?(val):("&nbsp;"))+"</td>";
        }
        out[n++] =	"<th class='r'>"
        if (stats.year[yyyy] && stats.year[yyyy].cnt[sorted_cnt[b]]){
          year_count+=stats.year[yyyy].cnt[sorted_cnt[b]].count;
          out[n++] =	stats.year[yyyy].cnt[sorted_cnt[b]].count+"</th>"
          out[n++] =	"<td class='l' nowrap>"+(typeof stats.year[yyyy].cnt[sorted_cnt[b]].best_ndb_id != "undefined" ? stats.year[yyyy].cnt[sorted_cnt[b]].best_ndb_id : "&nbsp;")+"</td>"
          out[n++] =	"<td>"
          if (!stats.year[yyyy].cnt[sorted_cnt[b]].best_ndb_dx) {
            out[n++] =	"&nbsp;";
          }
          else {
            out[n++] =	((stats.year[yyyy].cnt[sorted_cnt[b]].best_ndb_dx!=-1)?(stats.year[yyyy].cnt[sorted_cnt[b]].best_ndb_dx):("?"))
            if (stats.year[yyyy].cnt[sorted_cnt[b]].best_ndb_dx > year_best_ndb_dx) {
              year_best_ndb_dx = stats.year[yyyy].cnt[sorted_cnt[b]].best_ndb_dx;
              year_best_ndb_id = stats.year[yyyy].cnt[sorted_cnt[b]].best_ndb_id;
            }
          }
          out[n++] =	"</td>";
          out[n++] =	"<td class='l' nowrap>"+(typeof stats.year[yyyy].cnt[sorted_cnt[b]].best_dgps_id != "undefined" ? stats.year[yyyy].cnt[sorted_cnt[b]].best_dgps_id : "&nbsp;")+"</td>"
          out[n++] =	"<td>"
          if (!stats.year[yyyy].cnt[sorted_cnt[b]].best_dgps_dx) {
            out[n++] =	"&nbsp;";
          }
          else {
            out[n++] =	((stats.year[yyyy].cnt[sorted_cnt[b]].best_dgps_dx!=-1)?(stats.year[yyyy].cnt[sorted_cnt[b]].best_dgps_dx):("?"));
            if (stats.year[yyyy].cnt[sorted_cnt[b]].best_dgps_dx > year_best_dgps_dx) {
              year_best_dgps_dx = stats.year[yyyy].cnt[sorted_cnt[b]].best_dgps_dx;
              year_best_dgps_id = stats.year[yyyy].cnt[sorted_cnt[b]].best_dgps_id;
            }
          }
          out[n++] =	"</td>";
        }
        else {
          out[n++] =	"&nbsp;</th><td>&nbsp;</td><td>&nbsp;</td>"
        }
        out[n++] =	"</td></tr>\n"
      }
      out[n++] =	"<tr>\n"
      out[n++] =	"<th class='l'>Countries:</th>\n"
      for (var c=0; c<12; c++) {
        var mm = mm_arr[c];
        var month_count = 0;
        for (b=0; b<sorted_cnt.length; b++) {
          if (stats.year[yyyy][mm] && stats.year[yyyy][mm].cnt[sorted_cnt[b]]) {
            month_count++;
          }
        }
        out[n++] =	"<th class='r'>"+((month_count)?(month_count):("&nbsp;"))+"</th>";
      }
      out[n++] =	"<th class='r'>"+sorted_cnt.length+"</th>\n"
      out[n++] =	"<th class='l'>" + (year_best_ndb_id ? year_best_ndb_id : "&nbsp;") + "</th>\n"
      out[n++] =	"<th class='r'>" + (year_best_ndb_dx ? year_best_ndb_dx : "&nbsp;")+ "</th>\n"
      out[n++] =	"<th class='l'>" + (year_best_dgps_id ? year_best_dgps_id : "&nbsp;") + "</th>\n"
      out[n++] =	"<th class='r'>" + (year_best_dgps_dx ? year_best_dgps_dx : "&nbsp;")+ "</th>\n"
      out[n++] =	"</tr>\n"
      out[n++] =	"</table>\n<p> </p>\n\n"

      // ++++++++++++++++++++++
      // + Regions Report:    +
      // ++++++++++++++++++++++
      var regions =		new Array()
      for (c in rgn_arr) {
        if (stats.year[yyyy].rgn[c]) {
          regions[c] =		new Array();
          regions[c].name =	rgn_arr[c];
          regions[c].mm =	new Array();
          for (var b=0; b<12; b++) {
            var mm = mm_arr[b];
            var val =		((stats.year[yyyy][mm] && stats.year[yyyy][mm].rgn[c])?(stats.year[yyyy][mm].rgn[c]):(0))
            if (val>max_count['rgn']) {
              max_count['rgn'] = val;
            }
            regions[c].mm[b] =	val
          }
        }
      }
      out[n++] =		"<big><a name='"+yyyy+"rr'></a>"+yyyy+" Regions Report"+link_top+"</big></br>\n";
      out[n++] =		"<table cellpadding='1' cellspacing='0' border='1' class='r'>"
      out[n++] =		" <tr>\n"
      out[n++] =		"<th width='120'>&nbsp;</th>"
      for (var b=0; b<12; b++) {
        out[n++] =	"<th width='30'>"+months[b].substr(0,3)+"</th>";
      }
      out[n++] =		"<th>"+yyyy+"</th>";
      out[n++] =		"</tr>\n"
      for (c in regions) {
        if (stats.year[yyyy].rgn[c]) {
        
          out[n++] =	"<tr><th class='l'>"+regions[c].name+"</th>";
          for (var b=0; b<12; b++) {
            var val =	regions[c].mm[b]
            out[n++] =	"<td bgcolor='"+get_graph_color(val,max_count['rgn'])+"' class='r'>"+((val)?(val):("&nbsp;"))+"</td>";
          }
          out[n++] =	"<th class='r'>"+stats.year[yyyy].rgn[c]+"</th>";
          out[n++] =	"</tr>\n";
        }
      }
      out[n++] =	"</table>\n<p> </p>\n\n"

      // ++++++++++++++++++++++
      // + North of 60 Report:+
      // ++++++++++++++++++++++
      if (stats.year[yyyy].n60) {
        n60 =	new Array()
        for (var c=0; c<12; c++) {
          var mm = mm_arr[c];
          var val =	((stats.year[yyyy][mm] && stats.year[yyyy][mm].n60)?(stats.year[yyyy][mm].n60):(0))
          n60[c] =	val;
          if (val>max_count['n60']) {
            max_count['n60'] = val;
          }
        }
        out[n++] =	"<big><a name='"+yyyy+"n60'></a>"+yyyy+" North of 60 Degrees Report"+link_top+"</big></br>\n";
        out[n++] =	"<table cellpadding='1' cellspacing='0' border='1' class='r'>"
        out[n++] =	" <tr>\n"
        out[n++] = 	"<th width='120'>&nbsp;</th>"
        for (var b=0; b<12; b++) {
          out[n++] =	"<th width='30'>"+months[b].substr(0,3)+"</th>";
        }
        out[n++] =	"<th>"+yyyy+"</th>";
        out[n++] =	"</tr>\n"    
        out[n++] =	"<tr><th class='l'>Beacons</th>";
        for (var c=0; c<12; c++) {
          var mm = mm_arr[c];
          var val =	n60[c];
          out[n++] =	"<td bgcolor='"+get_graph_color(val,max_count['n60'])+"'>"+((val)?(val):("&nbsp;"))+"</td>";
        }
        out[n++] =	"<th class='r'>"+stats.year[yyyy].n60+"</th>";
        out[n++] =	"</tr>\n";
      out[n++] =	"</table>\n<p> </p>\n\n"
      }
      out[n++] =	"<hr width='75%' align='center'>\n"
    }
  }
  else {
    // +++++++++++++++++++
    // + Lifetime Report +
    // +++++++++++++++++++

    // +++++++++++++++++++
    // + links:          +
    // +++++++++++++++++++
    if (document.all) {	// for some reason NS 4.7 doesn't set bookmarks correctly when contents dynamically generated.
      var link_top =" <span class='links'>[ <a href='#top'>Top</a> ]</span>"
      out[n++] =	"<p align='center'><span class='links'>[ "
      out[n++] =	"<a href='#summary'>Summary</a> | ";
      out[n++] =	"<a href='#country'>Countries</a> | ";
      out[n++] =	"<a href='#region'>Regions</a> ";
      out[n++] =	"]</span></p>";
    }
    else {
      var link_top = "";
    }

    var max_count =	new Array();		// Used to calculate contrast settings for colour graduated boxes
    max_count['cnt'] =	0;
    var all_stations =	0;
    var all_n60 =		0;
    var all_n60_names =	new Array();
    var all_dxw =		0;
    var all_dxw_name =	"";
    var all_countries =	new Array();
    var all_rgn =		new Array();
    var all_dx_max =	0;
    for (var id in stats.all) {
      var cnt =		station[id].cnt+"_"+station[id].sta;
      all_stations++;
      if (station[id].lat>=60) {
        all_n60++;
        all_n60_names[all_n60_names.length] =	station[id].khz+"-"+station[id].call
      }
      if (station[id].dx>all_dx_max) {
        all_dx_max =	station[id].dx;
        all_dx_name =	station[id].khz+"-"+station[id].call;
      }
      if (station[id].dxw>all_dxw) {
        all_dxw =		station[id].dxw;
        all_dxw_name =	station[id].khz+"-"+station[id].call;
      }
      if (!all_countries[cnt]) {		// Country not yet defined
        all_countries[cnt] = 0;		// Initialise count
        var rgn =	station[id].rgn;
        if (!all_rgn[rgn]) {
          all_rgn[rgn] = new Array();
          all_rgn[rgn].cnt = 0;
          all_rgn[rgn].rgn = rgn;
        }
        all_rgn[station[id].rgn].cnt++;      
      }
      all_countries[cnt]++
    }


    var rgn_sorted = new Array();
    var i=0
    max_count['rgn'] =			0;
    for (var rgn in all_rgn) {
      rgn_sorted[i] =			new Array()
      rgn_sorted[i].rgn =		rgn;
      rgn_sorted[i].cnt =		all_rgn[rgn];
      if (rgn_sorted[i].cnt>max_count['rgn']) {
        max_count['rgn'] = rgn_sorted[i].rgn;
      }
    }

    var all_countries_sorted = new Array();
    var i=0
    for (var cnt in all_countries) {
      all_countries_sorted[i] = new Array()
      all_countries_sorted[i].cnt = cnt;
      all_countries_sorted[i].stations = all_countries[cnt]
      if (all_countries_sorted[i].stations>max_count['cnt']) {
        max_count['cnt'] = all_countries_sorted[i].stations;
      }
      i++
    }

    all_countries_sorted.sort(sortBy_life_country);

    var rgn_sorted =	new Array();
    var i=0;
    for (c in rgn_arr) {
      if (all_rgn[c]) {
        all_rgn[rgn]
        rgn_sorted[i] =		new Array();
        rgn_sorted[i].rgn =	rgn_arr[c];
        rgn_sorted[i].cnt =	all_rgn[c].cnt;
        if (rgn_sorted[i].cnt > max_count['rgn']) {
          max_count['rgn'] = rgn_sorted[i].cnt;
        }
        i++;
      }
    }
    rgn_sorted.sort(sortBy_life_rgn);


    out[n++] =		"<big><a name='summary'></a>Summary"+link_top+"</big></br>\n"
    out[n++] =		"<table cellpadding='1' cellspacing='0' border='1' class='r'>";
    out[n++] =		"  <tr>\n";
    out[n++] =		"    <th class='l'>Statistics</th>\n";
    out[n++] =		"    <th colspan='2' class='l'>Value</th>\n";
    out[n++] =		"  </tr>\n";
    out[n++] =		"  <tr>\n";
    out[n++] =		"    <th class='l'>Total beacons received:</th>\n"
    out[n++] =		"    <td colspan='2' class='l'>"+all_stations+"</td>\n"
    out[n++] =		"  </tr>\n";
    out[n++] =		"  <tr>\n";
    out[n++] =		"    <th class='l'>Beacons North of 60 Degrees:</th>\n"
    out[n++] =		"    <td class='l'>"+all_n60+"</td>"
    out[n++] =		"    <td class='l'><nobr>"+all_n60_names.join("</nobr>, <nobr>")+"</nobr></td>"
    out[n++] =		"  </tr>\n";
    out[n++] =		"  <tr>\n";
    out[n++] =		"    <th class='l'>Maximum DX:</th>\n"
    out[n++] =		"    <td class='l' nowrap>"+all_dx_max+" "+units_long+"</td>"
    out[n++] =		"    <td class='l'>"+all_dx_name+"</td>"
    out[n++] =		"  </tr>\n";
    out[n++] =		"  <tr>\n";
    out[n++] =		"    <th class='l'>Maximum DX/W:</th>\n"
    out[n++] =		"    <td class='l' nowrap>"+all_dxw+" "+units_long+"</td>"
    out[n++] =		"    <td class='l'>"+all_dxw_name+"</td>"
    out[n++] =		"  </tr>\n";
    out[n++] =		"</table>\n<p> </p>\n"

    // ++++++++++++++++++++++
    // + Countries Report:  +
    // ++++++++++++++++++++++
    out[n++] =		"<big><a name='country'></a>Countries Report"+link_top+"</big></br>\n"
    out[n++] =		"<table cellpadding='1' cellspacing='0' border='1' class='r'>\n";
    out[n++] =		"  <tr>\n";
    out[n++] =		"    <th class='l'>Country</th>\n";
    out[n++] =		"    <th class='l'>Beacons</th>\n";
    out[n++] =		"  </tr>\n";

    for (var i=0; i<all_countries_sorted.length; i++) {
      var name =	all_countries_sorted[i].cnt.match(rexp_country);
      var cnt =		name[1];
      var sta =		((name[2])?(" ("+name[2]+")"):(""));
      full =		cnt_arr[name[1]] + ((name[2])?(" ("+sta_arr[name[1]][name[2]]+")"):(""));
      var val =		all_countries_sorted[i].stations
      out[n++] =	"  <tr>\n";
      out[n++] =	"<th class='l'><a class='info' href='javascript:void 0' "
      out[n++] =	"onmouseover='window.status=\""+full+"\";return true;' onmouseout='window.status=\"\";return true;' "
      out[n++] =	"title='"+full+"'>"+cnt+sta+"</th>\n"
      out[n++] =	"    <td class='r' bgcolor='"+get_graph_color(val,max_count['cnt'])+"'>"+val+"</td>\n"
      out[n++] =	"  </tr>\n";
    }
    out[n++] =		"  <tr>\n";
    out[n++] =		"    <th class='l'>Countries:</th>\n"
    out[n++] =		"    <th class='r'>"+all_countries_sorted.length+"</th>\n"
    out[n++] =		"  </tr>\n";
    out[n++] =		"</table>\n<p> </p>\n\n"


   // ++++++++++++++++++++++
   // + Regions Report:    +
   // ++++++++++++++++++++++
    out[n++] =		"<big><a name='region'></a>Regions Report"+link_top+"</big></br>\n"
    out[n++] =		"<table cellpadding='1' cellspacing='0' border='1' class='r'>";
    out[n++] =		"  <tr>\n";
    out[n++] =		"    <th class='l'>Region</th>\n";
    out[n++] =		"    <th class='l'>Beacons</th>\n";
    out[n++] =		"  </tr>\n";
    for (var i=0; i<rgn_sorted.length; i++) {
      out[n++] =	"  <tr>\n";
      out[n++] =	"    <th class='l'>"+rgn_sorted[i].rgn+"</th>\n"
      out[n++] =	"    <td class='r' bgcolor='"+get_graph_color(rgn_sorted[i].cnt,max_count['rgn'])+"'>"+rgn_sorted[i].cnt+"</td>\n"
      out[n++] =	"  </tr>\n";
    }
    out[n++] =		"  <tr>\n";
    out[n++] =		"    <th class='l'>Regions:</th>\n"
    out[n++] =		"    <th class='r'>"+rgn_sorted.length+"</th>\n"
    out[n++] =		"  </tr>\n";
    out[n++] =		"</table>\n<p> </p>\n\n"
  }

  out[n++] =		"<p><a name='awards'></a><small><b>See <a href='http://www.beaconworld.org.uk' target='_blank'>http://www.beaconworld.org.uk</a> for details on how to join the NDB List and qualify for awards</b></small></p>";
  out[n++] =		"</body></html>\n"
  stat_h =		window.open('','statsViewer', 'width=700,height=480,status=1,resizable=1,menubar=1,location=0,toolbar=0,scrollbars=1');
  stat_h.focus(); stat_h.document.write(out.join("")); stat_h.document.close();
  if (progress_hd) {
    progress_hd.close();
  }
}



// ************************************
// * popup_text()                     *
// ************************************
function popup_text() {
  var txt_options =			false;
  if (get_cookie("txt_options")) {
    var txt_options = get_cookie("txt_options").split("|");
    var i=0;
    cookie['txt_date1'] =	txt_options[i++];
    cookie['txt_date2'] =	txt_options[i++];
    cookie['txt_dayNight'] =	txt_options[i++];
    cookie['txt_dx1'] =		txt_options[i++];
    cookie['txt_dx2'] =		txt_options[i++];
    cookie['txt_format'] =	txt_options[i++];
    cookie['txt_freq1'] =	txt_options[i++];
    cookie['txt_freq2'] =	txt_options[i++];
    cookie['txt_h_dxw'] =	txt_options[i++];
    cookie['txt_h_new'] =	txt_options[i++];
    cookie['txt_h_mod'] =	txt_options[i++];
    cookie['txt_showAll'] =	txt_options[i++];
    cookie['txt_sortBy'] =	txt_options[i++];
    cookie['txt_type'] =		txt_options[i++];
    cookie['txt_lat1'] =		txt_options[i++];
    cookie['txt_lat2'] =		txt_options[i++];
    cookie['txt_lon1'] =		txt_options[i++];
    cookie['txt_lon2'] =		txt_options[i++];
    // cookie['mod_abs'] is set in main prefs
  }
  // Make frequency span conform:
  if (cookie['txt_freq1'].toLowerCase() == "all") cookie['txt_freq1'] =	"0";
  if (cookie['txt_freq2'].toLowerCase() == "all") cookie['txt_freq2'] =	"100000";
  start_khz =	parseFloat(cookie['txt_freq1']);
  end_khz =	parseFloat(cookie['txt_freq2']);

  // Make dx span conform:
  if (cookie['txt_dx1'].toLowerCase() == "all")   cookie['txt_dx1'] =	"0";
  if (cookie['txt_dx2'].toLowerCase() == "all")   cookie['txt_dx2'] =	"100000";
  dx_min =	parseFloat(cookie['txt_dx1']);
  dx_max =	parseFloat(cookie['txt_dx2']);

  // Make lat span conform:
  if (cookie['txt_lat1'].toLowerCase() == "all")   cookie['txt_lat1'] =	"-90";
  if (cookie['txt_lat2'].toLowerCase() == "all")   cookie['txt_lat2'] =	"90";
  lat_min =	parseFloat(cookie['txt_lat1']);
  lat_max =	parseFloat(cookie['txt_lat2']);

  // Make lon span conform:
  if (cookie['txt_lon1'].toLowerCase() == "all")   cookie['txt_lon1'] =	"-180";
  if (cookie['txt_lon2'].toLowerCase() == "all")   cookie['txt_lon2'] =	"180";
  lon_min =	parseFloat(cookie['txt_lon1']);
  lon_max =	parseFloat(cookie['txt_lon2']);

  // Make date range conform:
  if (cookie['txt_date1'].toLowerCase() == "all")  cookie['txt_date1'] =	"19000001";
  if (cookie['txt_date2'].toLowerCase() == "all")  cookie['txt_date2'] =	"21000001";


  var sorted = new Array(); var i=0;
  for (a in station) {
    if ((parseFloat(station[a].khz)>=start_khz && parseFloat(station[a].khz)<=end_khz) &&
         ((cookie['txt_type'] == 'all') ||
          (cookie['txt_type'] == 'dgps' && station[a].call.substr(0,1) =="#") ||
          (cookie['txt_type'] == 'ndb' && station[a].call.substr(0,1) !="#"))) {
      var id =			station[a].khz+"-"+station[a].call;
      if (logbook[id]) {
        logbook[id]['shown'] =	false;
        logbook[id]['old'] =	false;
      }
      sorted[i++] =		station[a];
    }
  }
  // Output data
  var out =		new Array()
  var n =			0;
  var total =		0;

  var start_dd =	cookie['txt_date1'].substr(6,2);
  var start_mm =	cookie['txt_date1'].substr(4,2)-1;	// Months begin at 0
  var start_yyyy =	cookie['txt_date1'].substr(0,4)
  var end_dd =		cookie['txt_date2'].substr(6,2)
  var end_mm =		cookie['txt_date2'].substr(4,2)-1;	// Months begin at 0
  var end_yyyy =	cookie['txt_date2'].substr(0,4)

  out[n++] =		"Log showing " +((cookie['txt_showAll']=='1')?("all receptions"):("first reception")) + " of each "
  switch (cookie['txt_type']) {
    case "dgps": out[n++] = "DGPS "; break;
    case "ndb": out[n++] = "NDB "; break;
  }
  out[n++] = "beacon "

  if (cookie['txt_date1']==cookie['txt_date2']) {
    out[n++] =	"on day "+start_dd;
  }
  else {
    if (cookie['txt_date1']!="19000001" && cookie['txt_date2']!="21000001") {
      switch (cookie['txt_format']) {
        case "dd":         out[n++] =	"between day "+start_dd+" and "+end_dd; break
        case "ddmmyyyy":   out[n++] =	"between "+start_dd+"/"+start_mm+"/"+start_yyyy+" and "+end_dd+"/"+end_mm+"/"+end_yyyy; break
        case "dd.mm.yyyy": out[n++] =	"between "+start_dd+"."+start_mm+"."+start_yyyy+" and "+end_dd+"."+end_mm+"."+end_yyyy; break
        case "mmddyyyy":   out[n++] =	"between "+start_mm+"/"+start_dd+"/"+start_yyyy+" and "+end_mm+"/"+end_dd+"/"+end_yyyy; break
        default:           out[n++] =	"between "+cookie['txt_date1']+" and "+cookie['txt_date2']+" (in YYYYMMDD format)"; break
      }
    }
    if (cookie['txt_date1']!="19000001" && cookie['txt_date2']=="21000001") {
      switch (cookie['txt_format']) {
        case "dd":         out[n++] =	"from day "+start_dd+" onwards"; break
        case "ddmmyyyy":   out[n++] =	"from "+start_dd+"/"+start_mm+"/"+start_yyyy+" onwards"; break
        case "dd.mm.yyyy": out[n++] =	"from "+start_dd+"."+start_mm+"."+start_yyyy+" onwards"; break
        case "mmddyyyy":   out[n++] =	"from "+start_mm+"/"+start_dd+"/"+start_yyyy+" onwards"; break
        default:           out[n++] =	"from "+cookie['txt_date1']+" onwards (in YYYYMMDD format)"; break
      }
    }
    if (cookie['txt_date1']=="19000001" && cookie['txt_date2']!="21000001") {
      switch (cookie['txt_format']) {
        case "dd":         out[n++] =	"until day "+end_dd+"\n\n"; break
        case "ddmmyyyy":   out[n++] =	"until "+end_dd+"/"+end_mm+"/"+end_yyyy; break
        case "dd.mm.yyyy": out[n++] =	"until "+end_dd+"."+end_mm+"."+end_yyyy; break
        case "mmddyyyy":   out[n++] =	"until "+end_mm+"/"+end_dd+"/"+end_yyyy; break
        default:           out[n++] =	"until "+cookie['txt_date2']+" onwards (in YYYYMMDD format)"; break
      }
    }
  }

  if (!(start_khz==0 && end_khz==100000)) {
    out[n++] =	"\nAll frequencies"
    if (start_khz!=0) {
      out[n++] =	" from "+start_khz+"KHz";
    }
    if (end_khz!=100000) {
      out[n++] =	" to "+end_khz+"KHz";
    }
  }

  if (!(cookie['txt_dx1']==0 && cookie['txt_dx2']==100000)) {
    out[n++] =	"\nAll distances"
    if (cookie['txt_dx1']!=0) {
      out[n++] =	" from "+cookie['txt_dx1']+" "+get_units();
    }
    if (cookie['txt_dx2']!=100000) {
      out[n++] =	" to "+cookie['txt_dx2']+" "+get_units();
    }
  }

  if (!(cookie['txt_lat1']==-90 && cookie['txt_lat2']==90)) {
    out[n++] =	"\nAll Latitudes"
    if (cookie['txt_lat11']!=-90) {
      out[n++] =	" from "+cookie['txt_lat1'];
    }
    if (cookie['txt_lat2']!=90) {
      out[n++] =	" to "+cookie['txt_lat2'];
    }
  }

  if (!(cookie['txt_lon1']==-180 && cookie['txt_lon2']==180)) {
    out[n++] =	"\nAll Longitudes"
    if (cookie['txt_lon1']!=-180) {
      out[n++] =	" from "+cookie['txt_lon1'];
    }
    if (cookie['txt_lon2']!=180) {
      out[n++] =	" to "+cookie['txt_lon2'];
    }
  }

  if (cookie['txt_dayNight']!="x") {
    if (cookie['txt_dayNight']=="d") {
      out[n++] =	"\nDaytime loggings only";
    }
    else {
      out[n++] =	"\nNight-time loggings only";
    }
  }

  out[n++] =	"\nDaytime: "+lead(utc_daylight)+":00-"+lead((utc_daylight+4)%24)+":59, ";
  out[n++] =	"Night: "+lead((utc_daylight+5)%24)+":00-"+lead((utc_daylight-1)%24)+":59\n";

  out[n++] =	"\nOutput sorted by ";
  switch (cookie['txt_sortBy']) {
    case "call": out[n++] = "callsign"; break;
    case "cnt":  out[n++] = "country"; break;
    case "yyyymmddhhmm": out[n++] = "date"; break;
    case "dx":   out[n++] = "distance"; break;
    case "dxw":  out[n++] = "distance per watt"; break;
    case "lsb":  out[n++] = "LSB Mod Offset"; break;
    case "pwr":  out[n++] = "transmitter power"; break;
    case "qth":  out[n++] = "town"; break;
    case "sta":  out[n++] = "state / province"; break;
    case "hhmm": out[n++] = "time"; break;
    case "usb":  out[n++] = "USB Mod Offset"; break;
    default:     out[n++] = "Frequency"; break;
  }
  out[n++] =	"\n";


  out[n++] =	"----------------------------------------------------------------------\n";
  switch (cookie['txt_format']) {
    case "dd":       out[n++] = "DD ";         break
    case "ddmmyyyy": out[n++] = "DD/MM/YYYY "; break
    case "dd.mm.yyyy": out[n++] = "DD.MM.YYYY "; break
    case "mmddyyyy": out[n++] = "MM/DD/YYYY "; break
    case "yyyymmdd": out[n++] = "YYYYMMDD ";   break
    case "yyyy-mm-dd": out[n++] = "YYYY-MM-DD ";   break
    case "no_date" : break;
  }
  out[n++] =	(cookie['txt_format']=='no_date' ? "" : "UTC   ");
  out[n++] =	"kHz   Call  ";
  out[n++] =	(cookie['txt_h_mod']!='1' && cookie['mod_abs']!='1' ? "LSB  USB  " : "");
  out[n++] =	(cookie['txt_h_mod']!='1' && cookie['mod_abs']=='1' ? "LSB     USB     " : "");

  out[n++] =	"Pwr  "+pad(get_units(),6);
  out[n++] =	(cookie['txt_h_dxw']!='1' ? "dx/w " : "");
  out[n++] =	(cookie['txt_h_new']!='1' ? "+ " : "");
  out[n++] =	"Location\n";
  out[n++] =	"----------------------------------------------------------------------\n";

  var temp_shown =	0;
  var temp_new =		0;

// We now join station and log together and then flatten the table


//new TEXT(date,hhmm,khz,call,lsb,usb,pwr,dx,dxw,nu,qth,sta,cnt) {
  var temp_date =	0;
  var temp_hhmm =	0;
  var temp_khz =	0;
  var temp_call =	0;
  var temp_lsb =	0;
  var temp_usb =	0;
  var temp_pwr =	0;
  var temp_dx =	0;
  var temp_dxw =	0;
  var temp_nu =	0;
  var temp_qth =	0;
  var temp_sta =	0;
  var temp_cnt =	0;

  var start =		new Date(start_yyyy,start_mm,start_dd)
  var end =		new Date(end_yyyy,end_mm,end_dd,23,59,59,0)

  var text_array =	new Array();
  var k =		0;
  for (var a=0; a<sorted.length; a++) {
    var id =	sorted[a].khz+"-"+sorted[a].call
    if (logbook[id]) {
      for (var b=0; b<logbook[id]['entry'].length; b++) {
        total++
        var log_yyyy =	logbook[id]['entry'][b]['yyyymmddhhmm'].substr(0,4);
        var log_mm =	logbook[id]['entry'][b]['yyyymmddhhmm'].substr(4,2)-1;	// Months begin at 0
        var log_dd =	logbook[id]['entry'][b]['yyyymmddhhmm'].substr(6,2);
        var log_HH =	logbook[id]['entry'][b]['yyyymmddhhmm'].substr(8,2);
        var log_MM =	logbook[id]['entry'][b]['yyyymmddhhmm'].substr(10,2);
        var log_isDay =	(utc_daylight_array[0] == log_HH || utc_daylight_array[1] == log_HH || utc_daylight_array[2] == log_HH || utc_daylight_array[3] == log_HH);

        var log_date =	new Date(log_yyyy,log_mm,log_dd,log_HH,log_MM,0,0)

        if (log_date>=start && log_date<=end &&
            station[id].dx>=dx_min && station[id].dx<=dx_max &&
            station[id].lat>=lat_min && station[id].lat<=lat_max &&
            station[id].lon>=lon_min && station[id].lon<=lon_max &&
            (cookie['txt_dayNight'] == 'x' || (cookie['txt_dayNight']=='d' && log_isDay) || (cookie['txt_dayNight']=='n' && !log_isDay))) {
          var DD =	lead(log_date.getDate());
          var MM =	lead(log_date.getMonth()+1);	// Months begin at 0
          var YYYY =	log_date.getFullYear();
          var hh =	lead(log_date.getHours())
          var mm =	lead(log_date.getMinutes())
          temp_yyyymmddhhmm =		YYYY+""+MM+""+DD+""+hh+""+mm
          switch (cookie['txt_format']) {
            case "dd":         temp_date = DD+" "; break;
            case "ddmmyyyy":   temp_date = DD+"/"+MM+"/"+YYYY+" "; break;
            case "dd.mm.yyyy": temp_date = DD+"."+MM+"."+YYYY+" "; break;
            case "mmddyyyy":   temp_date = MM+"/"+DD+"/"+YYYY+" "; break;
            case "yyyymmdd":   temp_date = YYYY+""+MM+""+DD+" "; break;
            case "yyyy-mm-dd":   temp_date = YYYY+"-"+MM+"-"+DD+" "; break;
            case "no_date" :   temp_date = ""; break;
          }
          if (cookie['txt_showAll']=='1' || !logbook[id]['shown']) {
            if (!logbook[id]['shown']) {
              temp_shown++
              logbook[id]['shown'] = true;
            }
            temp_hhmm =		(cookie['txt_format']=="no_date" ? "" : ""+hh+":"+mm);
            temp_khz =		sorted[a].khz;
            temp_call =		sorted[a].call;
            temp_lsb =		sorted[a].lsb;
            temp_usb =		sorted[a].usb;
            temp_pwr =		sorted[a].pwr;
            temp_dx =		sorted[a].dx;
            temp_dxw =		sorted[a].dxw;
            temp_nu =		!logbook[id]['old'];
            temp_qth =		sorted[a].qth;
            temp_sta =		sorted[a].sta;
            temp_cnt =		sorted[a].cnt;
            if (!logbook[id]['old']){
              temp_new++;
              logbook[id]['old'] = true;
            }
            text_array[k++] =	new TEXT(temp_yyyymmddhhmm,temp_date,temp_hhmm,temp_khz,temp_call,temp_lsb,temp_usb,temp_pwr,temp_dx,temp_dxw,temp_nu,temp_qth,temp_sta,temp_cnt);
            logbook[id]['shown'] = true;
          }
        }
        else {
          logbook[id]['old'] = true;
        }
      }
    } 
  }

  text_array.sort(sortBy_call).sort(sortBy_khz).sort(eval("sortBy_"+cookie['txt_sortBy']));

  for (var k=0; k<text_array.length; k++) {
    out[n++] =	text_array[k].date;
    out[n++] =	text_array[k].hhmm+" ";
    out[n++] =	pad(text_array[k].khz,5)+" ";
    out[n++] =	pad(text_array[k].call,5)+" ";
    if (cookie['txt_h_mod']!='1' && cookie['mod_abs']!='1') {
      out[n++] =	(text_array[k].lsb ? pad(text_array[k].lsb,4)+" " : "     ");
      out[n++] =	(text_array[k].usb ? pad(text_array[k].usb,4)+" " : "     ");
    }
    if (cookie['txt_h_mod']!='1' && cookie['mod_abs']=='1') {
      out[n++] =	(text_array[k].lsb ? pad(text_array[k].lsb,7)+" " : "        ");
      out[n++] =	(text_array[k].usb ? pad(text_array[k].usb,7)+" " : "        ");
    }
    out[n++] =	pad(text_array[k].pwr,4)+" ";
    out[n++] =	pad(text_array[k].dx,5)+" ";
    if (cookie['txt_h_dxw']!='1') {
      out[n++] =	((text_array[k].dxw)?(pad(text_array[k].dxw,4)+" "):("     "));
    }
    if (cookie['txt_h_new']!='1') {
      out[n++] =	(text_array[k].nu ? "Y" : " ")+" ";
    }
    out[n++] =	text_array[k].qth+", ";
    out[n++] =	(text_array[k].sta!="" ? text_array[k].sta+", ": "");
    out[n++] =	text_array[k].cnt+"\n";
  }

  out[n++] =	"----------------------------------------------------------------------\n";
  out[n++] =	temp_shown+" beacons shown listed"+((temp_new && cookie['txt_h_new']!='1')?(", including "+temp_new+" beacons new to log (shown in + column)"):(""))+".\n";
  out[n++] =	"(Output generated by NDB WebLog "+version+" - looks best in courier font)"
  list_h =		window.open('','textOutput', 'width=800,height=600,status=1,resizable=1,menubar=1,location=0,toolbar=1,scrollbars=1');
  list_h.focus();
  list_h.document.open("text/plain");
  if (document.all)	list_h.document.write(out.join(""));
  else			list_h.document.write("<pre>"+out.join("")+"</pre>");
  list_h.document.close();
}



// ************************************
// * popup_text_options()             *
// ************************************
function popup_text_options() {
  var txt_options =			false;
  if (get_cookie("txt_options")) {
    var txt_options = get_cookie("txt_options").split("|");
    var i=0;
    cookie['txt_date1'] =	txt_options[i++];
    cookie['txt_date2'] =	txt_options[i++];
    cookie['txt_dayNight'] =	txt_options[i++];
    cookie['txt_dx1'] =		txt_options[i++];
    cookie['txt_dx2'] =		txt_options[i++];
    cookie['txt_format'] =	txt_options[i++];
    cookie['txt_freq1'] =	txt_options[i++];
    cookie['txt_freq2'] =	txt_options[i++];
    cookie['txt_h_dxw'] =	txt_options[i++];
    cookie['txt_h_new'] =	txt_options[i++];
    cookie['txt_h_mod'] =	txt_options[i++];
    cookie['txt_showAll'] =	txt_options[i++];
    cookie['txt_sortBy'] =	txt_options[i++];
    cookie['txt_type'] =		txt_options[i++];
    cookie['txt_lat1'] =		txt_options[i++];
    cookie['txt_lat2'] =		txt_options[i++];
    cookie['txt_lon1'] =		txt_options[i++];
    cookie['txt_lon2'] =		txt_options[i++];
  }
  if (!cookie['txt_format']) {
    cookie['txt_format'] = 'yyyymmdd';
  }
  if (!cookie['txt_sortBy']) {
    cookie['txt_sortBy'] = 'khz';
  }

  var out =	new Array()
  var n =	0;
  out[n++] =	"<!doctype html public '-//W3C//DTD HTML 4.01 Transitional//EN'>\n";
  out[n++] =	"<html><head><title>NDB WebLog > Text List Setup</title>\n";
  out[n++] =	"<meta HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=windows-1252\">\n";
  out[n++] =	"<link TITLE=\"new\" REL=\"stylesheet\" HREF=\"ndbweblog.css\" type=\"text/css\">\n";
  out[n++] =	"<script language='javascript' type='text/javascript'>\n"
  out[n++] =	"function go() {\n"
  out[n++] =	"  var txt_date1 =    (document.form.txt_date1.value?document.form.txt_date1.value:'All');\n"
  out[n++] =	"  var txt_date2 =    (document.form.txt_date2.value?document.form.txt_date2.value:'All');\n"
  out[n++] =	"  var txt_dayNight = (document.form.txt_dayNight.value?document.form.txt_dayNight.value:'All');\n"
  out[n++] =	"  var txt_dx1 =      (document.form.txt_dx1.value?document.form.txt_dx1.value:'All');\n"
  out[n++] =	"  var txt_dx2 =      (document.form.txt_dx2.value?document.form.txt_dx2.value:'All');\n"
  out[n++] =	"  var txt_format =   document.form.txt_format[document.form.txt_format.selectedIndex].value;\n"
  out[n++] =	"  var txt_freq1 =    (document.form.txt_freq1.value?document.form.txt_freq1.value:'All');\n"
  out[n++] =	"  var txt_freq2 =    (document.form.txt_freq2.value?document.form.txt_freq2.value:'All');\n"
  out[n++] =	"  var txt_h_new =    parseInt(document.form.txt_h_new[document.form.txt_h_new.selectedIndex].value);\n"
  out[n++] =	"  var txt_h_mod =    parseInt(document.form.txt_h_mod[document.form.txt_h_mod.selectedIndex].value);\n"
  out[n++] =	"  var txt_h_dxw =    parseInt(document.form.txt_h_dxw[document.form.txt_h_dxw.selectedIndex].value);\n"
  out[n++] =	"  var txt_lat1 =     (document.form.txt_lat1.value?document.form.txt_lat1.value:'All');\n"
  out[n++] =	"  var txt_lat2 =     (document.form.txt_lat2.value?document.form.txt_lat2.value:'All');\n"
  out[n++] =	"  var txt_lon1 =     (document.form.txt_lon1.value?document.form.txt_lon1.value:'All');\n"
  out[n++] =	"  var txt_lon2 =     (document.form.txt_lon2.value?document.form.txt_lon2.value:'All');\n"
  out[n++] =	"  var txt_showAll =  parseInt(document.form.txt_showAll[document.form.txt_showAll.selectedIndex].value);\n"
  out[n++] =	"  var txt_sortBy =   (document.form.txt_sortBy[document.form.txt_sortBy.selectedIndex].value);\n"
  out[n++] =	"  var txt_type =     (document.form.txt_type[document.form.txt_type.selectedIndex].value);\n"
  out[n++] =	"  var expires =	new Date();\n";
  out[n++] =	"  expires.setFullYear(expires.getFullYear()+1);\n";
  out[n++] =	"  expires =		expires.toGMTString();\n";
  out[n++] =	"  var D =		\"|\"\n";
  out[n++] =	"  var cookies = \"txt_options=\"+txt_date1+D+txt_date2+D+txt_dayNight+D+\n"
  out[n++] =	"                txt_dx1+D+txt_dx2+D+txt_format+D+txt_freq1+D+txt_freq2+D+\n"
  out[n++] =	"                txt_h_dxw+D+txt_h_new+D+txt_h_mod+D+txt_showAll+D+txt_sortBy+D+\n"
  out[n++] =	"                txt_type+D+txt_lat1+D+txt_lat2+D+txt_lon1+D+txt_lon2+\";expires=\"+expires;\n";
  out[n++] =	"  document.cookie = cookies;\n";
  out[n++] =	"  window.opener.popup_text(txt_format,txt_date1,txt_date2,txt_dayNight,txt_freq1,txt_freq2,txt_dx1,txt_dx2,txt_showAll,txt_sortBy,txt_h_new,txt_h_mod,txt_h_dxw,txt_type);\n";
  out[n++] =	"  window.close();\n";
  out[n++] =	"}\n"
  out[n++] =	"</script>\n"
  out[n++] =	"</head>\n"
  out[n++] =	"<body onload='document.body.focus()' onkeydown=\"window.opener.keydown('popup_text_options',event)\"><form name='form' action='./'>\n";
  out[n++] =	"<table cellpadding='0' cellspacing='0' border='0' class='noline'>\n";
  out[n++] =	"  <tr>\n";
  out[n++] =	"    <td class='plain'><table cellpadding='0' cellspacing='0' border='1' class='r' width='100%'>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <th colspan='2'>Text List > Output Options</th>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap width='50%'>Date Format&nbsp;</td>\n";
  out[n++] =	"        <td nowrap width='50%'><select name='txt_format'>\n"
  out[n++] =	"<option value='dd'"         +(cookie['txt_format']=='dd' ?         " selected" : "")+">DD (for CLEs)</option>\n"
  out[n++] =	"<option value='ddmmyyyy'"   +(cookie['txt_format']=='ddmmyyyy' ?   " selected" : "")+">DD/MM/YYYY</option>\n"
  out[n++] =	"<option value='dd.mm.yyyy'" +(cookie['txt_format']=='dd.mm.yyyy' ? " selected" : "")+">DD.MM.YYYY</option>\n"
  out[n++] =	"<option value='mmddyyyy'"   +(cookie['txt_format']=='mmddyyyy' ?   " selected" : "")+">MM/DD/YYYY</option>\n"
  out[n++] =	"<option value='yyyy-mm-dd'  " +(cookie['txt_format']=='yyyy-mm-dd' ?   " selected" : "")+">YYYY-MM-DD</option>\n"
  out[n++] =	"<option value='yyyymmdd'  " +(cookie['txt_format']=='yyyymmdd' ?   " selected" : "")+">YYYYMMDD</option>\n"
  out[n++] =	"<option value='no_date'"    +(cookie['txt_format']=='no_date' ?     " selected" : "")+">(No Time or date)</option>\n"
  out[n++] =	"</select></td>\n"
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap>For each beacon&nbsp;</td>\n";
  out[n++] =	"        <td nowrap><select name='txt_showAll'>\n"
  out[n++] =	"<option value='0'" +(cookie['txt_showAll']=='0' ? " selected" : "")+">Show first logging</option>\n"
  out[n++] =	"<option value='1'" +(cookie['txt_showAll']=='1' ? " selected" : "")+">Show all loggings</option>\n\n"
  out[n++] =	"</select></td>\n"
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap>Sort By&nbsp;</td>\n";
  out[n++] =	"        <td nowrap><select name='txt_sortBy'>\n"
  out[n++] =	"<option value='call'" +(cookie['txt_sortBy']=='call' ? " selected" : "")+">Call</option>\n"
  out[n++] =	"<option value='cnt'"  +(cookie['txt_sortBy']=='cnt'  ? " selected" : "")+">Country</option>\n"
  out[n++] =	"<option value='yyyymmddhhmm'" +(cookie['txt_sortBy']=='yyyymmddhhmm' ? " selected" : "")+">Date</option>\n"
  out[n++] =	"<option value='dx'"   +(cookie['txt_sortBy']=='dx'   ? " selected" : "")+">Distance</option>\n"
  out[n++] =	"<option value='dxw'"  +(cookie['txt_sortBy']=='dxw'  ? " selected" : "")+">DX per Watt</option>\n"
  out[n++] =	"<option value='khz'"  +(cookie['txt_sortBy']=='khz'  ? " selected" : "")+">KHz</option>\n"
  out[n++] =	"<option value='lsb'"  +(cookie['txt_sortBy']=='lsb'  ? " selected" : "")+">Modulation (LSB)</option>\n"
  out[n++] =	"<option value='usb'"  +(cookie['txt_sortBy']=='usb'  ? " selected" : "")+">Modulation (USB)</option>\n"
  out[n++] =	"<option value='pwr'"  +(cookie['txt_sortBy']=='pwr'  ? " selected" : "")+">TX power</option>\n"
  out[n++] =	"<option value='qth'"  +(cookie['txt_sortBy']=='qth'  ? " selected" : "")+">Location</option>\n"
  out[n++] =	"<option value='sta'"  +(cookie['txt_sortBy']=='sta'  ? " selected" : "")+">State / Province</option>\n"
  out[n++] =	"<option value='hhmm'" +(cookie['txt_sortBy']=='hhmm' ? " selected" : "")+">Time</option>\n"
  out[n++] =	"</select></td>\n"
  out[n++] =	"      </tr>\n";
  out[n++] =	"    </table></td>\n";
  out[n++] =	"  </tr>\n";
  out[n++] =	"  <tr>\n";
  out[n++] =	"    <td class='plain'>&nbsp;</td>\n";
  out[n++] =	"  </tr>\n";
  out[n++] =	"  <tr>\n";
  out[n++] =	"    <td class='plain'><table cellpadding='1' cellspacing='0' border='1' class='r' width='100%'>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <th colspan='2'>Text List > Filters</th>";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap width='50%'>Day / Night&nbsp;</td>\n";
  out[n++] =	"        <td nowrap width='50%'><select name='txt_dayNight'>\n"
  out[n++] =	"<option value='x'" +(cookie['txt_dayNight']=='x' ? " selected" : "")+">All times</option>\n"
  out[n++] =	"<option value='d'" +(cookie['txt_dayNight']=='d' ? " selected" : "")+">Daytime only</option>\n"
  out[n++] =	"<option value='n'"  +(cookie['txt_dayNight']=='n'  ? " selected" : "")+">Night only</option>\n"
  out[n++] =	"</select></td>\n"
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap>NDB / DGPS&nbsp;</td>\n";
  out[n++] =	"        <td nowrap><select name='txt_type'>\n"
  out[n++] =	"<option value='all'" +(cookie['txt_type']=='all' ? " selected" : "")+">NDB + DGPS</option>\n"
  out[n++] =	"<option value='ndb'" +(cookie['txt_type']=='ndb' ? " selected" : "")+">NDB only</option>\n"
  out[n++] =	"<option value='dgps'"  +(cookie['txt_type']=='dgps'  ? " selected" : "")+">DGPS only</option>\n"
  out[n++] =	"</select></td>\n"
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap>Dates (YYYYMMDD)&nbsp;</td>\n";
  out[n++] =	"        <td nowrap><input title='Minimum value' name='txt_date1' size='8' maxlength='8' value='"+(cookie['txt_date1']?cookie['txt_date1']:"All")+"' "
  out[n++] =	"onblur='if (document.form.txt_date1.value==\"\") document.form.txt_date1.value=\"All\"; return true;'> - ";
  out[n++] =	"<input title='Maximum value' name='txt_date2' size='8' maxlength='8' value='"+(cookie['txt_date2']?cookie['txt_date2']:"All")+"' "
  out[n++] =	"onblur='if (document.form.txt_date2.value==\"\") document.form.txt_date2.value=\"All\"; return true;'></td>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap>KHz&nbsp;</td>\n";
  out[n++] =	"        <td nowrap><input title='Minimum value' name='txt_freq1' size='8' maxlength='6' value='"+(cookie['txt_freq1']?cookie['txt_freq1']:"All")+"' "
  out[n++] =	"onblur='if (document.form.txt_freq1.value==\"\") document.form.txt_freq1.value=\"All\"; return true;'> - ";
  out[n++] =	"<input title='Maximum value' name='txt_freq2' size='8' maxlength='6' value='"+(cookie['txt_freq2']?cookie['txt_freq2']:"All")+"' "
  out[n++] =	"onblur='if (document.form.txt_freq2.value==\"\") document.form.txt_freq2.value=\"All\"; return true;'></td>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap>DX ("+get_units()+")&nbsp;</td>\n";
  out[n++] =	"        <td nowrap><input title='Minimum value' name='txt_dx1' size='8' maxlength='6' value='"+(cookie['txt_dx1']?cookie['txt_dx1']:"All")+"' "
  out[n++] =	"onblur='if (document.form.txt_dx1.value==\"\") document.form.txt_dx1.value=\"All\"; return true;'> - ";
  out[n++] =	"<input title='Maximum value' name='txt_dx2' size='8' maxlength='6' value='"+(cookie['txt_dx2']?cookie['txt_dx2']:"All")+"' "
  out[n++] =	"onblur='if (document.form.txt_dx2.value==\"\") document.form.txt_dx2.value=\"All\"; return true;'></td>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap title='Latitude (in decimal degrees from -90 to 90)\n"
  out[n++] =	"Hint: for a North of 60 Report use 60 and All'>Lat. (Decimal)&nbsp;</td>\n";
  out[n++] =	"        <td nowrap><input title='Minimum value' name='txt_lat1' size='8' maxlength='6' value='"+(cookie['txt_lat1']?cookie['txt_lat1']:"All")+"' "
  out[n++] =	"onblur='if (document.form.txt_lat1.value==\"\") document.form.txt_lat1.value=\"All\"; return true;'> - ";
  out[n++] =	"<input title='Maximum value' name='txt_lat2' size='8' maxlength='6' value='"+(cookie['txt_lat2']?cookie['txt_lat2']:"All")+"' "
  out[n++] =	"onblur='if (document.form.txt_lat2.value==\"\") document.form.txt_lat2.value=\"All\"; return true;'></td>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap title='Longitude (in decimal degrees from -180 to 180)\n"
  out[n++] =	"Hint: to show stations West of 100, use All and -100'>Lon. (Decimal)&nbsp;</td>\n";
  out[n++] =	"        <td nowrap><input title='Minimum value' name='txt_lon1' size='8' maxlength='6' value='"+(cookie['txt_lon1']?cookie['txt_lon1']:"All")+"' "
  out[n++] =	"onblur='if (document.form.txt_lon1.value==\"\") document.form.txt_lon1.value=\"All\"; return true;'> - ";
  out[n++] =	"<input title='Maximum value' name='txt_lon2' size='8' maxlength='6' value='"+(cookie['txt_lon2']?cookie['txt_lon2']:"All")+"' "
  out[n++] =	"onblur='if (document.form.txt_lon2.value==\"\") document.form.txt_lon2.value=\"All\"; return true;'></td>\n";
  out[n++] =	"      </tr>\n";
  out[n++] =	"    </table></td>\n";
  out[n++] =	"  </tr>\n";
  out[n++] =	"  <tr>\n";
  out[n++] =	"    <td class='plain'>&nbsp;</td>\n";
  out[n++] =	"  </tr>\n";
  out[n++] =	"  <tr>\n";
  out[n++] =	"    <td class='plain'><table cellpadding='1' cellspacing='0' border='1' class='r' width='100%'>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <th colspan='2'>Text List > Columns</th>";
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap width='50%'>'New' loggings&nbsp;</td>\n";
  out[n++] =	"        <td nowrap width='50%'><select name='txt_h_new'>\n"
  out[n++] =	"<option value='0'" +(cookie['txt_h_new']!='1' ? " selected" : "")+">Show</option>\n"
  out[n++] =	"<option value='1'" +(cookie['txt_h_new']!='1' ? "" : " selected")+">Hide</option>\n\n"
  out[n++] =	"</select></td>\n"
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap>Mod Offsets&nbsp;</td>\n";
  out[n++] =	"        <td nowrap><select name='txt_h_mod'>\n"
  out[n++] =	"<option value='0'" +(cookie['txt_h_mod']!='1' ? " selected" : "")+">Show</option>\n"
  out[n++] =	"<option value='1'" +(cookie['txt_h_mod']!='1' ? "" : " selected")+">Hide</option>\n\n"
  out[n++] =	"</select></td>\n"
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <td nowrap>DX per Watt&nbsp;</td>\n";
  out[n++] =	"        <td nowrap><select name='txt_h_dxw'>\n"
  out[n++] =	"<option value='0'" +(cookie['txt_h_dxw']!='1' ? " selected" : "")+">Show</option>\n"
  out[n++] =	"<option value='1'" +(cookie['txt_h_dxw']!='1' ? "" : " selected")+">Hide</option>\n\n"
  out[n++] =	"</select></td>\n"
  out[n++] =	"      </tr>\n";
  out[n++] =	"      <tr>\n";
  out[n++] =	"        <th colspan='2'><input type='button' value='Submit' onclick='go()'><input type='button' value='Cancel' onclick='window.close()'></th>";
  out[n++] =	"      </tr>\n";
  out[n++] =	"    </table></td>\n";
  out[n++] =	"  </tr>\n";
  out[n++] =	"</table>\n";
  out[n++] =	"</form>\n"
  text_options_h =		window.open('','zoneSelector', 'width=360,height=500,status=0,resizable=1,menubar=0,location=0,toolbar=0,scrollbars=0');
  text_options_h.focus();
  text_options_h.document.write(out.join(""));
  text_options_h.document.close();
}


// ************************************
// * show_time()                      *
// ************************************
function show_time() {
  var now =		new Date()
  var hhmm =		lead(now.getUTCHours())+":"+lead(now.getUTCMinutes());
  date =			format_date(now.getUTCFullYear()+""+lead(now.getUTCMonth()+1)+""+lead(now.getUTCDate()));
  var msg = 		"UTC Time: "+hhmm+" ("+((now.getUTCHours()>=utc_daylight && now.getUTCHours()<utc_daylight+4)?("Day"):("Night"))+"), Date: "+date;
  if (window.defaultStatus!=msg) {
    window.defaultStatus = msg;
  }
  setTimeout("top.show_time()",1000)
}


// ************************************
// * show_date_heading()              *
// ************************************
function show_date_heading() {
  switch (cookie['format']) {
    case "dd.mm.yyyy": return "DD.MM.YYYY"; break
    case "ddmmyyyy":   return "DD/MM/YYYY"; break
    case "mmddyyyy":   return "MM/DD/YYYY"; break
    case "yyyy-mm-dd": return "YYYY-MM-DD"; break
    default:           return "YYYYMMDD";   break
  }
}


// ************************************
// * sort functions:                  *
// ************************************
function sortBy_all_date(a,b) {
  anew = (a.all_yyyymmddhhmm ? a.all_yyyymmddhhmm : "999999999999");
  bnew = (b.all_yyyymmddhhmm ? b.all_yyyymmddhhmm : "999999999999");
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_all_date_d(a,b) {
  anew = (a.all_yyyymmddhhmm ? a.all_yyyymmddhhmm : "000000000000");
  bnew = (b.all_yyyymmddhhmm ? b.all_yyyymmddhhmm : "000000000000");
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_all_time(a,b) {
  anew = (a.all_time ? a.all_time : "9999");
  bnew = (b.all_time ? b.all_time : "9999");
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_all_time_d(a,b) {
  anew = (a.all_time ? a.all_time : "0000");
  bnew = (b.all_time ? b.all_time : "0000");
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_call(a,b) {
  if (a.call < b.call) return -1 ; if (a.call > b.call) return 1  ; return 0;
}

function sortBy_call_d(a,b) {
  if (a.call > b.call) return -1 ; if (a.call < b.call) return 1  ; return 0;
}

function sortBy_cnt(a,b) {
  if (a.cnt < b.cnt) return -1 ; if (a.cnt > b.cnt) return 1  ; return 0;
}

function sortBy_cnt_d(a,b) {
  if (a.cnt > b.cnt) return -1 ; if (a.cnt < b.cnt) return 1  ; return 0;
}

function sortBy_cyc(a,b) {	// Processed as text - because European NDBers describe cycles '2xID' etc
  anew = (a.cyc ? a.cyc : "ZZZZZZZZ");
  bnew = (b.cyc ? b.cyc : "ZZZZZZZZ");
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_cyc_d(a,b) {
  anew = (a.cyc ? a.cyc : " ");
  bnew = (b.cyc ? b.cyc : " ");
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_daid(a,b) {
  if (a.daid < b.daid) return -1 ; if (a.daid > b.daid) return 1  ; return 0;
}

function sortBy_daid_d(a,b) {
  if (a.daid > b.daid) return -1 ; if (a.daid < b.daid) return 1  ; return 0;
}

function sortBy_dir(a,b) {
  anew = (parseInt(a.dir) ? parseInt(a.dir) : 9999);
  bnew = (parseInt(b.dir) ? parseInt(b.dir) : 9999);
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_dir_d(a,b) {
  anew = (parseInt(a.dir) ? parseInt(a.dir) : 0);
  bnew = (parseInt(b.dir) ? parseInt(b.dir) : 0);
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_dx(a,b) {
  anew = (parseInt(a.dx) ? parseInt(a.dx) : 9999);
  bnew = (parseInt(b.dx) ? parseInt(b.dx) : 9999);
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_dx_d(a,b) {
  anew = (parseInt(a.dx) ? parseInt(a.dx) : 0);
  bnew = (parseInt(b.dx) ? parseInt(b.dx) : 0);
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_dxw(a,b) {
  anew = (parseFloat(a.dxw) ? parseFloat(a.dxw) : 9999);
  bnew = (parseFloat(b.dxw) ? parseFloat(b.dxw) : 9999);
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_dxw_d(a,b) {
  anew = (parseFloat(a.dxw) ? parseFloat(a.dxw) : 0);
  bnew = (parseFloat(b.dxw) ? parseFloat(b.dxw) : 0);
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_hhmm(a,b) {	// only used in text output
  if (a.hhmm < b.hhmm) return -1 ; if (a.hhmm > b.hhmm) return 1  ; return 0;
}


function sortBy_khz(a,b) {
  anew = parseFloat(a.khz); bnew = parseFloat(b.khz);
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_khz_d(a,b) {
  anew = parseFloat(a.khz); bnew = parseFloat(b.khz);
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_life_country(a,b) {
  anew = (a.cnt ? a.cnt : "ZZZ_ZZZ");
  bnew = (b.cnt ? b.cnt : "ZZZ_ZZZ");
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_life_rgn(a,b) {
  anew = a.rgn;
  bnew = b.rgn;
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_lsb(a,b) {
  anew = (parseFloat(a.lsb) ? parseFloat(a.lsb) : 9999);
  bnew = (parseFloat(b.lsb) ? parseFloat(b.lsb) : 9999);
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_lsb_d(a,b) {
  anew = (parseFloat(a.lsb) ? parseFloat(a.lsb) : 0);
  bnew = (parseFloat(b.lsb) ? parseFloat(b.lsb) : 0);
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_mm(a,b) {
  anew = (a.temp_mm ? a.temp_mm : "Z");
  bnew = (b.temp_mm ? b.temp_mm : "Z");
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_mm_d(a,b) {
  anew = (a.temp_mm ? a.temp_mm : "0");
  bnew = (b.temp_mm ? b.temp_mm : "0");
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_pwr(a,b) {
  anew = (parseInt(a.pwr) ? parseInt(a.pwr) : 9999);
  bnew = (parseInt(b.pwr) ? parseInt(b.pwr) : 9999);
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_pwr_d(a,b) {
  anew = (eval(a.pwr) ? eval(a.pwr) : 0);
  bnew = (eval(b.pwr) ? eval(b.pwr) : 0);
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_gsq(a,b) {
  if (a.gsq < b.gsq) return -1 ; if (a.gsq > b.gsq) return 1  ; return 0;
}

function sortBy_gsq_d(a,b) {
  if (a.gsq > b.gsq) return -1 ; if (a.gsq < b.gsq) return 1  ; return 0;
}

function sortBy_qth(a,b) {
  if (a.qth < b.qth) return -1 ; if (a.qth > b.qth) return 1  ; return 0;
}

function sortBy_qth_d(a,b) {
  if (a.qth > b.qth) return -1 ; if (a.qth < b.qth) return 1  ; return 0;
}

function sortBy_sta(a,b) {
  if (a.sta < b.sta) return -1 ; if (a.sta > b.sta) return 1  ; return 0;
}

function sortBy_sta_d(a,b) {
  if (a.sta > b.sta) return -1 ; if (a.sta < b.sta) return 1  ; return 0;
}

function sortBy_temp(a,b) {
  anew = (parseInt(a.temp) ? parseInt(a.temp) : 9999);
  bnew = (parseInt(b.temp) ? parseInt(b.temp) : 9999);
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_temp_d(a,b) {
  anew = (parseInt(a.temp) ? parseInt(a.temp) : 0);
  bnew = (parseInt(b.temp) ? parseInt(b.temp) : 0);
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_usb(a,b) {
  anew = (parseFloat(a.usb) ? parseFloat(a.usb) : 9999);
  bnew = (parseFloat(b.usb) ? parseFloat(b.usb) : 9999);
  if (anew < bnew) return -1 ; if (anew > bnew) return 1  ; return 0;
}

function sortBy_usb_d(a,b) {
  anew = (parseFloat(a.usb) ? parseFloat(a.usb) : 0);
  bnew = (parseFloat(b.usb) ? parseFloat(b.usb) : 0);
  if (anew > bnew) return -1 ; if (anew < bnew) return 1  ; return 0;
}

function sortBy_yyyymmddhhmm(a,b) {	// only used in text output
  if (a.yyyymmddhhmm < b.yyyymmddhhmm) return -1 ; if (a.yyyymmddhhmm > b.yyyymmddhhmm) return 1  ; return 0;
}


// ************************************
// * status_msg()                     *
// ************************************
function status_msg(what) {
  return " onmouseover='window.status=window.defaultStatus+\"      | "+what+"\";return true;' onmouseout='window.status=\"\";return true;'";
}

// Code to allow scrolling to named anchors in JS generated documents:
// From Martin Honnen - see this page:
// http://www.faqts.com/knowledge_base/view.phtml/aid/13648/fid/189
// Note that NS 4.8 works fine with the technique but still stuggles with it in this application about 1 time in every 2.

function getAnchorPosition(window_hd,anchorName) { 
  if (document.layers) {
    var anchor = eval(window_hd).document.anchors[anchorName]; 
    return { x: anchor.x, y: anchor.y }; 
  } 
  if (document.getElementById) { 
    var anchor = eval(window_hd).document.anchors[anchorName]; 
    var coords = {x: 0, y: 0 }; 
    while (anchor) { 
      coords.x += anchor.offsetLeft; 
      coords.y += anchor.offsetTop - 10; 
      anchor = anchor.offsetParent; 
    } 
    return coords; 
  } 
}

function checkScrollNecessary(window_hd,link) { 
  if (document.layers || (!document.all && document.getElementById)) { 
    var coords = getAnchorPosition(window_hd,link.hash.substring(1)); 
    eval(window_hd).scrollTo(coords.x,coords.y); 
    return false;
  } 
  return true; 
}

