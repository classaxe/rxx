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
STATION ("198","DIW","Dixon","NC","USA","10.0","N","1050","1040","2000","34.5683333333333","-77.4527777777778"," ");
STATION ("204","LCQ","LAKE CITY","FL","USA","?","N","","?","25","30.1852777777778","-82.5783333333333"," ");
STATION ("206","GLS","Galveston","TX","USA","10.0","N","1023","1020","2000","29.3338888888889","-94.7561111111111"," ");
STATION ("206","VNC","VENICE","FL","USA","?","N","","?","25","27.0613888888889","-82.4305555555556"," ");
STATION ("216","CLB","Wilmington / Carolina Beach","NC","USA","10.5","N","1035","1030"," ","34.1061111111111","-77.9613888888889"," ");
STATION ("221","OR","HERNY","FL","USA","?","N","","?","","28.5072222222222","-81.4341666666667"," ");
STATION ("227","LA","WIREY","FL","USA","?","N","","?","","27.9352777777778","-82.0755555555556"," ");
STATION ("242","PJN","'Plantation'  Fort Lauderdale","FL","USA"," ","N","1035","1034"," ","26.1322222222222","-80.2186111111111"," ");
STATION ("245","JYL","Sylvania","GA","USA"," ","N"," ","1020","25","32.6488888888889","-81.5936111111111"," ");
STATION ("248","FRT","'Fairmont'  Spartanburg","SC","USA"," ","N","1035","1032","400","34.9022222222222","-81.9847222222222"," ");
STATION ("253","RHZ","ZEPHYRHILLS","FL","USA","?","N","","?","25","28.2269444444444","-82.1569444444444"," ");
STATION ("260","MTH","MARATHON","FL","USA","?","N","","?","","24.7116666666667","-81.0952777777778"," ");
STATION ("261","CHN","WAUCHULA","FL","USA","?","N","","?","","27.51","-81.8833333333333"," ");
STATION ("269","GN","WYNDS","FL","USA","?","N","","?","25","29.67","-82.1719444444444"," ");
STATION ("270","TPF","'Knight'  Tampa","FL","USA","7.8","N","1040","1032"," ","27.9083333333333","-82.4541666666667"," ");
STATION ("275","FPR","Fort Pierce","FL","USA","8.1","N","1035","1037","49","27.4866666666667","-80.3730555555556"," ");
STATION ("280","MPG","Progreso  (Yucatan)","","MEX","6.5","N","1025","1017","1000","21.2733333333333","-89.7136111111111"," ");
STATION ("280","MQW","Mc. Rae","GA","USA","6.8","N"," ","1013","25","32.0944444444444","-82.8836111111111"," ");
STATION ("326","PKZ","'Pickens'  Pensacola","FL","USA"," ","N","1055","1025","400","30.4369444444444","-87.1783333333333"," ");
STATION ("326","ZEF","'Zephyr'  Elkin","NC","USA","7.7","N","1020","1018"," ","36.3130555555556","-80.7233333333333"," ");
STATION ("329","CH","'Ashly'  Charleston","SC","USA"," ","N","1045","1026","400","32.9761111111111","-80.0972222222222"," ");
STATION ("329","ISM","KISSIMMEE","FL","USA","?","N","","?","49","28.2891666666667","-81.4341666666667"," ");
STATION ("332","FIS","'Fish Hook'  Key West","FL","USA"," ","N","1045","1038"," ","24.5483333333333","-81.7863888888889"," ");
STATION ("332","HEG","'Herlong'  Jacksonville","FL","USA"," ","N"," ","1015"," ","30.2763888888889","-81.8088888888889"," ");
STATION ("335","LEE","LEESBURG","FL","USA","?","N","","?","25","28.8180555555556","-81.8072222222222"," ");
STATION ("338","FJ","Luuce","FL","USA"," ","N","1060","1000"," ","27.4966666666667","-80.4744444444444"," ");
STATION ("340","JES","SLOVER","GA","USA","?","N","","?","25","31.5522222222222","-81.8872222222222"," ");
STATION ("341","FM","CALOO","FL","USA","?","N","","?","","26.5161111111111","-81.95"," ");
STATION ("344","JA","'Dinns'  Jacksonville","FL","USA","7.4","N","1067","1006","400","30.4613888888889","-81.7997222222222"," ");
STATION ("344","ZIY","George Town  (Grand Cayman Isl","","CYM"," ","N","1026","1040"," ","19.2866666666667","-81.3866666666667","Owen Roberts Intl Apt;  (Its not -Georgetown)");
STATION ("346","PCM","Plant City","FL","USA","8.4","N","1040","1035","25","28.0022222222222","-82.1566666666667"," ");
STATION ("347","PA","PRINCE ALBERT","SK","CAN","11","N","","400","25w","53.2180555555556","-105.795277777778"," ");
STATION ("348","UHA","HAVANA","","CUB","?","N","","?","100","22.9327777777778","-82.4922222222222"," ");
STATION ("350","DF","Deer Lake","NL","CAN"," ","Y"," ","1020","1000","49.18","-57.4572222222222"," ");
STATION ("350","LE","'Leevy'  Raleigh","NC","USA","6.0","N","1040","1023","400","35.9272222222222","-78.7216666666667"," ");
STATION ("356","PB","'Rubin'  West Palm Beach","FL","USA"," ","N","1034","1043"," ","26.6875","-80.21"," ");
STATION ("357","EYA","'Eastport'  Jacksonville","FL","USA","6.5","N"," ","1009","25","30.4233333333333","-81.6091666666667"," ");
STATION ("360","HIT","SANDERVILLE","GA","USA","","N","","1020","","33.0208333333333","-82.9583333333333"," ");
STATION ("360","PI","CAPOK","FL","USA","?","N","","?","","27.995","-82.7036111111111"," ");
STATION ("360","PN","Port-Menier / Ile Anticosti","QC","CAN","10.4","Y"," ","400","500","49.8375","-64.3861111111111"," ");
STATION ("363","RNB","'Rainbow'  Millville","NJ","USA","8.2","N","1059","1040","50","39.4180555555556","-75.135"," ");
STATION ("365","DYB","'Dorchester Co'  Summerville","SC","USA"," ","N","1050","1012"," ","33.0611111111111","-80.2772222222222"," ");
STATION ("366","YMW","Maniwaki","QC","CAN","10.2","Y"," ","400","500","46.2075","-75.9563888888889"," ");
STATION ("368","TP","COSME","FL","USA","?","N","","?","","28.0852777777778","-82.525"," ");
STATION ("376","BHC","Baxley","GA","USA","15.0","N","1020","1016"," ","31.7119444444444","-82.39","AWOS-3");
STATION ("376","ZIN","Matthew Town  (Great Inagua Is","","BAH"," ","N","1050","1030","400","20.9597222222222","-73.6775","Matthew Town Apt");
STATION ("379","TL","'Wakul'  Tallahassee","FL","USA"," ","N","1045","1014","400","30.3261111111111","-84.3580555555556"," ");
STATION ("388","AM","'Picny'  Tampa","FL","USA"," ","N","1055","1026","400","27.8611111111111","-82.5455555555556"," ");
STATION ("391","DDP","San Juan / Dorado / Luiz Munoz","","PTR"," ","N","1020","1020","2000","18.4680555555556","-66.4122222222222","3kW Rptd - 200' Vert.");
STATION ("392","VEP","Vero Beach","FL","USA"," ","N","1040","1020","49","27.6638888888889","-80.4194444444445"," ");
STATION ("404","CKI","Kingstree","SC","USA","6.7","N"," ","1049","25","33.7175","-79.8547222222222","Fred Mooney (2004-10-24)");
STATION ("407","BZ","'Bullo'  Statesboro","GA","USA","5.62","N","1012","1026","25","32.4175","-81.6616666666667"," ");
STATION ("408","SFB","SANFORD","FL","USA","?","N","","?","","28.7847222222222","-81.2430555555556"," ");
STATION ("415","CBC","West End  (Cayman Brac)","","CYM","8.5","N","1023","1022","1000","19.6897222222222","-79.8566666666667","Gerrard Smith Intl Apt");
STATION ("417","EVB","NEW SMYRNA BEACH","FL","USA","?","N","","?","","29.0544444444444","-80.9408333333333"," ");
STATION ("423","OC","JUMPI","FL","USA","?","N","","?","","29.0563888888889","-82.2230555555556"," ");
STATION ("526","ZLS","Stella Maris- Long Island","","BAH","10.2","N","1044","990"," ","23.5805555555556","-75.2638888888889"," ");
