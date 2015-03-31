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
STATION ("220","BX","Blanc Sablon  (Lourdes de Blan","QC","CAN","10.61","Y"," ","407","2000","51.4213888888889","-57.2030555555556"," ");
STATION ("263","QY","Sydney","NS","CAN","10.25","Y"," ","404","500","46.2113888888889","-59.9755555555556"," ");
STATION ("267","FNO","Foyno"," ","NOR","8.37","N","377","379","25","59.3716666666667","5.15888888888889"," ");
STATION ("270","FLO","Santa Cruz Das Flores"," ","AZR","8.45","N","1004","1037"," ","39.4375","-31.2080555555556"," ");
STATION ("274","SAL","Sal Island / Amilcar Cabral"," ","CPV","9.90","Y","0","9","3000","16.7016666666667","-22.9486111111111"," ");
STATION ("275","VG","Vaage"," ","NOR","10.07","N","396","404"," ","59.2816666666667","5.36333333333333"," ");
STATION ("280","QX","Gander","NL","CAN","10.13","Y"," ","408","1000","48.9641666666667","-54.6680555555556","");
STATION ("281","CA","Cartwright","NL","CAN","10.33","Y"," ","405","1000","53.7105555555556","-57.0177777777778"," ");
STATION ("282","LA","Lyneham"," ","ENG","10.02","N","400","399","25","51.5083333333333","-2.00583333333333"," ");
STATION ("283.5","#481","Dziwnow"," ","POL"," ","N"," "," "," ","54.0288888888889","14.7358333333333","DGPS; Ref ID: 741/742; 100bps");
STATION ("284","#430","Mizen Head"," ","IRL"," ","N"," "," "," ","51.4511111111111","-9.82194444444444","DGPS; Ref ID: 660/661; 100 bps");
STATION ("285","#350","Cabo Machicharo"," ","ESP"," ","N"," ","0"," ","43.45","-2.75","DGPS; Ref ID: 500; 100 bps");
STATION ("285.5","#443","Earls Hill / Stirling"," ","SCT"," ","N"," "," "," ","56.0666666666667","-4.06666666666667","DGPS; Ref ID: 693; 100 bps");
STATION ("286.5","#513","Skomvaer Lt. / Roest Isl."," ","NOR"," ","N"," "," "," ","67.4186111111111","11.8830555555556","DGPS; Ref ID: 793/823; 100bps");
STATION ("287","#406","Klamila"," ","FIN"," ","N"," "," "," ","60.5","27.4327777777778","DGPS; Ref ID: 606; 100bps");
STATION ("287","#416","Skardsfjr Lt.","","ISL"," ","N"," "," "," ","63.5163888888889","-17.9830555555556"," ");
STATION ("287.5","#343","Porto Santo"," ","MDR"," ","N"," "," "," ","33.0625","-16.375","DGPS; Ref ID: 486/487; 200 bps");
STATION ("288","#942","Cape Ray","NL","CAN"," ","N","0","0"," ","47.6330555555556","-59.2330555555556","DGPS; Ref ID: 340/341; 200 bps");
STATION ("288.5","#435","Tory Island Lt."," ","IRL"," ","N"," "," "," ","55.2666666666667","-8.25","DGPS; Ref ID: 670/671; 100bps");
STATION ("289","#351","Cabo Mayor"," ","ESP"," ","N"," "," "," ","43.4","-4.41666666666667","DGPS; Ref ID: 502; 100bps");
STATION ("289","#806","Driver","VA","USA"," ","N","0","0"," ","36.95","-76.55","DGPS; Ref ID: 12/13; 100 bps");
STATION ("289.5","#451","Hammarodde Lt. Bornholm"," ","DNK"," ","N"," "," "," ","55.31","14.7783333333333","DGPS; Ref ID: 700/701; 100bps");
STATION ("290","#452","Blavandshuk Lt"," ","DNK"," ","N"," "," "," ","55.5586111111111","8.08444444444445","DGPS; Ref ID: 705/706; 100 bps");
STATION ("290.5","#447","Flamborough Head Lt."," ","ENG"," ","N"," "," "," ","54.1255555555556","-9.05555555555555E-02","DGPS; Ref ID: 687/697; 100bps");
STATION ("291","#439","Wormleighton"," ","ENG"," ","N"," "," "," ","52.2052777777778","-1.375","DGPS; Ref ID: 691; 100bps");
STATION ("291.5","#445","Sumburgh Head"," ","SHE"," ","N"," "," "," ","59.85","-1.26666666666667","DGPS; Ref ID: 685/695; 100bps");
STATION ("292","#460","Holmsj�"," ","SWE"," ","N"," "," "," ","56.4327777777778","15.65","DGPS; Ref ID: 720; 100bps");
STATION ("292.5","BA","Estaca de Bares"," ","ESP","35.08","Y","0","0"," ","43.7833333333333","-7.68333333333333","2xID+25st");
STATION ("293","#353","Estaca de Bares"," ","ESP","","N","","","","43.7833333333333","-7.68333333333333","DGPS; Ref ID: 506; 100bps");
STATION ("293","#432","Loop Head Lt."," ","IRL"," ","N"," "," "," ","52.5677777777778","-9.94138888888889","DGPS; Ref ID: 665/666; 100bps");
STATION ("293","#466","Kullen High Lt"," ","SWE","","N","","","","56.3016666666667","12.4558333333333","DGPS; Ref ID: 732; 100baud");
STATION ("293.5","#400","Porkkala Pilot Station"," ","FIN"," ","N"," "," "," ","59.9805555555556","24.4030555555556","DGPS; Ref ID: 600; 100bps");
STATION ("294","#428","Vlieland Lt"," ","HOL"," ","N"," "," "," ","53.3","5.06666666666667","DGPS; Ref ID: 655/656; 100bps");
STATION ("295","#352","Cabo Penas"," ","ESP"," ","N"," "," "," ","43.6455555555556","-5.875","DGPS; Ref ID: 504; 100bps");
STATION ("295","#817","Isabela","","PTR"," ","N","0","0"," ","18.4616666666667","-67.0666666666667","DGPS; Ref ID: 34/35; 100 bps");
STATION ("295.5","#444","Butt Of Lewis"," ","SCT"," ","N"," "," "," ","58.5166666666667","-6.26666666666667","DGPS; Ref ID: 684/694; 100bps");
STATION ("296","#354","Cabo Finisterre"," ","ESP"," ","N"," "," "," ","42.8833333333333","-9.26666666666667","DGPS; Ref ID: 508; 100bps (+ #352)");
STATION ("296","#453","Skagen West Lt"," ","DNK"," ","N"," "," "," ","57.7497222222222","10.5963888888889","DGPS; Ref ID: 710/711; 100 bps");
STATION ("296.5","#469","Gothenburg"," ","SWE"," ","N"," "," "," ","57.6163888888889","11.9663888888889","DGPS; Ref ID: 736; 100bps");
STATION ("296.5","FI","CABO FINISTERRE"," ","ESP","34.83","Y","0","0","","42.8833333333333","-9.27","2xID+25.35st");
STATION ("297","#446","Girdle Ness"," ","SCT"," ","N"," "," "," ","57.1330555555556","-2.05","DGPS; Ref ID: 686/696; 100bps");
STATION ("297.5","#442","Point Lynas Lt."," ","WLS"," ","N"," "," "," ","53.4266666666667","-4.29138888888889","DGPS; Ref ID: 682/692; 100bps");
STATION ("298","#468","Nyn�shavn"," ","SWE"," ","N"," ","8"," ","58.9405555555556","17.9544444444444","DGPS; Ref ID: 734; 100bps");
STATION ("298.5","#492","Dune Helgoland"," ","DEU"," ","N"," "," "," ","54.1830555555556","7.88305555555556","DGPS; Ref ID: 762; 100 bps");
STATION ("299","#330","Gatteville"," ","FRA"," ","N"," "," "," ","49.7","-1.26583333333333","DGPS; Ref ID: 460; 100 bps");
STATION ("299.5","#463","Skutskar"," ","SWE"," ","N"," "," "," ","60.6163888888889","17.4330555555556","DGPS; Ref ID: 726; 100 bps");
STATION ("300","ZMR","ZAMORA"," ","ESP","6.20","N"," ","1055","100","41.5283333333333","-5.65166666666667"," ");
STATION ("301","#462","Gilze Rijen"," ","HOL"," ","N"," "," "," ","51.6166666666667","4.93333333333333","DGPS; Ref ID: 655 (462); 100bps");
STATION ("301","#510","Halten Lt."," ","NOR"," ","N"," "," "," ","64.1747222222222","9.41","DGPS; Ref ID: 790/820; 100 bps");
STATION ("301","RTN","Romorantin"," ","FRA","19.59","Y","0","0"," ","47.3197222222222","1.68777777777778","ID+15st");
STATION ("301.5","#404","Turku"," ","FIN"," ","N"," ","4"," ","60.4330555555556","22.2163888888889","DGPS; Ref ID: 604; 100 bps");
STATION ("302","#467","Hjortens Udde"," ","SWE"," ","N"," "," "," ","58.6330555555556","12.6663888888889","DGPS; Ref ID: 733; 100 bps");
STATION ("302.5","#508","Svinoey Lt."," ","NOR"," ","N"," "," "," ","62.3344444444444","5.27694444444445","DGPS; Ref ID: 788/818; 100 bps");
STATION ("303.5","#493","Zeven"," ","DEU"," ","N"," "," "," ","53.2833333333333","9.25","DGPS; Ref ID: 763; 100 bps");
STATION ("304","#503","Lista"," ","NOR"," ","N"," "," "," ","58.1080555555556","6.57","DGPS; Ref ID: 783/813; 100 bps");
STATION ("304.5","#535","Klaipeda Rear Lt."," ","LTU"," ","N"," "," "," ","55.735","21.1069444444444","DGPS; Ref ID: 535; 200 bps");
STATION ("305.5","#341","Sagres"," ","POR"," ","N"," ","1"," ","37.0333333333333","-9","DGPS; Ref ID: 482/483; 200 bps");
STATION ("306","#441","Lizard Lt."," ","ENG"," ","N"," "," "," ","49.9666666666667","-5.2","#681:306, Lizard Lt., England (UK)");
STATION ("306.5","AV","Avord"," ","FRA","23.2","Y","0","0"," ","46.8816666666667","2.93","ID+19st");
STATION ("307","#334","Les Sables D'Olonne"," ","FRA","","N","","","","46.5205555555556","-1.79166666666667","DGPS; Ref ID: 464; 100 baud");
STATION ("307","#530","Ristna Lt."," ","EST"," ","N"," "," "," ","58.9427777777778","22.0538888888889","DGPS; Ref ID: 840; 100 bps");
STATION ("307","#934","Fox Island","NS","CAN"," ","N"," "," "," ","45.3333333333333","-61.0833333333333","DGPS; Ref ID: 336/337; 200 bps");
STATION ("307.5","#440","St Catherines Pt Lt"," ","ENG"," ","N"," "," "," ","50.5752777777778","-1.29666666666667","DGPS; Ref ID: 680/690; 100 bps");
STATION ("307.5","#464","Kapellskar Lt."," ","SWE"," ","N"," "," "," ","59.7186111111111","19.0866666666667","DGPS; Ref ID: 728; 100 bps");
STATION ("308","#342","Horta"," ","AZR"," ","N"," "," "," ","38.5333333333333","-28.6166666666667"," ");
STATION ("308","#491","Gross Mohrdorf"," ","DEU"," ","N"," "," "," ","54.3663888888889","12.9163888888889","DGPS; Ref ID: 761; 100 bps");
STATION ("308.5","#332","Pont Du Buis"," ","FRA"," ","N"," "," "," ","48.3","-4.08333333333333","DGPS; Ref ID: 462; 100 bps; May have replaced Cap st Mathieu");
STATION ("309.5","#449","Nash Point"," ","WLS"," ","N"," "," "," ","51.3955555555556","-3.54166666666667","DGPS; Ref ID: 689/699; 100 bps");
STATION ("310","#336","Cap Ferret"," ","FRA"," ","N"," "," "," ","44.5663888888889","-1.25","DGPS; Ref ID: 466; 100 bps");
STATION ("310.5","#500","Faerder Lt."," ","NOR"," ","N"," "," "," ","59.0272222222222","10.5261111111111","DGPS; Ref ID: 780/810; 100 bps");
STATION ("311","#005","Shepelevskiy 2"," ","RUS","","N","","","","59.9791666666667","29.1247222222222","DGPS; Ref ID 5; 100 bps");
STATION ("311.5","#340","Cabo Carvoeiro Lt"," ","POR"," ","N"," ","0"," ","39.3588888888889","-9.40666666666667","DGPS; Ref ID: 480/481; 200 bps");
STATION ("312.5","#425","Hoek Van Holland"," ","HOL"," ","N"," "," "," ","51.9816666666667","4.11388888888889","DGPS; Ref ID: 650/651; 200 bps");
STATION ("313.5","#366","Cabo San Sebastian"," ","ESP"," ","N"," "," "," ","41.8833333333333","3.2","DGPS; Ref ID: 532; 100 bps");
STATION ("313.5","#496","Mauken"," ","DEU"," ","N"," "," "," ","51.7166666666667","12.8166666666667","DGPS; Ref ID: 766; 100 bps");
STATION ("314","#507","Utvaer Lt."," ","NOR"," ","N"," "," "," ","61.0408333333333","4.51916666666667","DGPS; Ref ID: 787/817; 100 bps");
STATION ("316","OE","Dublin"," ","IRL","8.48","N","396","405"," ","53.43","-6.42861111111111","Also heard as OE e");
STATION ("317","VS","VALENCIENNES"," ","FRA","19.92","Y","0","0","","50.3516666666667","3.355"," ");
STATION ("318","BE","BORDEAUX"," ","FRA","20.28","Y","0","0","25","44.8705555555556","-.397777777777777"," ");
STATION ("318","MAD","MADEIRA"," ","POR","7.00","N","1026","","2000","32.7480555555556","-16.7063888888889"," ");
STATION ("319","VAR","Stavanger / Sola / Varhaug"," ","NOR","8.09","N","386","417","25","58.6255555555556","5.63027777777778"," ");
STATION ("320","CAE","CAERNARFON"," ","WLS","10.27","N","400","400","","53.1","-4.34"," ");
STATION ("320","HA","HANNOVER"," ","DEU","6.61","N","","1018","25","52.4638888888889","9.805"," ");
STATION ("320.5","SWN","SWANSEA"," ","ENG","7.48","N","399","399","","51.6016666666667","-4.06472222222222"," ");
STATION ("321","CRN","GALWAY"," ","IRL","7.86","N","396","394","U","53.3008333333333","-8.94194444444444"," ");
STATION ("321","STM","St Marys (Scilly Is)"," ","ENG","7.27","N","400","400","","49.9136111111111","-6.29027777777778"," ");
STATION ("322","MIO","MONTIJO"," ","POR","9.60","N","1022","","","38.7094444444444","-9.04305555555556","Slow ID");
STATION ("323","SMA","SANTA MARIA"," ","POR","8.47","N","1034","1039","","36.9966666666667","-25.1752777777778"," ");
STATION ("323","WPL","WELSHPOOL"," ","WLS","6.64","N","413","411","U","52.63","-3.15388888888889"," ");
STATION ("325","OF","Filton"," ","ENG","8.32","N","400","401"," ","51.5216666666667","-2.59"," ");
STATION ("325","PG","Trollh�ttan"," ","SWE","5.73","Y","","413"," ","58.2666666666667","12.4166666666667"," ");
STATION ("326","RSH","Dublin / Rush"," ","IRL","4.54","N","400","400"," ","53.5116666666667","-6.11027777777778"," ");
STATION ("327","POR","Porto"," ","POR","8.39","N","1022",""," ","41.3480555555556","-8.70805555555556"," ");
STATION ("328","CL","Carlisle	"," ","ENG","8.6","N","399","400"," ","54.9402777777778","-2.80388888888889"," ");
STATION ("328","HAV","HAVERFORDWEST"," ","ENG","10.03","N","399","398","","51.2488888888889","-4.96833333333333"," ");
STATION ("328.5","EGT","Londonderry / Eglinton"," ","NIR","5.26","N","389","393"," ","55.045","-7.15416666666667"," ");
STATION ("330","NSM","Inishmaan Isl.	"," ","IRL","4.77","N"," ","368"," ","53.0833333333333","-9.53333333333333"," ");
STATION ("331","GLW","Glasgow	"," ","SCT","6.45","N","400","399"," ","55.87","-4.43333333333333"," ");
STATION ("331","GST","Gloucester"," ","ENG","5.21","N","389","378","50","51.8916666666667","-2.16666666666667"," ");
STATION ("331","SC","Flores"," ","AZR","8.08","N","1016","","","39.4477777777778","-31.1252777777778"," ");
STATION ("332","FAR","Faro"," ","POR","7.43","N","1027",""," ","37.0086111111111","-7.92583333333333"," ");
STATION ("332","OY","Belfast / Aldergrove"," ","NIR","10.8","N","382","413"," ","54.6925","-6.08527777777778"," ");
STATION ("334","GMN","Gormanston	"," ","IRL","5.32","N","396","390"," ","53.6480555555556","-6.22666666666667"," ");
STATION ("334","KER","KERRY"," ","IRL","~5","N","~400","~400","","52.1819444444444","-9.52444444444444"," ");
STATION ("335","SCO","Amerada Hess / Scott Platform"," ","XOE","9.58","N","402","421"," ","58.2705555555556",".208055555555556"," ");
STATION ("335","WCO","Westcott"," ","ENG","6.91","N","407","407"," ","51.8530555555556","-.9625"," ");
STATION ("336","AQ","Aberdeen	"," ","SCT"," ","N","400","391"," ","57.1383333333333","-2.40472222222222"," ");
STATION ("337","EX","Exeter"," ","ENG","9.51","N","392","417"," ","50.7519444444444","-3.29361111111111"," ");
STATION ("337","MY","Myggenaes","","FRO"," ","N","448","388"," ","62.1069444444444","-7.58777777777778"," ");
STATION ("337","WTN","WARTON"," ","ENG","8.41","N","408","391","","53.7513888888889","-2.85222222222222"," ");
STATION ("338","FNY","Robin Hood / Doncaster"," ","ENG","6.51","N","400","400","","53.4791666666667","-1.04166666666667","Much better LSB");
STATION ("338","PST","Porto Santo"," ","POR","7.01","N","1025","1025","2000","33.0683333333333","-16.3580555555556"," ");
STATION ("338","PST","Porto Santo / Ilheu De Baixo","","MDR"," ","N","1020","1025","2000","33.0680555555556","-16.3577777777778"," ");
STATION ("339","BIA","Bournemouth"," ","ENG","9","N","426","398","40","50.7775","-1.84222222222222"," ");
STATION ("339","OL","Shannon"," ","IRL","8.97","N","1016","","","52.7488888888889","-8.82388888888889"," ");
STATION ("340","HAW","HAWARDEN"," ","ENG","10.24","N","403","398"," ","53.1797222222222","-2.97777777777778"," ");
STATION ("340","HAW","Hawarden"," ","WLS","10","N","385","414"," ","53.1875","-2.95805555555556"," ");
STATION ("341","EDN","Edinburgh	"," ","SCT"," ","N","402","392"," ","55.9783333333333","-3.28527777777778"," ");
STATION ("342","VLD","VALLADOLID"," ","ESP","13.66","N","","1029","","41.7894444444444","-4.73888888888889"," ");
STATION ("344","HN","HOHN"," ","DEU","8.25","Y","1032","","25","54.3261111111111","9.67027777777778","ID+4.2st");
STATION ("346","WLU","Luxembourg"," ","LUX","9.98","N"," ","1013","15","49.5677777777778","6.05416666666667"," ");
STATION ("347","MSK","Morskogen"," ","NOR","10.11","N","400","400"," ","60.45","11.2666666666667"," ");
STATION ("347","YG","Charlottetown","PE","CAN","10.2","Y"," "," ","1600","46.1922222222222","-63.1480555555556"," ");
STATION ("347.5","TD","Teeside"," ","ENG","10.48","N","413","407"," ","54.5602777777778","-1.33333333333333"," ");
STATION ("348","ATF","Aberdeen / Dyce"," ","SCT","7.72","N","398","405","25","57.0775","-2.10555555555556"," ");
STATION ("349","RS","Rennes / Saint Jacques"," ","FRA","20","Y","0","0"," ","48.0530555555556","-1.58444444444444","ID+17st");
STATION ("349.5","LPL","LIVERPOOL"," ","ENG","6.96","N","391","431"," ","53.3394444444444","-2.725"," ");
STATION ("350","GLG","Glasgow","","SCT"," ","N"," "," "," ","55.9241666666667","-4.33555555555556","INACTIVE");
STATION ("350","LAA","Oulu / Laanila","","FIN"," ","N","400","410"," ","64.9661111111111","25.2111111111111"," ");
STATION ("350","MUT","MURET"," ","FRA","20.77","Y","0","0","","43.4797222222222","1.18166666666667","ID+16st");
STATION ("351","OSA;2","Ouessant"," ","FRA","19.79","Y","","","","48.4791666666667","-5.04166666666667","ID+14.9st");
STATION ("352","ENS","ENNIS"," ","IRL","3.89","N","398","384"," ","52.905","-8.9275"," ");
STATION ("352","NT","Newcastle"," ","ENG"," ","N","422","393"," ","55.0502777777778","-1.6425"," ");
STATION ("353","SB","Saint Brieuc / Armor"," ","FRA","20.76","Y","0","0"," ","48.5675","-2.7825","ID+16.9st");
STATION ("354","MTZ","Metz"," ","FRA","19.67","Y","0","0"," ","49.2761111111111","6.20861111111111","ID+15st");
STATION ("355","PIK","Prestwick	"," ","SCT","5.84","N","403","405"," ","55.5058333333333","-4.57722222222222"," ");
STATION ("356","CVU","CASTRES-MAZAMET"," ","FRA","22.47","Y","0","0","","43.6325","2.20916666666667","ID+16.5st");
STATION ("356","WBA","Wolwerhampton"," ","ENG","5.87","N","400","398"," ","52.5175","-2.26138888888889"," ");
STATION ("356.5","SM","ST MAWGAN"," ","ENG","10.03","N","406","394","","50.4480555555556","-4.99444444444444"," ");
STATION ("359","LOR","Lorient"," ","FRA","19.48","Y","0","0","40","47.7630555555556","-3.44055555555556","ID+13.8st");
STATION ("359","RWY","ISLE OF MAN"," ","IOM","10.02","N","405","399","20","54.0808333333333","-4.62972222222222"," ");
STATION ("359.5","CDN","Chateaudun"," ","FRA","","Y","0","0","25","48.0622222222222","1.36361111111111","");
STATION ("360","HT","HORTA"," ","POR","8.27","N","","1031","","38.5197222222222","-28.6294444444444"," ");
STATION ("361","CFN","DONEGAL"," ","IRL","6.15","N","407","410","50w","55.0438888888889","-8.33916666666667","7xID then 5.7 sec dash");
STATION ("362","CTE","Castejon"," ","ESP","8.67","N","1035","1035","","40.3955555555556","-2.39166666666667","");
STATION ("362","OB","Cork"," ","IRL","8.92","N","","1043","","51.7552777777778","-8.44055555555556"," ");
STATION ("364","KNK","CONNAUGHT"," ","IRL","8.21","N","1015","1017","","53.8963888888889","-8.93694444444444"," ");
STATION ("367","PG","PORTO"," ","POR","9.91","N","","991","","41.0780555555556","-8.63638888888889"," ");
STATION ("368","UW","Edinburgh"," ","SCT","8.28","N","375","423"," ","55.905","-3.5025"," ");
STATION ("368","WTD","Waterford"," ","IRL","5.45","N","398","399"," ","52.1888888888889","-7.08333333333333"," ");
STATION ("368.5","WHI","WHITEGATE"," ","ENG","5.45","N","404","402","25","53.185","-2.62305555555556"," ");
STATION ("370","GR","Grindstone  (Iles de la Madele","QC","CAN","10.5","Y"," ","389","25","47.375","-61.9072222222222"," ");
STATION ("370.5","AP","Aberporth"," ","WLS","8.54","N","387","419"," ","52.1161111111111","-4.55972222222222"," ");
STATION ("371","MGL","Ponta Delgada  (Sao Miguel Isl"," ","AZR","8.31","N","1031","1032"," ","37.7413888888889","-25.5836111111111"," ");
STATION ("371","MLX","MORLAIX"," ","FRA","29.97","Y","0","0","","48.6466666666667","-3.7625","ID+24.3st");
STATION ("371","STR","SINTRA"," ","POR","9.57","N","","1008","","38.8777777777778","-9.40166666666667"," ");
STATION ("373","MP","Cherbourg"," ","FRA","20.33","Y","0","0"," ","49.6380555555556","-1.37222222222222"," ");
STATION ("374","CBN","Cumbernauld"," ","SCT","5.83","N","393","396"," ","55.9755555555556","-3.97472222222222"," ");
STATION ("375","CCH","CALAMOCHA"," ","ESP","13.62","N","1038","1040","250","40.9022222222222","-1.29805555555556"," ");
STATION ("377","PNT","PONTIVY"," ","FRA","19.84","Y","0","0","","48.0511111111111","-2.79444444444444","ID+15st");
STATION ("378","KLY","Killiney for Dublin"," ","IRL","7.19","N","420","405"," ","53.2694444444444","-6.10638888888889"," ");
STATION ("379","CZ","Charleville"," ","FRA","","Y","0","0"," ","49.75","4.725","ID+15st");
STATION ("380","CAC","Caceres"," ","ESP","6.75","N","1020","1026"," ","39.5288888888889","-6.42861111111111","EX CCS");
STATION ("380","CBL","CAMPBELTOWN"," ","SCT","6.67","N","421","393","","55.4355555555556","-5.68805555555556"," ");
STATION ("382","CAA","CAZAUX"," ","FRA","18.53","Y","0","0","25","44.5511111111111","-1.11972222222222"," ");
STATION ("382","SLP","SLEAP"," ","ENG","6.22","N","407","403","","52.8336111111111","-2.77361111111111"," ");
STATION ("383","SHD","Scotstown Head"," ","SCT","5.45","N","405","396"," ","57.5591666666667","-1.81722222222222"," ");
STATION ("384","SLG","SLIGO"," ","IRL","8.19","N","405","408","U","54.2780555555556","-8.59972222222222"," ");
STATION ("385","NA","Natashquan","QC","CAN","10.31","Y"," ","400","500","50.2222222222222","-61.8416666666667"," ");
STATION ("385","TEO","TOLEDO"," ","ESP","?","N","","?","","39.9727777777778","-4.34583333333333"," ");
STATION ("385","WL","Barrow / Walney Isl"," ","ENG","6","N","385","413"," ","54.1255555555556","-3.26277777777778"," ");
STATION ("386","BZ","BRIZE NORTON"," ","ENG","10.04","N","400","400","50w","51.7491666666667","-1.60138888888889"," ");
STATION ("387","CML","Clonmel"," ","IRL","5.03","N","418","409","50","52.4536111111111","-7.48"," ");
STATION ("388.5","CDF","CARDIFF"," ","ENG","7.87","N","387","406"," ","51.3933333333333","-3.33666666666667"," ");
STATION ("389","CP","Lisboa / Caparica"," ","POR","7.59","N","1022","1017"," ","38.6422222222222","-9.22138888888889"," ");
STATION ("390","DR","Dinard"," ","FRA","20.06","Y","0","0"," ","48.4819444444444","-2.05277777777778","ID+16.4st");
STATION ("390","JT","Stephenville","NL","CAN"," ","Y"," ","415","450","48.5438888888889","-58.7547222222222"," ");
STATION ("390","SO","Santiago de Compostela"," ","ESP","10.77","N","","1024","","42.9791666666667","-8.45805555555556","");
STATION ("391","DDP","San Juan / Dorado / Luiz Munoz"," ","PTR"," ","N","","1042","2000","18.4680555555556","-66.4122222222222","3kW Rptd - 200' Vert.");
STATION ("392","RW","Tegel West"," ","DEU","10.09","N"," ","1020","50","52.5452777777778","13.1511111111111"," ");
STATION ("393","VL","Valladolid"," ","ESP","7.39","N","1028","","","41.7291666666667","-4.79166666666667","");
STATION ("394","DND","Dundee"," ","SCT","5.74","N","414","376"," ","56.455","-3.11472222222222"," ");
STATION ("395","B","Bilbao"," ","ESP","6.97","N","994",""," ","43.3733333333333","-3.03333333333333"," ");
STATION ("395","FOY","Foynes"," ","IRL","6.18","N","396","404","50","52.5661111111111","-9.19527777777778"," ");
STATION ("395","LAY","Islay"," ","SCT","6.47","N","402","399"," ","55.6827777777778","-6.24916666666667"," ");
STATION ("396.5","PY","Plymouth"," ","ENG","7.76","N","399","400"," ","50.4241666666667","-4.11222222222222"," ");
STATION ("397","OP","Dublin"," ","IRL","9.77","N","403","402","15","53.4136111111111","-6.13833333333333"," ");
STATION ("398","OK","Connaught"," ","IRL","8.17","N"," ","1026"," ","53.9238888888889","-8.69972222222222"," ");
STATION ("399","MTN","Salamanca"," ","ESP","11.79","N","1038","1012","100","40.9886111111111","-5.32972222222222"," ");
STATION ("399","NGY","New Galloway"," ","SCT","6.41","N","418","390","80","55.1775","-4.16861111111111"," ");
STATION ("401","COA","La Coruna"," ","ESP","21.41","N","1010","","","43.3541666666667","-8.29166666666667","Long gap");
STATION ("402","FNR","Finner Army Camp"," ","IRL","5.08","N","420","378","","54.495","-8.23333333333333"," ");
STATION ("402.5","LBA","Leeds / Bradford"," ","ENG","9.98","N","416","409","45","53.8647222222222","-1.65277777777778"," ");
STATION ("407","GAR","Garristown"," ","IRL","5.45","N","422","379","50","53.5286111111111","-6.44722222222222"," ");
STATION ("414","BRI","BRISTOL"," ","ENG","10.53","N","396","409","","51.3802777777778","-2.71611111111111"," ");
STATION ("417","SNO","SANTIAGO"," ","ESP","13.59","N","","1055","200","42.9075","-8.42833333333333"," ");
STATION ("419","RA","Tyra East"," ","XOE","4.12","N"," ","390"," ","55.4316666666667","4.48111111111111"," ");
STATION ("420","BPL","Blackpool"," ","ENG","7.87","N","370","412"," ","53.7733333333333","-3.02666666666667"," ");
STATION ("420","HB","Belfast Harbour"," ","NIR","7.26","N","407","386"," ","53.615","-5.88166666666667"," ");
STATION ("420","SPP","SEVILLA"," ","ESP","11.50","N","","1037","100","37.4180555555556","-5.79527777777778"," ");
STATION ("421","GE","GETAFE"," ","ESP","9.26","N","","1023","","40.1997222222222","-3.84416666666667"," ");
STATION ("423","SCA","SALAMANCA"," ","ESP","11.07","N","","1042","100","40.9272222222222","-5.61638888888889"," ");
STATION ("425","TST","Evora"," ","POR","4.24","N","","407","50","38.5205555555556","-7.87472222222222","");
STATION ("426","CB","COIMBRA"," ","POR","9.86","N","399","400","","40.1591666666667","-8.47305555555556"," ");
STATION ("426","PW","Prestwick"," ","SCT","5.05","N","407","403"," ","55.5441666666667","-4.68138888888889"," ");
STATION ("426","TJA","TORREJON"," ","ESP","7.86","N","","1013","100","40.5680555555556","-3.35722222222222"," ");
STATION ("428","BST","Lanveoc"," ","FRA","18.02","Y","0","0","25","48.2836111111111","-4.43222222222222","ID+12.4st");
STATION ("428","CTX","Chateauroux"," ","FRA","19.45","Y","0","0","50","46.9363888888889","1.80111111111111","ID+14.4st");
STATION ("428","MCH","Manchester"," ","ENG","6.31","N","412","","50","53.3530555555556","-2.27277777777778"," ");
STATION ("428","MNF","MORON"," ","ESP","6.56","N","","1049","100","37.2944444444444","-5.56194444444444"," ");
STATION ("432","HMB","HAMMAM BOU HADJAR"," ","ALG","19.88","Y","0","0","25","35.3630555555556","-.968888888888889","ID+14.7st");
STATION ("433","JER","JEREZ"," ","ESP","8.66","N","","1035","50","36.8344444444444","-6.01611111111111"," ");
STATION ("433","VON","VIGO"," ","ESP","17.71","N","","1019","200","42.1858333333333","-8.63888888888889"," ");
STATION ("490","$01C","Portpatrick"," ","SCT","","N","","","","54.8541666666667","-5.12472222222222","BCST @ 0820, 2020 UTC; Callsign GPK");
STATION ("490","$01T","Niton"," ","ENG"," ","N"," "," "," ","50.5622222222222","-1.29166666666667","BCST @ 0310' 0710' 1110' 1510' 1910' 2310 UTC; Callsign GNI (French)");
STATION ("490","$01U","Cullercoats"," ","ENG","","N","","","","55.0205555555556","-1.45805555555556","BCST @ 0720, 1920 UTC; Callsign GCC");
STATION ("490","$02E","Corsen"," ","FRA","","N","","","","48.4372222222222","-4.79166666666667","BCST @ 0040, 0440, 0840, 1240, 1640, 2040 UTC; Callsign FRC");
STATION ("490","$02G","Monsanto"," ","ESP","","N","","","","38.7291666666667","-9.20805555555556","BCST @ 0100, 0500, 0900, 1300, 1700, 2100 UTC; Callsign CTV");
STATION ("490","$03M","Cabo de la Nao (Valencia)"," ","ESP","","N","","","","38.7291666666667",".124722222222223","BCST@ 0200, 0600, 1000, 1400, 1800, 2200 UTC Callsign: EAV (Spanish)");
STATION ("518","$01B","Bod� - Lgp"," ","NOR"," ","N"," "," "," ","67.2669444444444","14.3827777777778","BCST @ 0010' 0410' 0810' 1210' 1610' 2010 UTC; Callsign LGP");
STATION ("518","$01E","Niton"," ","ENG"," ","N"," "," "," ","50.5830555555556","-1.3","BCST @ 0040' 0440' 0840' 1240' 1640' 2040 UTC; Callsign GNI");
STATION ("518","$01G","Cullercoats","","ENG"," ","N"," "," "," ","55.0669444444444","-1.46694444444444","BCST @ 0100' 0500' 0900' 1300' 1700' 2100 UTC; Callsign GCC");
STATION ("518","$01I","Gothenburg (Grimeton)"," ","SWE","","N","","","","57.1041666666667","12.375","BCST@ 0120, 0520, 0920, 1320, 1720, 2120 UTC Callsign: SAG New ID since 01/12/2004");
STATION ("518","$01J","Karlskrona (Gisl�vshammer)"," ","SWE"," ","N"," "," "," ","55.4827777777778","14.3158333333333","BCST @ 0130' 0530' 0930' 1330' 1730' 2130 UTC; Callsign SAH");
STATION ("518","$01K","Niton - Gni (cherbrg)"," ","ENG"," ","N"," "," "," ","50.5830555555556","-1.3","BCST @ 0140' 0540' 0940' 1340' 1740' 2140 UTC; Callsign GNI");
STATION ("518","$01L","Rogaland"," ","NOR"," ","N","0","0"," ","58.6497222222222","5.6","BCST @ 0150' 0550' 0950' 1350' 1750' 2150 UTC; Callsign LGQ");
STATION ("518","$01M","Oostende - Ost","","BEL"," ","N"," "," "," ","51.1830555555556","2.8"," ");
STATION ("518","$01N","Oerlandet - Lgd","","NOR"," ","N"," "," "," ","63.6669444444444","9.55"," ");
STATION ("518","$01O","Portpatrick","","SCT"," ","N","0","0"," ","54.85","-5.11666666666667","BCST @ 0220' 0620' 1020' 1420' 1820' 2220 UTC; Callsign GPK");
STATION ("518","$01P","Netherlands (Den Helder)","","HOL"," ","N","0","0"," ","52.95","4.78305555555556","BCST @ 0230' 0630' 1030' 1430' 1830' 2230 UTC; Callsign PBK");
STATION ("518","$01Q","Malin Head","","IRL"," ","N"," "," "," ","55.3669444444444","-7.35","BCST @ 0240' 0640' 1040' 1440' 1840' 2240 UTC; Callsign EJM");
STATION ("518","$01R","Reykjavik"," ","ISL","","N","","","","63.0622222222222","-21.8747222222222","BCST @ 0250, 0650, 1050, 1450, 1850, 2250 UTC; Callsign TFA");
STATION ("518","$01T","Oostende - Ost"," ","BEL"," ","N"," "," "," ","51.1827777777778","2.8","BCST @ 0310' 0710' 1110' 1510' 1910' 2310 UTC; Callsign OST");
STATION ("518","$01U","Tallin - Esa"," ","EST"," ","N"," "," "," ","59.5","24.5","BCST @ 0320' 0720' 1120' 1520' 1920' 2320 UTC; Callsign ESA");
STATION ("518","$01W","Valentia"," ","IRL"," ","N"," "," "," ","51.9372222222222","-10.375","BCST @ 0340' 0740' 1140' 1540' 1940' 2340 UTC; Callsign EJK");
STATION ("518","$01X","Reykjavik - Tfa","","ISL"," ","N"," "," "," ","64.0827777777778","-21.85"," ");
STATION ("518","$02A","Corsen","","FRA"," ","N"," "," "," ","48.46","-5.05","BCST @ 0000' 0400' 0800' 1200' 1600' 2000 UTC; Callsign FRC");
STATION ("518","$02D","Coruna"," ","ESP","","N","","","","42.8955555555556","-9.28416666666667","BCST @ 0030, 0430, 0830, 1230, 1630, 2030 UTC; Callsign EAR");
STATION ("518","$02I","Las Palmas"," ","CNR","","N","","","","28.1875","-15.4580555555556","BCST @ 0120, 0520, 0920, 1320, 1720, 2120 UTC; Callsign EAL");
STATION ("518","$02R","Monsanto - Ctv251"," ","POR"," ","N"," "," "," ","38.7327777777778","-9.18277777777778","BCST @ 0250' 0650' 1050' 1450' 1850' 2250 UTC; Callsign CTV-251");
STATION ("518","$03B","Alger"," ","ALG","","N","","","","","","Co-ordinates unknown");
STATION ("518","$03D","Istanbul"," ","TUR","","N","","","","41.0622222222222","28.9580555555556","BCST @ 0030, 0430, 0830, 1230, 1630, 2030 UTC; Callsign TAH");
STATION ("518","$03I","Gothenburg (Grimeton)"," ","SWE"," ","N"," "," "," ","57.1041666666667","12.375","BCST@ 0120' 0520' 0920' 1320' 1720' 2120 UTC Callsign: SAG New ID since 01/12/2004");
STATION ("518","$03J","Varna"," ","BUL","","N","","","","43.0622222222222","27.7916666666667","BCST @ 0130, 0530, 0930, 1330, 1730, 2130 UTC; Callsign LZW");
STATION ("518","$03Q","Split"," ","BIH"," ","N"," "," "," ","43.5205555555556","16.4580555555556","BCST @ 0240' 0640' 1040' 1440' 1840' 2240 UTC; Callsign 9AS");
STATION ("518","$03V","Augusta"," ","SCY","","N","","","","37.2291666666667","15.2080555555556","BCST @ 0330, 0730, 1130, 1530, 1930, 2330 UTC; Callsign IQA");
STATION ("518","$03W","Astrakhan"," ","RUS"," ","N"," "," "," ","46.3125","47.9580555555556","BCST @ 0340' 0740' 1140' 1540' 1940' 2340 UTC; Callsign UJB");
STATION ("518","$03W;2","La Garde (Toulon)"," ","FRA"," ","N"," "," "," ","43.1041666666667","5.95805555555556","BCST @ 0340' 0740' 1140' 1540' 1940' 2340 UTC; Callsign FRL");
STATION ("518","$03X","Cabo de la Nao (Valencia)"," ","ESP"," ","N"," "," "," ","38.7291666666667",".124722222222223","BCST @ 0350' 0750' 1150' 1550' 1950' 2350 UTC; Callsign EAV");
STATION ("518","$04Q","Sydney","NS","CAN"," ","N"," "," "," ","46.1875","-59.875","BCST @ 0240- 0640- 1040- 1440- 1840- 2240 UTC; Callsign VCO");
STATION ("518","$04X","Labrador","NL","CAN"," ","N"," "," "," ","53.3","-60.55","BCST @ 0350- 0750- 1150- 1550- 1950- 2350- 0910- 2110 UTC; Jul-Oct; Callsign VOK");
STATION ("518","$17F","Archangel"," ","RUS","","N","","","","64.5622222222222","40.5416666666667","BCST @ 0050, 0450, 0850, 1250, 1650, 2050 UTC (Polar region); Callsign UGE");
STATION ("520","F9","Chatham  (Miramichi)","NB","CAN","10.47","Y"," ","404","123","47.01","-65.4675"," ");
STATION ("545","LIC","LICHFIELD"," ","ENG","5.95","N","","404","","52.7466666666667","-1.71944444444444"," ");
