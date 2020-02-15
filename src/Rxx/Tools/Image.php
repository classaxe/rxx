<?php
/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 9:24 PM
 */
// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   RNA / REU / RWW              *
// * Filename:  img.php                      *
// *                                         *
// * Created:   21/08/2004 (MF)              *
// * Email:     martin@classaxe.com          *
// *******************************************

namespace Rxx\Tools;

use Rxx\Database;
use Rxx\Rxx;

/**
 * Class Image
 * @package Rxx\Tools
 */
class Image
{

// TODO: Add NA Map coords for ATG, ATN and LCA - we have listeners here
// TODO: Fix EU Map code   for SER - should be SRB

    const MAP_COLORS = [
        'EU' => [
            'e8ffe8' => 'ENG,POR,DEU,ALB,HNG,TUR,LVA,ISL',
            'e8e8ff' => 'IRL,ESP,SUI,SVK,SMR,NOR',
            'ffffc0' => 'WLS,GSY,AND,ITA,CZE,SRB,MDA,RUS',
            'ffc8c8' => 'IOM,FRA,DNK,SVN,CVA,BUL,LTU',
            'ffd8ff' => 'SHE,JSY,BEL,COR,AUT,BIH,KAL,GRC,UKR,SWE',
            'ffe098' => 'SCT,LUX,SAR,HRV,MKD,BLR,GEO,FIN',
            'd0f8ff' => 'GSY,ORK,NIR,HOL,BAL,SCY,LIE,POL,MNE,ROU,EST,FRO'
        ],
        'NA' => [
            'e8ffe8' => 'CA,WA,TX,NM,VA,NE,NC,SC,MN,IL,FL,AZ,MO,OR,MI,CO,IN,HI,AK,AL,MS,PA,NY,TN,MD,WV,ID,NV,UT,MT,WY,ND,SD,KS,OK,AR,LA,GA,IA,KY,OH,DC,DE,NJ,RI,CT,MA,VT,NH,ME,WI,PTR',
            'e8e8ff' => 'YT,BC,NT,AB,SK,NU,MB,ON,QC,NL,NB,PE,NS',
            'ffffc0' => 'GRL,MEX,JMC,SVD,PNR',
            'ffc8c8' => 'BAH,GTM,DOM',
            'ffd8ff' => 'BLZ,CTR,BER,HTI,VIR',
            'ffd898' => 'CUB,NCG,VRG',
            'd0ffff' => 'HND,PTR,CYM'
        ]
    ];

