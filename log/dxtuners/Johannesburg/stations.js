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
STATION ("202","JB","Johannesburg","","AFS","6.19","N","1152","1152","80","-26.045","28.265","Much better USB");
STATION ("210","LL","Lanseria"," ","AFS","8.2","N","1035","1035"," ","-25.9494444444444","27.9125","Info from http://www.sapfa.org.za/navaids_list.php");
STATION ("227.5","KS","KROONSTAD"," ","AFS","10.00","N","1032","1026","250","-27.6788888888889","27.2872222222222"," ");
STATION ("230","WR","Wonderboom","","AFS","4.35","N","1024","1024","100","-25.6536111111111","28.2425","Much better USB");
STATION ("257","PZ","PIETERMARITZBURG"," ","AFS","4.67","N","","1130","100","-29.7","30.45"," ");
STATION ("257.5","WB","WONDERBOOM","","AFS","4.77","N","1041","1041","100","-25.6561111111111","28.2991666666667"," ");
STATION ("260","GG","GEORGE"," ","AFS","7.01","N","1034","","1000","-34.0019444444444","22.4855555555556","Much better on LSB");
STATION ("270","LA","Lanseria","","AFS","4.35","N","1027","1027","100","-26.0102777777778","27.8394444444444","Much better USB");
STATION ("275","CL","CAROLINA"," ","AFS","3.89","N","1003","1003","250","-26.0919444444444","30.0963888888889","Continuous ID");
STATION ("277.5","JWN","JWANENG","","BOT","6.74","N","1019","1019","100","-24.6005555555556","24.6830555555556"," ");
STATION ("280","KD","KLERKSDORP"," ","AFS","?","N","1017","1024","50","-26.8866666666667","26.7227777777778"," ");
STATION ("280","WH","WINDHOEK"," ","NMB","7.41","N","1018","1039","100","-22.4841666666667","17.4702777777778","Best on LSB");
STATION ("287.5","RDW","RAND","","AFS","?","N","","?","30","-26.2672222222222","28.1625"," ");
STATION ("290","MM","MAFIKENG"," ","AFS","12.36","N","399","","","-25.8769444444444","25.5108333333333"," ");
STATION ("305","MH","MAFIKENG"," ","AFS","11.87","N","394","","100","-25.7413888888889","25.5747222222222"," ");
STATION ("307.5","RD","RAND","","AFS","10.13","N","1118","1118","50","-26.3138888888889","28.0897222222222"," ");
STATION ("315","PJ","PORT ST JOHNS","","AFS","8.52","N"," ","1027","250","-31.6355555555556","29.5502777777778"," ");
STATION ("325","LE","LUSAKA"," ","ZMB","8.66","N","1019","1024"," ","-15.3408333333333","28.5322222222222"," ");
STATION ("327","BR","BEIRA"," ","MOZ","3.67","N","1019","1017","","-19.7813888888889","34.85"," ");
STATION ("337.5","RA","RAND","","AFS","7.44","N"," ","1023","100","-26.4238888888889","28.2636111111111"," ");
STATION ("345","LW","LANGEBAANWEG","","AFS","8.57","N","1020"," ","3000","-32.9808333333333","18.1488888888889"," ");
STATION ("345","VAL","VAL","","AFS","3.62","N"," ","1012","100","-26.7991666666667","28.9227777777778","Continuous ID");
STATION ("365","KM","KIMBERLEY"," ","AFS","10.90","N","1107","1094","1000","-28.7919444444444","24.7808333333333"," ");
STATION ("370","VL","VILANKULO"," ","MOZ","5.26","N","1017","?","","-21.9675","35.2833333333333"," ");
STATION ("372.5","GC","GRAND CENTRAL","","AFS","4.31","N","1099","1106","100","-25.9919444444444","28.1402777777778","Miskeying as OGCI");
STATION ("378","LH","LILONGWE"," ","MWI","7.82","N","","1024","","-13.8438888888889","33.8475"," ");
STATION ("385","MT","MEYERTON","","AFS","3.42","N","1014","1015","250","-26.5572222222222","28.0325","Keys as MTI");
STATION ("400","IN","INHAMBANE"," ","MOZ","3.92","N","770","770"," ","-23.8672222222222","35.4"," ");
STATION ("405","PK","KRUGER"," ","AFS","5.42","N","398"," ","100","-25.3833333333333","31.1088888888889","Far better on LSB");
STATION ("417.5","VM","VENETIA","","AFS","?","N"," ","?","25","-22.4569444444444","29.3041666666667"," ");
STATION ("420","JN","Johannesburg"," ","AFS","3.16","N","1027","1028","","-26.2211111111111","28.2308333333333","Data from http://www.sapfa.org.za/navaids_list.php");
STATION ("427.5","OB","OVERBERG"," ","AFS","8.71","N","1023","1017","400","-34.5955555555556","20.2847222222222","Huge signal on LSB");
STATION ("442.5","ORI","PIETERMARITZBURG","","AFS","4.64","N"," ","1030","50","-29.5644444444444","30.4458333333333"," ");
STATION ("445","JA","Johannesburg"," ","AFS","","N","","","","-26.0763888888889","28.2755555555556"," ");
STATION ("457.5","SMH","ULUNDI"," ","AFS","6.25","N","1024","1020","100","-28.2352777777778","31.4813888888889"," ");
STATION ("460","VV","VEREENIGING","","AFS","4.99","N","1009","1009","50","-26.575","27.9583333333333"," ");
STATION ("465","RI","RICHARDS BAY"," ","AFS","7.93","N","1045","1034","100","-28.6847222222222","32.1358333333333"," ");
STATION ("472.5","LU","LOUIS TRICHARDT","","AFS","8.36","N","1021","1010","100","-23.1602777777778","29.5955555555556","Best on LSB");
STATION ("480","PLG","PILANESBERG"," ","AFS","10.77","N","1041","1044","100","-25.3955555555556","27.1216666666667","Far better on LSB");
STATION ("485","LS","LOUIS TRICHARDT"," ","AFS","6.12","N","1044","1010","100","-23.1597222222222","29.725"," ");
STATION ("485","UR","MARGATE","","AFS","3.71","N","1190","1196","500","-30.8041666666667","30.3694444444444","Continuous ID");
