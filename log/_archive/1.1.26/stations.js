// ***********************************************************************
// * FILE HEADER:                                                        *
// ***********************************************************************
// * Filename:    stations.js                                            *
// * Author:      Martin Francis (martin@classaxe.com)                   *
// ***********************************************************************
// * This is an editable file containing actual station data.            *
// *                                                                     *
// * For your own use, you will probably need to add additional stations *
// * to this list if your log contains stations not already listed here. *
// *                                                                     *
// * Put station details in the following format:                        *
// * STATION(khz,call,qth,ste,cnt,cyc,daid,lsb,usb,pwr,lat,lon,notes);   *
// *                                                                     *
// * Each field should be enclosed with quotes and set to "" if unknown. *
// * For any given beacon:                                               *
// *   KHz     is the frequency of the carrier;                          *
// *   call    is the callsign;                                          *
// *   qth     is the town in which the beacon is located;               *
// *   ste     is the state or province abbreviation (eg MI = Michigan)  *
// *           or "" if not applicable (e.g. Bahamas)                    *
// *   cnt     is the ITU country code;                                  *
// *   cyc     is the number of seconds between repetitions of the call  *
// *   daid    stands for 'Dash after ID' and is either "Y" or "N"       *
// *   lsb     is the offset of the lower sideband from the carrier      *
// *           (Note Canadian NDBs are USB only, for these set to "")    *
// *   usb     is the offset of the upper sideband from the carrier      *
// *   pwr     is the power in watts of the transmitter                  *
// *   lat     is the decimal latitude value (S values are negative)     *
// *   lon     is the decimal longitude value (W values are negative)    *
// *   notes   These notes will show with each logging of the station.   *
// *                                                                     *
// * OBTAINING DATA:                                                     *
// * I obtained much of the data included here from Alex Wiecek's NDB    *
// * beacon program which you can download from his site at:             *
// * http://www.ve3gop.com                                               *
// *                                                                     *
// * Other data came from Micael Oexener's excellent North American NDB  *
// * nadbook and the AirNav web site at http://www.airnav.com.           *
// *                                                                     *
// * Use the NDB WEBLOG Station Editor to modify or add to this data.    *
// ***********************************************************************
STATION ("198","DIW","Dixon","NC","USA","10.02","N","1050","1040","2000","34.5683333333333","-77.4527777777778"," ",1);