    const MAP_FLOOD = [
        'EU' => [
            'AND' => [[163, 560]],
            'ALB' => [[360, 575]],
            'AUT' => [[300, 490]],
            'AZR' => [], // Appears off the map
            'BAL' => [[165, 606],[178, 597],[189, 595]],
            'BEL' => [[197, 441]],
            'BIH' => [[335, 540]],
            'BLR' => [[440, 390]],
            'BUL' => [[410, 560]],
            'COR' => [[240, 565]],
            'CVA' => [[289, 574]],
            'CZE' => [[310, 450]],
            'DNK' => [[248, 344],[257, 364],[261, 370],[271, 360],[268, 373],[298, 372]],
            'DEU' => [[240, 434]],
            'ENG' => [[133, 401],[132, 440]],
            'ESP' => [[107, 588]],
            'EST' => [[415, 295],[386, 296],[384, 306]],
            'FIN' => [[420, 210],[360, 267],[367, 272],[369, 279]],
            'FRA' => [[170, 515]],
            'FRO' => [[67, 206],[70, 202]],
            'GSY' => [[118, 459]],
            'GIB' => [[90, 642]],
            'GEO' => [[605, 565]],
            'GRC' => [[375, 600],[400, 613],[410, 656],[425, 604],[410, 624],[422, 614],[443, 643]],
            'HNG' => [[350, 500]],
            'HOL' => [[205, 420]],
            'HRV' => [[320, 515]],
            'IOM' => [[94, 382]],
            'IRL' => [[55, 400]],
            'ISL' => [[39, 118]],
            'ITA' => [[270, 545]],
            'JSY' => [[124, 463]],
            'KAL' => [[375, 375]],
            'LIE' => [[249, 493]],
            'LTU' => [[400, 360]],
            'LUX' => [[210, 455]],
            'LVA' => [[415, 330]],
            'MCO' => [[218, 547]],
            'MDA' => [[450, 490]],
            'MKD' => [[377, 573]],
            'MLT' => [[300, 645]],
            'MNE' => [[350, 555]],
            'NIR' => [[65, 375]],
            'NOR' => [[245, 240]],
            'ORK' => [[109, 305]],
            'POL' => [[355, 425]],
            'POR' => [[66, 594]],
            'ROU' => [[405, 510]],
            'RUS' => [[570, 300]],
            'SAR' => [[242, 593]],
            'SCT' => [[68, 319],[61, 328],[73, 331],[76, 347],[74, 358],[69, 359],[83, 355],[86, 361],[100, 340],[61, 334],[60, 339]],
            'SCY' => [[300, 625]],
            'SRB' => [[370, 540]],
            'SHE' => [[134, 268],[134, 273],[129, 278]],
            'SMR' => [[286, 550]],
            'SUI' => [[230, 495]],
            'SVB' => [], // Appears off the map
            'SVK' => [[350, 470]],
            'SVN' => [[300, 510]],
            'SWE' => [[320, 185],[323, 336],[338, 324]],
            'TUR' => [[434, 576],[519, 606]],
            'UKR' => [[480, 465]],
            'WLS' => [[97, 397],[105, 410]],
            'sea' => [[600, 40],[100, 100],[448, 585],[500, 550]]
        ],
        'NA' => [
            'AB' =>     [[260, 180]],
            'AK' =>     [[125, 100],[96, 144]],
            'ALS' =>    [[125, 100],[96, 144]],
            'AL' =>     [[415, 360]],
            'AR' =>     [[375, 340]],
            'ATG' =>    [],
            'ATN' =>    [],
            'AZ' =>     [[245, 345]],
            'BAH' =>    [[475, 415],[482, 418],[477, 433],[481, 429],[489, 430],[494, 435],[503, 438],[498, 446],[506, 451],[514, 453],[513, 464],[521, 457]],
            'BC' =>     [[200, 190],[190, 220],[163, 189]],
            'BER' =>    [[563, 370]],
            'BLZ' =>    [[402, 499]],
            'CA' =>     [[200, 330]],
            'CO' =>     [[290, 310]],
            'CT' =>     [[500, 285]],
            'CTR' =>    [[435, 565]],
            'CYM' =>    [[460, 475]],
            'CUB' =>    [[473, 457],[443, 460]],
            'DC' =>     [[475, 308]],
            'DE' =>     [[486, 311]],
            'DOM' =>    [[535, 485]],
            'FL' =>     [[450, 400],[452, 433]],
            'GA' =>     [[435, 365]],
            'GRL' =>    [[545, 45]],
            'GTM' =>    [[390, 520]],
            'HND' =>    [[415, 520]],
            'HI' =>     [[36, 29],[46, 32],[55, 37],[65, 47]],
            'HWA' =>    [[36, 29],[46, 32],[55, 37],[65, 47]],
            'HTI' =>    [[520, 480]],
            'IA' =>     [[370, 280]],
            'ID' =>     [[240, 265]],
            'IN' =>     [[415, 300]],
            'IL' =>     [[400, 300]],
            'JMC' =>    [[485, 490]],
            'KS' =>     [[340, 315]],
            'KY' =>     [[420, 320]],
            'LA' =>     [[375, 370]],
            'LCA' =>    [],
            'MA' =>     [[505, 280]],
            'MB' =>     [[350, 180]],
            'MD' =>     [[477, 304]],
            'ME' =>     [[515, 255]],
            'MEX' =>    [[320, 450]],
            'MI' =>     [[405, 250],[425, 275]],
            'MN' =>     [[365, 250]],
            'MO' =>     [[375, 315]],
            'MS' =>     [[395, 360]],
            'MT' =>     [[275, 240]],
            'NB' =>     [[535, 245]],
            'NC' =>     [[465, 335]],
            'NCG' =>    [[425, 540]],
            'ND' =>     [[330, 240]],
            'NE' =>     [[330, 290]],
            'NH' =>     [[505, 270]],
            'NJ' =>     [[490, 300]],
            'NL' =>     [[545, 195],[590, 230],[608, 240]],
            'NM' =>     [[285, 345]],
            'NS' =>     [[555, 255]],
            'NT' =>     [[250, 120],[280, 55],[305, 65],[320, 40],[310, 35],[330, 32]],
            'NU' =>     [[350, 115],[415, 112],[325, 75],[380, 55],[330, 45],[360, 60],[425, 125],[440, 130],[347, 45],
                [450, 90],[410, 45],[417, 23],[377, 28],[378, 37],[362, 26],[364, 41],[377, 45],[445, 168],
                [448, 164],[446, 174],[436, 82],[447, 120],[510, 132],[349, 33],[352, 40],[371, 20]
            ],
            'NV' =>     [[220, 305]],
            'NY' =>     [[480, 275],[502, 293]],
            'OH' =>     [[440, 300]],
            'OK' =>     [[340, 340]],
            'ON' =>     [[420, 215]],
            'OR' =>     [[210, 270]],
            'PA' =>     [[470, 295]],
            'PE' =>     [[552, 247]],
            'PNR' =>    [[460, 580]],
            'PR' =>     [[565, 490]],
            'PTR' =>    [[565, 490]],
            'QC' =>     [[480, 200],[547, 222]],
            'RI' =>     [[508, 285]],
            'SC' =>     [[455, 350]],
            'SD' =>     [[330, 265]],
            'SK' =>     [[310, 180]],
            'SVD' =>    [[400, 533]],
            'TN' =>     [[415, 335]],
            'TX' =>     [[330, 370]],
            'UT' =>     [[254, 306]],
            'VA' =>     [[465, 320],[486, 319]],
            'VIR' =>    [[573, 495]],
            'VRG' =>    [[577, 490]],
            'VT' =>     [[500, 265]],
            'WA' =>     [[215, 238]],
            'WI' =>     [[395, 260]],
            'WV' =>     [[450, 310]],
            'WY' =>     [[280, 270]],
            'YT' =>     [[190, 120]],
            'sea' =>    [[10, 10],[40, 50],[412, 240],[412, 263],[437, 255],[447, 282],[469, 269]]
        ]
    ];

