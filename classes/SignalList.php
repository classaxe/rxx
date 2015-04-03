<?php

class SignalList
{
    protected $html = '';
    protected $stats = array();
    protected $region;
    protected $filter_system;
    protected $ObjSignal;

    public function __construct()
    {
        global $mode, $submode, $targetID, $filter_active, $filter_date_1, $filter_date_2;
        global $filter_dx_gsq, $filter_dx_max, $filter_channels, $filter_dx_min, $filter_dx_units;
        global $filter_heard, $filter_id, $filter_system, $region, $filter_khz_1, $filter_khz_2;
        global $filter_sp, $filter_itu, $listenerID, $sortBy, $grp, $limit, $offset, $offsets;
        global $filter_custom;
        global $type_NDB, $type_TIME, $type_DGPS, $type_DSC, $type_NAVTEX, $type_HAMBCN, $type_OTHER;

        $this->ObjSignal = new Signal;

        $this->region = $region;
        if ($this->region=="") {
            switch (system) {
                case "REU":
                    $this->region = "eu";
                    break;
                case "RNA":
                    $this->region = "na";
                    break;
            }
        }
        $this->filter_system = $filter_system;
        if ($this->filter_system=="") {
            switch(system) {
                case "RNA":    $this->filter_system = 1;
                    break;
                case "REU":    $this->filter_system = 2;
                    break;
                case "RWW":    $this->filter_system = 3;
                    break;
            }
        }
    }

    public function draw()
    {
        global $mode, $submode, $targetID, $filter_active, $filter_date_1, $filter_date_2;
        global $filter_dx_gsq, $filter_dx_max, $filter_channels, $filter_dx_min, $filter_dx_units;
        global $filter_heard, $filter_id, $filter_khz_1, $filter_khz_2;
        global $filter_sp, $filter_itu, $listenerID, $sortBy, $grp, $limit, $offset, $offsets;
        global $filter_custom;
        global $type_NDB, $type_TIME, $type_DGPS, $type_DSC, $type_NAVTEX, $type_HAMBCN, $type_OTHER;

        $this->setup();

        if (!($listenerID && is_array($listenerID) && count($listenerID) && $listenerID[0])) {
            $listenerID =    false;
        }
        if ($filter_heard=="(All States and Countries)") {
            $filter_heard="";
        }

        if ($filter_id && substr($filter_id, 0, 1)=="#") {
            $type_DGPS=1;
        }
        if ($filter_id && substr($filter_id, 0, 1)=="$") {
            $type_NAVTEX=1;
        }
        switch ($this->filter_system) {
            case "1":
                $filter_system_SQL =                    "(`heard_in_na` = 1 OR `heard_in_ca` = 1)";
                $filter_listener_SQL =                "(`region` = 'na' OR `region` = 'ca' OR (`region` = 'oc' AND `SP` = 'hi'))";
                break;
            case "2":
                $filter_system_SQL =                    "`heard_in_eu` = 1";
                $filter_listener_SQL =                "(`region` = 'eu')";
                break;
            case "3":
                if ($this->region!="") {
                    $filter_system_SQL =                "`heard_in_".$this->region."`=1";
                    $filter_listener_SQL =                "(`region` = '".$this->region."')";
                } else {
                    $filter_system_SQL =                "1";
                    $filter_listener_SQL =                "1";
                }
                break;
            case "not_logged":
                $filter_system_SQL =
                 "(`heard_in_af` = 0 AND `heard_in_as` = 0 AND `heard_in_ca` = 0 AND `heard_in_eu` = 0 AND\n"
                ."`heard_in_iw` = 0 AND `heard_in_na` = 0 AND `heard_in_oc` = 0 AND `heard_in_sa` = 0)";
                $filter_listener_SQL =                "0";
                break;
            case "all":
                $filter_system_SQL =                     "1";
                $filter_listener_SQL =                "1";
                break;
        }
        switch ($filter_custom){
            case 'cle160':
                $filter_custom_SQL =
                 "`signals`.`ITU` IN('"
                .implode("','", explode(' ', 'ABW AFG AFS AGL AIA ALB ALG ALS AND ANI AOE ARG ARM ARS ASC ATA ATG ATN AUI AUS AUT AZE AZR BAH BAL BAR BDI BEL BEN BER BFA BGD BHR BIH BLR BLZ BOL BOT BRA BRB BRI BRM BRU BTN BUL CAB CAF CBG CEU CHL CHN CHR CKH CKS CLI CLM CLN CME CNR COD COG COM COR CPV CTI CTR CUB CVA CYM CYP CZE'))
                ."') OR `signals`.`SP` IN('"
                .implode("','", explode(' ', 'AB AK AL AR AT AZ BC CA CO CT'))
                ."')";
                break;
        }
        if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
            switch ($submode){
                case "delete":
                    $sql =    "DELETE FROM `logs` WHERE `signalID` = \"".addslashes($targetID)."\"";
                    mysql_query($sql);
                    $sql =    "DELETE FROM `signals` WHERE `ID` = \"".addslashes($targetID)."\"";
                    mysql_query($sql);
                    break;
            }
        }
        $filter_type =    array();
        if (!($type_NDB || $type_DGPS || $type_DSC || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER)) {
            switch (system) {
                case "RNA":    $type_NDB =    1;
                    break;
                case "REU":    $type_NDB =    1;
                    break;
                case "RWW":    $type_NDB =    1;
                    break;
            }
        }
        if ($type_NDB || $type_DGPS || $type_DSC || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER) {
            if ($type_NDB) {
                $filter_type[] =     "`type` = ".NDB;
            }
            if ($type_DGPS) {
                $filter_type[] =     "`type` = ".DGPS;
            }
            if ($type_DSC) {
                $filter_type[] =     "`type` = ".DSC;
            }
            if ($type_TIME) {
                $filter_type[] =     "`type` = ".TIME;
            }
            if ($type_HAMBCN) {
                $filter_type[] =     "`type` = ".HAMBCN;
            }
            if ($type_NAVTEX) {
                $filter_type[] =     "`type` = ".NAVTEX;
            }
            if ($type_OTHER) {
                $filter_type[] =     "`type` = ".OTHER;
            }
        }
        $filter_type =    "(".implode($filter_type, " OR ").")";
        if ($filter_heard) {
            $tmp =        explode(" ", strToUpper($filter_heard));
            sort($tmp);
            $filter_heard =    implode(" ", $tmp);
        }
      // Filter on 'Heard in':
        $filter_heard_SQL =    explode(" ", strToUpper($filter_heard));
        if ($grp=="all") {
            $filter_heard_SQL =    "`signals`.`heard_in` LIKE '%".implode($filter_heard_SQL, "%' AND `signals`.`heard_in` LIKE '%")."%'";
        } else {
            $filter_heard_SQL =    "`logs`.`heard_in` = '".implode($filter_heard_SQL, "' OR `logs`.`heard_in` = '")."'";
        }
        if ($filter_sp) {
            $tmp =        explode(" ", strToUpper($filter_sp));
            sort($tmp);
            $filter_sp =        implode(" ", $tmp);
            $filter_sp_SQL =    explode(" ", strToUpper($filter_sp));
            $filter_sp_SQL =    "`signals`.`SP` = '".implode($filter_sp_SQL, "' OR `signals`.`SP` = '")."'";
        }
        if ($filter_itu) {
            $tmp =        explode(" ", strToUpper($filter_itu));
            sort($tmp);
            $filter_itu =    implode(" ", $tmp);
            $filter_itu_SQL =    explode(" ", strToUpper($filter_itu));
            $filter_itu_SQL =    "`signals`.`ITU` = '".implode($filter_itu_SQL, "' OR `signals`.`ITU` = '")."'";
        }
      // Filter on Date Last Heard:
        if ($filter_date_1 || $filter_date_2) {
            if ($filter_date_1 == "") {
                $filter_date_1 = "1900-01-01";
            }
            if ($filter_date_2 == "") {
                $filter_date_2 = "2020-01-01";
            }
        }
      // Filter on Frequencies:
        if ($filter_khz_1 || $filter_khz_2) {
            if ($filter_khz_1 == "") {
                $filter_khz_1 = 0;
            }
            if ($filter_khz_2 == "") {
                $filter_khz_2 = 1000000;
            }
            $filter_khz_1 =    (float)$filter_khz_1;
            $filter_khz_2 =    (float)$filter_khz_2;
        }
        $filter_sp =        strToUpper($filter_sp);
        $filter_itu =        strToUpper($filter_itu);
        $filter_id =        strToUpper($filter_id);
        if (!isset($filter_dx_units)) {
            $filter_dx_units = "km";
        }
        if ($filter_dx_gsq) {
            $filter_dx_gsq =    strtoUpper(substr($filter_dx_gsq, 0, 4)).strtoLower(substr($filter_dx_gsq, 4, 2));
            $a =         GSQ_deg($filter_dx_gsq);
            $filter_dx_lat =    $a["lat"];
            $filter_dx_lon =    $a["lon"];
        }
        $filter_by_range = ($filter_dx_gsq && ($filter_dx_min || $filter_dx_max));
        if (!$filter_by_range && ($sortBy == "range" || $sortBy == "range_d")) {
            $sortBy =    "khz";
        }


