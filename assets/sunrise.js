// Created by Gene Davis - Computer Support Group: Dec 6 1994 - Oct 8 2001


// Global variables:
var twAngle = -6.0;    // For civil twilight, set to -12.0 for
                       // nautical, and -18.0 for astr. twilight

var srAngle = -35.0/60.0;    // For sunrise/sunset

var sRiseT;        // For sunrise/sunset times
var sSetT;
var srStatus;

var twStartT;      // For twilight times
var twEndT;
var twStatus;

var sDIST;     // Solar distance, astronomical units
var sRA;       // Sun's Right Ascension
var sDEC;      // Sun's declination

var sLON;      // True solar longitude


//-------------------------------------------------------------
// A function to compute the number of days elapsed since
// 2000 Jan 0.0  (which is equal to 1999 Dec 31, 0h UT)
//-------------------------------------------------------------
function dayDiff2000(y,m,d){
  return (367.0*(y)-((7*((y)+(((m)+9)/12)))/4)+((275*(m))/9)+(d)-730530.0);
}


var RADEG = 180.0 / Math.PI
var DEGRAD = Math.PI / 180.0


//-------------------------------------------------------------
// The trigonometric functions in degrees

function sind(x) { return Math.sin((x)*DEGRAD); }
function cosd(x) { return Math.cos((x)*DEGRAD); }
function tand(x) { return Math.tan((x)*DEGRAD); }

function atand(x) { return (RADEG*Math.atan(x)); }
function asind(x) { return (RADEG*Math.asin(x)); }
function acosd(x) { return (RADEG*Math.acos(x)); }

function atan2d(y,x) {
  var at2 = (RADEG*Math.atan(y/x));
  if( x < 0 && y < 0)
    at2 -= 180;
  else if( x < 0 && y > 0)
    at2 += 180;
  return at2;
}


//-------------------------------------------------------------
// This function computes times for sunrise/sunset. 
// Sunrise/sunset is considered to occur when the Sun's upper
// limb is 35 arc minutes below the horizon (this accounts for
// the refraction of the Earth's atmosphere).
//-------------------------------------------------------------
function sunRiseSet(year,month,day,lat,lon) {
  return ( sunTimes( year, month, day, lat, lon, srAngle, 1) )
}



//-------------------------------------------------------------
// This function computes the start and end times of civil
// twilight. Civil twilight starts/ends when the Sun's center
// is 6 degrees below the horizon.
//-------------------------------------------------------------
function civTwilight(year,month,day,lat,lon) {
  return ( sunTimes( year, month, day, lat, lon, twAngle, 0) )
}