 /**
     *
     */
    public static function generate_station_map()
    {
        $path_arr =   (explode('?', $_SERVER["REQUEST_URI"]));
        $path_arr =   explode('/', $path_arr[0]);
        $ID =         array_pop($path_arr);
        $system_ID =  array_pop($path_arr);
        switch ($system_ID) {
            case "1":
                $region =   "(li.region = 'na' OR li.region = 'ca' OR li.itu = 'HWA')";
                break;
            case "2":
                $region =   "(li.region = 'eu')";
                break;
        }
        $sql =
            "SELECT DISTINCT\n"
            ."  li.sp,\n"
            ."  li.itu\n"
            ."FROM\n"
            ."  listeners li\n"
            ."WHERE\n"
            ."  (li.map_x != 0 OR li.map_y != 0) AND\n"
            ."  li.count_logs != 0 AND\n"
            ."  $region";
//  \Rxx\Rxx::z($sql);
        $result =   Database::query($sql);
        $reporters_in =     [];
        for ($i=0; $i< Database::numRows($result); $i++) {
            $row =  Database::fetchArray($result);
            $reporters_in[] =   ($row['sp'] ? $row['sp'] : $row['itu']);
        }
        $reporters_in = implode(',', $reporters_in);

        $sql =
            "SELECT\n"
            ."  `call`,\n"
            ."  heard_in,\n"
            ."  itu,\n"
            ."  khz,\n"
            ."  sp\n"
            ."FROM\n"
            ."  `signals`\n"
            ."WHERE\n"
            ."  ID = ".$ID;
//  \Rxx\Rxx::z($sql);
        $result =   Database::query($sql);
        $row =  Database::fetchArray($result, MYSQLI_ASSOC);
        $heard_in = implode(',', explode(" ", $row['heard_in']));
        $text =     (float)$row['khz']."-".$row['call'];
        $based_in =     ($row['sp'] ? $row['sp'] : $row['itu']);

        $sql =
            "SELECT\n"
            ."  map_x,\n"
            ."  map_y,\n"
            ."  primary_QTH\n"
            ."FROM\n"
            ."  listeners li\n"
            ."WHERE\n"
            ."  (li.map_x != 0 OR li.map_y != 0) AND\n"
            ."  li.count_logs != 0 AND\n"
            ."  ".$region;
//  \Rxx\Rxx::z($sql);
        $result =   @Database::query($sql);
        $reporters =    array();
        for ($i=0; $i< Database::numRows($result); $i++) {
            $row =  Database::fetchArray($result, MYSQLI_ASSOC);
            if ($row['map_x']) {
                $reporters[] =  $row['map_x'].",".$row['map_y'].",".$row['primary_QTH'];
            }
        }
        $reporters =    implode($reporters, "|");

        $sql =
            "SELECT DISTINCT\n"
            ."  `map_x`,\n"
            ."  `map_y`,\n"
            ."  `name`,\n"
            ."  `heard_in`,\n"
            ."  `dx_miles`,\n"
            ."  `primary_QTH`,\n"
            ."  MAX(`daytime`) as `daytime`\n"
            ."FROM\n"
            ."  `listeners` li\n"
            ."INNER JOIN `logs` ON\n"
            ."  `li`.`ID` = `logs`.`listenerID`\n"
            ."WHERE\n"
            ."  `logs`.`signalID` = ".$ID." AND\n"
            ."  (li.map_x !=0 OR li.map_y != 0) AND\n"
            ."  ".$region."\n"
            ."GROUP BY\n"
            ."    li.ID\n"
            ."ORDER BY\n"
            ."  heard_in,\n"
            ."  name";
//  \Rxx\Rxx::z($sql);

        $result =   Database::query($sql);
        $reporter_rxed =    array();
        for ($i=0; $i< Database::numRows($result); $i++) {
            $row =  Database::fetchArray($result, MYSQLI_ASSOC);
            if ($row['map_x']) {
                $name = str_replace(array('&aelig;','&ouml;','&auml;'), array('ae','o','a'), $row['name']);
                if (strlen($name) > 20) {
                    $name = substr($name, 0, 17)."...";
                }
                $reporter_rxed[] =  $row['map_x'].",".$row['map_y'].",".$row['daytime'].",".$row['primary_QTH'].",". Rxx::pad($row['heard_in'], 3)." ". Rxx::pad($name, 20). Rxx::lead($row['dx_miles'], 4);
            }
        }
        $reporter_rxed =    implode($reporter_rxed, "|");

        switch($system_ID) {
            case 1:
                header('Content-Type: image/gif');
                static::draw_station_map_na($based_in, $reporters_in, $reporters, $heard_in, $reporter_rxed, $text);
                break;
            case 2:
                header('Content-Type: image/gif');
                static::draw_station_map_eu($based_in, $reporters_in, $reporters, $heard_in, $reporter_rxed, $text);
                break;
        }
    }