        $sql =
         "SELECT\n"
        .($filter_heard || $listenerID ?
        "  COUNT(distinct `signals`.`ID`) AS `count`\n"
           ."FROM\n  `signals`,\n  `logs`\n"
           ."WHERE\n  `signals`.`ID` = `logs`.`signalID` AND\n"
           ."  ".$filter_system_SQL." "
           .($filter_heard ? "AND\n ($filter_heard_SQL)" : "")
           .($listenerID ? "AND\n(`logs`.`listenerID`=".implode($listenerID, " OR `logs`.`listenerID`=").") " : "")
        :
        "  COUNT(*) AS `count`\n"
           ."FROM\n  `signals`\nWHERE\n  ".$filter_system_SQL
        )
        .($filter_active ? " AND\n `active` = 1" : "")
        .($filter_by_range && $filter_dx_min ? " AND\n round(degrees(acos(sin(radians($filter_dx_lat)) * sin(radians(signals.lat)) + cos(radians($filter_dx_lat)) * cos(radians(signals.lat)) * cos(radians($filter_dx_lon - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) > $filter_dx_min" : "")
        .($filter_by_range && $filter_dx_max ? " AND\n round(degrees(acos(sin(radians($filter_dx_lat)) * sin(radians(signals.lat)) + cos(radians($filter_dx_lat)) * cos(radians(signals.lat)) * cos(radians($filter_dx_lon - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) < $filter_dx_max" : "")
        .($filter_custom ? " AND\n  ($filter_custom_SQL)" : "")
        .($filter_date_2 ? " AND\n (`last_heard` >= \"$filter_date_1\" AND `last_heard` <= \"$filter_date_2\")" : "")
        .($filter_id ? " AND\n (`signals`.`call` LIKE \"%$filter_id%\")" : "")
        .($filter_itu ? " AND\n ($filter_itu_SQL)" : "")
        .($filter_khz_2 ? " AND\n (`khz` >= $filter_khz_1 AND `khz` <= $filter_khz_2)" : "")
        .($filter_channels==1 ? " AND\n MOD((`khz`* 1000),1000) = 0" : "")
        .($filter_channels==2 ? " AND\n MOD((`khz`* 1000),1000) != 0" : "")
        .($filter_sp ? " AND\n ($filter_sp_SQL)" : "")
        .($filter_type ? " AND\n $filter_type" : "");
    //  print("<pre>$sql</pre>");
        $total =    0;
        if ($result = @mysql_query($sql)) {
            $row =    mysql_fetch_array($result, MYSQL_ASSOC);
            $total =    $row["count"];
        }
        if (empty($limit)) {
            $limit = 50;

        } else {
            $limit = (int) $limit;
        }
        if (empty($offset)) {
            $offset = 0;

        } else {
            $offset = (int) $offset;
        }
        if ($offset<0) {
            $offset=0;
        }
        if ($sortBy =='CLE64' and $filter_dx_gsq=='') {
            $sortBy = '';
        }
        if ($sortBy=="") {
            $sortBy = "khz";
        }
        $sortBy_SQL =        "";
        switch ($sortBy) {
            case "call":        $sortBy_SQL =    "`active` DESC, `call` ASC, `khz` ASC";
                break;
            case "call_d":        $sortBy_SQL =    "`active` DESC, `call` DESC, `khz` ASC";
                break;
            case "dx":            $sortBy_SQL =    "`active` DESC, `dx_km` ASC";
                break;
            case "dx_d":        $sortBy_SQL =    "`active` DESC, `dx_km` DESC";
                break;
            case "dx_deg":        $sortBy_SQL =    "`active` DESC, CAST(`dx_range` AS UNSIGNED) ASC";
                break;
            case "dx_deg_d":    $sortBy_SQL =    "`active` DESC, CAST(`dx_range` AS UNSIGNED) DESC";
                break;
            case "format":        $sortBy_SQL =    "`active` DESC, `signals`.`format`='' OR `signals`.`format` IS NULL, `signals`.`format` ASC";
                break;
            case "format_d":    $sortBy_SQL =    "`active` DESC, `format`='' OR `format` IS NULL, `format` DESC";
                break;
            case "gsq":            $sortBy_SQL =    "`active` DESC, `GSQ` ASC";
                break;
            case "gsq_d":        $sortBy_SQL =    "`active` DESC, `GSQ` DESC";
                break;
            case "heard_in":    $sortBy_SQL =    "`active` DESC, `heard_in` ASC, `khz` ASC, `call` ASC";
                break;
            case "heard_in_d":    $sortBy_SQL =    "`active` DESC, `heard_in` DESC, `khz` ASC, `call` ASC";
                break;
            case "itu":            $sortBy_SQL =    "`active` DESC, `ITU` ASC, `SP` ASC, `khz` ASC, `call` ASC";
                break;
            case "itu_d":        $sortBy_SQL =    "`active` DESC, `ITU` DESC, `SP` ASC, `khz` ASC, `call` ASC";
                break;
            case "khz":            $sortBy_SQL =    "`active` DESC, `khz` ASC, `call` ASC";
                break;
            case "khz_d":        $sortBy_SQL =    "`active` DESC, `khz` DESC, `call` ASC";
                break;
            case "last_heard":    $sortBy_SQL =    "`active` DESC, `last_heard` IS NULL, `last_heard` ASC";
                break;
            case "last_heard_d":$sortBy_SQL =    "`active` DESC, `last_heard` IS NULL, `last_heard` DESC";
                break;
            case "logs":        $sortBy_SQL =    "`active` DESC, `logs` IS NULL, `logs` ASC";
                break;
            case "logs_d":        $sortBy_SQL =    "`active` DESC, `logs` IS NULL, `logs` DESC";
                break;
            case "LSB":
                if ($offsets=='') {
                    $sortBy_SQL =        "`active` DESC, `signals`.`LSB` IS NULL, `signals`.`LSB` ASC";
                } else {
                    $sortBy_SQL =        "`active` DESC, `signals`.`LSB` IS NULL, `signals`.`khz`-(`signals`.`LSB`/1000) ASC";
                }
                break;
            case "LSB_d":
                if ($offsets=='') {
                    $sortBy_SQL =        "`active` DESC, `signals`.`LSB` IS NULL, `signals`.`LSB` DESC";
                } else {
                    $sortBy_SQL =        "`active` DESC, `signals`.`LSB` IS NULL, `signals`.`khz`-(`signals`.`LSB`/1000) DESC";
                }
                break;
            case "notes":        $sortBy_SQL =    "`active` DESC, `notes`='' OR `notes` IS NULL, `notes` ASC";
                break;
            case "notes_d":        $sortBy_SQL =    "`active` DESC, `notes`='' OR `notes` IS NULL, `notes` DESC";
                break;
            case "QTH":            $sortBy_SQL =    "`active` DESC, `QTH`='' OR `QTH` IS NULL, `QTH` ASC";
                break;
            case "QTH_d":        $sortBy_SQL =    "`active` DESC, `QTH`='' OR `QTH` IS NULL, `QTH` DESC";
                break;
            case "pwr":            $sortBy_SQL =    "`active` DESC, `pwr`=0, `pwr` ASC";
                break;
            case "pwr_d":        $sortBy_SQL =    "`active` DESC, `pwr`=0, `pwr` DESC";
                break;
            case "range_dx_km":        $sortBy_SQL =    "`active` DESC, `lat` IS NULL, `range_dx_km` ASC";
                break;
            case "range_dx_km_d":    $sortBy_SQL =    "`active` DESC, `lat` IS NULL, `range_dx_km` DESC";
                break;
            case "range_dx_deg":    $sortBy_SQL =    "`active` DESC, `lat` IS NULL, CAST(`range_dx_deg` AS UNSIGNED) ASC";
                break;
            case "range_dx_deg_d":    $sortBy_SQL =    "`active` DESC, `lat` IS NULL, CAST(`range_dx_deg` AS UNSIGNED) DESC";
                break;
            case "sec":            $sortBy_SQL =    "`active` DESC, `signals`.`sec`='' OR `signals`.`sec` IS NULL, CAST(`signals`.`sec` AS UNSIGNED) ASC";
                break;
            case "sec_d":        $sortBy_SQL =    "`active` DESC, `signals`.`sec`='' OR `signals`.`sec` IS NULL, CAST(`signals`.`sec` AS UNSIGNED) DESC";
                break;
            case "sp":            $sortBy_SQL =    "`active` DESC, `SP`='' or `SP` IS NULL,`SP` ASC,`ITU` ASC, `khz` ASC, `call` ASC";
                break;
            case "sp_d":        $sortBy_SQL =    "`active` DESC, `SP`='' or `SP` IS NULL,`SP` DESC,`ITU` ASC, `khz` ASC, `call` ASC";
                break;
            case "USB":
                if ($offsets=='') {
                    $sortBy_SQL =        "`active` DESC, `signals`.`USB` IS NULL, `signals`.`USB` ASC";
                } else {
                    $sortBy_SQL =        "`active` DESC, `signals`.`USB` IS NULL, `signals`.`khz`+(`signals`.`USB`/1000) ASC";
                }
                break;
            case "USB_d":
                if ($offsets=='') {
                    $sortBy_SQL =        "`active` DESC, `signals`.`USB` IS NULL, `signals`.`USB` DESC";
                } else {
                    $sortBy_SQL =        "`active` DESC, `signals`.`USB` IS NULL, `signals`.`khz`+(`signals`.`USB`/1000) DESC";
                }
                break;
            case "CLE64":
                if ($filter_dx_gsq) {
                    $sortBy_SQL =    "`lat` IS NULL, `active` DESC, LEFT(`signals`.`call`,1)>='A' DESC, LEFT(`signals`.`call`,1) ASC, `range_dx_km` DESC";
                }
                break;
            case "CLE64_d":
                if ($filter_dx_gsq) {
                    $sortBy_SQL =    "`lat` IS NULL, `active` DESC, LEFT(`signals`.`call`,1)<='Z' ASC, LEFT(`signals`.`call`,1) DESC, `range_dx_km` DESC";
                }
                break;
        }
        if ($filter_id) {
            $sortBy_SQL =    "`call` = '$filter_id' DESC, $sortBy_SQL";
        }
        $sql =
         "SELECT\n"
        .($filter_heard || $listenerID ?
        ($listenerID ?
           "  DISTINCT `signals`.*,\n"
          ." `logs`.`dx_km`,\n"
          ." `logs`.`dx_miles`\n"
        :
           "  DISTINCT `signals`.*\n"
        )
        .($filter_dx_gsq ?
           ",\n"
          ."  CAST(COALESCE(ROUND(DEGREES(ACOS(SIN(RADIANS(".$filter_dx_lat.")) * SIN(RADIANS(signals.lat)) + COS(RADIANS(".$filter_dx_lat.")) * COS(RADIANS(signals.lat)) * COS(RADIANS($filter_dx_lon - signals.lon))))*69, 2),'') AS UNSIGNED) AS `range_dx_miles`,\n"
          ."  CAST(COALESCE(ROUND(DEGREES(ACOS(SIN(RADIANS(".$filter_dx_lat.")) * SIN(RADIANS(signals.lat)) + COS(RADIANS(".$filter_dx_lat.")) * COS(RADIANS(signals.lat)) * COS(RADIANS($filter_dx_lon - signals.lon))))*111.05, 2),'') AS UNSIGNED) AS `range_dx_km`,\n"
          ."  CAST(COALESCE(ROUND((DEGREES(atan2(sin(RADIANS(signals.lon) - RADIANS(".$filter_dx_lon.")) * cos(RADIANS(signals.lat)), cos(RADIANS(".$filter_dx_lat.")) * sin(RADIANS(signals.lat)) - sin(RADIANS(".$filter_dx_lat.")) * cos(RADIANS(signals.lat)) * cos(RADIANS(signals.lon) - RADIANS(".$filter_dx_lon.")))) + 360) mod 360),'') AS UNSIGNED) AS `range_dx_deg`\n"
        :
           ""
        )
        ."FROM\n"
        ."  `signals`,\n"
        ."  `logs`\n"
        ."WHERE\n"
        ."  `signals`.`ID` = `logs`.`signalID` AND\n"
        ."  ".$filter_system_SQL." "
        .($filter_heard ?
            "AND\n"
           ."  (".$filter_heard_SQL.")"
         :  ""
         )
        .($listenerID ?
            "AND\n"
           ."  (`logs`.`listenerID`=".implode($listenerID, " OR `logs`.`listenerID`=").") "
         :
            ""
         )
        :
         " `signals`.*"
        .($filter_dx_gsq ?
           ",\n"
          ."  CAST(COALESCE(ROUND(DEGREES(ACOS(SIN(RADIANS(".$filter_dx_lat.")) * SIN(RADIANS(signals.lat)) + COS(RADIANS(".$filter_dx_lat.")) * COS(RADIANS(signals.lat)) * COS(RADIANS(".$filter_dx_lon." - signals.lon))))*69, 2),'') AS UNSIGNED) AS `range_dx_miles`,\n"
          ."  CAST(COALESCE(ROUND(DEGREES(ACOS(SIN(RADIANS(".$filter_dx_lat.")) * SIN(RADIANS(signals.lat)) + COS(RADIANS(".$filter_dx_lat.")) * COS(RADIANS(signals.lat)) * COS(RADIANS(".$filter_dx_lon." - signals.lon))))*111.05, 2),'') AS UNSIGNED) AS `range_dx_km`,\n"
          ."  CAST(COALESCE(ROUND((DEGREES(atan2(sin(RADIANS(signals.lon) - RADIANS(".$filter_dx_lon.")) * cos(RADIANS(signals.lat)), cos(RADIANS(".$filter_dx_lat.")) * sin(RADIANS(signals.lat)) - sin(RADIANS(".$filter_dx_lat.")) * cos(RADIANS(signals.lat)) * cos(RADIANS(signals.lon) - RADIANS(".$filter_dx_lon.")))) + 360) mod 360),'') AS UNSIGNED) AS `range_dx_deg`\n"
         :
            "\n"
         )
        ."FROM\n"
        ."  `signals`\n"
        ."WHERE\n"
        ."  ".$filter_system_SQL
        )
        .($filter_active ?
         " AND\n"
        ."  `active` = 1"
        :
         ""
        )
        .($filter_by_range && $filter_dx_min ?
          " AND\n"
         ."  round(degrees(acos(sin(radians(".$filter_dx_lat.")) * sin(radians(signals.lat)) + cos(radians(".$filter_dx_lat.")) * cos(radians(signals.lat)) * cos(radians(".$filter_dx_lon." - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) > ".$filter_dx_min
        : ""
        )
        .($filter_by_range && $filter_dx_max ?
          " AND\n"
         ."  round(degrees(acos(sin(radians(".$filter_dx_lat.")) * sin(radians(signals.lat)) + cos(radians(".$filter_dx_lat.")) * cos(radians(signals.lat)) * cos(radians(".$filter_dx_lon." - signals.lon))))*".($filter_dx_units=="km" ? "111.05" : "69").", 2) < ".$filter_dx_max
        : ""
        )
        .($filter_custom ? " AND\n  ($filter_custom_SQL)" : "")
        .($filter_date_2 ?
          " AND\n  (`last_heard` >= \"$filter_date_1\" AND `last_heard` <= \"$filter_date_2\")"
        : ""
        )
        .($filter_id ?
          " AND\n  (`signals`.`call` LIKE \"%$filter_id%\")"
        : ""
        )
        .($filter_itu ? " AND\n ($filter_itu_SQL)" : "")
        .($filter_khz_2 ? " AND\n  (`khz` >= $filter_khz_1 AND `khz` <= $filter_khz_2)" : "")
        .($filter_channels==1 ? " AND\n MOD((`khz`* 1000),1000) = 0" : "")
        .($filter_channels==2 ? " AND\n MOD((`khz`* 1000),1000) != 0" : "")
        .($filter_sp ? " AND\n ($filter_sp_SQL)" : "")
        .($filter_type ? " AND\n  $filter_type" : "")
        .($sortBy_SQL ? "\nORDER BY\n  ".$sortBy_SQL : "")
        .($limit!=-1 ? "\nLIMIT\n  $offset, $limit" : "");