//-------------------------------------------------------------
// The main function for sun rise/set times
//
// year,month,date = calendar date, 1801-2099 only.
// Eastern longitude positive, Western longitude negative
// Northern latitude positive, Southern latitude negative
//
// altit = the altitude which the Sun should cross. Set to
//         -35/60 degrees for rise/set, -6 degrees for civil,
//         -12 degrees for nautical and -18 degrees for
//         astronomical twilight.
//
// sUppLimb: non-zero -> upper limb, zero -> center. Set to
//           non-zero (e.g. 1) when computing rise/set times,
//           and to zero when computing start/end of twilight.
//
// Status:  0 = sun rises/sets this day, times stored in Global
//              variables
//          1 = sun above the specified "horizon" 24 hours.
//              Rising set to time when the sun is at south,
//              minus 12 hours while Setting is set to the
//              south time plus 12 hours.
//         -1 = sun is below the specified "horizon" 24 hours.
//              Rising and Setting are both set to the time
//              when the sun is at south.
//-------------------------------------------------------------
function sunTimes(year, month, day, lat, lon, altit, sUppLimb) {
  var dayDiff    // Days since 2000 Jan 0.0 (negative before)
  var sRadius    // Sun's apparent radius
  var diuArc     // Diurnal arc
  var sSouthT    // Time when Sun is at south
  var locSidT    // Local sidereal time

  var stCode = 0     // Status code from function - usually 0
  dayDiff = dayDiff2000(year,month,day) + 0.5 - lon/360.0;	// Compute dayDiff of 12h local mean solar time
  locSidT = revolution( GMST0(dayDiff) + 180.0 + lon );		// Compute local sideral time of this moment
  sunRaDec( dayDiff );						// Compute Sun's RA + Decl at this moment
  sSouthT = 12.0 - rev180(locSidT - sRA)/15.0;			// Compute time when Sun is at south - in hours UT
  sRadius = 0.2666 / sDIST;					// Compute the Sun's apparent radius, degrees
  if ( sUppLimb != 0)            				// Do correction to upper limb, if necessary
    altit -= sRadius;
    /* Compute the diurnal arc that the Sun traverses */
    /* to reach the specified altitude altit:         */
   var cost;
   cost = ( sind(altit) - sind(lat) * sind(sDEC) ) / ( cosd(lat) * cosd(sDEC) );

   if ( cost >= 1.0 ) {
     stCode = -1;
     diuArc = 0.0;       // Sun always below altit
   }

   else if ( cost <= -1.0 ) {
     stCode = 1;
     diuArc = 12.0;      // Sun always above altit
   }

   else
     diuArc = acosd(cost)/15.0;   // The diurnal arc, hours

    /* Store rise and set times - in hours UT */
  if ( sUppLimb != 0)       // For sunrise/sunset
  {
    sRiseT = sSouthT - diuArc;

    if(sRiseT < 0)        // Sunrise day before
      sRiseT += 24;
      
    sSetT  = sSouthT + diuArc;

    if(sSetT > 24)        // Sunset next day
      sSetT -= 24;

    srStatus = stCode;
  }
  else                      // For twilight times
  {
    twStartT = sSouthT - diuArc;

    if(twStartT < 0)
      twStartT += 24;
      
    twEndT  = sSouthT + diuArc;

    if(twEndT > 24)
      twEndT -= 24;

    twStatus = stCode;
  }
} //================== sunTimes() =====================


//-------------------------------------------------------------
// This function computes the sun's spherical coordinates
//-------------------------------------------------------------
function sunRaDec(dayDiff)
{
  var eclObl;   // Obliquity of ecliptic
                // (inclination of Earth's axis)
  var x;
  var y;
  var z;

      /* Compute Sun's ecliptical coordinates */

  sunPos( dayDiff );

      /* Compute ecliptic rectangular coordinates (z=0) */

  x = sDIST * cosd(sLON);

  y = sDIST * sind(sLON);

      /* Compute obliquity of ecliptic */
      /* (inclination of Earth's axis) */

//  eclObl = 23.4393 - 3.563E-7 * dayDiff;

  eclObl = 23.4393 - 3.563/10000000 * dayDiff;  // for Opera

      /* Convert to equatorial rectangular coordinates */
      /* - x is unchanged                               */

  z = y * sind(eclObl);
  y = y * cosd(eclObl);

      /* Convert to spherical coordinates */

  sRA = atan2d( y, x );
  sDEC = atan2d( z, Math.sqrt(x*x + y*y) );

} //================= sunRaDec() =======================



//-------------------------------------------------------------
// Computes the Sun's ecliptic longitude and distance
// at an instant given in dayDiff, number of days since
// 2000 Jan 0.0.  The Sun's ecliptic latitude is not
// computed, since it's always very near 0. 
//-------------------------------------------------------------
function sunPos(dayDiff)
{
  var M;       // Mean anomaly of the Sun
  var w;       // Mean longitude of perihelion
               // Note: Sun's mean longitude = M + w
  var e;       // Eccentricity of Earth's orbit
  var eAN;     // Eccentric anomaly
  var x;       // x, y coordinates in orbit
  var y;
  var v;       // True anomaly

      /* Compute mean elements */

  M = revolution( 356.0470 + 0.9856002585 * dayDiff );

//  w = 282.9404 + 4.70935E-5 * dayDiff;
//  e = 0.016709 - 1.151E-9 * dayDiff;

  w = 282.9404 + 4.70935/100000 * dayDiff;    // for Opera
  e = 0.016709 - 1.151/1000000000 * dayDiff;  // for Opera

      /* Compute true longitude and radius vector */

  eAN = M + e * RADEG * sind(M) * ( 1.0 + e * cosd(M) );

  x = cosd(eAN) - e;
  y = Math.sqrt( 1.0 - e*e ) * sind(eAN);

  sDIST = Math.sqrt( x*x + y*y );    // Solar distance

  v = atan2d( y, x );                // True anomaly

  sLON = v + w;                      // True solar longitude

  if ( sLON >= 360.0 )
    sLON -= 360.0;                   // Make it 0..360 degrees

} //=================== sunPos() =============================