    public static function generate_listener_map()
    {
        $path_arr =   (explode('?', $_SERVER["REQUEST_URI"]));
        $path_arr =   explode('/', $path_arr[0]);
        $system_ID =  array_pop($path_arr);
        switch ($system_ID) {
            case "1":   $region= "(`listeners`.`region`='na' OR `listeners`.`region`='ca' OR `listeners`.`itu` = 'HWA')";
                break;
            case "2":   $region="`listeners`.`region`='eu'";
                break;
        }
        switch ($system_ID) {
            case "1":   $region= "(`listeners`.`region`='na' OR `listeners`.`region`='ca' OR `listeners`.`itu` = 'HWA')";
                break;
            case "2":   $region="`listeners`.`region` = 'eu'";
                break;
        }

        $sql =
            "SELECT DISTINCT\n"
            ."  `sp`,\n"
            ."  `itu`\n"
            ."FROM\n"
            ."  `listeners`\n"
            ."WHERE\n"
            ."  ".$region."\n"
            ."ORDER BY\n"
            ."  `itu`";
        $result =   @Database::query($sql);
        $reporters_in =     [];
        for ($i=0; $i< Database::numRows($result); $i++) {
            $row =  Database::fetchArray($result);
            $reporters_in[] =   ($row['sp'] ? $row['sp'] : $row['itu']);
        }
        $reporters_in = implode(',', $reporters_in);

        $sql =
            "SELECT\n"
            ."  `map_x`,\n"
            ."  `map_y`,\n"
            ."  `primary_QTH`\n"
            ."FROM\n"
            ."  `listeners`\n"
            ."WHERE\n"
            ."  ".$region;
        $result =   @Database::query($sql);
        $reporters =    array();
        for ($i=0; $i< Database::numRows($result); $i++) {
            $row =  Database::fetchArray($result, MYSQLI_ASSOC);
            if ($row['map_x']) {
                $reporters[] =  $row['map_x'].",".$row['map_y'].",".$row['primary_QTH'];
            }
        }
        $reporters =    implode($reporters, "|");

        $sql =
            "SELECT DISTINCT\n"
            ."  `map_x`,\n"
            ."  `map_y`,\n"
            ."  `name`,\n"
            ."  `heard_in`,\n"
            ."  `primary_QTH`\n"
            ."FROM\n"
            ."  `listeners`,\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `listeners`.`ID` = `logs`.`listenerID` AND\n"
            ."  `listeners`.`map_x` IS NOT NULL AND\n"
//    ."  `logs`.`signalID` = \"$ID\" AND\n"
            ."  $region\n"
            ."GROUP BY\n"
            ."  `listeners`.`ID`\n"
            ."ORDER BY\n"
            ."  `heard_in`,\n"
            ."  `name`";
//  \Rxx\Rxx::z($sql);die;
        $result =   Database::query($sql);
        $reporter_rxed =    array();
        for ($i=0; $i< Database::numRows($result); $i++) {
            $row =  Database::fetchArray($result, MYSQLI_ASSOC);
            if ($row['map_x']) {
                $name = str_replace(array('&aelig;','&ouml;'), array('ae','o'), $row['name']);
                if (strlen($name) > 20) {
                    $name = substr($name, 0, 17)."...";
                }
                $reporter_rxed[] =
                    $row['map_x'].",".$row['map_y'].",,".$row['primary_QTH'].","
                    . Rxx::pad($row['heard_in'], 3)." ". Rxx::pad($name, 20);
            }
        }

        switch($system_ID) {
            case 1:
                header('Content-Type: image/gif');
                static::draw_station_map_na('', '', $reporters, $reporters_in, "", "");
                break;
            case 2:
                header('Content-Type: image/gif');
                static::draw_station_map_eu('', '', $reporters, $reporters_in, "", "");
                break;
        }
    }

    /**
     *
     */
    public static function generate_map_eu()
    {
        $image =        imageCreate(688, 665);

        $bgcol =        ImageColorAllocate($image, 18, 52, 86);
        $black =        ImageColorAllocate($image, 0, 0, 0);
        $white =        ImageColorAllocate($image, 255, 255, 255);
        $darkgray =     ImageColorAllocate($image, 120, 120, 120);

        $image_map =    imageCreateFromGif("assets/eu_map_outline.gif");
        ImageCopyMerge($image, $image_map, 0, 0, 0, 0, 688, 665, 100);

        foreach (static::MAP_COLORS['EU'] AS $rgb => $countries) {
            $r = hexdec(substr($rgb, 0, 2));
            $g = hexdec(substr($rgb, 2, 2));
            $b = hexdec(substr($rgb, 4, 2));
            Image::draw_fill_country_list_eu($countries, $image, ImageColorAllocate($image, $r, $g, $b));
        }

        Image::ImageRectangleWithRoundedCorners($image, 3, 5, 300, 56, 5, $darkgray, $white);
        ImageString($image, 5, 10, 10, "NDBList State and Country codes", $black);
        ImageString($image, 2, 10, 27, "Please use these when reporting.", $black);
        ImageString($image, 2, 10, 40, "Contact: martin@classaxe.com", $black);

        $codes =        imageCreateFromGif("assets/eu_map_codes.gif");
        ImageCopyMerge($image, $codes, 0, 0, 0, 0, 653, 665, 30);

        ImageColorTransparent($image, $bgcol);
        ImageGif($image);
        ImageDestroy($image);
        ImageDestroy($codes);
        ImageDestroy($image_map);
    }

    /**
     *
     */
    public static function generate_map_na()
    {
        $image =        imageCreate(653, 620);

        $bgcol =        ImageColorAllocate($image, 18, 52, 86);
        $black =        ImageColorAllocate($image, 0, 0, 0);
        $white =        ImageColorAllocate($image, 255, 255, 255);
        $darkgray =     ImageColorAllocate($image, 120, 120, 120);

        $image_map =    imageCreateFromGif("assets/na_map_outline.gif");
        ImageCopyMerge($image, $image_map, 0, 0, 0, 0, 653, 620, 100);
        ImageDestroy($image_map);

        foreach (static::MAP_COLORS['NA'] AS $rgb => $countries) {
            $r = hexdec(substr($rgb, 0, 2));
            $g = hexdec(substr($rgb, 2, 2));
            $b = hexdec(substr($rgb, 4, 2));
            Image::draw_fill_country_list_na($countries, $image, ImageColorAllocate($image, $r, $g, $b));
        }

        Image::ImageRectangleWithRoundedCorners($image, 3, 565, 300, 616, 5, $darkgray, $white);
        ImageString($image, 5, 10, 570, "NDBList State and Country codes", $black);
        ImageString($image, 2, 10, 587, "Please use these when reporting.", $black);
        ImageString($image, 2, 10, 600, "Contact: martin@classaxe.com", $black);

        $codes =        imageCreateFromGif("assets/na_map_codes.gif");
        ImageCopyMerge($image, $codes, 0, 0, 0, 0, 653, 620, 30);
        ImageDestroy($codes);

        header('Content-Type: image/gif');
        ImageColorTransparent($image, $bgcol);
        ImageGif($image);
        ImageDestroy($image);
    }

