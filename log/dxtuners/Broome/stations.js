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
STATION ("260","PD","PORT HEDLAND","WE","AUS","9.15","N","","1025","","-20.3891666666667","118.642222222222"," ");
STATION ("266","HLC","HALLS CREEK","WE","AUS","9.3","N","1030","","","-18.23","127.666388888889"," ");
STATION ("320","BRM","Broome","WE","AUS","11.1","N","1021","1021","50","-17.9375","122.208055555556"," ");
STATION ("332","DBY","DERBY","WE","AUS","9.8","N","","1006","","-17.3569444444444","123.666944444444"," ");
STATION ("350","CIN","Curtin","WE","AUS","8.95","N","1022","1024"," ","-17.5741666666667","123.831111111111"," ");
STATION ("365","OL","Balikpapan"," ","INS","8.40","N","1025","1016"," ","-1.25194444444444","116.910833333333"," ");
STATION ("372","WYM","WYNDHAM","WE","AUS","9.95","N","","1010","","-15.5097222222222","128.151388888889"," ");
STATION ("372","GIG","Gingin","WE","AUS","10.3","N","1027","1023","1000","-31.4597222222222","115.865555555556"," ");
STATION ("375","OJ","Hasanuddin"," ","INS","10.25","N","1042","1026"," ","-5.08222222222222","119.518611111111","Steve Razlaff wrotes: 'Tentative new one- Indonesia.  Slow ident with either a pause betwe");
STATION ("385","OK","Kupang","","INS","8.65","N"," ","1019"," ","-10.1669444444444","123.674444444444"," ");
STATION ("391","KO","Dili","","TMP","10.45","N","1035"," "," ","-8.55194444444444","125.520277777778"," ");
STATION ("392","GIB","GIBB RIVER","WE","AUS","9.15","N","1037","","50","-16.4294444444444","126.431388888889"," ");
STATION ("407","LTN","LAVERTON","WE","AUS","8.9","N","1031","","","-28.6122222222222","122.4225"," ");
STATION ("407","FTZ","FITZROY CROSSING","WE","AUS","9.8","N","1014","","","-18.1847222222222","125.556944444444"," ");
