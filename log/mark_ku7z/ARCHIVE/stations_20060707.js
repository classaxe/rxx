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
// * I obtained much of the data included here from Alex Weicek's NDB    *
// * beacon program which you can download from his site at:             *
// * http://members.rogers.com/wiecek6010                                *
// *                                                                     *
// * Other data came from Micael Oexener's excellent North American NDB  *
// * nadbook and the AirNav web site at http://www.airnav.com.           *
// *                                                                     *
// * Use the NDB WEBLOG Station Editor to modify or add to this data.    *
// ***********************************************************************
STATION ("319","VAR","Varhaug","","NOR","8.2","N","","","25","58.6269","5.6281","");
STATION ("326","RSH","Rush","","IRL","","N","","","","53.5117","-6.1103","");
STATION ("327","POR","Porto","","POR","8.4","N","","","","41.3481","-8.7081","");
STATION ("328","CL","Carlisle","","ENG","8.6","N","","400","","54.9403","-2.8039","");
STATION ("334","GMN","Gormanston","","IRL","","N","","","","53.6481","-6.2267","");
STATION ("338","PST","Porto Santo","","POR","7.1","N","","","2000","33.0683","-16.3581","");
STATION ("350","GLG","Glasgow","","SCT","","N","","","25","55.9242","-4.3358","");
STATION ("337","MY","Myggenes","","FRO","12","N","","325","","62.1067","-7.5875","(N of 60) <a href='http://www.classaxe.com/dx/ndb/mp3/337_my_1.mp3' target='_blank'>MP3</a>, <a href='http://www.classaxe.com/dx/ndb/mp3/337_my_1.mp3' target='_blank'>Double-speed MP3</a>");
STATION ("341","EDN","Edinburgh","","SCT","7.5","N","","","25","55.9783","-3.2853","");
STATION ("350","LAA","Laanila","","FIN","8.5","N","","","","64.9661","25.2111","(N of 60)");
STATION ("352","NT","Newcastle","","ENG","10.7","N","","","90","55.0503","-1.6425","");
STATION ("355","PIK","Prestwick","","SCT","","N","","","","55.5058","-4.5772","");

STATION ("284","#430","Mizen Head"," ","IRL"," ","N"," "," "," ","51.4511111111111","-9.82194444444444","DGPS; Ref ID: 660,661; 100 baud");
STATION ("285","#350","Cabo Machichaco"," ","ESP","","N","","","","43.45","-2.75","DGPS; Ref ID: 500; 100 baud");
STATION ("285.5","#443","Earls Hill / Stirling"," ","SCT"," ","N"," "," "," ","56.0666666666667","-4.06666666666667","DGPS; Ref ID: 693; 100 Baud");
STATION ("287.5","#486","Porto Santo"," ","MDR","","N","","","","33.0666666666667","-16.35","DGPS; Ref ID: 486/487; 200 baud");
STATION ("288.5","#435","Tory Island Lt."," ","IRL"," ","N"," "," "," ","55.2666666666667","-8.25","DGPS; Ref ID 435; 100 baud");
STATION ("289","#351","Cabo Mayor"," ","ESP","","N","","","","43.4","-4.41666666666667","DGPS; Ref ID: 502; 100 baud");
STATION ("290","#452","Blavandshuk Lt"," ","DNK","","N","","","","55.5586111111111","8.08444444444445","DGPS; Ref ID: 705/706; 100 baud");
STATION ("290.5","#447","Flamborough Head Lt."," ","ENG"," ","N"," "," "," ","54.1255555555556","-9.05555555555555E-02","DGPS; Ref ID: 687/695; 100 baud");
STATION ("291","#439","Wormleighton"," ","ENG"," ","N"," "," "," ","52.2052777777778","-1.375","DGPS; Ref ID 691; 100 baud");
STATION ("291.5","#445","Sumburgh Head"," ","SHE"," ","N"," "," "," ","59.85","-1.26666666666667","DGPS; Ref ID: 685/695; 100 baud");
STATION ("293","#432","Loop Head Lt."," ","IRL"," ","N"," "," "," ","52.5677777777778","-9.94138888888889","DGPS; Ref ID 665/666; 100 baud");
STATION ("295","#360","Cabo Penas"," ","ESP"," ","N"," "," "," ","43.6497222222222","-5.85","DGPS; Ref ID: 504; 100 baud");
STATION ("296","#354","Cabo Finisterre"," ","ESP","","N","","","","42.8833333333333","-9.26666666666667","DGPS; Ref ID: 508; 100 baud");
STATION ("296","#453","Skagen West Lt"," ","DNK","","N","","","","57.7497222222222","10.5963888888889","DGPS; Ref ID: 710/711; 100 baud");
STATION ("297","#446","Girdle Ness"," ","SCT"," ","N"," "," "," ","57.1330555555556","-2.05","DGPS; Ref ID 686/696; 100 baud");
STATION ("297.5","#442","Point Lynas Lt."," ","WLS"," ","N"," "," "," ","53.4266666666667","-4.29138888888889","DGPS; Ref ID: 682/693; 100 baud");
STATION ("298.5","#492","Dune Helgoland"," ","DEU"," ","N"," "," "," ","54.1830555555556","7.88305555555556","DGPS; Ref ID: 762; 100 baud");
STATION ("299","#330","Gatteville"," ","FRA"," ","N"," "," "," ","49.7","-1.26583333333333","DGPS; Ref ID: 460; 100 baud");
STATION ("301","#462","Gilze Rijen"," ","HOL","","N","","","","51.6166666666667","4.93333333333333","DGPS; Ref ID: 426 (real ref ID: 655); 200 baud");
STATION ("304","#503","Lista"," ","NOR"," ","N"," "," "," ","58.1080555555556","6.57","DGPS; Ref ID: 783/813; 100 baud");