    /**
     * @param $based_in
     * @param $reporters_in
     * @param $reporters
     * @param $heard_in
     * @param $reporter_rxed
     * @param $text
     */
    public static function draw_station_map_eu($based_in, $reporters_in, $reporters, $heard_in, $reporter_rxed, $text)
    {
        if ($reporter_rxed) {
            $image =        imageCreate(860, 665);
        } else {
            $image =        imageCreate(688, 665);
        }

        $col_background =   ImageColorAllocate($image, 18, 52, 86);
        $col_heard_no =     ImageColorAllocate($image, 255, 210, 210);
        $col_heard_yes =    ImageColorAllocate($image, 230, 255, 230);
        $col_based_in =     ImageColorAllocate($image, 150, 220, 255);
        $map_sea =          ImageColorAllocate($image, 230, 230, 255);

        $image_map =        imageCreateFromGif("assets/eu_map_outline.gif");
        ImageCopyMerge($image, $image_map, 0, 0, 0, 0, 688, 665, 100);

        $codes =            imageCreateFromGif("assets/eu_map_codes.gif");
        $img_point1 =       imageCreateFromGif("assets/map_point1.gif");
        $img_point2 =       imageCreateFromGif("assets/map_point2.gif");
        $img_point3 =       imageCreateFromGif("assets/map_point3.gif");
        $img_point4 =       imageCreateFromGif("assets/map_point4.gif");

        if ($reporters_in) {
            Image::draw_fill_country_list_eu($reporters_in, $image, $col_heard_no);
        }
        if ($heard_in) {
            Image::draw_fill_country_eu("sea", $image, $map_sea);
            Image::draw_fill_country_list_eu($heard_in, $image, $col_heard_yes);
        }
        if ($based_in) {
            Image::draw_fill_country_list_eu($based_in, $image, $col_based_in);
        }
        if ($text) {
            Image::draw_map_legend($image, $text, $col_based_in, $col_heard_yes, $col_heard_no, 3, 3, $img_point1, $img_point2, $img_point3, $img_point4);
        }

        if ($reporters) {
            Image::draw_map_point_list($reporters, $image, $img_point3, $img_point4);
        }

        if ($reporter_rxed) {
            Image::draw_map_point_list_names($reporter_rxed, $image, $img_point1, $img_point2, 695, 0);
        }

        ImageCopyMerge($image, $codes, 0, 0, 0, 0, 860, 665, 30);
        ImageColorTransparent($image, $col_background);

        ImageGif($image);
        ImageDestroy($image_map);
        ImageDestroy($image);
        ImageDestroy($codes);
        ImageDestroy($img_point1);
        ImageDestroy($img_point2);
        ImageDestroy($img_point3);
        ImageDestroy($img_point4);
    }

    /**
     * @param $based_in
     * @param $reporters_in
     * @param $reporters
     * @param $heard_in
     * @param $reporter_rxed
     * @param $text
     */
    public static function draw_station_map_na($based_in, $reporters_in, $reporters, $heard_in, $reporter_rxed, $text)
    {
        $image =            imageCreate(653, 620);

        $col_background =   ImageColorAllocate($image, 18, 52, 86);
        $col_heard_no =     ImageColorAllocate($image, 255, 210, 210);
        $col_heard_yes =    ImageColorAllocate($image, 230, 255, 230);
        $col_based_in =     ImageColorAllocate($image, 150, 220, 255);
        $map_sea =          ImageColorAllocate($image, 230, 230, 255);

        $image_map =        imageCreateFromGif("assets/na_map_outline.gif");
        ImageCopyMerge($image, $image_map, 0, 0, 0, 0, 653, 620, 100);

        $codes =            imageCreateFromGif("assets/na_map_codes.gif");
        $img_point1 =       imageCreateFromGif("assets/map_point1.gif");
        $img_point2 =       imageCreateFromGif("assets/map_point2.gif");
        $img_point3 =       imageCreateFromGif("assets/map_point3.gif");
        $img_point4 =       imageCreateFromGif("assets/map_point4.gif");

        if ($reporters_in) {
            Image::draw_fill_country_list_na(strtoupper($reporters_in), $image, $col_heard_no);
        }
        if ($heard_in) {
            Image::draw_fill_country_na("sea", $image, $map_sea);
            Image::draw_fill_country_list_na(strtoupper($heard_in), $image, $col_heard_yes);
        }
        if ($based_in) {
            Image::draw_fill_country_list_na(strtoupper($based_in), $image, $col_based_in);
        }
        if ($text) {
            Image::draw_map_legend($image, $text, $col_based_in, $col_heard_yes, $col_heard_no, 520, 535, $img_point1, $img_point2, $img_point3, $img_point4);
        }
        if ($reporters) {
            Image::draw_map_point_list($reporters, $image, $img_point3, $img_point4);
        }

        if ($reporter_rxed) {
            Image::draw_map_point_list_names($reporter_rxed, $image, $img_point1, $img_point2, 3, 578-(count(explode("|", $reporter_rxed))*8));
        }

        ImageCopyMerge($image, $codes, 0, 0, 0, 0, 653, 620, 30);
        ImageColorTransparent($image, $col_background);

        ImageGif($image);
        ImageDestroy($image);
        ImageDestroy($image_map);
        ImageDestroy($codes);
        ImageDestroy($img_point1);
        ImageDestroy($img_point2);
        ImageDestroy($img_point3);
        ImageDestroy($img_point4);
    }