var INV360 = 1.0 / 360.0;


//-------------------------------------------------------------
// Reduce angle to within 0..360 degrees
//-------------------------------------------------------------
function revolution( x )
{
  return (x - 360.0 * Math.floor( x * INV360 ));
}


//-------------------------------------------------------------
// Reduce angle to within -180..+180 degrees
//-------------------------------------------------------------
function rev180( x )
{
  return ( x - 360.0 * Math.floor( x * INV360 + 0.5 ) );
}


//-------------------------------------------------------------
// This function computes GMST0, the Greenwhich Mean Sidereal
// Time at 0h UT (i.e. the sidereal time at the Greenwhich
// meridian at 0h UT).
// GMST is then the sidereal time at Greenwich at any time of
// the day.  GMST0 is generalized as well, and is defined as:
//
//  GMST0 = GMST - UT
//
// This allows GMST0 to be computed at other times than 0h UT
// as well.  While this sounds somewhat contradictory, it is
// very practical:
// Instead of computing GMST like:
//
//  GMST = (GMST0) + UT * (366.2422/365.2422)
//
// where (GMST0) is the GMST last time UT was 0 hours, one simply
// computes:
//
//  GMST = GMST0 + UT
//
// where GMST0 is the GMST "at 0h UT" but at the current moment!
// Defined in this way, GMST0 will increase with about 4 min a
// day.  It also happens that GMST0 (in degrees, 1 hr = 15 degr)
// is equal to the Sun's mean longitude plus/minus 180 degrees!
// (if we neglect aberration, which amounts to 20 seconds of arc
// or 1.33 seconds of time)
//-------------------------------------------------------------
function GMST0( dayDiff )
{
  var const1 = 180.0 + 356.0470 + 282.9404;

//  var const2 = 0.9856002585 + 4.70935E-5;

  var const2 = 0.9856002585 + 4.70935/100000;  // for Opera


  return ( revolution( const1 + const2 * dayDiff ) );
      
} //=================== GMST0() =========================


/* * * * * * * *    SUNRISET SCRIPT - END -  * * * * * * * * */




/* * * * * * * * * *     SUNFORM SCRIPT    * * * * * * * * * *\
*                                                             *
*  Uses input from a form to computes Sun rise/set times,     *
*  start/end of twilight, using the SUNRISET script.          *
*                                                             *
*  In English (lCode = 0)
**/
              
function checkInt(item, min, max, bText, lCode)
{
  var checkVal = parseInt(item.value)
  var returnVal = false

  var aText1 = "Please enter a number"
  var aText2 = "Please enter a number >= " + min
  var aText3 = "Please enter a number <= " + max

  if ( isNaN(checkVal) ) 
     alert(bText + ":\n" + aText1)

  else if (checkVal < min) 
    alert(bText + ":\n" + aText2)

  else if (checkVal > max) 
    alert(bText + ":\n" + aText3)

  else 
    returnVal = true

  return returnVal
  
} //================= checkInt() ===========================


function checkFloat(item, min, max, bText, lCode)
{
  var checkVal = parseFloat(item.value)
  var returnVal = false

  var aText1 = "Please enter a number"
  var aText2 = "Please enter a number >= " + min
  var aText3 = "Please enter a number <= " + max


  if ( isNaN(checkVal) ) 
      alert(bText + ":\n" + aText1)

  else if (checkVal < min) 
      alert(bText + ":\n" + aText2)

  else if (checkVal > max) 
      alert(bText + ":\n" + aText3)

  else 
      returnVal = true

  return returnVal
  
} //================= checkFloat() =========================