    //  print("<pre>$sql</pre>");
    //  $this->html.=	"<!--\n\n $sql \n\n-->";
        $result =     @mysql_query($sql); // Use @ to prevent tablescan warning in RWW
        $this->html.=
        "<script type='text/javascript'>\n"
        ."function get_type(form){\n"
        ."  if (form.type_DGPS.checked)   return ".DGPS.";\n"
        ."  if (form.type_DSC.checked)    return ".DSC.";\n"
        ."  if (form.type_HAMBCN.checked) return ".HAMBCN.";\n"
        ."  if (form.type_NAVTEX.checked) return ".NAVTEX.";\n"
        ."  if (form.type_NDB.checked)    return ".NDB.";\n"
        ."  if (form.type_HAMBCN.checked) return ".HAMBCN.";\n"
        ."  if (form.type_OTHER.checked)  return ".OTHER.";\n"
        ."  if (form.type_TIME.checked)   return ".TIME.";\n"
        ."  return '';\n"
        ." }\n"
        ."</script>"
        ."<form name='form' action='".system_URL."/".$mode."' method='POST'>\n"
        ."<input type='hidden' name='mode' value='$mode'>\n"
        ."<input type='hidden' name='submode' value=''>\n"
        ."<input type='hidden' name='targetID' value=''>\n"
        ."<input type='hidden' name='sortBy' value='$sortBy'>\n"
        ."<table cellpadding='0' cellspacing='0' border='0'><tr><td><h2>Signal List</h2><br>\n"
        ."<ul>\n"
        ."<li>Click on any station <b>ID</b> for details, <b>GSQ</b> for location map, <b>Heard In</b> list for reception map and <b>Logs</b> value to see all logs for the station.</li>\n"
        ."<li>To list different types of signals, check the boxes shown for 'Types' below. Inactive stations are normally shown at the end of the report.</li>\n"
        ."<li>This report prints best in Landscape.</li></ul></td></tr></table>\n";
        if ($type_NDB) {
            $this->html.=
            "<br><table cellpadding='0' cellspacing='0' border='0'><tr><td><h2>Reporting NDBs</h2><br>Please use the following list as an additional data source - the ship listings from around 404KHz may prove particularly useful:<br>\n"
            ."[ <a href='http://www.dxinfocentre.com/ndb.htm' target='_blank'><b>William Hepburn's LF List</b></a> ]</td></tr></table>\n";
        }
        if ($type_NAVTEX) {
            $this->html.=
            "<br><table cellpadding='0' cellspacing='0' border='0'><tr><td><h2>Reporting Navtex Stations</h2><br>Please use the following lists as your primary reference source - these lists are very current and should be considered authorative:<br>\n"
            ."[ <a href='http://www.dxinfocentre.com/navtex.htm' target='_blank'><b>William Hepburn's LF List</b></a> |"
            ."  <a href='http://www.dxinfocentre.com/maritimesafetyinfo.htm' target='_blank'><b>William Hepburn's HF List</b></a> ]</td></tr></table>\n";
        }
        if ($type_HAMBCN) {
            $this->html.=
            "<br><table cellpadding='0' cellspacing='0' border='0'><tr><td><h2>Reporting Ham Beacons</h2><br>Please use the following lists as your primary reference source - these lists are very current and should be considered authorative:<br>\n"
            ."[ <a href='http://www.lwca.org/sitepage/part15' target='_blank'><b>LOWFERS</b></a> |  <a href='http://www.keele.ac.uk/depts/por/28.htm' target='_blank'><b>HF</b></a> | <a href='http://www.keele.ac.uk/depts/por/50.htm' target='_blank'><b>50MHz</b></a> ]</td></tr></table>\n";
        }
        if ($type_DGPS) {
            $this->html.=
            "<br><table cellpadding='0' cellspacing='0' border='0'><tr><td><h2>Reporting DGPS Stations</h2><br>Please use the following lists as your primary reference source - these lists are very current and should be considered authorative:<br>\n"
            ."[ <a href='http://www.ndblist.info/dgnavinfo/datamodes/worldDGPSdatabase.pdf' target='_blank'><b>NDB List PDF (by Frequency)</b></a> | <a href='http://www.navcen.uscg.gov/?pageName=dgpsSiteInfo&All' target='_blank'><b>USCG DGPS Site List</b></a> ]</td></tr></table>\n";
        }
        $this->html.=
        "<table cellpadding='2' border='0' cellspacing='1'>\n"
        ."  <tr>\n"
        ."    <td align='center' valign='top' colspan='2'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18' class='noprint' alt=''></td>\n"
        ."        <td width='100%' class='downloadTableHeadings_nosort' align='center'>Customise Report</td>\n"
        ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18' class='noprint' alt=''></td>\n"
        ."      </tr>\n"
        ."    </table>\n"
        ."    <table cellpadding='0' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm'>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>Show</th>\n"
        ."        <td nowrap>".show_page_bar($total, $limit, $offset, 1, 1, 1)."</td>\n"
        ."      </tr>"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>Types&nbsp;</th>\n"
        ."        <td nowrap style='padding: 0px;'><table cellpadding='0' cellspacing='1' border='0' width='100%' class='tableForm'>\n"
        ."          <tr>\n"
        ."            <td bgcolor='".Signal::$colors[DGPS]."' width='14%' nowrap onclick='toggle(document.form.type_DGPS)'><input type='checkbox' onclick='toggle(document.form.type_DGPS);' name='type_DGPS' value='1'".($type_DGPS? " checked" : "").">DGPS</td>"
        ."            <td bgcolor='".Signal::$colors[DSC]."' width='14%' nowrap onclick='toggle(document.form.type_DSC)'><input type='checkbox' onclick='toggle(document.form.type_DSC);' name='type_DSC' value='1'".($type_DSC? " checked" : "").">DSC</td>"
        ."            <td bgcolor='".Signal::$colors[HAMBCN]."' width='14%' nowrap onclick='toggle(document.form.type_HAMBCN)'><input type='checkbox' onclick='toggle(document.form.type_HAMBCN)' name='type_HAMBCN' value='1'".($type_HAMBCN ? " checked" : "").">Ham</td>"
        ."            <td bgcolor='".Signal::$colors[NAVTEX]."' width='15%' nowrap onclick='toggle(document.form.type_NAVTEX)'><input type='checkbox' onclick='toggle(document.form.type_NAVTEX)' name='type_NAVTEX' value='1'".($type_NAVTEX ? " checked" : "").">NAVTEX&nbsp;</td>"
        ."            <td bgcolor='".Signal::$colors[NDB]."' width='14%' nowrap onclick='toggle(document.form.type_NDB)'><input type='checkbox' onclick='toggle(document.form.type_NDB)' name='type_NDB' value='1'".($type_NDB? " checked" : "").">NDB</td>"
        ."            <td bgcolor='".Signal::$colors[TIME]."' width='14%' nowrap onclick='toggle(document.form.type_TIME)'><input type='checkbox' onclick='toggle(document.form.type_TIME)' name='type_TIME' value='1'".($type_TIME? " checked" : "").">Time</td>"
        ."            <td bgcolor='".Signal::$colors[OTHER]."' width='15%' nowrap onclick='toggle(document.form.type_OTHER)'><input type='checkbox' onclick='toggle(document.form.type_OTHER)' name='type_OTHER' value='1'".($type_OTHER ? " checked" : "").">Other</td>"
        ."          </tr>\n"
        ."        </table></td>"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>Frequencies&nbsp;</th>\n"
        ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."          <tr>\n"
        ."            <td><input title='Lowest frequency (or leave blank)' type='text' name='filter_khz_1' size='6' maxlength='9' value='".($filter_khz_1 !="0" ? $filter_khz_1 : "")."' class='formfield'> - <input title='Highest frequency (or leave bank)' type='text' name='filter_khz_2' size='6' maxlength='9' value='".($filter_khz_2 != 1000000 ? $filter_khz_2 : "")."' class='formfield'> KHz</td>\n"
        ."            <td>Channels</td>\n"
        ."            <td><select name='filter_channels' class='formField'>\n"
        ."<option value=''".($filter_channels=='' ? ' selected' : '').">All</option>\n"
        ."<option value='1'".($filter_channels=='1' ? ' selected' : '').">Only 1 KHz</option>\n"
        ."<option value='2'".($filter_channels=='2' ? ' selected' : '').">Not 1 KHz</option>\n"
        ."</select></td>\n"
        ."            <td align='right'><span title='Callsign or DGPS ID (Exact matches are shown at the top of the report, partial matches are shown later)'><b>Call / ID</b></span> <input type='text' name='filter_id' size='6' maxlength='12' value='$filter_id' class='formfield' title='Limit results to signals with this ID or partial ID -\nuse _ to indicate a wildcard character'></td>"
        ."          </tr>\n"
        ."        </table></td>"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>Locations&nbsp;</th>\n"
        ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."          <tr>\n"
        ."            <td nowrap>&nbsp;<span title='List of States or Provinces'><a href='".system_URL."/show_sp' onclick='show_sp();return false' title='NDBList State and Province codes'><b>States</b></a></span> <input title='Enter one or more states or provinces (e.g. MI or NB) to show only signals physically located there' type='text' name='filter_sp' size='20' value='$filter_sp' class='formfield'></td>\n"
        ."            <td nowrap align='right'><span title='List of Countries'><a href='".system_URL."/show_itu' onclick='show_itu();return false' title='NDBList Country codes'>&nbsp;<b>Countries</b></a></span> <input title='Enter one or more NDBList approved 3-letter country codes (e.g. CAN or BRA) to show only signals physically located there' type='text' name='filter_itu' size='20' value='$filter_itu' class='formfield'></td>"
        ."          </tr>\n"
        ."	 </table></td>"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>Range</th>\n"
        ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."          <tr>\n"
        ."            <td>&nbsp;<b>From GSQ</b> <input title='Enter a grid square to show only signals physically located between the distances indicated' type='text' name='filter_dx_gsq' size='6' maxlength='6' value='$filter_dx_gsq' class='formfield' onKeyUp='set_range(form)' onchange='set_range(form)'></td>"
        ."            <td><b><span title='Distance'>DX</span></b> <input title='Enter a value to show only signals equal or greater to this distance' type='text' name='filter_dx_min' size='5' maxlength='5' value='$filter_dx_min' onKeyUp='set_range(form)' onchange='set_range(form)'".($filter_dx_gsq ? " class='formfield'" : " class='formfield_disabled' disabled")."> - "
        ."<input title='Enter a value to show only signals up to this distance' type='text' name='filter_dx_max' size='5' maxlength='5' value='$filter_dx_max' onKeyUp='set_range(form)' onchange='set_range(form)'".($filter_dx_gsq ? " class='formfield'" : " class='formfield_disabled' disabled")."></td>"
        ."            <td width='45'><label for='filter_dx_units_km'><input type='radio' id='filter_dx_units_km' name='filter_dx_units' value='km'".($filter_dx_units=="km" ? " checked" : "").($filter_dx_gsq && ($filter_dx_min || $filter_dx_max) ? "" : " disabled").">km</label></td>"
        ."            <td width='55'><label for='filter_dx_units_miles'><input type='radio' id='filter_dx_units_miles' name='filter_dx_units' value='miles'".($filter_dx_units=="miles" ? " checked" : "").($filter_dx_gsq && ($filter_dx_min || $filter_dx_max) ? "" : " disabled").">miles&nbsp;</label></td>"
        ."          </tr>\n"
        ."	 </table></td>"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left' valign='top'><span title='Only signals heard by the selected listener'>Heard by<br><br><span style='font-weight: normal;'>Use SHIFT or <br>CONTROL to<br>select multiple<br>values</span></span></th>"
        ."        <td><select name='listenerID[]' multiple class='formfield' onchange='set_listener_and_heard_in(document.form)' style='font-family: monospace; width: 425; height: 90px;' >\n"
        .get_listener_options_list($filter_listener_SQL, $listenerID, "Anyone (or enter values in \"Heard here\" box)")
        ."</select></td>\n"
        ."      </tr>\n";
        if (system=="RWW") {
            $this->html.=
            "     <tr class='rowForm'>\n"
            ."       <th align='left'>Heard in&nbsp;</th>\n"
            ."       <td>\n"
            ."<select name='region' onchange='document.form.go.disabled=1;document.form.submit()' class='formField' style='width: 100%;'>\n"
            .get_region_options_list($this->region, "(All Continents)")
            ."</select>"
            ."</td>"
            ."      </tr>\n";
        }
        $this->html.=
        "      <tr class='rowForm'>\n"
        ."        <th align='left'><span title='Only signals heard in these states and countries'>Heard here</span></th>\n"
        ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."          <tr>\n"
        ."            <td title='Separate multiple options using spaces' nowrap>\n"
        ."<input type='text' name='filter_heard' size='41' value='".($filter_heard ? strToUpper($filter_heard) : "(All States and Countries)")."'\n"
        .($filter_heard=="" ? "style='color: #0000ff' ":"")
        ."onclick=\"if(this.value=='(All States and Countries)') { this.value=''; this.style.color='#000000'}\"\n"
        ."onblur=\"if(this.value=='') { this.value='(All States and Countries)'; this.style.color='#0000ff';}\"\n"
        ."onchange='set_listener_and_heard_in(form)' onKeyUp='set_listener_and_heard_in(form)' ".($listenerID ? "class='formfield_disabled' disabled" : "class='formfield'").">"
        ."            <td width='45'><label for='radio_grp_any' title='Show where any terms match'><input id='radio_grp_any' type='radio' value='any' name='grp'".($grp!="all" ? " checked" : "").($listenerID || !$filter_heard ? " disabled" : "").">Any</label></td>\n"
        ."            <td width='55'><label for='radio_grp_all' title='Show where all terms match'><input id='radio_grp_all' type='radio' value='all' name='grp'".($grp=="all" ? " checked" : "").($listenerID || !$filter_heard ? " disabled" : "").">All</label></td>\n"
        ."          </tr>\n"
        ."	 </table></td>"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>Last Heard</th>\n"
        ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."          <tr>\n"
        ."            <td><input title='Enter a start date to show only signals last heard after this date (YYYY-MM-DD format)' type='text' name='filter_date_1' size='10' maxlength='10' value='".($filter_date_1 != "1900-01-01" ? $filter_date_1 : "")."' class='formfield'> -\n"
        ."<input title='Enter an end date to show only signals last heard before this date (YYYY-MM-DD format)' type='text' name='filter_date_2' size='10' maxlength='10' value='".($filter_date_2 != "2020-01-01" ? $filter_date_2 : "")."' class='formfield'></td>"
        ."            <td align='right'><b>Offsets</b> <select name='offsets' class='formField'>\n"
        ."<option value=''".($offsets=="" ? " selected" : "") .">Relative</option>\n"
        ."<option value='abs'".($offsets=="" ? "" : " selected") .">Absolute</option>\n"
        ."</select></td>"
        ."          </tr>\n"
        ."	 </table></td>"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>Sort By</th>\n"
        ."        <td nowrap><table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
        ."          <tr>\n"
        ."            <td><select name='sortBy_column' class='fixed'>\n"
        ."<option value='khz'".($sortBy=="khz" || $sortBy=="khz_d" ? " selected" : "") .">KHz - Nominal carrier</option>\n"
        ."<option value='call'".($sortBy=="call" || $sortBy=="call_d" ? " selected" : "") .">ID &nbsp;- Callsign or ID</option>\n"
        ."<option value='LSB'".($sortBy=="LSB" || $sortBy=="LSB_d" ? " selected" : "") .">LSB - Offset value in Hz</option>\n"
        ."<option value='USB'".($sortBy=="USB" || $sortBy=="USB_d" ? " selected" : "") .">USB - Offset value in Hz</option>\n"
        ."<option value='sec'".($sortBy=="sec" || $sortBy=="sec_d" ? " selected" : "") .">Sec - Cycle time in sec</option>\n"
        ."<option value='format'".($sortBy=="format" || $sortBy=="format_d" ? " selected" : "") .">Fmt - Signal Format</option>\n"
        ."<option value='QTH'".($sortBy=="QTH" || $sortBy=="QTH_d" ? " selected" : "") .">QTH - 'Name' and location</option>\n"
        ."<option value='sp'".($sortBy=="sp" || $sortBy=="sp_d" ? " selected" : "") .">S/P - State or Province</option>\n"
        ."<option value='itu'".($sortBy=="itu" || $sortBy=="itu_d" ? " selected" : "") .">ITU - Country code</option>\n"
        ."<option value='gsq'".($sortBy=="gsq" || $sortBy=="gsq_d" ? " selected" : "") .">GSQ - Grid Square</option>\n"
        ."<option value='pwr'".($sortBy=="pwr" || $sortBy=="pwr_d" ? " selected" : "") .">PWR - TX power in watts</option>\n"
        ."<option value='notes'".($sortBy=="notes" || $sortBy=="notes_d" ? " selected" : "") .">Notes column</option>\n"
        ."<option value='heard_in'".($sortBy=="heard_in" || $sortBy=="heard_in_d" ? " selected" : "") .">Heard In column</option>\n"
        ."<option value='logs'".($sortBy=="logs" || $sortBy=="logs_d" ? " selected" : "") .">Logs - Number of loggings</option>\n"
        ."<option value='last_heard'".($sortBy=="last_heard" || $sortBy=="last_heard_d" ? " selected" : "") .">Date last heard</option>\n"
        ."<option value='CLE64'".($sortBy=="CLE64" || $sortBy=="CLE64_d" ? " selected" : "") ." style='color: #ff0000;'>CLE64 - First letter / DX</option>\n"
        ."</select></td>"
        ."            <td width='45'><label for='sortBy_d'><input type='checkbox' id='sortBy_d' name='sortBy_d' value='_d'".(substr($sortBy, strlen($sortBy)-2, 2)=="_d" ? " checked" : "").">Z-A</label></td>"
        ."            <td align='right'><label for='chk_filter_active'><input id='chk_filter_active' type='checkbox' name='filter_active' value='1'".($filter_active ? " checked" : "").">Only active&nbsp;</label></td>"
        ."          </tr>\n"
        ."	 </table></td>"
        ."      </tr>\n"
        .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
        "      <tr class='rowForm'>\n"
        ."        <th align='left'>Admin:</th>\n"
        ."        <td nowrap><select name='filter_system' class='formField'>\n"
        ."<option value='1'".($this->filter_system=='1' ? " selected" : "").">RNA</option>\n"
        ."<option value='2'".($this->filter_system=='2' ? " selected" : "").">REU</option>\n"
        ."<option value='3'".($this->filter_system=='3' ? " selected" : "").">RWW</option>\n"
        ."<option value='not_logged'".($this->filter_system=='not_logged' ? " selected" : "").">Unlogged signals</option>\n"
        ."<option value='all'".($this->filter_system=='all' ? " selected" : "").">Show everything</option>\n"
        ."</select> Select system</td>\n"
        ."      </tr>"
        : ""
        )
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>Custom Filter:</th>\n"
        ."        <td nowrap><select name='filter_custom' class='formField'>\n"
        ."<option value=''".($filter_custom=='' ? " selected" : "").">(None)</option>\n"
        ."<option value='cle160'".($filter_custom=='cle160' ? " selected" : "").">CLE160</option>\n"
        ."</select></td>\n"
        ."      </tr>"
        ."      <tr class='rowForm noprint'>\n"
        ."        <th colspan='2'><input type='submit' onclick='return send_form(form)' name='go' value='Go' style='width: 100px;' class='formButton' title='Execute search'>\n"
        ."<input name='clear' type='button' class='formButton' value='Clear' style='width: 100px;' onclick='clear_signal_list(document.form)'></th>"
        ."      </tr>\n"
        ."    </table>"
        ."    </td>"
        ."    <td>&nbsp;</td>\n"
        ."    <td align='center' valign='top'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18' class='noprint' alt=''></td>\n"
        ."        <td width='100%' class='downloadTableHeadings_nosort' align='center'>Signals</td>\n"
        ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18' class='noprint' alt=''></td>\n"
        ."      </tr>\n"
        ."    </table>\n"
        ."    <table cellpadding='2' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm' width='100%'>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>RNA only</th>\n"
        ."        <td align='right'>".$this->stats['RNA_only']."</td>\n"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>REU only</th>\n"
        ."        <td align='right'>".$this->stats['REU_only']."</td>\n"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>RNA + REU</th>\n"
        ."        <td align='right'>".$this->stats['RNA_and_REU']."</td>\n"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>RWW</th>\n"
        ."        <td align='right'>".$this->stats['RWW']."</td>\n"
        ."      </tr>\n"
        .(isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session ?
        "      <tr class='rowForm'>\n"
        ."        <th align='left'>Unassigned signals</th>\n"
        ."        <td align='right'>".$this->stats['Unassigned']."</td>\n"
        ."      </tr>\n"
        : ""
        )
        ."    </table><br>\n"
        ."    <table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18' class='noprint' alt=''></td>\n"
        ."        <td width='100%' class='downloadTableHeadings_nosort' align='center' nowrap>".system." Listeners</td>\n"
        ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18' class='noprint' alt=''></td>\n"
        ."      </tr>\n"
        ."    </table>\n"
        ."    <table cellpadding='2' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm' width='100%'>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>Locations</th>\n"
        ."        <td align='right'>".$this->stats['locations']."</td>\n"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>Loggings</th>\n"
        ."        <td align='right'>".$this->stats['logs']."</td>\n"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>First log</th>\n"
        ."        <td align='right'>".$this->stats['first_log']."</td>\n"
        ."      </tr>\n"
        ."      <tr class='rowForm'>\n"
        ."        <th align='left'>Last log</th>\n"
        ."        <td align='right'>".$this->stats['last_log']."</td>\n"
        ."      </tr>\n"
        ."    </table></td>"
        ."    <td>&nbsp;</td>\n"
        ."    <td align='center' valign='top'><table cellpadding='0' border='0' cellspacing='0' width='100%'>\n"
        ."      <tr>\n"
        ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_left.gif' width='15' height='18' class='noprint' alt=''></td>\n"
        ."        <td width='100%' class='downloadTableHeadings_nosort' align='center' nowrap>Poll - Vote Now</td>\n"
        ."        <td width='18'><img src='".BASE_PATH."assets/corner_top_right.gif' width='15' height='18' class='noprint' alt=''></td>\n"
        ."      </tr>\n"
        ."    </table>\n"
        ."    <table cellpadding='2' cellspacing='0' border='1' bordercolor='#c0c0c0' class='tableForm' width='100%'>\n"
        ."      <tr class='rowForm'>\n"
        ."        <td align='left'>".doNow()."</td>\n"
        ."      </tr>\n"
        ."    </table><br>\n"
        ."  </tr>"
        ."</table></form><br>\n";

        if ($result && mysql_num_rows($result)) {
            if ($sortBy=='CLE64') {
                $this->html.=    "<table cellpadding='0' cellspacing='0' border='0'><tr><td><ul><li><b><font color='#ff0000'>CLE64 Custom sort order applied:</font></b><br> - Show <b>active</b> beacons first<br> - Sort by <b>first letter</b> of callsign: <b>A-Z</b><br> - Sort by <b>DX</b> from Grid Square <b>$filter_dx_gsq</b>.<br><b>Tip:</b> You can further <b>refine this search</b> by entering values in 'Heard here', 'Heard by' or adding other criteria such as range limits.</li></ul></td></tr></table>\n";
            }
            if ($sortBy=='CLE64_d') {
                $this->html.=    "<table cellpadding='0' cellspacing='0' border='0'><tr><td><ul><li><b><font color='#ff0000'>CLE64 Custom sort order applied:</font></b><br> - Show <b>active</b> beacons first<br> - Sort by <b>first letter</b> of callsign: <b>Z-A</b><br> - Sort by <b>DX</b> from Grid Square <b>$filter_dx_gsq</b>.<br><b>Tip:</b> You can further <b>refine this search</b> by entering values in 'Heard here', 'Heard by' or adding other criteria such as range limits.</li></ul></td></tr></table>\n";
            }
            if ($filter_id) {
                $this->html.=    "<table cellpadding='0' cellspacing='0' border='0'><tr><td><b>Note:</b> Any exact matches for <b>$filter_id</b> will shown at the top of this list, regardless of the station's current status.</td></tr></table>\n";
            }
            $this->html.=
            "<table cellpadding='2' cellspacing='0' border='1' bordercolor='#c0c0c0' bgcolor='#ffffff' class='downloadtable'>\n"
            ."  <thead>\n"
            ."  <tr id=\"header\">\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='khz".($sortBy=="khz" ? "_d" : "")."';document.form.submit()\" title=\"Sort by Frequency\">KHz ".($sortBy=='khz' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='khz_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='call".($sortBy=="call" ? "_d" : "")."';document.form.submit()\" title=\"Sort by Callign or DGPS Station ID\">ID ".($sortBy=='call' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='call_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            .($type_NDB ?
            "    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='LSB".($sortBy=="LSB" ? "_d" : "")."';document.form.submit()\" title=\"Sort by LSB (-ve Offset)\">LSB ".($sortBy=='LSB' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='LSB_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='USB".($sortBy=="USB" ? "_d" : "")."';document.form.submit()\" title=\"Sort by USB (+ve Offset)\">USB ".($sortBy=='USB' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='USB_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='sec".($sortBy=="sec" ? "_d" : "")."';document.form.submit()\" title=\"Sort by cycle duration\">Sec ".($sortBy=='sec' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='sec_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='format".($sortBy=="format" ? "_d" : "")."';document.form.submit()\" title=\"Sort by cycle format\">Fmt ".($sortBy=='format' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='format_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            : ""
            )
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='QTH".($sortBy=="QTH" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Name' and Location\">'Name' and Location ".($sortBy=='QTH' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='QTH_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='sp".($sortBy=="sp" ? "_d" : "")."';document.form.submit()\" title=\"Sort by State / Province\">S/P ".($sortBy=='sp' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='sp_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='itu".($sortBy=="itu" ? "_d" : "")."';document.form.submit()\" title=\"Sort by NDB List Country Code\">ITU ".($sortBy=='itu' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='itu_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='gsq".($sortBy=="gsq" ? "_d" : "")."';document.form.submit()\" title=\"Sort by GSQ Grid Locator Square\">GSQ ".($sortBy=='gsq' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='gsq_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="pwr_d" ? "pwr" : "pwr_d")."';document.form.submit()\" title=\"Sort by Transmitter Power\">PWR ".($sortBy=='pwr' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='pwr_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='notes".($sortBy=="notes" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Notes' column\">Notes ".($sortBy=='notes' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='notes_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='heard_in".($sortBy=="heard_in" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Heard In' column\">Heard In <span style='font-weight: normal'>(Click for Map - <b>bold</b> = daytime logging)</span>".($sortBy=='heard_in' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='heard_in_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='logs".($sortBy=="logs" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Logs' column\" nowrap>Logs ".($sortBy=='logs' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='logs_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
            ."    <th class='downloadTableHeadings' rowspan='2' align='left' valign='bottom' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='last_heard".($sortBy=="last_heard" ? "_d" : "")."';document.form.submit()\" title=\"Sort by 'Last Heard' column (YYYY-MM-DD)\" nowrap>Last Heard ".($sortBy=='last_heard' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='last_heard_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n";

            if ($listenerID) {
                $this->html.=    "    <th class='downloadTableHeadings_nosort' colspan='2'>Range from<br>Listener</th>\n";
            }
            if ($filter_dx_gsq) {
                $this->html.=    "    <th class='downloadTableHeadings_nosort' colspan='3'>Range from<br>GSQ</th>\n";
            }
            if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
                $this->html.=    "    <th class='downloadTableHeadings_nosort' rowspan='2' valign='bottom'>&nbsp;</th>\n";
            }
            if ($listenerID || $filter_dx_gsq) {
                $this->html.=    "  <tr>\n";
                if ($listenerID) {
                    $this->html.=
                    "    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="dx_d" ? "dx" : "dx_d")."';document.form.submit()\" title=\"Sort by 'KM' column\"  nowrap>KM ".($sortBy=='dx' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='dx_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
                    ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="dx_d" ? "dx" : "dx_d")."';document.form.submit()\" title=\"Sort by 'Miles' column\" nowrap>Miles ".($sortBy=='dx' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='dx_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
              //		 ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="dx_deg" ? "dx_deg_d" : "dx_deg")."';document.form.submit()\" title=\"Sort by 'Degrees' column\" nowrap>Deg ".($sortBy=='dx_deg' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='dx_deg_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>": "")."</th>\n"
                    ;
                }
                if ($filter_dx_gsq) {
                    $this->html.=
                    "    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="range_dx_km_d" ? "range_dx_km" : "range_dx_km_d")."';document.form.submit()\" title=\"Sort by 'Range KM' column\" nowrap>KM ".($sortBy=='range_dx_km' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='range_dx_km_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
                    ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="range_dx_km_d" ? "range_dx_km" : "range_dx_km_d")."';document.form.submit()\" title=\"Sort by 'Range Miles' column\" nowrap>Miles ".($sortBy=='range_dx_km' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='range_dx_km_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n"
                    ."    <th class='downloadTableHeadings' align='left' onmouseover=\"column_over(this,1);\" onmouseout=\"column_over(this,0);\" onmousedown=\"column_over(this,2);\" onclick=\"document.form.sortBy.value='".($sortBy=="range_dx_deg" ? "range_dx_deg_d" : "range_dx_deg")."';document.form.submit()\" title=\"Sort by 'Degrees' column\" nowrap>Deg ".($sortBy=='range_dx_deg' ? "<img src='".BASE_PATH."assets/icon_sort_asc.gif' alt='A-Z'>" : "").($sortBy=='range_dx_deg_d' ? "<img src='".BASE_PATH."assets/icon_sort_desc.gif' alt='Z-A'>" : "")."</th>\n";
                }
                $this->html.=        "  </tr>\n";
            }
            $this->html.=
            "  </tr>\n"
            ."  </thead>\n"
            ."  <tbody>";
            for ($i=0; $i<mysql_num_rows($result); $i++) {
                $row =    mysql_fetch_array($result, MYSQL_ASSOC);
                if (isset($filter_by_dx) && $filter_by_dx) {
                    $dx =        get_dx($filter_by_lat, $filter_by_lon, $row["lat"], $row["lon"]);
                }
                if (!$row["active"]) {
                    $class='inactive';
                    $title = '(Reportedly off air or decommissioned)';
                } else {
                    switch ($row["type"]) {
                        case NDB:        $class='ndb';
                            $title = 'NDB';
                            break;
                        case DGPS:    $class='dgps';
                            $title = 'DGPS Station';
                            break;
                        case DSC:        $class='dsc';
                            $title = 'DSC Station';
                            break;
                        case TIME:    $class='time';
                            $title = 'Time Signal Station';
                            break;
                        case NAVTEX:    $class='navtex';
                            $title = 'NAVTEX Station';
                            break;
                        case HAMBCN:    $class='hambcn';
                            $title = 'Amateur signal';
                            break;
                        case OTHER:    $class='other';
                            $title = 'Other Utility Station';
                            break;
                    }
                }
                $call =    ($filter_id ? highlight($row["call"], $filter_id) : $row["call"]);
                $heard_in = ($filter_heard ? highlight($row["heard_in_html"], str_replace(" ", "|", $filter_heard)) : $row["heard_in_html"]);
                $SP =    $row["SP"];
                $ITU =    ($filter_itu ? highlight($row["ITU"], str_replace(" ", "|", $filter_itu)) : $row["ITU"]);
                $this->html.=
                "<tr class='rownormal ".$class."' title='".$title."'>"
                ."<td><a href='".system_URL."/signal_list?filter_khz_1=".(float)$row["khz"]."&amp;filter_khz_2=".(float)$row["khz"]."&amp;limit=-1' title='Filter on this value'>".(float)$row["khz"]."</a></td>\n"
                ."<td><a onmouseover='window.status=\"View profile for ".(float)$row["khz"]."-".$row["call"]."\";return true;' onmouseout='window.status=\"\";return true;' href=\"".system_URL."/".$row["ID"]."\" onclick=\"signal_info('".$row["ID"]."');return false\"><b>$call</b></a></td>\n";
                if ($type_NDB) {
                    $this->html.=
                    "<td align='right'>".$row["LSB_approx"].($row["LSB"]<>"" ? ($offsets=="" ? $row["LSB"] : number_format((float) ($row["khz"]-($row["LSB"]/1000)), 3, '.', '')): "&nbsp;")."</td>\n"
                    ."<td align='right'>".$row["USB_approx"].($row["USB"]<>"" ? ($offsets=="" ? $row["USB"] : number_format((float) ($row["khz"]+($row["USB"]/1000)), 3, '.', '')): "&nbsp;")."</td>\n"
                    ."<td>".($row["sec"] ? $row["sec"] : "&nbsp;")."</td>\n"
                    ."<td>".($row["format"] ? stripslashes($row["format"]) : "&nbsp;")."</td>\n";
                }
                $this->html.=
                "<td>".($row["QTH"] ? get_sp_maplinks($SP, $row['ID'], $row["QTH"]) : "&nbsp;")."</td>\n"
                ."<td>".($SP ? "<a href='".system_URL."/signal_list?filter_sp=".$row["SP"]."' title='Filter on this value'>".$SP."</a>" : "&nbsp;")."</td>\n"
                ."<td>".($ITU ? "<a href='".system_URL."/signal_list?filter_itu=".$row["ITU"]."' title='Filter on this value'>".$ITU."</a>" : "&nbsp;")."</td>\n"
                ."<td>".($row["GSQ"] ? "<a href='.' onclick='popup_map(\"".$row["ID"]."\",\"".$row["lat"]."\",\"".$row["lon"]."\");return false;' title='Show map (accuracy limited to nearest Grid Square)'><span class='fixed'>".$row["GSQ"]."</span></a>" : "&nbsp;")."</td>\n"
                ."<td>".($row["pwr"] ? $row["pwr"] : "&nbsp;")."</td>\n"
                ."<td>".($row["notes"] ? stripslashes($row["notes"]) : "&nbsp;")."</td>\n"
                ."<td>".($heard_in ? $heard_in : "&nbsp;")."</td>\n"
                ."<td align='right'>"
                .($row["logs"] ? "<a href=\"".system_URL."/signal_log/".$row["ID"]."\" onclick='signal_log(\"".$row["ID"]."\");return false;'><b>".$row["logs"]."</b></a>" : "&nbsp;")."</td>\n"
                ."<td>".($row["last_heard"]!="0000-00-00" ? $row["last_heard"] : "&nbsp;")."</td>\n";

                if ($listenerID) {
                    $this->html.=
                    "<td align='right'>".($row["dx_km"]!=='' ? $row["dx_km"] : "&nbsp;")."</td>\n"
                    ."<td align='right'>".($row["dx_miles"]!=='' ? $row["dx_miles"] : "&nbsp;")."</td>\n"
              //	  ."<td align='right'>".($row["dx_deg"] ? $row["dx_deg"] : "&nbsp;")."</td>\n"
                    ;
                }

                if ($filter_dx_gsq) {
                    $this->html.=
                    "<td align='right'>".($row["range_dx_km"]!=='' ? round($row["range_dx_km"]) : "&nbsp;")."</td>\n"
                    ."<td align='right'>".($row["range_dx_miles"]!=='' ? round($row["range_dx_miles"]) : "&nbsp;")."</td>\n"
                    ."<td align='right'>".($row["range_dx_deg"]!=='' ? round($row["range_dx_deg"]) : "&nbsp;")."</td>\n";
                }

                if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
                    $this->html.=
                    "<td nowrap><a href='javascript: if (confirm(\"CONFIRM\\n\\nAre you sure you wish to delete this signal and\\nall associated logs?\")) { document.form.submode.value=\"delete\"; document.form.targetID.value=\"".$row["ID"]."\"; document.form.submit();}'>Del</a>\n"
                    ."<a href='javascript:signal_merge(".$row["ID"].")'>Merge</a></td>\n";
                }
            }
            $this->html.=     "  </tr>"
            ."</tbody>"
            ."</table>\n"
            ."<br>\n"
            ."<span class='noscreen'>\n"
            ."<b><i>(End of printout)</i></b>\n"
            ."</span>\n";
        } else {
            $this->html.=    "<h2>Results</h2><br><br><h3>No results for search criteria</h3><br><br><br>\n";
        }

            $this->html.=
            "<span class='noprint'>\n"
            ."<input type='button' value='Print...' onclick='".(($limit!=-1 && $limit<$total) ? "if (confirm(\"Information\\n\\nThis printout works best in Landscape.\\n\\nYou are not presently displaying all $total available records.\\nContinue anyway?\")) { window.print(); }": "window.print()")."' class='formbutton' style='width: 150px;'> ";
        if (isset($_COOKIE['cookie_admin']) && $_COOKIE['cookie_admin']==admin_session) {
            $this->html.=    "<input type='button' class='formbutton' value='Add signal...' style='width: 150px' onclick='signal_add(document.form.filter_id.value,document.form.filter_khz_1.value,\"\",\"\",\"\",\"\",\"\",get_type(document.form))'> ";
        }
            $this->html.=
            "<input type='button' class='formbutton' value='All ".system." > Excel' style='width: 150px' title='Get the whole database as Excel' onclick='export_signallist_excel();'> "
            ."<input type='button' class='formbutton' value='All ".system." > PDF'   style='width: 150px' title='get the whole database as PDF' onclick='export_signallist_pdf();'> "
            ."<input type='button' class='formbutton' value='All ".system." > ILG'   style='width: 150px' title='get the whole database as ILGRadio format for Ham Radio Deluxe' onclick='if (confirm(\"EXPORT ENTIRE ".system." DATABASE TO IRGRadio Database format?\\n\\nThis can be a time consuming process - typically 5 minutes or more.\")) { show_ILG(); }'> "
            ."</span>\n"
            ."<script type='text/javascript'>document.form.filter_id.focus();document.form.filter_id.select();</script>\n";
            return $this->html;
    }

    protected function getCountLocations()
    {
        $this->stats['locations'] =    listener_get_count($this->region);
    }

    protected function getCountLogs()
    {
        switch ($this->filter_system) {
            case "1":
                $filter =   "`region` = 'na'";
                break;
            case "2":
                $filter =   "`region` = 'eu'";
                break;
            case "3":
                $filter =   ($this->region!="" && $this->region!="na" ? "`region` = '".$this->region."'" : "1");
                break;
            case "all":
                $filter = "1";
                break;
            case "not_logged":
                $filter = "0";
                break;
        }
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `logs`\n"
            ."WHERE\n"
            ."    ".$filter;
        $this->stats['logs'] =    $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getCountSignalsREUOnly()
    {
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_af`=0 AND\n"
            ."    `heard_in_as`=0 AND\n"
            ."    `heard_in_ca`=0 AND\n"
            ."    `heard_in_eu`=1 AND\n"
            ."    `heard_in_iw`=0 AND\n"
            ."    `heard_in_na`=0 AND\n"
            ."    `heard_in_oc`=0 AND\n"
            ."    `heard_in_sa`=0\n"
            .")";
        $this->stats['REU_only'] = $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getCountSignalsRNAOnly()
    {
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_af`=0 AND\n"
            ."    `heard_in_as`=0 AND\n"
            ."    `heard_in_ca`=0 AND\n"
            ."    `heard_in_eu`=0 AND\n"
            ."    `heard_in_iw`=0 AND\n"
            ."    `heard_in_na`=1 AND\n"
            ."    `heard_in_oc`=0 AND\n"
            ."    `heard_in_sa`=0\n"
            .")";
        $this->stats['RNA_only'] = $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getCountSignalsRNAAndREU()
    {
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_eu`=1 AND\n"
            ."    `heard_in_na`=1\n"
            .")";
        $this->stats['RNA_and_REU'] = $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getCountSignalsRWW()
    {
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE\n"
            ."    `logs` > 0";
        $this->stats['RWW'] = $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getCountSignalsUnassigned()
    {
        $sql =
             "SELECT\n"
            ."    COUNT(*)\n"
            ."FROM\n"
            ."    `signals`\n"
            ."WHERE (\n"
            ."    `heard_in_af`=0 AND\n"
            ."    `heard_in_as`=0 AND\n"
            ."    `heard_in_ca`=0 AND\n"
            ."    `heard_in_eu`=0 AND\n"
            ."    `heard_in_iw`=0 AND\n"
            ."    `heard_in_na`=0 AND\n"
            ."    `heard_in_oc`=0 AND\n"
            ."    `heard_in_sa`=0\n"
            .")";
        $this->stats['Unassigned'] = $this->ObjSignal->getFieldForSql($sql);
    }

    protected function getDateFirstAndLastLogs()
    {
        $filter = "1";
        switch ($this->filter_system) {
            case "1":
                $filter =
                    "(`region` = 'na' OR `region` = 'ca' OR (`region` = 'oc' AND `heard_in` = 'hi'))";
                break;
            case "2":
                $filter =
                    "(`region` = 'eu')";
                break;
            case "3":
                if ($this->region!="") {
                    $filter =
                        "(`region` = '".$this->region."')";
                }
                break;
        }
        $sql =
             "SELECT\n"
            ."    DATE_FORMAT(MIN(`date`),'%e %b %Y') AS `first_log`,\n"
            ."    DATE_FORMAT(MAX(`date`),'%e %b %Y') AS `last_log`\n"
            ."FROM\n"
            ."    `logs`\n"
            ."WHERE\n"
            ."    ".$filter." AND\n"
            ."    `date` !=\"\" AND\n"
            ."    `date` !=\"0000-00-00\"";
        $row = $this->ObjSignal->getRecordForSql($sql);
        $this->stats = array_merge($this->stats, $row);
    }

    protected function setup()
    {
        $this->setupLoadStats();
    }

    protected function setupLoadStats()
    {
        $this->getCountSignalsRNAOnly();
        $this->getCountSignalsREUOnly();
        $this->getCountSignalsRNAAndREU();
        $this->getCountSignalsRWW();
        $this->getCountSignalsUnassigned();
        $this->getCountLocations();
        $this->getCountLogs();
        $this->getDateFirstAndLastLogs();
    }
}