    /**
     * @param $list
     * @param $image
     * @param $color
     */
    public static function draw_fill_country_list_eu($list, &$image, $color)
    {
        $countries = explode(',', $list);
        foreach ($countries as $country) {
            Image::draw_fill_country_eu($country, $image, $color);
        }
    }

    /**
     * @param $list
     * @param $image
     * @param $color
     */
    public static function draw_fill_country_list_na($list, &$image, $color)
    {
        $countries = explode(",", $list);
        foreach ($countries as $country) {
            Image::draw_fill_country_na($country, $image, $color);
        }
    }

    /**
     * @param $country
     * @param $image
     * @param $color
     */
    public static function draw_fill_country_eu($country, &$image, $color)
    {
        $coords = static::MAP_FLOOD['EU'];

        if (!$country) {
            return;
        }

        if (!isset($coords[$country])) {
//            print $country. ' ';
            return;
        }

        foreach ($coords[$country] as $point) {
            ImageFill($image, $point[0], $point[1], $color);
        }
    }

    /**
     * @param $country
     * @param $image
     * @param $color
     */
    public static function draw_fill_country_na($country, &$image, $color)
    {
        $coords = static::MAP_FLOOD['NA'];

        if (!$country) {
            return;
        }

        if (!isset($coords[$country])) {
//            print $country. ' ';
            return;
        }

        foreach ($coords[$country] as $point) {
            ImageFill($image, $point[0], $point[1], $color);
        }
    }

    /**
     * @param $image
     * @param $text
     * @param $col_tx
     * @param $col_heard
     * @param $col_rep
     * @param $x
     * @param $y
     * @param $img_point1
     * @param $img_point2
     * @param $img_point3
     * @param $img_point4
     */
    public static function draw_map_legend(&$image, $text, $col_tx, $col_heard, $col_rep, $x, $y, &$img_point1, &$img_point2, &$img_point3, &$img_point4)
    {
        $black =    ImageColorAllocate($image, 0, 0, 0);
        $white =    ImageColorAllocate($image, 255, 255, 255);
        $darkgray =     ImageColorAllocate($image, 120, 120, 120);

        Image::ImageRectangleWithRoundedCorners($image, $x+3, $y+3, $x+129, $y+81, 5, $darkgray, $darkgray);
        Image::ImageRectangleWithRoundedCorners($image, $x, $y, $x+126, $y+78, 5, $black, $white);
        ImageString($image, 4, $x+25, $y+5, $text, $black);

        ImageFilledRectangle($image, $x+10, $y+25, $x+22, $y+31, $col_tx);
        ImageRectangle($image, $x+10, $y+25, $x+22, $y+31, $black);
        ImageString($image, 2, $x+29, $y+21, "Transmitter", $black);

        ImageFilledRectangle($image, $x+10, $y+35, $x+22, $y+41, $col_heard);
        ImageRectangle($image, $x+10, $y+35, $x+22, $y+41, $black);
        ImageString($image, 2, $x+29, $y+31, "Heard Here", $black);

        ImageFilledRectangle($image, $x+10, $y+45, $x+22, $y+51, $col_rep);
        ImageRectangle($image, $x+10, $y+45, $x+22, $y+51, $black);
        ImageString($image, 2, $x+29, $y+41, "Not heard here", $black);

        ImageCopyMerge($image, $img_point1, $x+8, $y+53, 0, 0, 9, 9, 100);
        ImageCopyMerge($image, $img_point3, $x+16, $y+53, 0, 0, 9, 9, 100);
        ImageString($image, 2, $x+29, $y+51, "Listener QTH", $black);

        ImageCopyMerge($image, $img_point2, $x+8, $y+63, 0, 0, 9, 9, 100);
        ImageCopyMerge($image, $img_point4, $x+16, $y+63, 0, 0, 9, 9, 100);
        ImageString($image, 2, $x+29, $y+61, "Secondary QTH", $black);
    }

    /**
     * @param $im
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * @param $radius
     * @param $linecolor
     * @param bool $fillcolor
     */
    public static function ImageRectangleWithRoundedCorners(&$im, $x1, $y1, $x2, $y2, $radius, $linecolor, $fillcolor = false)
    {
        if ($fillcolor) {
            imagefilledarc($im, $x1+$radius, $y1+$radius, $radius*2, $radius*2, 180, 270, $fillcolor, IMG_ARC_PIE);
            imagefilledarc($im, $x2-$radius, $y1+$radius, $radius*2, $radius*2, 270, 0, $fillcolor, IMG_ARC_PIE);
            imagefilledarc($im, $x1+$radius, $y2-$radius, $radius*2, $radius*2, 90, 180, $fillcolor, IMG_ARC_PIE);
            imagefilledarc($im, $x2-$radius, $y2-$radius, $radius*2, $radius*2, 0, 90, $fillcolor, IMG_ARC_PIE);
            imagefilledrectangle($im, $x1+$radius, $y1, $x2-$radius, $y2, $fillcolor);
            imagefilledrectangle($im, $x1, $y1+$radius, $x2, $y2-$radius, $fillcolor);
        }
        imagearc($im, $x1+$radius, $y1+$radius, $radius*2, $radius*2, 180, 270, $linecolor);
        imagearc($im, $x2-$radius, $y1+$radius, $radius*2, $radius*2, 270, 0, $linecolor);
        imagearc($im, $x1+$radius, $y2-$radius, $radius*2, $radius*2, 90, 180, $linecolor);
        imagearc($im, $x2-$radius, $y2-$radius, $radius*2, $radius*2, 0, 90, $linecolor);
        imageline($im, $x1+$radius, $y1, $x2-$radius, $y1, $linecolor);
        imageline($im, $x1+$radius, $y2, $x2-$radius, $y2, $linecolor);
        imageline($im, $x1, $y1+$radius, $x1, $y2-$radius, $linecolor);
        imageline($im, $x2, $y1+$radius, $x2, $y2-$radius, $linecolor);
    }