function formValues(form, lCode)
{
  var latText = "Latitude"
  var lonText = "Longitude"
  var yearText = "Year"
  var monthText = "Month"
  var dayText = "Date"

  if( !checkFloat(form.lat_dddd, -90.0, 90.0, latText, lCode) )
    return
    
  var lat = parseFloat(form.lat_dddd.value)

  var absLat = Math.abs(lat)

  if( !checkFloat(form.lon_dddd, -180.0, 180.0, lonText, lCode) )
    return 

  var lon = parseFloat(form.lon_dddd.value)

  var absLon = Math.abs(lon)

                      // Changed 1801 -> 1901    1998-06-29

  if( !checkInt(form.inpYear, 1901, 2099, yearText, lCode) )
    return

  var year = parseInt(form.inpYear.value)

  if( !checkInt(form.inpMonth, 1, 12, monthText, lCode) )
    return
    
  var month = parseInt(form.inpMonth.value)

  var monthMax

  if(month == 2 && (year % 4) == 0)
    monthMax = 29

  else if(month == 2) 
    monthMax = 28

  else if( (month == 4) || (month == 6) || (month == 9) ||
           (month == 11) ) 
    monthMax = 30

  else
    monthMax = 31

  if( !checkInt(form.inpDay, 1, monthMax, dayText, lCode) )
    return
    
  var day = parseInt(form.inpDay.value)

  
  sunRiseSet(year,month,day,lat,lon);

  civTwilight(year,month,day,lat,lon);


  var resultText = ""

  var twsText = "Twilight starts: "
  var tweText = "Twilight ends:   "
  var twAllText = "Twilight all night"
  var twNoText = "No twilight this day"

  var srText = "Sunrise:         "
  var ssText = "Sunset:          "
  var sAllText = "Sun is up 24 hrs"
  var sNoText = "Sun is down 24 hrs"




  resultText += "On " + year + "-"

  if(month < 10)
    resultText += "0"

  resultText += month + "-"

  if(day < 10)
    resultText += "0"

  resultText += day + "\n"
  resultText += "At "+absLat

  if(lat < 0)
    resultText += "S "

  else
    resultText += "N "

  resultText += " / "+absLon

  if(lon < 0)
    resultText += "W\n"

  else
    resultText += "E\n"
  resultText += "-----------------------\n";
  resultText += "Sunrise / Sunset UTC\n";
  resultText += "-----------------------\n";

  var twst_h = Math.floor(twStartT)
  var twst_m = Math.floor((twStartT - twst_h)*60)

  var sris_h = Math.floor(sRiseT)
  var sris_m = Math.floor((sRiseT - sris_h)*60)

  var sset_h = Math.floor(sSetT)
  var sset_m = Math.floor((sSetT - sset_h)*60)

  var twen_h = Math.floor(twEndT)
  var twen_m = Math.floor((twEndT - twen_h)*60)

  if(twStatus == 0)
  {
    resultText += twsText

    if(twst_h < 10)
      resultText += "0"

    resultText += twst_h + "."

    if(twst_m < 10)
      resultText += "0"

    resultText += twst_m + "\n"
  }

  else if(twStatus > 0 && srStatus <= 0)
  {
    resultText += twAllText + "\n"
  }

  else
  {
    resultText += twNoText + "\n"
  }


  if(srStatus == 0)
  {
    resultText += srText
    
    if(sris_h < 10)
      resultText += "0"

    resultText += sris_h + "."

    if(sris_m < 10)
      resultText += "0"

    resultText += sris_m + "\n"
    
    resultText += ssText

    if(sset_h < 10)
      resultText += "0"

    resultText += sset_h + "."

    if(sset_m < 10)
      resultText += "0"

    resultText += sset_m + "\n"
  }

  else if(srStatus > 0)
  {
    resultText += sAllText + "\n"
  }

  else
  {
    resultText += sNoText + "\n"
  }

  if(twStatus == 0)
  {
    resultText += tweText

    if(twen_h < 10)
      resultText += "0"

    resultText += twen_h + "."

    if(twen_m < 10)
      resultText += "0"

    resultText += twen_m
  }
  resultText += "\n-----------------------";
  resultText += "\n(From "+document.title.substr(0,3)+")";

  form.outpResult.value = resultText

}  //================= formValues() ========================= 

/* * * * * * * * *   SUNFORM SCRIPT - END -  * * * * * * * * */

/* * * * * * * * * *    SUNTABLE SCRIPT    * * * * * * * * * *\
*                                                             *
*  Uses the SUNRISET script to computes Sun rise/set times,   *
*  start/end of twilight, and displays them in a table.       *
*                                                             *
*  In English (lCode = 0)              *
*                                                             *
**/

