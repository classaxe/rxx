// ***********************************************************************// * FILE HEADER:                                                        *// ***********************************************************************// * Filename:    stations.js                                            *// * Author:      Martin Francis (martin@classaxe.com)                   *// ***********************************************************************// * This is an editable file containing actual station data.            *// *                                                                     *// * For your own use, you will probably need to add additional stations *// * to this list if your log contains stations not already listed here. *// *                                                                     *// * Put station details in the following format:                        *// * STATION(khz,call,qth,ste,cnt,cyc,daid,lsb,usb,pwr,lat,lon,notes);   *// *                                                                     *// * Each field should be enclosed with quotes and set to "" if unknown. *// * For any given beacon:                                               *// *   KHz     is the frequency of the carrier;                          *// *   call    is the callsign;                                          *// *   qth     is the town in which the beacon is located;               *// *   ste     is the state or province abbreviation (eg MI = Michigan)  *// *           or "" if not applicable (e.g. Bahamas)                    *// *   cnt     is the ITU country code;                                  *// *   cyc     is the number of seconds between repetitions of the call  *// *   daid    stands for 'Dash after ID' and is either "Y" or "N"       *// *   lsb     is the offset of the lower sideband from the carrier      *// *           (Note Canadian NDBs are USB only, for these set to "")    *// *   usb     is the offset of the upper sideband from the carrier      *// *   pwr     is the power in watts of the transmitter                  *// *   lat     is the decimal latitude value (S values are negative)     *// *   lon     is the decimal longitude value (W values are negative)    *// *   notes   These notes will show with each logging of the station.   *// *                                                                     *// * OBTAINING DATA:                                                     *// * I obtained much of the data included here from Alex Weicek's NDB    *// * beacon program which you can download from his site at:             *// * http://members.rogers.com/wiecek6010                                *// *                                                                     *// * Other data came from Micael Oexener's excellent North American NDB  *// * nadbook and the AirNav web site at http://www.airnav.com.           *// *                                                                     *// * Use the NDB WEBLOG Station Editor to modify or add to this data.    *// ***********************************************************************
STATION ("214","YIO","Pond Inlet","NU","CAN","ID+6st","Y","","400","25","72.6931","-77.9508","");
STATION ("216","CLB","Carolina Beach","NC","USA","10.5","N","","1026","400","34.1061","-77.9614","<a href='pix/clb_thumb.jpg' target='_blank'>Screen Capture</a>");
STATION ("256","YCY","Clyde River","NU","CAN","ID+3st","Y","","400","","70.4853","-68.5267","<a href='pix/ycy_thumb.jpg' target='_blank'>Screen Capture</a>");
STATION ("258.5","ML","Molde","","NOR","","N","","400","","62.725","7.145","");
STATION ("267","FNO","Foyno","","NOR","","N","","400","25","59.3725","5.1511","Daytimer/Local");
STATION ("270","FLO","Florez","","AZR","ID+4sg","N","","1020","","39.4458","-31.1675","Pos. USB");
STATION ("275","VG","Vaage","","NOR","","N","","400","","59.2814","5.3631","Local");
STATION ("282","LA","Lyneham","","ENG","10","N","400","400","25","51.5083","-2.0058","");
STATION ("282","AO","Opera","","HNG","","?","","1020","","47.0817","20.2117","");
STATION ("283","SF","Sandefjord/torp","","NOR","","N","","400","","59.1047","10.2594","");
STATION ("284.5","MA","Cabo Machicharo","","ESP","2xIDLT","Y","","0","","43.4575","-2.75138","Marine");
STATION ("285","PT","Katowiche","","POL","ID+2sg","N","","1020","","50.4739","19.15028","");
STATION ("285.5","#693","Earls Hill (stirling)","","SCT","","N","","","","56.0667","-4.0667","100 Baud, Station ID 443");
STATION ("287.5","#715","Thorshavn","","FRO","","N","","","","62.0205","-6.83783","100 Baud, Station ID 454");
STATION ("288.5","#670","Tory Island","","IRL","","N","","","","55.26667","-8.25","100 Baud, Station ID 435");
STATION ("289.5","MY","Cabo Major","","ESP","","Y","","0","","43.49166","-3.79","Marine");
STATION ("291.5","#685","Sumburgh Head","","SHE","","N","","","","59.85","-1.2667","100 Baud, Station ID 445");
STATION ("292.5","BA","Estaca de Bares","","ESP","2xIDLT","Y","","0","","43.78","-7.68","Marine");
STATION ("295.5","#684","Butt Of Lewis","","SCT","","N","","","","58.51667","-6.2667","100 Baud, Station ID 444");
STATION ("296.5","FI","Cabo Finisterre","","POR","2xIDLT","Y","","0","","42.8833","-9.27","Marine");
STATION ("297","#686","Girdle Ness","","SCT","","N","","","","57.1333","-2.05","100 Baud, Station ID 446");
STATION ("301","RTN","Romorantin","","FRA","ID+15st","Y","","0","","47.3197","1.6878","");
STATION ("303","KPG","Kaupanger","","NOR","","N","","400","","61.1772","7.2144","");
STATION ("305","KA","Klaipeda","","LTU","","?","","0","","55.72777","21.09777","Marine");
STATION ("305.7","DA","Dalatangi","","ISL","","Y","","900","","65.27","-13.5761","");
STATION ("306","#681","Lizard","","ENG","","?","","","","49.96667","-5.2","100 Baud, Station ID 441");
STATION ("306.5","AV","Avord","","FRA","ID+19st","Y","","0","","46.8817","2.93","");
STATION ("308","DI","Dagali","","NOR","","N","","400","","60.4114","8.4711","");
STATION ("309","LG","Saatenes","","SWE","","N","","400","","58.4547","12.7172","");
STATION ("310","TRL","Troll","","XOE","","?","","","","60.6458","3.7263","Daytimer");
STATION ("315.5","SS","Scatsta","","SHE","9sec","N","","400","","60.46","-1.21527","");
STATION ("316","BGU","Bergerud","","NOR","","N","","400","","59.8486","11.2622","");
STATION ("316","RE","Reykjanesskoli","","ISL","","N","","1020","","65.9264","-22.4317","");
STATION ("316","OE","Dublin","","IRL","8","N","400","400","","53.43","-6.4286","");
STATION ("317","HG","Hegra","","NOR","","N","","400","","63.4586","11.0753","");
STATION ("319","VAR","Varhaug","","NOR","ID+8sg","N","400","400","25","58.6269","5.6281","Daytimer");
STATION ("320","DO","Djupivogur","","ISL","ID+4sg","N","","1020","","64.6525","-14.2769","");
STATION ("322","GDA","Gdansk","","POL","ID+2sg","?","","1020","","54.345","18.5958","");
STATION ("322","KOR","Korso/hels.","","FIN","ID+4sg","?","","400","","60.3719","25.0683","");
STATION ("322","OU","Bromma","","SWE","","?","","400","","59.3164","18.0467","");
STATION ("322","RL","La Rochelle","","FRA","ID+16st","Y","","0","","46.17722","-1.0975","");
STATION ("322","ORS","Orleans","","FRA","","Y","","0","","47.9383","2.2433","");
STATION ("323","SMA","Santa Maria","","AZR","ID+6sg","N","","1020","","36.9966","-25.1752","<a href='pix/sma_thumb.jpg' target='_blank'>Screen Capture</a>");
STATION ("324","AS","Kemi","","FIN","ID+2sg","N","","400","","65.675","24.39","");
STATION ("324","MS","Mosjoen","","NOR","","N","","400","","65.8277","13.2655","");
STATION ("324","HE","Heddal","","NOR","","N","","400","","59.6094","9.0061","");
STATION ("324","MOU","Moulins","","FRA","ID+14st","Y","","0","50","46.7058","3.6308","");
STATION ("324","ON","Norrköping","","SWE","ID+1sg","N","","400","","58.5844","16.385","");
STATION ("325","AC","Glasgow","","SCT","7.5","N","400","400","25","55.8139","-4.5425","");
STATION ("325","DP","Diepholz","","DEU","ID+4&9st","?","","1020","25","52.5922","8.4528","");
STATION ("325","RH","Reykholt","","ISL","","N","","1020","","64.6642","-21.2864","Slow");
STATION ("325","NUT","Hemavan","","SWE","","N","","400","","65.83667","15.045","");
STATION ("325","AVS","Asturias","","ESP","ID+5sg","N","","1020","","43.55777","-6.01722","");
STATION ("325","JOE","Joensuu","","FIN","","?","","","","62.6858","29.4719","");
STATION ("325","PG","Trollhättan","","SWE","","N","","400","","58.2636","12.4175","");
STATION ("326","OG","Gavle/sandviken","","SWE","","N","","","","60.6636","16.9539","");
STATION ("326","FSK","Fauske","","NOR","","N","","400","","67.2639","15.1403","");
STATION ("326","TO","Malvik","","NOR","","N","","400","","63.4394","10.6783","");
STATION ("326","SUI","Tampere","","FIN","","N","","400","","61.515","24.0533","");
STATION ("326","RSH","Rush","","IRL","4.5","N","400","400","","53.5117","-6.1103","");
STATION ("326","YW","Tyra West (dan.)","","XOE","","N","","400","","55.4259","4.45","");
STATION ("326","CMC","Mac Culloch","","XOE","","N","","","","58.1925","0.4333","");
STATION ("327","LNZ","Linz","","AUT","7.5","N","1020","1020","","48.2369","14.3216","");
STATION ("327","Y","Sveg","","SWE","","N","","400","","62.04777","14.40444","");
STATION ("327","MVC","Merville","","FRA","ID+4&11st","Y","","0","","50.5716","2.58833","");
STATION ("327","POR","Porto","","POR","ID+4sg","N","","1020","","41.3481","-8.7081","");
STATION ("328","DK","Vilhemina","","SWE","ID+1sg","N","","400","","64.5922","16.7253","");
STATION ("328","CL","Carlisle","","ENG","8.5","N","400","400","","54.9403","-2.8039","");
STATION ("328","HIG","S. Sebastian","","ESP","ID+20st","Y","","1020","200","43.38611","-1.79583","");
STATION ("329","FJ","Fjoertoft","","NOR","","N","","400","","62.6936","6.4269","");
STATION ("329","IB","Tallin","","EST","ID+6sg","?","","400","","59.4139","24.6067","");
STATION ("329","NMS","Namsos","","NOR","","N","","400","","64.4947","11.8086","");
STATION ("329","VX","Växjö","","SWE","ID+1sg","N","","400","","56.8572","14.6975","");
STATION ("329","NS","Niort/Souche","","FRA","ID+16st","Y","","0","","46.3416","-0.2883","");
STATION ("330","SKS","Karlstad","","SWE","ID+2sg","N","","400","","59.3767","13.2733","");
STATION ("330","PNO","Piaseczno","","POL","ID+3sg","N","","1020","","52.0533","21.0625","");
STATION ("330","HN","Hornafjordur","","ISL","8","N","","1020","","64.2692","-15.2133","");
STATION ("331","GLW","Glasgow","","SCT","6","N","400","400","","55.87","-4.4333","");
STATION ("331","NSM","Inishmaan Isl.","","IRL","5","N","","400","","53.0833","-9.5333","");
STATION ("331","TE","Thisted","","DNK","ID+1sg","N","","400","","57.0533","8.8642","Daytimer");
STATION ("331.5","TLF","Toulouse","","FRA","ID+23st","Y","","0","","43.6","1.2166","");
STATION ("332","FAR","Faro","","POR","ID+3sg","N","","1020","","37.0086","-7.9258","");
STATION ("331","TUR","Tours","","FRA","ID+16st","Y","","0","15","47.5658","0.7825","");
STATION ("332","LL","Lille/Lesquin","","FRA","ID+8sg","N","400","400","","54.58166","3.21638","");
STATION ("332","LL","Lille/lesquin","","FRA","10","N","400","400","","54.5817","3.2164","");
STATION ("332","VA","Ylievieska","","FIN","ID+6sg","?","","400","","64.1294","24.6425","");
STATION ("333","GFC","Gullfaks C","","XOE","","N","","","50","61.215","2.2736","Daytimer");
STATION ("333","LE","Västeraas","","SWE","","N","","400","","59.6639","16.6672","");
STATION ("333.5","HRD","Harding","","XOE","","N","","","","59.1646","1.3058","");
STATION ("334","GMN","Gormanston","","IRL","5","N","400","400","","53.6481","-6.2267","");
STATION ("334","OPA","Oppaker","","NOR","","?","","400","","60.2042","11.52","");
STATION ("335","BER","Bern","","SUI","ID+6sg","N","","400","","46.8988","7.5102","");
STATION ("335","EL","Ellidavatn","","ISL","","N","","1020","","64.0811","-21.7708","");
STATION ("336","BTA","Bratta","","NOR","","N","","400","","60.0489","5.3003","Daytimer");
STATION ("336","RS","Roeros","","NOR","","N","","400","","62.5797","11.335","");
STATION ("337","MY","Mykinnes","","FRO","ID+6sg","N","","400","","62.1067","-7.5875","Daytimer");
STATION ("338","PST","Porto Santo","","MDR","ID+4sg","N","","1020","2000","33.0683","-16.358","<a href='pix/pst_thumb.jpg' target='_blank'>Screen Capture</a>");
STATION ("338","OA","Jönköping","","SWE","ID+1sg","N","","400","","57.8164","14.1147","");
STATION ("339","LS","Langeneset","","NOR","","N","","400","","61.4111","5.8867","");
STATION ("339","SD","Sindal","","DNK","","N","","400","","57.5013","10.1519","");
STATION ("339","EA","Arlanda, Stockholm","","SWE","","N","","400","","59.68","18.1083","");
STATION ("340","HEI","Heidrun","","XOE","ID+2sg","N","","400","40","65.3256","7.3158","");
STATION ("341","EDN","Edinburgh","","SCT","8","N","400","400","25","55.9783","-3.2853","");
STATION ("341","LO","Billund","","DNK","ID+6sg","N","","400","","55.7444","9.2797","");
STATION ("341","POR","Pori","","FIN","ID+4sg","?","","400","","61.5064","21.6694","");
STATION ("341","NKS","Karlstad","","SWE","ID+3sg","N","","400","","59.5319","13.4197","");
STATION ("341","AMB","Amboise","","FRA","ID+14st","?","","0","50","47.4181","1.0408","");
STATION ("342","LL","Leirin/fagernes","","NOR","","N","","400","","61.0161","9.2964","");
STATION ("342","SUT","Hemavan","","SWE","","N","","400","","65.71083","15.20166","");
STATION ("342","TA","Thistle Alpha","","XOE","","N","","","","61.2147","1.3447","");
STATION ("343","KUS","Kaunas","","LTU","ID+1&2sg","N","","400","","54.9655","24.1266","");
STATION ("343","CGO","Charles De Gaulle","","FRA","ID+5sg","N","","400","","48.9872","2.4006","");
STATION ("344","HEK","Heka","","FIN","ID+5sg","?","","400","","60.2547","25.4911","");
STATION ("344","WIK","Wick","","SCT","6.5","N","400","400","25","58.4467","-3.0631","");
STATION ("345","BN","Birkeland","","NOR","","N","","400","","58.3039","8.2269","");
STATION ("345","FT","Fleten","","NOR","","N","","400","","61.3517","5.5525","");
STATION ("345","STM","Strommen/moirana","","NOR","","N","","400","","66.29","13.7686","");
STATION ("345","HL","Helgafell","","ISL","7s","?","","1020","","63.4242","-20.2819","");
STATION ("345","BT","Melbork/elblag","","POL","","N","","400","","54.1666","19.4166","");
STATION ("345","SUS","Susi","","FIN","","N","","400","","63.2197","23.0403","");
STATION ("345","ILA","Caslav","","CZE","","?","","1020","","49.9039","15.4328","Neg. keyed CF");
STATION ("346","GS","Gävle/sandviken","","SWE","","N","","400","","60.5528","16.9517","");
STATION ("346","LHO","Le Havre","","FRA","ID+6sg","N","","400","","49.595","0.1833","");
STATION ("346","MI","Mikkeli","","FIN","","N","","400","","61.713","27.0686","");
STATION ("346","WLU","Luxembourg","","LUX","ID+5sg","N","","1020","15","49.5678","6.0542","");
STATION ("347","JAD","Jade Platform","","XOE","","Y","","","","56.5057","2.1514","");
STATION ("347","MSK","Morskogen","","NOR","","N","","400","","60.4556","11.2686","");
STATION ("348","ATF","Aberdeen/dyce","","SCT","8","N","400","400","25","57.0775","-2.1056","Daytimer");
STATION ("348","SAD","Sandsund","","NOR","ID+4sg","N","","400","","68.0875","13.6369","");
STATION ("348","WA","Arlanda, Stockholm","","SWE","","N","","400","","59.6566","17.9183","");
STATION ("348","VG","Vagar","","FRO","ID+3sg","?","","400","","62.0433","-7.1956","");
STATION ("349","SPA","Sleipner A","","XOE","","N","","","","58.3666","1.9069","Daytimer");
STATION ("349","JX","Växjö","","SWE","ID+7sg","N","","400","","56.9961","14.7572","");
STATION ("349","TAR","Tarva","","NOR","ID+7sg","N","","400","","63.825","9.4253","");
STATION ("350","LAA","Laanila","","FIN","ID+4sg","?","","400","","64.9661","25.2111","");
STATION ("350","GLG","Glasgow","","SCT","ID+4sg","?","400","400","25","55.9242","-4.3358","");
STATION ("351","OV","Visby","","SWE","ID+1sg","N","","400","","57.7317","18.3983","");
STATION ("351","SBH","Sumburgh Head","","SHE","6","N","","400","","59.8822","-1.2947","");
STATION ("352","TRF","Tyrifjord","","NOR","","N","","400","","59.9292","10.2692","");
STATION ("352","ZO","Sola","","NOR","","N","","400","","58.9569","5.6353","Daytimer");
STATION ("352","NT","Newcastle","","ENG","10.5","N","400","400","90","55.0503","-1.6425","");
STATION ("352","RBU","Rambu","","NOR","ID+2sg","N","","400","","62.5058","11.5567","");
STATION ("353","OBA","Oseberg A","","XOE","ID+3sg","N","","1020","","60.4914","2.8247","");
STATION ("353","KRT","Kartuzy","","POL","ID+4sg","?","","1000","","54.2942","18.2028","");
STATION ("354","CGC","Cognac","","FRA","ID+18st","Y","","0","50","45.6692","-0.3061","");
STATION ("354","MTZ","Metz","","FRA","ID+15st","Y","","0","","49.2761","6.2086","");
STATION ("355","PIK","Prestwick","","SCT","6","N","400","400","","55.5058","-4.5772","");
STATION ("355","ONW","Antwerpen","","BEL","20s","Y","1020","1020","","51.1675","4.5664","");
STATION ("355","RK","Reykjavik","","ISL","5","N","","1020","","64.1514","-22.0289","");
STATION ("355","VGA","Vigra","","NOR","","N","","400","","62.5633","6.1297","");
STATION ("356","WBA","Wolwerhampton","","ENG","5s","N","400","400","","52.5175","-2.2614","");
STATION ("357","LP","Cholet","","FRA","ID+15st","Y","","0","","47.1364","-0.83611","");
STATION ("357","SEP","Seppi","","FIN","ID+5sg","?","","400","","63.1272","21.7106","");
STATION ("358","GRK","Graakallen","","NOR","ID+3sg","N","","400","25","63.4189","10.2681","");
STATION ("358","TUN","Tulln","","AUT","10","N","1020","1020","","48.3094","15.9811","");
STATION ("359","LK","Lindköping","","SWE","ID+2sg","N","","400","","58.4514","13.1197","");
STATION ("359","LOR","Lorient","","FRA","ID+15st","Y","","0","40","47.7631","-3.4406","");
STATION ("360","ASK","Askoey","","NOR","","N","","400","50","60.4219","5.18","Daytimer");
STATION ("360","SL","Svinafell/hornafj.","","ISL","ID+3sg","?","","1020","","64.3836","-15.3867","");
STATION ("360","OS","Göteborg","","SWE","ID+1sg","N","","400","","57.8119","11.8814","");
STATION ("360.5","MAK","Mackel","","BEL","12","Y","","1020","25","50.9647","3.4961","");
STATION ("361","LIE","Lieto","","FIN","ID+6sg","?","","400","","60.5208","22.4186","");
STATION ("361","RO","Kauhava","","FIN","ID+6sg","?","","400","","63.0233","23.0636","");
STATION ("361","NB","Bordeaux","","FRA","ID+11st","Y","","0","25","45.1475","-0.5497","");
STATION ("362","BVK","Baatvik","","NOR","","N","","400","","62.2408","5.81","");
STATION ("362","NN","Eskilstuna","","SWE","ID+1sg","N","","400","","59.3941","16.7086","");
STATION ("362","JAN","Jan Mayen","","JMY","ID+5sg","N","","400","50","70.9611","-8.5758","");
STATION ("362","CJN","Castejon","","ESP","ID+5sg","?","","1020","","40.3839","-2.5228","");
STATION ("363","ERL","Erlangen","","DEU","ID+5st","Y","","","","","","");
STATION ("364","GRU","Grudziadz","","POL","ID+4sg","?","","1700","","53.5153","18.7717","");
STATION ("364","OK","Keflarvik","","ISL","5.5","N","","1020","","64.0494","-22.605","");
STATION ("365","VS","Vesilahti","","FIN","","?","","","","61.3769","23.4458","");
STATION ("365","ES","Egilsstadir","","ISL","ID+6sg","N","","1020","","65.2386","-14.4519","A single 'e' observed in each gap.");
STATION ("366","UTH","Uthaug","","NOR","ID+6sg","N","","400","","63.7228","9.5778","");
STATION ("366","HK","Holmavik","","ISL","","?","","1020","","65.6436","-21.4736","");
STATION ("366","KM","Kalmar","","SWE","ID+2sg","N","","400","","56.7475","16.2308","");
STATION ("367","OQ","Frigg","","XOE","","N","","400","","59.5242","2.0355","");
STATION ("368","FN","Finndal/skien","","NOR","","N","","400","","59.1578","9.5594","");
STATION ("368","WTD","Waterford","","IRL","5","N","400","400","","52.1889","-7.0833","");
STATION ("368","UW","Edinburgh","","SCT","8","N","400","400","","55.905","-3.5025","");
STATION ("368","OY","Sveg","","SWE","","N","","400","","62.0453","14.3261","");
STATION ("368.5","ELU","Luxembourg","","LUX","ID+3sg","N","","1020","15","49.6797","6.3553","");
STATION ("369","GL","Nantes","","FRA","","N","","","50","47.0528","-1.6878","");
STATION ("369","STG","Stegen","","NOR","","N","","400","","61.8608","6.3675","");
STATION ("370","KS","Kinloss","","SCT","10","N","400","400","100","57.6503","-3.5869","");
STATION ("370","OHT","Arlanda","","SWE","","N","","400","","59.5739","17.8903","");
STATION ("370","STR","Sintra","","POR","ID+6sg","N","","1020","","38.8777","-9.4017","");
STATION ("370.5","AP","Aberporth","","WLS","ID+6sg","N","","400","","52.1161","-4.5597","");
STATION ("370.5","LB","Angelholm","","SWE","ID+2sg","N","","400","","56.3467","12.7683","");
STATION ("371","BRS","Bremsnes","","NOR","ID+7sg","N","","400","50","63.0847","7.6478","");
STATION ("371","HAA","Hamar","","NOR","ID+3sg","N","","400","","60.8172","11.07","");
STATION ("371","MGL","Ponta Delgada","","AZR","ID+4sg","N","","1020","","37.7414","-25.5838","<a href='pix/mgl_thumb.jpg' target='_blank'>Screen Capture</a>");
STATION ("372","ODR","Odderoeya","","NOR","","N","","400","","58.14","7.9989","");
STATION ("372","NDO","Nordholz","","DEU","ID+3st","Y","","1020","","53.785","8.8061","");
STATION ("372","OZN","Prins Cr. Sund","","GRL","ID+30sg","N","","400","","60.0572","-43.1597","<a href='pix/ozn_thumb.jpg' target='_blank'>Screen Capture</a>");
STATION ("373","KEM","Kemi","","FIN","ID+5sg","N","","400","25","65.84639","24.60278","");
STATION ("373","MP","Cherbourg","","FRA","","?","","","","49.638","-1.3722","");
STATION ("374","CBN","Cumbernauld","","SCT","5","N","400","400","","55.9756","-3.9747","");
STATION ("374","BL","Bringeland","","NOR","","N","","400","","61.3897","5.7603","");
STATION ("374.5","ANC","Ancona","","ITA","ID+3sg","N","","1020","35","43.5867","13.4722","");
STATION ("375","SNR","Snorre","","XOE","","N","","400","","61.4506","2.1433","Daytimer");
STATION ("375","CHO","Chociwel","","POL","","?","","1020","","53.4753","15.3328","");
STATION ("375","FR","Pori","","FIN","ID+6sg","N","","400","","61.4183","21.9269","");
STATION ("375","TR","Thor Platform","","XOE","","N","","400","50","56.6414","3.3256","Daytimer");
STATION ("375","VM","Vestmannaeyar","","ISL","ID+3st","Y","","1020","100","63.39972","-20.28833","<a href='pix/vm_thumb.jpg' target='_blank'>Screen Capture</a>");
STATION ("376","TL","Tingwall","","SHE","ID+2sg","N","","400","","60.18833","-1.2461","Daytimer");
STATION ("377","PA","Rovaniemi","","FIN","ID+6sg","?","","400","","66.4689","25.6486","");
STATION ("377","SM","Mora/siljan","","SWE","","N","","400","","60.8866","14.565","");
STATION ("378","RSY","Rennesoey","","NOR","","N","","400","","59.1331","5.6417","Daytimer");
STATION ("379","REK","Reksten","","NOR","","N","","400","","61.5628","4.8492","Daytimer");
STATION ("380","FLR","Flornes","","NOR","7","N","","400","50","63.4592","11.3408","");
STATION ("380","ULA","Ula","","XOE","ID+6sg","N","","400","50","57.1106","2.8447","Daytimer");
STATION ("381","AB","Akraberg","","FRO","ID+6sg","N","","400","","61.395","-6.6811","Daytimer");
STATION ("381","ESP","Espoo","","FIN","ID+3sg","?","","400","","60.2481","24.7964","");
STATION ("381","RG","Rygge","","NOR","ID+7sg","N","","400","","59.3244","11.005","");
STATION ("383","LST","Lista","","NOR","","N","","400","50","58.0714","6.6867","");
STATION ("383","SHD","Scotstown Head","","SCT","5","N","400","400","","57.5592","-1.8172","Daytimer");
STATION ("384","HNS","Hoynes","","NOR","","N","","400","","61.9106","5.7106","");
STATION ("384","TY","Torsby","","SWE","","N","","400","","60.0997","13.0422","");
STATION ("385","OAN","Orleans","","FRA","ID+15st","Y","","0","25","48.0011","1.7686","");
STATION ("385","WL","Barrow","","ENG","8","N","400","400","","54.1256","-3.2628","");
STATION ("385","KV","Halli","","FIN","ID+6sg","N","","400","","61.8627","24.9816","");
STATION ("385.5","MLE","Miller","","XOE","","N","","","","58.432","1.2407","");
STATION ("386","BDL","Brent Delta","","XOE","","N","","400","","61.0756","1.441","");
STATION ("386","LK","Tallin","","EST","ID+6sg","?","","400","","59.4128","24.925","");
STATION ("387","SOK","Sokna","","NOR","","N","","400","","60.2369","9.9153","");
STATION ("387","ING","St. Inglevert","","FRA","","?","","","25","50.8831","1.7417","");
STATION ("388","COR","Corner","","SWE","6","N","","400","","59.2614","17.4839","");
STATION ("388","KRU","Kronoby","","FIN","ID+4sg","?","","400","","63.7917","23.1739","");
STATION ("389","CP","Caparica","","POR","ID+5sg","N","","1020","","38.6422","-9.2214","");
STATION ("389","CC","Charleroi","","BEL","4.5","N","","1020","15","50.4689","4.4847","");
STATION ("389","HN","Hovden","","NOR","ID+6sg","N","","400","","62.1633","6.0247","");
STATION ("389","MR","Myra/skien","","NOR","","N","","400","","59.2678","9.5744","");
STATION ("389","ZRZ","Zaragoza","","ESP","","?","","","300","41.73027","-1.19305","");
STATION ("390","DR","Dinard","","FRA","ID+16st","Y","","0","","48.48194","-2.0528","");
STATION ("390","JEV","Jever","","DEU","ID+3st","Y","","1020","25","53.5169","8.0189","");
STATION ("390","LV","Arvika","","SWE","ID+1sg","N","","400","","59.6261","12.6358","");
STATION ("390.5","ITR","Istres","","FRA","ID+16st","Y","","0","25","43.52611","4.92972","");
STATION ("391","OKR","Stefanik-N","","SVK","ID+6sg","N","","1020","2000","48.2236","17.29027","");
STATION ("391","DDP","Dorado","","PTR","ID+6sg","N","","1020","2000","18.4681","-66.4122","<a href='pix/ddp_thumb.jpg' target='_blank'>Screen Capture</a>");
STATION ("391","BV","Bauvais/tillé","","FRA","ID+15st","Y","","0","","49.49166","2.0294","");
STATION ("392","AS","Angers","","FRA","ID+16st","Y","","0","","47.5772","-0.15138","");
STATION ("392","GDY","Godby","","FIN","ID+4sg","N","","400","","60.1866","19.9544","");
STATION ("392","RW","Tegel West","","DEU","ID+9sg","N","","400","50","52.5453","13.1511","");
STATION ("392","KF","Keflavik","","ISL","ID+4st","Y","","1020","","63.985","-22.7317","");
STATION ("392","RAN","Lappeenranta","","FIN","ID+5sg","N","","400","","61.0183","28.0156","");
STATION ("393","RD","Unid","","XUE","","?","","","","","","");
STATION ("393","ARA","Amoco/arbroath A","","XOE","","N","","","","57.3741","1.3819","Daytimer");
STATION ("393","TAT","Tautra","","NOR","ID+6sg","N","","400","","62.6797","6.9122","");
STATION ("393","AB","Hagfors","","SWE","","N","","400","","59.95","13.5866","");
STATION ("394","DND","Dundee","","SCT","6","N","400","400","","56.455","-3.1147","");
STATION ("394","JOK","Kauhajoki","","FIN","","?","","400","","62.4983","22.5983","");
STATION ("394","NB","Bromma","","SWE","ID+1sg","N","","400","","59.4008","17.8139","");
STATION ("394","NV","Nevers","","FRA","ID+16st","Y","","0","","46.9566","3.19","");
STATION ("395","B","Bilbao","","ESP","ID+9sg","N","","1020","","43.3733","-3.0333","");
STATION ("395","FC","Figeac/livernon","","FRA","ID+17st","Y","","0","","44.6716","1.7883","");
STATION ("395","FOY","Foynes","","IRL","6","?","400","400","50","52.5661","-9.1953","");
STATION ("395","KW","Kirkwall","","ORK","7","N","","400","","58.9594","-2.9114","Daytimer");
STATION ("396","YG","Rygge/enge","","NOR","7","N","","400","","59.3697","10.8311","");
STATION ("396","ROC","Rochefort","","FRA","ID+14sg","N","","0","","45.8883","0.98625","");
STATION ("396","MV","Mesters Vig","","GRL","","N","","400","","72.2333","-23.9167","<a href='pix/mv_thumb.jpg' target='_blank'>Screen Capture</a>");
STATION ("396","HYV","Hyvinkää","","FIN","ID+4sg","N","","400","","60.5527","24.7144","");
STATION ("396.5","PY","Plymouth","","ENG","8","N","400","400","","50.4242","-4.1122","");
STATION ("397","LM","Borlänge","","SWE","","N","","400","","60.3903","15.5875","");
STATION ("397","NF","Falköping","","SWE","","N","","400","","58.2197","13.6517","");
STATION ("397","OL","Szczecin","","POL","ID+2sg","N","","1020","","53.555","14.96","");
STATION ("397","OP","Dublin","","IRL","9.5","?","400","400","15","53.4136","-6.1383","");
STATION ("397.5","NIA","Brittania","","XOE","","N","","400","","58.0486","-1.1386","");
STATION ("398","AL","Lepsoey","","NOR","ID+4sg","N","","400","","62.5958","6.2214","");
STATION ("398","ESS","Kronoby","","FIN","","N","","400","","63.6367","23.1133","");
STATION ("398","OK","Connaught","","IRL","9.5","N","","1020","","53.9239","-8.6997","");
STATION ("398","PEO","Skavsta","","SWE","ID+1sg","N","","400","","58.7967","17.0483","");
STATION ("399","FM","Trollhättan","","SWE","","N","","400","","58.3614","12.2922","");
STATION ("399","NGY","New Galloway","","SCT","6.5","N","400","400","80","55.1775","-4.1686","");
STATION ("399.5","ONO","Oostende","","BEL","12","Y","1020","1020","25","51.2175","2.9964","");
STATION ("400","EN","Oerebro","","SWE","ID+1sg","N","","400","","59.2908","15.065","");
STATION ("400","NS","Kerr Mc Gee / Platform","","XOE","","N","","1020","","60.805","1.4486","");
STATION ("400","NTD","Notodden","","NOR","","N","","400","","59.5869","9.1442","");
STATION ("401","JP","Joensuu","","FIN","","N","","400","","62.6378","29.7572","");
STATION ("402","LX","Eskilstuna","","SWE","ID+2sg","N","","400","","59.2758","16.7161","");
STATION ("402.5","LBA","Leeds/bradford","","ENG","10","N","400","400","45","53.8647","-1.6528","");
STATION ("403","NM","Mora North","","SWE","ID+1sg","N","","400","","61.0311","14.4603","");
STATION ("403","OJ","Jönköping","","SWE","ID+2sg","N","","400","","57.6931","14.0319","");
STATION ("404","DA","Dalen/sandefj.","","NOR","","N","","400","","59.2639","10.2564","");
STATION ("404","LA","Arlanda","","SWE","","N","","400","","59.7294","17.9467","");
STATION ("404","NL","Nolsoe","","FRO","ID+6sg","N","","400","","61.9581","-6.6","Daytimer");
STATION ("404","VNG","Vangsnes","","NOR","ID+4sg","N","","400","","61.15","6.65","Ca. position");
STATION ("405","AV","Göteborg","","SWE","","N","","400","","57.7386","11.8675","");
STATION ("405","BIC","Briare / Chatillon","","FRA","ID+51st","Y","","0","","47.6116","2.7833","");
STATION ("407","BG","Berga","","SWE","ID+2sg","N","","400","","59.0714","18.21","");
STATION ("407","GAR","Garristown","","IRL","5","N","400","400","50","53.5286","-6.4472","");
STATION ("407","KA","Karlskoga","","SWE","","N","","400","","59.3178","14.4764","");
STATION ("408","BRK","Bruck","","AUT","10","N","1020","1020","","48.0628","16.7167","");
STATION ("408","SD","Sandane","","NOR","","N","","400","","61.8461","6.0369","");
STATION ("409","CZE","Czempin","","POL","ID+3sg","?","","1020","","52.1361","16.7286","");
STATION ("409","CZDAR","Cze Dar Mix?","","POL","7.5s","N","","1020","","","","");
STATION ("409","DAR","Darlowo","","POL","ID+4sg","?","","1020","","54.4108","16.3961","");
STATION ("409","SG","Saatenäs","","SWE","ID+1sg","N","","400","","58.3811","12.7078","");
STATION ("410","BO","Boden","","SWE","","N","","400","","65.8392","21.7214","");
STATION ("410","MYYY6","Normand Pioneer","","XOE","","?","","","","","","Vessel");
STATION ("410","C6NO5","Saipem 7000","","XOE","","?","","","","","","Vessel");
STATION ("411","M","Kokkola","","FIN","","N","","400","","63.7333","23.15","");
STATION ("411","SOL","Unid","","XUE","ID+2st","Y","","1020","","","","");
STATION ("412","I","Halli","","FIN","","N","","400","","61.8577","24.8344","");
STATION ("413","BOA","Bologna","","ITA","ID+4sg","N","","A2A","40","44.5672","11.2","");
STATION ("413.5","DLS","Lübars","","DEU","ID+1sg","?","","1020","50","52.6139","13.3636","");
STATION ("414","HD","Hestad","","NOR","ID+3sg","N","","400","","66.0603","12.5503","");
STATION ("414","LF6N","Statfjord","","XOE","","N","","400","","61.1748","1.5409","");
STATION ("414","SLB","Solberg","","NOR","ID+6sg","N","","400","","60.0122","10.9739","");
STATION ("414","CRC","Unid","","XUE","","N","1020","1020","","","","");
STATION ("414","SJA","Senja","","NOR","","N","","400","","69.1611","17.8158","");
STATION ("415","OL","Lindköping","","SWE","ID+2sg","N","","400","","58.3794","15.8072","");
STATION ("416","BCS","Baccus","","SWE","ID+2sg","N","","400","","62.5703","17.045","");
STATION ("416","R","Arvika","","SWE","","N","","400","","59.6633","12.64","416?");
STATION ("416","TOR","Toramo","","FIN","","N","","400","","66.6386","25.9708","");
STATION ("417","AH","Angelholm","","SWE","ID+3sg","N","","400","","56.2658","12.9033","");
STATION ("417","R","Gävle","","SWE","2xID+2sg","N","","400","","60.61666","16.95","");
STATION ("418","L","Tallin","","EST","ID+7sg","?","","400","25","59.4131","24.8767","");
STATION ("418","MK","Calais","","FRA","ID+16st","Y","","0","","50.82961","2.0545","");
STATION ("419","RA","Tyra East","","XOE","","N","","400","","55.4318","4.4812","");
STATION ("419","HY","Vaasa","","FIN","","N","","400","","62.98","21.815","");
STATION ("419","RD","Vesteraas","","SWE","ID+2sg","N","","400","","59.5178","16.6078","");
STATION ("420","KN","Kobona","","RUS","","?","","","","60.0333","31.55","");
STATION ("420","SS","Styrup/malmo","","SWE","ID+1sg","N","","400","","55.46","13.3919","");
STATION ("421","BL","Borlänge","","SWE","","N","","400","","60.4708","15.4125","");
STATION ("421","MF","Malmstad","","SWE","3","N","","400","","56.6533","12.8083","");
STATION ("421","T","Lindköping","","SWE","ID+1sg","N","","400","","58.3964","15.7303","");
STATION ("423","CWL","Cranwell","","ENG","10","N","400","400","5","53.0261","-0.4889","");
STATION ("423","FE","Odense","","DNK","ID+2sg","N","","400","","55.5216","10.4633","");
STATION ("424","HRW","Heathrow","","ENG","9","N","400","400","10","51.4783","-0.4583","");
STATION ("425","OU","Umeaa","","SWE","","N","","400","","63.8453","20.1794","");
STATION ("426","PW","Prestwick","","SCT","5","N","400","400","","55.5442","-4.6814","");
STATION ("427","LUE","Lunde","","SWE","","N","","400","","62.88","17.8267","");
STATION ("427","RY","Royan (Medis)","","FRA","ID+15st","Y","","0","","45.6158","-0.8666","");
STATION ("428","BST","Lanveoc","","FRA","ID+13st","Y","","0","25","48.2836","-4.43222","");
STATION ("428","CTX","Chateauroux","","FRA","ID+15st","?","","0","50","46.9364","1.8011","");
STATION ("428","MCH","Manchester","","ENG","6","N","400","400","50","53.3531","-2.2728","");
STATION ("429","POZ","Poznan/pozarevac","","POL","ID+3sg","N","","1020","40","52.3981","16.9444","");
STATION ("430","SN","St.Yan","","FRA","","?","","","","46.29436","4.12138","");
STATION ("431","SAY","Stornoway","","SCT","6","N","400","400","","58.2139","-6.3261","");
STATION ("432","AKU","Ivalo","","FIN","","N","","","","68.6783","27.6144","");
STATION ("432","MDA","Armada","","XOE","","N","","","","57.5716","1.5027","");
STATION ("432","PK","Prvek","","CZE","ID+2sg","N","","1020","","50.01083","15.8127","");
STATION ("432","G","Ämari","","EST","","N","","400","","59.2597","24.2047","");
STATION ("433.5","HEN","Henton","","ENG","4.5","N","400","400","25","51.7597","-0.7903","");
STATION ("434","MV","Melun","","FRA","ID+16st","Y","","0","","48.5538","2.978","");
STATION ("435","GR","Gorka","","RUS","2xID+20sg","N","","400","","59.8166","32.35","");
STATION ("474","RZ","Rzeszow","","POL","ID+2sg","N","","1020","","50.1086","22.1338","");
STATION ("520","AE","Slipsk/redzikow","","POL","","?","","1020","","54.4667","17.1","");
STATION ("525","HG","Wroclaw","","POL","ID+3sg","?","","1020","","51.0822","16.9514","");
STATION ("544","LFGC","Bideford Dolphin","","XOE","","?","","","","","","Vessel");
STATION ("544","LF5E","Huldra","","XOE","","?","","","","","","");
STATION ("561","LF4B","Troll, Gass","","XOE","","?","","","","","","");
STATION ("598","LF4Q","West Vanguard ?","","XOE","","N","","400","","","","");
STATION ("598","LF6K","Unid Platform","","XOE","","N","","","","","","");
STATION ("615","LF6M","Statfjord B","","XOE","","N","","400","50","61.2069","1.8306","Pos. USB/Daytimer");
STATION ("615","OM","Gorm A (dan.)","","XOE","","Y","","400","","55.3448","4.4536","Pos. USB/Daytimer");