    /**
     * @param $list
     * @param $image
     * @param $img_point3
     * @param $img_point4
     */
    public static function draw_map_point_list($list, &$image, &$img_point3, &$img_point4)
    {
        $arr = explode("|", $list);
        for ($i=0; $i<count($arr); $i++) {
            $coords = explode(",", $arr[$i]);
            $xc = $coords[0];
            $yc = $coords[1];
            $primary_QTH = $coords[2];
            if ($primary_QTH) {
                ImageCopyMerge($image, $img_point3, $xc-4, $yc-4, 0, 0, 9, 9, 100);
            } else {
                ImageCopyMerge($image, $img_point4, $xc-4, $yc-4, 0, 0, 9, 9, 100);
            }
        }
    }

    /**
     * @param $list
     * @param $image
     * @param $img_point
     * @param $img_point2
     * @param $x
     * @param $y
     */
    public static function draw_map_point_list_names($list, &$image, &$img_point, &$img_point2, $x, $y)
    {
        $col_background =   ImageColorAllocate($image, 255, 0, 0);
        ImageColorTransparent($image, $col_background);
        $white =            ImageColorAllocate($image, 255, 255, 255);
        $black =            ImageColorAllocate($image, 0, 0, 0);
        $darkgray =         ImageColorAllocate($image, 110, 110, 110);
        $arr =            explode("|", $list);

        Image::ImageRectangleWithRoundedCorners($image, $x+3, $y+3, $x+162, (count($arr)*8)+$y+3+35, 5, $darkgray, $darkgray);
        Image::ImageRectangleWithRoundedCorners($image, $x, $y, $x+159, (count($arr)*8)+$y+35, 5, $black, $white);

        ImageString($image, 1, $x+13, $y+4, "QTH LISTENER           MILES", $black);
        ImageString($image, 1, $x+3, $y+12, "------------------------------", $black);

        for ($i=0; $i<count($arr); $i++) {
            $coords = explode(",", $arr[$i]);
            if (!isset($coords[4])) {
                continue;
            }
            $xc =       $coords[0];
            $yc =       $coords[1];
            $daytime =      $coords[2];
            $primary_QTH =  $coords[3];
            $name =         stripslashes($coords[4]);
            if ($primary_QTH) {
                ImageCopyMerge($image, $img_point, $xc-4, $yc-4, 0, 0, 9, 9, 100);
                ImageCopyMerge($image, $img_point, $x+3, ($i*8)+$y+20, 0, 0, 9, 9, 100);
            } else {
                ImageCopyMerge($image, $img_point2, $xc-4, $yc-4, 0, 0, 9, 9, 100);
                ImageCopyMerge($image, $img_point2, $x+3, ($i*8)+$y+20, 0, 0, 9, 9, 100);
            }
            if ($daytime) {
                ImageString($image, 1, $x+13, ($i*8)+$y+20, $name, $black);
            } else {
                ImageString($image, 1, $x+13, ($i*8)+$y+20, $name, $darkgray);
            }
        }
        ImageString($image, 1, $x+13, ($i*8)+$y+20+5, "Daytime logs are shown", $darkgray);
        ImageString($image, 1, $x+13, ($i*8)+$y+20+5, "                       bold", $black);

    }