function toDayValues(name, lat, lon, lCode)
{
  toDay = new Date();

  var year = toDay.getYear();

  if (year < 100)
    year += 1900;

  var month = (toDay.getMonth() + 1);

  var day =  toDay.getDate();

  sunRiseSet(year,month,day,lat,lon);

  civTwilight(year,month,day,lat,lon);

  showTable(name, lat, lon, year, month, day, lCode);
}


function showTable(name, lat, lon, year, month, day, lCode)
{
  var absLat = Math.abs(lat)
  var absLon = Math.abs(lon)

  document.write("<TABLE BORDER=1 WIDTH='100%' BGCOLOR='#ffffff'>\n")
  document.write("<TR><TD COLSPAN=2 ALIGN=CENTER>")
  document.write(name + "</TD></TR>\n")
  
  document.write("<TR><TD ALIGN=CENTER COLSPAN=2>" + absLat)

  if(lat < 0)
    document.write("S ")

  else
    document.write("N ")

  document.write(absLon)

  if(lon < 0)
    document.write("W")

  else
    document.write("E")

  document.write("</TD></TR>\n")
  

  document.write("<TR><TD COLSPAN=2 ALIGN=CENTER>" + year +"-")

  if(month < 10)
    document.write("0")

  document.write(month + "-")

  if(day < 10)
    document.write("0")

  document.write(day + "</TD></TR>\n")

  var twsText = "Twilight starts: "
  var tweText = "Twilight ends: "
  var twAllText = "Twilight all night"
  var twNoText = "No twilight this day"

  var srText = "Sunrise: "
  var ssText = "Sunset: "
  var sAllText = "Sun is up 24 hrs"
  var sNoText = "Sun is down 24 hrs"


  var twst_h = Math.floor(twStartT)
  var twst_m = Math.floor((twStartT - twst_h)*60)

  var sris_h = Math.floor(sRiseT)
  var sris_m = Math.floor((sRiseT - sris_h)*60)

  var sset_h = Math.floor(sSetT)
  var sset_m = Math.floor((sSetT - sset_h)*60)

  var twen_h = Math.floor(twEndT)
  var twen_m = Math.floor((twEndT - twen_h)*60)

  if(twStatus == 0)
  {
    document.write("<TR><TD ALIGN=RIGHT>" + twsText + "</TD>")
    document.write("<TD>")

    if(twst_h < 10)
      document.write("0")

    document.write(twst_h + ".")

    if(twst_m < 10)
      document.write("0")

    document.write(twst_m + "</TD></TR>\n")
  }

  else if(twStatus > 0 && srStatus <= 0)
  {
    document.write("<TR><TD COLSPAN=2>" + twAllText)
    document.write("</TD</TR>\n")
  }

  else
  {
    document.write("<TR><TD COLSPAN=2>" + twNoText)
    document.write("</TD</TR>\n")
  }


  if(srStatus == 0)
  {
    document.write("<TR><TD ALIGN=RIGHT>" + srText)
    document.write("</TD><TD>")
    
    if(sris_h < 10)
      document.write("0")

    document.write(sris_h + ".")

    if(sris_m < 10)
      document.write("0")

    document.write(sris_m + "</TD</TR>\n")
    
    document.write("<TR><TD ALIGN=RIGHT>" + ssText)
    document.write("</TD><TD>")

    if(sset_h < 10)
      document.write("0")

    document.write(sset_h + ".")

    if(sset_m < 10)
      document.write("0")

    document.write(sset_m + "</TD></TR>\n")
  }

  else if(srStatus > 0)
  {
    document.write("<TR><TD COLSPAN=2>" + sAllText)
    document.write("</TD</TR>\n")
  }

  else
  {
    document.write("<TR><TD COLSPAN=2>" + sNoText)
    document.write("</TD</TR>\n")
  }

  if(twStatus == 0)
  {
    document.write("<TR><TD ALIGN=RIGHT>" + tweText + "</TD>")
    document.write("<TD>")

    if(twen_h < 10)
      document.write("0")

    document.write(twen_h + ".")

    if(twen_m < 10)
      document.write("0")

    document.write(twen_m + "</TD></TR>\n")
    }
  document.write("</TABLE>")
}

/* * * * * * * * *   SUNTABLE SCRIPT - END -  * * * * * * * * */

//-->