STATION ("292","#460","Holmsjö"," ","SWE"," ","N"," "," "," ","56.4327777777778","15.65","DGPS; Ref ID: 720; 100 baud");
STATION ("293.5","#400","Porkkala Pilot Station"," ","FIN"," ","N"," "," "," ","59.9805555555556","24.4030555555556","DGPS; Red ID: 600; 100 baud");
STATION ("295.5","#444","Butt Of Lewis"," ","SCT"," ","N"," "," "," ","58.5166666666667","-6.26666666666667","DGPS; Ref ID: 684/694; 100 baud");
STATION ("296.5","#469","Gothenburg"," ","SWE"," ","N"," "," "," ","57.6163888888889","11.9663888888889","DGPS; Ref ID: 736; 100 baud");
STATION ("298","#468","Nynäshavn"," ","SWE"," ","N"," "," "," ","58.9405555555556","17.9544444444444","DGPS; Ref ID: 734; 100 baud");
STATION ("299.5","#463","Skutskar"," ","SWE"," ","N"," "," "," ","60.6163888888889","17.4330555555556","DGPS; Ref ID 726; 100 baud");
STATION ("301.5","#404","Turku"," ","FIN"," ","N"," "," "," ","60.4330555555556","22.2163888888889","DGPS; Ref ID: 604; 100 baud");
STATION ("302","#467","Hjortens Udde"," ","SWE"," ","N"," "," "," ","58.6330555555556","12.6663888888889","DGPS; Ref ID: 733; 100 baud");
STATION ("303.5","#493","Zeven"," ","DEU","","N","","","","53.2833333333333","9.25","DGPS; Ref ID: 763; 100 baud");
STATION ("305.5","#341","Sagres"," ","POR","","N","","","","37.0333333333333","-9","DGPS; Ref ID: 482/483; 200 baud");
STATION ("307.5","#440","St Catherine's Point Lt"," ","ENG","","N","","","","50.5752777777778","-1.29666666666667","DGPS; Ref ID: 680/690; 100 baud");
STATION ("309.5","#689","Nash Point Lt"," ","WLS","","N","","","","51.4005555555556","-3.55111111111111","DGPS; Ref ID: 689/699; 100 baud");
STATION ("310","#336","Cap Ferret"," ","FRA"," ","N"," "," "," ","44.5663888888889","-1.25","DGPS; Ref ID: 466; 100 baud");
STATION ("310.5","#500","Faerder Lt"," ","NOR","","N","","","","59.0272222222222","10.5261111111111","DGPS; Ref ID: 780/810; 100 baud");
STATION ("311.5","#340","Cabo Carvoeiro Lt"," ","POR","","N","","","","39.3588888888889","-9.40666666666667","DGPS; Ref ID: 480/481; 200 baud");
STATION ("312.5","#425","Hoek van Holland"," ","HOL","","N","","","","51.9816666666667","4.11388888888889","DGPS; Ref ID: 425 (Real ref ID: 650/651); 200 baud");
STATION ("313.5","#366","Cabo San Sebastian"," ","ESP","","N","","","","41.8833333333333","3.2","DGPS; Ref ID: 366 (Real Ref ID: 532); 100 baud");
STATION ("314","#507","Utvaer Lt."," ","NOR"," ","N"," "," "," ","61.0408333333333","4.51916666666667","DGPS; Ref ID: 787/817; 100 baud");
STATION ("316","OE","Dublin"," ","IRL","8.48","N","380","420"," ","53.43","-6.42861111111111"," ");
STATION ("319","VAR","Varhaug"," ","NOR","8.09","N","386","417","25","58.6255555555556","5.63027777777778"," ");
