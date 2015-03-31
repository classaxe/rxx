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
STATION ("284","#430","Mizen Head"," ","IRL"," ","N"," "," "," ","51.4511111111111","-9.82194444444444","DGPS; Ref ID: 660/661; 100 bps");
STATION ("285.5","#443","Earls Hill / Stirling"," ","SCT"," ","N"," "," "," ","56.0666666666667","-4.06666666666667","DGPS; Ref ID: 693; 100 bps");
STATION ("287.5","#343","Porto Santo"," ","MDR"," ","N"," "," "," ","33.0625","-16.375","DGPS; Ref ID: 486/487; 200 bps");
STATION ("290.5","#447","Flamborough Head Lt."," ","ENG"," ","N"," "," "," ","54.1255555555556","-9.05555555555555E-02","DGPS; Ref ID: 687/697; 100bps");
STATION ("291","#439","Wormleighton"," ","ENG"," ","N"," "," "," ","52.2052777777778","-1.375","DGPS; Ref ID: 691; 100bps");
STATION ("292.5","BA","Estaca de Bares","","ESP","2xIDLT","Y"," ","0"," ","43.7833333333333","-7.68333333333333"," ");
STATION ("293","#432","Loop Head Lt."," ","IRL"," ","N"," "," "," ","52.5677777777778","-9.94138888888889","DGPS; Ref ID: 665/666; 100bps");
STATION ("297.5","#442","Point Lynas Lt."," ","WLS"," ","N"," "," "," ","53.4266666666667","-4.29138888888889","DGPS; Ref ID: 682/692; 100bps");
STATION ("298.5","#492","Dune Helgoland"," ","DEU"," ","N"," "," "," ","54.1830555555556","7.88305555555556","DGPS; Ref ID: 762; 100 bps");
STATION ("299","#330","Gatteville"," ","FRA"," ","N"," "," "," ","49.7","-1.26583333333333","DGPS; Ref ID: 460; 100 bps");
STATION ("303.5","#493","Zeven"," ","DEU"," ","N"," "," "," ","53.2833333333333","9.25","DGPS; Ref ID: 763; 100 bps");
STATION ("305.5","#341","Sagres"," ","POR"," ","N"," ","1"," ","37.0333333333333","-9","DGPS; Ref ID: 482/483; 200 bps");
STATION ("306","#441","Lizard Lt."," ","ENG"," ","N"," "," "," ","49.9666666666667","-5.2","#681:306, Lizard Lt., England (UK)");
STATION ("307.5","#440","St Catherines Pt Lt"," ","ENG"," ","N"," "," "," ","50.5752777777778","-1.29666666666667","DGPS; Ref ID: 680/690; 100 bps");
STATION ("308.5","#332","Pont Du Buis"," ","FRA"," ","N"," "," "," ","48.3","-4.08333333333333","DGPS; Ref ID: 462; 100 bps; May have replaced Cap st Mathieu");
STATION ("309.5","#449","Nash Point"," ","WLS"," ","N"," "," "," ","51.3955555555556","-3.54166666666667","DGPS; Ref ID: 689/699; 100 bps");
STATION ("311.5","#340","Cabo Carvoeiro Lt"," ","POR"," ","N"," ","0"," ","39.3588888888889","-9.40666666666667","DGPS; Ref ID: 480/481; 200 bps");
STATION ("316","OE","Dublin"," ","IRL","8.48","N","396","405"," ","53.43","-6.42861111111111","Also heard as OE e");
STATION ("319","VAR","Stavanger / Sola / Varhaug"," ","NOR","8.09","N","386","417","25","58.6255555555556","5.63027777777778"," ");
STATION ("320.5","SWN","SWANSEA"," ","ENG","7.48","N","399","399","","51.6016666666667","-4.06472222222222"," ");
STATION ("327","POR","Porto"," ","POR","8.39","N","1015","1014"," ","41.3480555555556","-8.70805555555556"," ");
STATION ("328","HAV","HAVERFORDWEST"," ","ENG","10.03","N","399","398","","51.2488888888889","-4.96833333333333"," ");
STATION ("332","FAR","Faro"," ","POR","","N","1027",""," ","37.0086111111111","-7.92583333333333"," ");
STATION ("334","GMN","Gormanston	"," ","IRL","5.32","N","394","393"," ","53.6480555555556","-6.22666666666667"," ");
STATION ("335","WCO","Westcott"," ","ENG","6.91","N","407","407"," ","51.8530555555556","-.9625"," ");
STATION ("337","EX","Exeter"," ","ENG","9.51","N","391","413"," ","50.7519444444444","-3.29361111111111"," ");
STATION ("338","PST","Porto Santo"," ","POR","7.01","N","1019","1017","2000","33.0683333333333","-16.3580555555556"," ");
STATION ("339","BIA","Bournemouth"," ","ENG","9","N","426","398","40","50.7775","-1.84222222222222"," ");
STATION ("345","LN","LANNION"," ","FRA","19.97","Y","0","0","25","48.7191666666667","-3.30777777777778","ID+T");
STATION ("349","RS","Rennes / Saint Jacques"," ","FRA","20","Y","0","0"," ","48.0530555555556","-1.58444444444444","ID+17st");
STATION ("349.5","LPL","LIVERPOOL"," ","ENG","6.96","N","391","431"," ","53.3394444444444","-2.725"," ");
STATION ("351","DSA","DIEPPE-ST AUBIN","","FRA","","N","","","","49.8827777777778","1.08305555555556"," ");
STATION ("351","OSA;2","Ouessant"," ","FRA","19.79","Y","","","","48.4791666666667","-5.04166666666667","ID+14.9st");
STATION ("352","WOD","WOODLEY"," ","ENG","6.43","N","407","404","","51.4527777777778","-.878888888888889"," ");
STATION ("353","SB","Saint Brieuc / Armor"," ","FRA","20.76","Y","0","0"," ","48.5675","-2.7825","ID+16.9st");
STATION ("353.5","EME","East Midlands"," ","ENG","4.1","N","405","400","25","52.8327777777778","-1.19277777777778"," ");
STATION ("356","WBA","Wolwerhampton"," ","ENG","5.87","N","402","399"," ","52.5175","-2.26138888888889"," ");
STATION ("356.5","SM","ST MAWGAN"," ","ENG","10.03","N","406","394","","50.4480555555556","-4.99444444444444"," ");
STATION ("359","RWY","ISLE OF MAN"," ","IOM","10.02","N","405","399","20","54.0808333333333","-4.62972222222222"," ");
STATION ("361","GUY","Guernsey"," ","GSY","6.54","N","398","405","","49.4372222222222","-2.62472222222222","USB Best");
STATION ("363.5","CT","Coventry"," ","ENG","","N","409","","","52.4108333333333","-1.40416666666667"," ");
STATION ("368","WTD","Waterford"," ","IRL","5.45","N","398","399"," ","52.1888888888889","-7.08333333333333"," ");
STATION ("370","PSA","SPESSART"," ","DEU","7.14","N","","1019","25","49.8622222222222","9.34805555555556"," ");
STATION ("370.5","AP","Aberporth"," ","WLS","8.54","N","387","419"," ","52.1161111111111","-4.55972222222222"," ");
STATION ("371","MGL","Ponta Delgada  (Sao Miguel Isl"," ","AZR","8.31","N","1031","1032"," ","37.7413888888889","-25.5836111111111"," ");
STATION ("371","MLX","MORLAIX"," ","FRA","29.97","Y","0","0","","48.6466666666667","-3.7625","ID+24.3st");
STATION ("372","ODR","Odderoeya"," ","NOR","8.39","N"," ","377"," ","58.1416666666667","7.99972222222222"," ");
STATION ("378","KLY","Killiney for Dublin"," ","IRL","7.19","N","420","405"," ","53.2694444444444","-6.10638888888889"," ");
STATION ("383","ALD","ALDERNEY"," ","GSY","5.44","N","394","409","50","49.7086111111111","-2.19944444444444"," ");
STATION ("388.5","CDF","CARDIFF"," ","ENG","7.87","N","387","406"," ","51.3933333333333","-3.33666666666667"," ");
STATION ("389","CP","Lisboa / Caparica"," ","POR","7.59","N","1022","1017"," ","38.6422222222222","-9.22138888888889"," ");
STATION ("395","FOY","Foynes"," ","IRL","6.18","N","396","404","50","52.5661111111111","-9.19527777777778"," ");
STATION ("395","GSG","Gourin"," ","FRA","","Y","0","0","","48.1455555555556","-3.62472222222222","");
STATION ("396.5","PY","Plymouth"," ","ENG","7.76","N","399","400"," ","50.4241666666667","-4.11222222222222"," ");
STATION ("397","OP","Dublin"," ","IRL","9.77","N","403","402","15","53.4136111111111","-6.13833333333333"," ");
STATION ("399","EAG","LOGRONO"," ","ESP","7.29","N","","1027","25","42.4547222222222","-2.32277777777778"," ");
STATION ("399","MTN","Salamanca"," ","ESP","11.79","N","1038","1012","100","40.9886111111111","-5.32972222222222"," ");
STATION ("399","NGY","New Galloway"," ","SCT","6.41","N","418","390","80","55.1775","-4.16861111111111"," ");
STATION ("400","AG","Agen / La Garenne"," ","FRA","19.82","Y","0","0"," ","44.1505555555556",".673611111111111"," ");
STATION ("407","GAR","Garristown"," ","IRL","5.45","N","422","379","50","53.5286111111111","-6.44722222222222"," ");
STATION ("414","BRI","BRISTOL"," ","ENG","10.53","N","396","409","","51.3802777777778","-2.71611111111111"," ");