    /**
     *
     */
    public static function state_map_gif()
    {
        global  $SP, $ITU, $listenerID, $simple, $filter_active, $hide_labels, $hide_placenames;
        global  $type_NDB, $type_TIME, $type_DGPS, $type_NAVTEX, $type_HAMBCN, $type_OTHER, $places, $ID;
        $SP =       strToLower($SP);

        $sql =  "SELECT * FROM `maps` WHERE `SP` = '$SP'";
        $result =   Database::query($sql);
        if (! Database::numRows($result)) {
            return;
        }
        $coords =   Database::fetchArray($result, MYSQLI_ASSOC);

        $filter_type =  array();

        if (!($type_NDB || $type_DGPS || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER)) {
            switch (system) {
                case "RNA":     $type_NDB =     1;
                    break;
                case "REU":     $type_NDB =     1;
                    break;
                case "RWW":     $type_NDB =     1;
                    break;
            }
        }

        if ($type_NDB || $type_DGPS || $type_TIME || $type_HAMBCN || $type_NAVTEX || $type_OTHER) {
            if ($type_NDB) {
                $filter_type[] =     "`type` = ".NDB;
            }
            if ($type_DGPS) {
                $filter_type[] =     "`type` = ".DGPS;
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
        $filter_type =  "(".implode($filter_type, " OR ").")";

        if ($simple==1) {
            $file =     "assets/maps_simple/".$SP."_bw.gif";
        } else {
            $file =     "assets/maps/".$SP."_bw.gif";
        }
        $image =        imageCreateFromGif($file);

        $red =  ImageColorAllocate($image, 200, 0, 0);
        $blue =     ImageColorAllocate($image, 0, 0, 255);
        $grey =     ImageColorAllocate($image, 32, 32, 32);

        $img_DGPS =         imageCreateFromGif("assets/map_point_DGPS.gif");
        $img_HAM =      imageCreateFromGif("assets/map_point_HAMBCN.gif");
        $img_NAVTEX =   imageCreateFromGif("assets/map_point_NAVTEX.gif");
        $img_NDB =      imageCreateFromGif("assets/map_point_NDB.gif");
        $img_TIME =         imageCreateFromGif("assets/map_point_TIME.gif");
        $img_OTHER =    imageCreateFromGif("assets/map_point_OTHER.gif");
        $img_inactive =     imageCreateFromGif("assets/map_point_inactive.gif");
        $img_place =    imageCreateFromGif("assets/map_point_place.gif");
        $img_capital =  imageCreateFromGif("assets/map_point_capital.gif");

        if ($places) {
            $sql =  "SELECT * FROM `places` WHERE `sp` = '$SP' AND (`population`>=$places OR `capital` = '1')";
            $result =   @Database::query($sql);
            for ($i=0; $i< Database::numRows($result); $i++) {
                $row =  Database::fetchArray($result, MYSQLI_ASSOC);
                $xpos =     (int)($coords['ix2'] -  ((($coords['ix2'] - $coords['ix1'])/($coords['lon2'] - $coords['lon1'])) * ($coords['lon2'] - $row['lon'])));
                $ypos =     (int)($coords['iy1'] + ((($coords['lat2'] - $row['lat']) / ($coords['lat2'] - $coords['lat1'])) * ($coords['iy2']-$coords['iy1'])));
                if ($row['capital']=='1') {
                    ImageCopyMerge($image, $img_capital, $xpos-4, $ypos-4, 0, 0, 9, 9, 80);
                } else {
                    ImageCopyMerge($image, $img_place, $xpos-4, $ypos-4, 0, 0, 9, 9, 80);
                }
                if ($hide_placenames!="1") {
                    ImageString($image, 1, $xpos+4, $ypos-4, $row['name'], $red);
                }
            }
        }

        $sql =   "SELECT DISTINCT\n"
            ."  `signals`.`ID`,\n"
            ."  `signals`.`call`,\n"
            ."  `signals`.`khz`,\n"
            ."  `signals`.`lat`,\n"
            ."  `signals`.`lon`,\n"
            ."  `signals`.`type`,\n"
            ."  `signals`.`active`\n"
            ."FROM\n"
            ."  `signals`,\n"
            ."  `logs`\n"
            ."WHERE\n"
            ."  `logs`.`signalID` = `signals`.`ID`\n"
            .($filter_active ? " AND\n `active` = 1\n" : "")
            .($SP         ? "AND\n  `signals`.`SP` = '$SP'\n" : "")
            .($ITU        ? "AND\n  `signals`.`ITU` = '$ITU'\n" : "")
            .($listenerID ? "AND\n  `logs`.`listenerID` = '$listenerID'\n" : "")
            .($filter_type ? " AND\n $filter_type" : "");


        $result =    @Database::query($sql);


        for ($i=0; $i< Database::numRows($result); $i++) {
            $row =  Database::fetchArray($result, MYSQLI_ASSOC);
            $xpos =     (int)($coords['ix2'] -  ((($coords['ix2'] - $coords['ix1'])/($coords['lon2'] - $coords['lon1'])) * ($coords['lon2'] - $row['lon'])));
            $ypos =     (int)($coords['iy1'] + ((($coords['lat2'] - $row['lat']) / ($coords['lat2'] - $coords['lat1'])) * ($coords['iy2']-$coords['iy1'])));
            if ($row['ID']==$ID) {
                ImageString($image, 1, $xpos+4, $ypos+4, (float)$row['khz'], $blue);
                ImageString($image, 1, $xpos+4, $ypos-4, $row['call'], $blue);
            }
            if ($hide_labels!="1" && $row['ID']!=$ID) {
                ImageString($image, 1, $xpos+4, $ypos+4, (float)$row['khz'], $grey);
                ImageString($image, 1, $xpos+4, $ypos-4, $row['call'], $grey);
            }

            if ($row['active']) {
                switch($row['type']) {
                    case DGPS:
                        ImageCopyMerge($image, $img_DGPS, $xpos-4, $ypos-4, 0, 0, 9, 9, 80);
                        break;
                    case HAMBCN:
                        ImageCopyMerge($image, $img_HAM, $xpos-4, $ypos-4, 0, 0, 9, 9, 80);
                        break;
                    case NAVTEX:
                        ImageCopyMerge($image, $img_NAVTEX, $xpos-4, $ypos-4, 0, 0, 9, 9, 80);
                        break;
                    case NDB:
                        ImageCopyMerge($image, $img_NDB, $xpos-4, $ypos-4, 0, 0, 9, 9, 80);
                        break;
                    case TIME:
                        ImageCopyMerge($image, $img_TIME, $xpos-4, $ypos-4, 0, 0, 9, 9, 80);
                        break;
                    case OTHER:
                        ImageCopyMerge($image, $img_OTHER, $xpos-4, $ypos-4, 0, 0, 9, 9, 80);
                        break;
                }
            } else {
                ImageCopyMerge($image, $img_inactive, $xpos-4, $ypos-4, 0, 0, 9, 9, 80);
            }
        }

        header("Content-type: image/gif");
        ImageGIF($image);
        ImageDestroy($image);
        ImageDestroy($img_DGPS);
        ImageDestroy($img_HAM);
        ImageDestroy($img_NAVTEX);
        ImageDestroy($img_NDB);
        ImageDestroy($img_TIME);
        ImageDestroy($img_OTHER);
        ImageDestroy($img_inactive);
        ImageDestroy($img_place);
    }
}