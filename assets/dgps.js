function DGPS(id1,id2,ref,khz,bps,qth,sta,cnt){
  dgps[id1] =	new Array(ref,khz,bps,qth,sta,cnt,id1,id2);
  dgps[id2] =	new Array(ref,khz,bps,qth,sta,cnt,id1,id2);
}

function dgps_lookup(id) {
  if (id=="") {
    return "Enter a valid Reference Station ID\nand try again";
  }

  if (typeof dgps[id] == 'undefined') {
    return "Station not recognised";
  }
  var a = dgps[id];
  return("Station "+a[0]+"\n"+a[1]+" KHz "+a[2]+" Baud"+"\n"+a[3]+" "+a[4]+" "+a[5]+"\nReference ID(s): "+a[6]+(a[6]!=a[7] ? ", "+a[7] : ""));
}

dgps =	new Array();

DGPS ("8","9","#804","286","200","Sandy Hook","NJ","USA");
DGPS ("22","23","#811","286","100","Key West","FL","USA");
DGPS ("258","259","#879","286","100","Upolu Point","","HWA");
DGPS ("310","311","#918","286","200","Wiarton","ON","CAN");
DGPS ("60","61","#849","287","100","Polson","MT","USA");
DGPS ("266","267","#883","287","100","Pigeon Point","CA","USA");
DGPS ("272","273","#886","287","100","Fort Stevens","OR","USA");
DGPS ("2","3","#801","288","100","Portsmouth Harbor","NH","USA");
DGPS ("284","285","#892","288","100","Gustavus","","ALS");
DGPS ("340","341","#942","288","200","Cap Ray","NL","CAN");
DGPS ("12","13","#806","289","100","Driver","VA","USA");
DGPS ("18","19","#809","289","100","Cape Canaveral","FL","USA");
DGPS ("296","297","#898","289","100","Cold Bay","","ALS");
DGPS ("44","45","#799","290","200","Penobscot","ME","USA");
DGPS ("168","169","#869","290","200","Louisville","KY","USA");
DGPS ("254","255","#877","290","100","Pahoa","","HWA");
DGPS ("184","185","#788","291","100","Hawk Run","PA","USA");
DGPS ("54","55","#845","291","100","Albuquerque","NM","USA");
DGPS ("192","193","#778","292","100","Kensington","SC","USA");
DGPS ("112","113","#836","292","200","Cheboygan","MI","USA");
DGPS ("96","97","#841","292","100","Pine River","MN","USA");
DGPS ("270","271","#885","292","100","Cape Mendocino","CA","USA");
DGPS ("288","289","#894","292","100","Cape Hinchinbrook","","ALS");
DGPS ("6","7","#803","293","100","Moriches","NY","USA");
DGPS ("28","29","#814","293","200","English Turn","LA","USA");
DGPS ("196","197","#771","294","100","New Bern","NC","USA");
DGPS ("34","35","#817","295","100","Isabela","","PTR");
DGPS ("282","283","#891","295","100","Level Island","","ALS");
DGPS ("326","327","#939","295","200","Partridge Island","NB","CAN");
DGPS ("30","31","#815","296","100","Galveston","TX","USA");
DGPS ("100","101","#830","296","100","Wisconsin Point","WI","USA");
DGPS ("312","313","#929","296","200","St. Jean sur Richelieu","QC","CAN");
DGPS ("106","107","#833","297","100","Milwaukee","WI","USA");
DGPS ("10","11","#805","298","200","Cape Henlopen","DE","USA");
DGPS ("16","17","#808","298","100","Charleston","SC","USA");
DGPS ("102","103","#831","298","100","Upper Keweenaw","MI","USA");
DGPS ("166","167","#868","298","200","Omaha","IA","USA");
DGPS ("290","291","#895","298","100","Potato Point","","ALS");
DGPS ("330","331","#937","298","200","Hartlen Point","NS","CAN");
DGPS ("162","163","#866","299","200","Sallisaw","OK","USA");
DGPS ("344","345","#946","299","200","Rigolet","NL","CAN");
DGPS ("26","27","#813","300","100","Mobile Point","AL","USA");
DGPS ("172","173","#871","300","100","Appleton","WA","USA");
DGPS ("260","261","#880","300","200","Kokole Point","","HWA");
DGPS ("306","307","#906","300","200","Sandspit","BC","CAN");
DGPS ("318","319","#926","300","200","Rivière du Loup","QC","CAN");
DGPS ("48","49","#822","301","200","Macon","GA","USA");
DGPS ("246","247","#828","301","100","Angleton","TX","USA");
DGPS ("114","115","#837","301","100","Saginaw Bay","MI","USA");
DGPS ("58","59","#847","301","200","Annapolis","MD","USA");
DGPS ("262","263","#881","302","100","Point Loma","CA","USA");
DGPS ("276","277","#888","302","100","Whidbey Island","WA","USA");
DGPS ("801","801","#901","302","","Miraflores","","PNR");
DGPS ("174","175","#873","303","100","Myton","UT","USA");
DGPS ("32","33","#816","304","100","Aransas Pass","TX","USA");
DGPS ("40","41","#820","305","100","Alexandria","VA","USA");
DGPS ("164","165","#867","305","200","Kansas City","MO","USA");
DGPS ("280","281","#890","305","100","Biorka Island","","ALS");
DGPS ("198","199","#772","306","200","Acushnet","MA","USA");
DGPS ("308","309","#919","306","200","Cardinal","ON","CAN");
DGPS ("50","51","#825","307","100","Hackleburg","AL","USA");
DGPS ("130","131","#834","307","100","Hagerstown","MD","USA");
DGPS ("200","201","#872","307","100","Pueblo","CO","USA");
DGPS ("336","337","#934","307","200","Fox Island","NS","CAN");
DGPS ("110","111","#835","309","200","Pickford","MI","USA");
DGPS ("146","147","#850","309","100","Clark","SD","USA");
DGPS ("170","171","#870","309","200","Reedy Point","DE","USA");
DGPS ("300","301","#909","309","200","Alert Bay","BC","CAN");
DGPS ("316","317","#927","309","200","Lauzon","QC","CAN");
DGPS ("148","149","#859","310","100","Whitney","NE","USA");
DGPS ("152","153","#861","310","200","Memphis","TN","USA");
DGPS ("268","269","#884","310","200","Point Blunt","CA","USA");
DGPS ("292","293","#896","310","100","Kenai","","ALS");
DGPS ("342","343","#944","310","200","Cape Norman","NL","CAN");
DGPS ("156","157","#863","311","200","Rock Island","IA","USA");
DGPS ("74","75","#798","312","100","Austin","NV","USA");
DGPS ("24","25","#812","312","200","Egmont Key","FL","USA");
DGPS ("244","245","#827","312","200","Tampa","FL","USA");
DGPS ("334","335","#935","312","200","Western Head","NS","CAN");
DGPS ("1008","1009","#821","313","200","Portsmouth","VA","USA");
DGPS ("150","151","#860","313","200","Vicksburg","MS","USA");
DGPS ("62","63","#874","313","100","Billings","MT","USA");
DGPS ("294","295","#897","313","100","Kodiak","","ALS");
DGPS ("320","321","#925","313","200","Moisie","QC","CAN");
DGPS ("210","211","#764","314","200","Lincoln","CA","USA");
DGPS ("302","303","#908","315","200","Amphitrite Point","BC","CAN");
DGPS ("338","339","#940","315","200","Cape Race","NL","CAN");
DGPS ("42","43","#800","316","100","Brunswick","ME","USA");
DGPS ("68","69","#848","316","100","Spokane","WA","USA");
DGPS ("144","145","#858","317","100","Hartsville","TN","USA");
DGPS ("158","159","#864","317","200","St Paul","MN","USA");
DGPS ("52","53","#823","318","100","Summerfield","TX","USA");
DGPS ("256","257","#878","318","100","Chico","CA","USA");
DGPS ("36","37","#818","319","100","Savannah","GA","USA");
DGPS ("116","117","#838","319","200","Detroit","MI","USA");
DGPS ("64","65","#876","319","100","Flagstaff","AZ","USA");
DGPS ("332","333","#936","319","200","Point Escuminac","NB","CAN");
DGPS ("160","161","#865","320","200","Millers Ferry","AL","USA");
DGPS ("304","305","#907","320","200","Richmond","BC","CAN");
DGPS ("264","265","#882","321","100","Lompoc","CA","USA");
DGPS ("20","21","#810","322","100","Miami","FL","USA");
DGPS ("104","105","#832","322","100","Sturgeon Bay","WI","USA");
DGPS ("118","119","#839","322","100","Youngstown","NY","USA");
DGPS ("154","155","#862","322","200","St Louis","MO","USA");
DGPS ("274","275","#887","323","200","Robinson Point","WA","USA");
DGPS ("278","279","#889","323","100","Annette Island","","ALS");
DGPS ("94","95","#844","324","200","Hudson Falls","NY","USA");
DGPS ("4","5","#802","325","200","Chatham","MA","USA");
DGPS ("176","177","#851","325","100","Medora","ND","USA");

