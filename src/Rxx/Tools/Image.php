<?php
/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 9:24 PM
 */
// Version 1.0.3
/*
1.0.3 (2013-03-19)
  1) generate_station_map() now accepts systemID and ID as URL path
1.0.2 (2009-08-24)
  1) Bit of a tidy up for SQL statements
1.0.1 (2008-02-26)
  1) Changes to generate_station_map() to replace &auml;
1.0.0 Initial
*/


// *******************************************
// * FILE HEADER:                            *
// *******************************************
// * Project:   RNA / REU / RWW              *
// * Filename:  img.php                      *
// *                                         *
// * Created:   21/08/2004 (MF)              *
// * Revised:   25/06/2005 (MF)              *
// * Email:     martin@classaxe.com          *
// *******************************************

namespace Rxx\Tools;

/**
 * Class Image
 * @package Rxx\Tools
 */
class Image
{
    /**
     *
     */
    public static function generate_station_map()
    {
        global $mode;
        $path_arr =   (explode('?', $_SERVER["REQUEST_URI"]));
        $path_arr =   explode('/', $path_arr[0]);
        $ID =         array_pop($path_arr);
        $system_ID =  array_pop($path_arr);
        switch ($system_ID) {
            case "1":   $region= "(`listeners`.`region`='na' OR `listeners`.`region`='ca' OR `listeners`.`itu` = 'HWA')";
                break;
            case "2":   $region="`listeners`.`region`='eu'";
                break;
        }
        $out =  array();
        $sql =
            "SELECT DISTINCT\n"
            ."  `sp`,\n"
            ."  `itu`\n"
            ."FROM\n"
            ."  `listeners`\n"
            ."WHERE\n"
            ."  `count_logs` !=0 AND\n"
            ."  $region";
//  z($sql);
        $result =   \Rxx\Database::query($sql);
        $reporters_in =     array();
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result);
            $reporters_in[] =   ($row['sp'] ? $row['sp'] : $row['itu']);
        }
        $reporters_in = (implode($reporters_in, ","));

        $sql =
            "SELECT\n"
            ."  `call`,\n"
            ."  `heard_in`,\n"
            ."  `itu`,\n"
            ."  `khz`,\n"
            ."  `sp`\n"
            ."FROM\n"
            ."  `signals`\n"
            ."WHERE\n"
            ."  `ID` = ".$ID;
//  z($sql);
        $result =   \Rxx\Database::query($sql);
        $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
        $heard_in =     str_replace(" ", ",", $row['heard_in']);
        $text =     (float)$row['khz']."-".$row['call'];
        $based_in =     ($row['sp'] ? $row['sp'] : $row['itu']);

        $sql =
            "SELECT\n"
            ."  `map_x`,\n"
            ."  `map_y`,\n"
            ."  `primary_QTH`\n"
            ."FROM\n"
            ."  `listeners`\n"
            ."WHERE\n"
            ."  ".$region;
//  z($sql);
        $result =   @\Rxx\Database::query($sql);
        $reporters =    array();
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
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
            ."  `listeners`\n"
            ."INNER JOIN `logs` ON\n"
            ."  `listeners`.`ID` = `logs`.`listenerID`\n"
            ."WHERE\n"
            ."  `logs`.`signalID` = ".$ID." AND\n"
            ."  `listeners`.`map_x`!=0 AND\n"
            ."  ".$region."\n"
            ."GROUP BY\n"
            ."  `listeners`.`ID`\n"
            ."ORDER BY\n"
            ."  `heard_in`,\n"
            ."  `name`";
//  z($sql);

        $result =   \Rxx\Database::query($sql);
        $reporter_rxed =    array();
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
            if ($row['map_x']) {
                $name = str_replace(array('&aelig;','&ouml;','&auml;'), array('ae','o','a'), $row['name']);
                if (strlen($name) > 20) {
                    $name = substr($name, 0, 17)."...";
                }
                $reporter_rxed[] =  $row['map_x'].",".$row['map_y'].",".$row['daytime'].",".$row['primary_QTH'].",".\Rxx\Rxx::pad($row['heard_in'], 3)." ".\Rxx\Rxx::pad($name, 20).\Rxx\Rxx::lead($row['dx_miles'], 4);
            }
        }
        $reporter_rxed =    implode($reporter_rxed, "|");

        switch($system_ID) {
            case 1:
                Image::draw_station_map_na($based_in, $reporters_in, $reporters, $heard_in, $reporter_rxed, $text);
                break;
            case 2:
                Image::draw_station_map_eu($based_in, $reporters_in, $reporters, $heard_in, $reporter_rxed, $text);
                break;
        }
    }

    /**
     *
     */
    public static function generate_listener_map()
    {
        global $mode;
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

        $out =  array();
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
        $result =   @\Rxx\Database::query($sql);
        $reporters_in =     array();
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result);
            $reporters_in[] =   ($row['sp'] ? $row['sp'] : $row['itu']);
        }
        $reporters_in = (implode($reporters_in, ","));

        $sql =
            "SELECT\n"
            ."  `map_x`,\n"
            ."  `map_y`,\n"
            ."  `primary_QTH`\n"
            ."FROM\n"
            ."  `listeners`\n"
            ."WHERE\n"
            ."  ".$region;
        $result =   @\Rxx\Database::query($sql);
        $reporters =    array();
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
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
//  z($sql);die;
        $result =   \Rxx\Database::query($sql);
        $reporter_rxed =    array();
        for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
            $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
            if ($row['map_x']) {
                $name = str_replace(array('&aelig;','&ouml;'), array('ae','o'), $row['name']);
                if (strlen($name) > 20) {
                    $name = substr($name, 0, 17)."...";
                }
                $reporter_rxed[] =  $row['map_x'].",".$row['map_y'].",,".$row['primary_QTH'].",".Rxx::pad($row['heard_in'], 3)." ".Rxx::pad($name, 20);
            }
        }

        switch($system_ID) {
            case 1:
                Image::draw_station_map_na("", "", $reporters, $reporters_in, "", "");
                break;
            case 2:
                Image::draw_station_map_eu("", "", $reporters, $reporters_in, "", "");
                break;
        }
    }

    /**
     *
     */
    public static function generate_map_eu()
    {
        $image =        imageCreate(688, 665);
        $image_map =        imageCreateFromGif("assets/eu_map_outline.gif");

        $col_background =   ImageColorAllocate($image, 18, 52, 86);
        ImageCopyMerge($image, $image_map, 0, 0, 0, 0, 688, 665, 100);

        $codes =        imageCreateFromGif("assets/eu_map_codes.gif");
        $black =        ImageColorAllocate($image, 0, 0, 0);
        $white =        ImageColorAllocate($image, 255, 255, 255);
        $blue =         ImageColorAllocate($image, 120, 120, 255);
        $gray =         ImageColorAllocate($image, 150, 150, 150);
        $darkgray =         ImageColorAllocate($image, 120, 120, 120);

        $map_1 =        ImageColorAllocate($image, 230, 255, 230);
        $map_2 =        ImageColorAllocate($image, 230, 230, 255);
        $map_3 =        ImageColorAllocate($image, 255, 255, 192);
        $map_4 =        ImageColorAllocate($image, 255, 201, 201);
        $map_5 =        ImageColorAllocate($image, 255, 220, 255);
        $map_6 =        ImageColorAllocate($image, 255, 223, 150);
        $map_7 =        ImageColorAllocate($image, 209, 250, 255);
        $map_sea =      ImageColorAllocate($image, 245, 245, 255);


        Image::draw_fill_country_list_eu("ENG,POR,DEU,ALB,HNG,TUR,LVA,ISL", $image, $map_1);
        Image::draw_fill_country_list_eu("IRL,ESP,SUI,SVK,SMR,NOR", $image, $map_2);
        Image::draw_fill_country_list_eu("WLS,GSY,AND,ITA,CZE,SER,MDA,RUS", $image, $map_3);
        Image::draw_fill_country_list_eu("IOM,FRA,DNK,SVN,CVA,BUL,LTU", $image, $map_4);
        Image::draw_fill_country_list_eu("SHE,JSY,BEL,COR,AUT,BIH,KAL,GRC,UKR,SWE", $image, $map_5);
        Image::draw_fill_country_list_eu("SCT,LUX,SAR,HRV,MKD,BLR,GEO,FIN", $image, $map_6);
        Image::draw_fill_country_list_eu("GSY,ORK,NIR,HOL,BAL,SCY,LIE,POL,MNE,ROU,EST,FRO", $image, $map_7);

        Image::ImageRectangleWithRoundedCorners($image, 3, 5, 300, 56, 5, $darkgray, $white);
        ImageString($image, 5, 10, 10, "NDBList State and Country codes", $black);
        ImageString($image, 2, 10, 27, "Please use these when reporting.", $black);
        ImageString($image, 2, 10, 40, "Contact: mapmaster@beaconworld.org.uk", $black);


        ImageCopyMerge($image, $codes, 0, 0, 0, 0, 653, 665, 30);
        ImageColorTransparent($image, $col_background);

        ImageGif($image);
        ImageDestroy($image);
        ImageDestroy($image_map);
        ImageDestroy($codes);
    }

    /**
     *
     */
    public static function generate_map_na()
    {
        $image =        imageCreate(653, 620);
        $image_map =        imageCreateFromGif("assets/na_map_outline.gif");
        $col_background =   ImageColorAllocate($image, 18, 52, 86);
        ImageCopyMerge($image, $image_map, 0, 0, 0, 0, 653, 620, 100);

        $codes =        imageCreateFromGif("assets/na_map_codes.gif");

        $black =        ImageColorAllocate($image, 0, 0, 0);
        $white =        ImageColorAllocate($image, 255, 255, 255);
        $blue =         ImageColorAllocate($image, 120, 120, 255);
        $gray =         ImageColorAllocate($image, 150, 150, 150);
        $darkgray =         ImageColorAllocate($image, 120, 120, 120);

        $map_1 =        ImageColorAllocate($image, 230, 255, 230);
        $map_2 =        ImageColorAllocate($image, 230, 230, 255);
        $map_3 =        ImageColorAllocate($image, 255, 255, 192);
        $map_4 =        ImageColorAllocate($image, 255, 201, 201);
        $map_5 =        ImageColorAllocate($image, 255, 220, 255);
        $map_6 =        ImageColorAllocate($image, 255, 223, 150);
        $map_7 =        ImageColorAllocate($image, 209, 250, 255);
        $map_sea =      ImageColorAllocate($image, 250, 250, 255);

        Image::draw_fill_country_list_na("CA,WA,TX,NM,VA,NE,NC,SC,MN,IL,FL,AZ,MO,OR,MI,CO,IN,HI,AK,AL,MS,PA,NY,TN,MD,WV,ID,NV,UT,MT,WY,ND,SD,KS,OK,AR,LA,GA,IA,KY,OH,DC,DE,NJ,RI,CT,MA,VT,NH,ME,WI,PTR", $image, $map_1);
        Image::draw_fill_country_list_na("YT,BC,NT,AB,SK,NU,MB,ON,QC,NL,NB,PE,NS", $image, $map_2);
        Image::draw_fill_country_list_na("GRL,MEX,JMC,SVD,PNR", $image, $map_3);
        Image::draw_fill_country_list_na("BAH,GTM,DOM", $image, $map_4);
        Image::draw_fill_country_list_na("BLZ,CTR,BER,HTI,VIR", $image, $map_5);
        Image::draw_fill_country_list_na("CUB,NCG,VRG", $image, $map_6);
        Image::draw_fill_country_list_na("HND,PTR", $image, $map_7);

        Image::ImageRectangleWithRoundedCorners($image, 3, 565, 300, 616, 5, $darkgray, $white);
        ImageString($image, 5, 10, 570, "NDBList State and Country codes", $black);
        ImageString($image, 2, 10, 587, "Please use these when reporting.", $black);
        ImageString($image, 2, 10, 600, "Contact: mapmaster@beaconworld.org.uk", $black);

        ImageCopyMerge($image, $codes, 0, 0, 0, 0, 653, 620, 30);
        ImageColorTransparent($image, $col_background);

        ImageGif($image);
        ImageDestroy($image);
        ImageDestroy($image_map);
        ImageDestroy($codes);
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
        global $map;
        if ($reporter_rxed) {
            $image =        imageCreate(860, 665);
        } else {
            $image =        imageCreate(688, 665);
        }
        $image_map =        imageCreateFromGif("assets/eu_map_outline.gif");

        $col_background =   ImageColorAllocate($image, 18, 52, 86);
        ImageCopyMerge($image, $image_map, 0, 0, 0, 0, 688, 665, 100);

        $codes =        imageCreateFromGif("assets/eu_map_codes.gif");
        $img_point1 =       imageCreateFromGif("assets/map_point1.gif");
        $img_point2 =       imageCreateFromGif("assets/map_point2.gif");
        $img_point3 =       imageCreateFromGif("assets/map_point3.gif");
        $img_point4 =       imageCreateFromGif("assets/map_point4.gif");


        $col_heard_no =     ImageColorAllocate($image, 255, 210, 210);
        $col_heard_yes =    ImageColorAllocate($image, 230, 255, 230);
        $black =        ImageColorAllocate($image, 0, 0, 0);
        $white =        ImageColorAllocate($image, 255, 255, 255);
        $blue =         ImageColorAllocate($image, 150, 220, 255);


        $map_sea =      ImageColorAllocate($image, 222, 222, 255);
        $map_1 =        ImageColorAllocate($image, 230, 255, 230);
        $map_2 =        ImageColorAllocate($image, 230, 230, 255);
        $map_3 =        ImageColorAllocate($image, 255, 255, 192);
        $map_4 =        ImageColorAllocate($image, 255, 201, 201);
        $map_5 =        ImageColorAllocate($image, 255, 220, 255);
        $map_6 =        ImageColorAllocate($image, 255, 223, 150);
        $map_7 =        ImageColorAllocate($image, 209, 250, 255);

        if (isset($heard_in)) {
            Image::draw_fill_country_eu("sea", $image, $map_sea);
            Image::draw_fill_country_list_eu($reporters_in, $image, $col_heard_no);
        }
        if (isset($heard_in)) {
            Image::draw_fill_country_list_eu(strtoupper($heard_in), $image, $col_heard_yes);
        }
        if (isset($based_in)) {
            Image::draw_fill_country_list_eu(strtoupper($based_in), $image, $blue);
        }
        if (isset($text)&&$text!="") {
            Image::draw_map_legend($image, $text, $blue, $col_heard_yes, $col_heard_no, 3, 3, $img_point1, $img_point2, $img_point3, $img_point4);
        }
        if (isset($reporter_o)) {
            ImageCopyMerge($image, $img_reporter_o, 0, 0, 0, 0, 653, 620, 100);
        }

        if (isset($reporters)) {
            Image::draw_map_point_list($reporters, $image, $img_point3, $img_point4);
        }

        if (isset($reporter_rxed)) {
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
        $image =        imageCreate(653, 620);
        $image_map =        imageCreateFromGif("assets/na_map_outline.gif");

        $col_background =   ImageColorAllocate($image, 18, 52, 86);
        ImageCopyMerge($image, $image_map, 0, 0, 0, 0, 653, 620, 100);

        $codes =        imageCreateFromGif("assets/na_map_codes.gif");
        $img_point1 =       imageCreateFromGif("assets/map_point1.gif");
        $img_point2 =       imageCreateFromGif("assets/map_point2.gif");
        $img_point3 =       imageCreateFromGif("assets/map_point3.gif");
        $img_point4 =       imageCreateFromGif("assets/map_point4.gif");

        $col_heard_no =     ImageColorAllocate($image, 255, 210, 210);
        $col_heard_yes =    ImageColorAllocate($image, 230, 255, 230);
        $black =        ImageColorAllocate($image, 0, 0, 0);
        $white =        ImageColorAllocate($image, 255, 255, 255);
        $blue =         ImageColorAllocate($image, 150, 220, 255);

        $map_sea =      ImageColorAllocate($image, 222, 222, 255);

        if ($heard_in) {
            Image::draw_fill_country_na("sea", $image, $map_sea);
            Image::draw_fill_country_list_na($reporters_in, $image, $col_heard_no);
        }
        if ($heard_in) {
            Image::draw_fill_country_list_na(strtoupper($heard_in), $image, $col_heard_yes);
        }
        if ($based_in) {
            Image::draw_fill_country_list_na(strtoupper($based_in), $image, $blue);
        }
        if ($text) {
            Image::draw_map_legend($image, $text, $blue, $col_heard_yes, $col_heard_no, 520, 535, $img_point1, $img_point2, $img_point3, $img_point4);
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
        $arr = explode(",", $list);
        for ($i=0; $i<count($arr); $i++) {
            Image::draw_fill_country_eu($arr[$i], $image, $color);
        }
    }

    /**
     * @param $list
     * @param $image
     * @param $color
     */
    public static function draw_fill_country_list_na($list, &$image, $color)
    {
        $arr = explode(",", $list);
        for ($i=0; $i<count($arr); $i++) {
            Image::draw_fill_country_na($arr[$i], $image, $color);
        }
    }

    /**
     * @param $country
     * @param $image
     * @param $color
     */
    public static function draw_fill_country_eu($country, &$image, $color)
    {
        switch ($country) {
            case "AND":
                ImageFill($image, 163, 560, $color);
                break;
            case "ALB":
                ImageFill($image, 360, 575, $color);
                break;
            case "AUT":
                ImageFill($image, 300, 490, $color);
                break;
            case "BAL":
                ImageFill($image, 165, 606, $color);
                ImageFill($image, 178, 597, $color);
                ImageFill($image, 189, 595, $color);
                break;
            case "BEL":
                ImageFill($image, 197, 441, $color);
                break;
            case "BIH":
                ImageFill($image, 335, 540, $color);
                break;
            case "BLR":
                ImageFill($image, 440, 390, $color);
                break;
            case "BUL":
                ImageFill($image, 410, 560, $color);
                break;
            case "COR":
                ImageFill($image, 240, 565, $color);
                break;
            case "CVA":
                ImageFill($image, 289, 574, $color);
                break;
            case "CZE":
                ImageFill($image, 310, 450, $color);
                break;
            case "DNK":
                ImageFill($image, 248, 344, $color);
                ImageFill($image, 257, 364, $color);
                ImageFill($image, 261, 370, $color);
                ImageFill($image, 271, 360, $color);
                ImageFill($image, 268, 373, $color);
                ImageFill($image, 298, 372, $color);
                break;
            case "DEU":
                ImageFill($image, 240, 434, $color);
                break;
            case "ENG":
                ImageFill($image, 133, 401, $color);
                ImageFill($image, 132, 440, $color);
                break;
            case "ESP":
                ImageFill($image, 107, 588, $color);
                break;
            case "EST":
                ImageFill($image, 415, 295, $color);
                ImageFill($image, 386, 296, $color);
                ImageFill($image, 384, 306, $color);
                break;
            case "FIN":
                ImageFill($image, 420, 210, $color);
                ImageFill($image, 360, 267, $color);
                ImageFill($image, 367, 272, $color);
                ImageFill($image, 369, 279, $color);
                break;
            case "FRA":
                ImageFill($image, 170, 515, $color);
                break;
            case "FRO":
                ImageFill($image, 67, 206, $color);
                ImageFill($image, 70, 202, $color);
                break;
            case "GSY":
                ImageFill($image, 118, 459, $color);
                break;
            case "GIB":
                ImageFill($image, 90, 642, $color);
                break;
            case "GEO":
                ImageFill($image, 605, 565, $color);
                break;
            case "GRC":
                ImageFill($image, 375, 600, $color);
                ImageFill($image, 400, 613, $color);
                ImageFill($image, 410, 656, $color);
                ImageFill($image, 425, 604, $color);
                ImageFill($image, 410, 624, $color);
                ImageFill($image, 422, 614, $color);
                ImageFill($image, 443, 643, $color);
                break;
            case "HNG":
                ImageFill($image, 350, 500, $color);
                break;
            case "HOL":
                ImageFill($image, 205, 420, $color);
                break;
            case "HRV":
                ImageFill($image, 320, 515, $color);
                break;
            case "IOM":
                ImageFill($image, 94, 382, $color);
                break;
            case "IRL":
                ImageFill($image, 55, 400, $color);
                break;
            case "ISL":
                ImageFill($image, 39, 118, $color);
                break;
            case "ITA":
                ImageFill($image, 270, 545, $color);
                break;
            case "JSY":
                ImageFill($image, 124, 463, $color);
                break;
            case "KAL":
                ImageFill($image, 375, 375, $color);
                break;
            case "LIE":
                ImageFill($image, 249, 493, $color);
                break;
            case "LTU":
                ImageFill($image, 400, 360, $color);
                break;
            case "LUX":
                ImageFill($image, 210, 455, $color);
                break;
            case "LVA":
                ImageFill($image, 415, 330, $color);
                break;
            case "MCO":
                ImageFill($image, 218, 547, $color);
                break;
            case "MDA":
                ImageFill($image, 450, 490, $color);
                break;
            case "MKD":
                ImageFill($image, 377, 573, $color);
                break;
            case "MLT":
                ImageFill($image, 300, 645, $color);
                break;
            case "MNE":
                ImageFill($image, 350, 555, $color);
                break;
            case "NIR":
                ImageFill($image, 65, 375, $color);
                break;
            case "NOR":
                ImageFill($image, 245, 240, $color);
                break;
            case "ORK":
                ImageFill($image, 109, 305, $color);
                break;
            case "POL":
                ImageFill($image, 355, 425, $color);
                break;
            case "POR":
                ImageFill($image, 66, 594, $color);
                break;
            case "ROU":
                ImageFill($image, 405, 510, $color);
                break;
            case "RUS":
                ImageFill($image, 570, 300, $color);
                break;
            case "SAR":
                ImageFill($image, 242, 593, $color);
                break;
            case "SCT":
                ImageFill($image, 68, 319, $color);
                ImageFill($image, 61, 328, $color);
                ImageFill($image, 73, 331, $color);
                ImageFill($image, 76, 347, $color);
                ImageFill($image, 74, 358, $color);
                ImageFill($image, 69, 359, $color);
                ImageFill($image, 83, 355, $color);
                ImageFill($image, 86, 361, $color);
                ImageFill($image, 100, 340, $color);
                ImageFill($image, 61, 334, $color);
                ImageFill($image, 60, 339, $color);
                break;
            case "SCY":
                ImageFill($image, 300, 625, $color);
                break;
            case "SER":
                ImageFill($image, 370, 540, $color);
                break;
            case "SHE":
                ImageFill($image, 134, 268, $color);
                ImageFill($image, 134, 273, $color);
                ImageFill($image, 129, 278, $color);
                break;
            case "SMR":
                ImageFill($image, 286, 550, $color);
                break;
            case "SUI":
                ImageFill($image, 230, 495, $color);
                break;
            case "SVK":
                ImageFill($image, 350, 470, $color);
                break;
            case "SVN":
                ImageFill($image, 300, 510, $color);
                break;
            case "SWE":
                ImageFill($image, 320, 185, $color);
                ImageFill($image, 323, 336, $color);
                ImageFill($image, 338, 324, $color);
                break;
            case "TUR":
                ImageFill($image, 434, 576, $color);
                ImageFill($image, 519, 606, $color);
                break;
            case "UKR":
                ImageFill($image, 480, 465, $color);
                break;
            case "WLS":
                ImageFill($image, 97, 397, $color);
                ImageFill($image, 105, 410, $color);
                break;
            case "sea":
                ImageFill($image, 600, 40, $color);
                ImageFill($image, 100, 100, $color);
                ImageFill($image, 448, 585, $color);
                ImageFill($image, 500, 550, $color);
                break;
        }
    }

    /**
     * @param $country
     * @param $image
     * @param $color
     */
    public static function draw_fill_country_na($country, &$image, $color)
    {
        switch ($country) {
            case "AB":
                ImageFill($image, 260, 180, $color);
                break;
            case "AK":
            case "ALS":
                ImageFill($image, 125, 100, $color);
                ImageFill($image, 96, 144, $color);
                break;
            case "AL":
                ImageFill($image, 415, 360, $color);
                break;
            case "AR":
                ImageFill($image, 375, 340, $color);
                break;
            case "AZ":
                ImageFill($image, 245, 345, $color);
                break;
            case "BAH":
                ImageFill($image, 475, 415, $color);
                ImageFill($image, 482, 418, $color);
                ImageFill($image, 477, 433, $color);
                ImageFill($image, 481, 429, $color);
                ImageFill($image, 489, 430, $color);
                ImageFill($image, 494, 435, $color);
                ImageFill($image, 503, 438, $color);
                ImageFill($image, 498, 446, $color);
                ImageFill($image, 506, 451, $color);
                ImageFill($image, 514, 453, $color);
                ImageFill($image, 513, 464, $color);
                ImageFill($image, 521, 457, $color);
                break;
            case "BC":
                ImageFill($image, 200, 190, $color);
                ImageFill($image, 190, 220, $color);
                ImageFill($image, 163, 189, $color);
                break;
            case "BER":
                ImageFill($image, 563, 370, $color);
                break;
            case "BLZ":
                ImageFill($image, 402, 499, $color);
                break;
            case "CA":
                ImageFill($image, 200, 330, $color);
                break;
            case "CO":
                ImageFill($image, 290, 310, $color);
                break;
            case "CT":
                ImageFill($image, 500, 285, $color);
                break;
            case "CTR":
                ImageFill($image, 435, 565, $color);
                break;
            case "CUB":
                ImageFill($image, 473, 457, $color);
                ImageFill($image, 443, 460, $color);
                break;
            case "DC":
                ImageFill($image, 475, 308, $color);
                break;
            case "DE":
                ImageFill($image, 486, 311, $color);
                break;
            case "DOM":
                ImageFill($image, 535, 485, $color);
                break;
            case "FL":
                ImageFill($image, 450, 400, $color);
                ImageFill($image, 452, 433, $color);
                break;
            case "GA":
                ImageFill($image, 435, 365, $color);
                break;
            case "GRL":
                ImageFill($image, 545, 45, $color);
                break;
            case "GTM":
                ImageFill($image, 390, 520, $color);
                break;
            case "HND":
                ImageFill($image, 415, 520, $color);
                break;
            case "HI":
            case "HWA":
                ImageFill($image, 36, 29, $color);
                ImageFill($image, 46, 32, $color);
                ImageFill($image, 55, 37, $color);
                ImageFill($image, 65, 47, $color);
                break;
            case "HTI":
                ImageFill($image, 520, 480, $color);
                break;
            case "IA":
                ImageFill($image, 370, 280, $color);
                break;
            case "ID":
                ImageFill($image, 240, 265, $color);
                break;
            case "IN":
                ImageFill($image, 415, 300, $color);
                break;
            case "IL":
                ImageFill($image, 400, 300, $color);
                break;
            case "JMC":
                ImageFill($image, 485, 490, $color);
                break;
            case "KS":
                ImageFill($image, 340, 315, $color);
                break;
            case "KY":
                ImageFill($image, 420, 320, $color);
                break;
            case "LA":
                ImageFill($image, 375, 370, $color);
                break;
            case "MA":
                ImageFill($image, 505, 280, $color);
                break;
            case "MB":
                ImageFill($image, 350, 180, $color);
                break;
            case "MD":
                ImageFill($image, 477, 304, $color);
                break;
            case "ME":
                ImageFill($image, 515, 255, $color);
                break;
            case "MEX":
                ImageFill($image, 320, 450, $color);
                break;
            case "MI":
                ImageFill($image, 405, 250, $color);
                ImageFill($image, 425, 275, $color);
                break;
            case "MN":
                ImageFill($image, 365, 250, $color);
                break;
            case "MO":
                ImageFill($image, 375, 315, $color);
                break;
            case "MS":
                ImageFill($image, 395, 360, $color);
                break;
            case "MT":
                ImageFill($image, 275, 240, $color);
                break;
            case "NB":
                ImageFill($image, 535, 245, $color);
                break;
            case "NC":
                ImageFill($image, 465, 335, $color);
                break;
            case "NCG":
                ImageFill($image, 425, 540, $color);
                break;
            case "ND":
                ImageFill($image, 330, 240, $color);
                break;
            case "NE":
                ImageFill($image, 330, 290, $color);
                break;
            case "NH":
                ImageFill($image, 505, 270, $color);
                break;
            case "NJ":
                ImageFill($image, 490, 300, $color);
                break;
            case "NL":
                ImageFill($image, 545, 195, $color);
                ImageFill($image, 590, 230, $color);
                ImageFill($image, 608, 240, $color);
                break;
            case "NM":
                ImageFill($image, 285, 345, $color);
                break;
            case "NS":
                ImageFill($image, 555, 255, $color);
                break;
            case "NT":
                ImageFill($image, 250, 120, $color);
                ImageFill($image, 280, 55, $color);
                ImageFill($image, 305, 65, $color);
                ImageFill($image, 320, 40, $color);
                ImageFill($image, 310, 35, $color);
                ImageFill($image, 330, 32, $color);
                break;
            case "NU":
                ImageFill($image, 350, 115, $color);
                ImageFill($image, 415, 112, $color);
                ImageFill($image, 325, 75, $color);
                ImageFill($image, 380, 55, $color);
                ImageFill($image, 330, 45, $color);
                ImageFill($image, 360, 60, $color);
                ImageFill($image, 425, 125, $color);
                ImageFill($image, 440, 130, $color);
                ImageFill($image, 347, 45, $color);
                ImageFill($image, 450, 90, $color);
                ImageFill($image, 410, 45, $color);
                ImageFill($image, 417, 23, $color);
                ImageFill($image, 377, 28, $color);
                ImageFill($image, 378, 37, $color);
                ImageFill($image, 362, 26, $color);
                ImageFill($image, 364, 41, $color);
                ImageFill($image, 377, 45, $color);
                ImageFill($image, 445, 168, $color);
                ImageFill($image, 448, 164, $color);
                ImageFill($image, 446, 174, $color);
                ImageFill($image, 436, 82, $color);
                ImageFill($image, 447, 120, $color);
                ImageFill($image, 510, 132, $color);
                ImageFill($image, 349, 33, $color);
                ImageFill($image, 352, 40, $color);
                ImageFill($image, 371, 20, $color);
                break;
            case "NV":
                ImageFill($image, 220, 305, $color);
                break;
            case "NY":
                ImageFill($image, 480, 275, $color);
                ImageFill($image, 502, 293, $color);
                break;
            case "OH":
                ImageFill($image, 440, 300, $color);
                break;
            case "OK":
                ImageFill($image, 340, 340, $color);
                break;
            case "ON":
                ImageFill($image, 420, 215, $color);
                break;
            case "OR":
                ImageFill($image, 210, 270, $color);
                break;
            case "PA":
                ImageFill($image, 470, 295, $color);
                break;
            case "PE":
                ImageFill($image, 552, 247, $color);
                break;
            case "PNR":
                ImageFill($image, 460, 580, $color);
                break;
            case "PR":
            case "PTR":
                ImageFill($image, 565, 490, $color);
                break;
            case "QC":
                ImageFill($image, 480, 200, $color);
                ImageFill($image, 547, 222, $color);
                break;
            case "RI":
                ImageFill($image, 508, 285, $color);
                break;
            case "SC":
                ImageFill($image, 455, 350, $color);
                break;
            case "SD":
                ImageFill($image, 330, 265, $color);
                break;
            case "SK":
                ImageFill($image, 310, 180, $color);
                break;
            case "SVD":
                ImageFill($image, 400, 533, $color);
                break;
            case "TN":
                ImageFill($image, 415, 335, $color);
                break;
            case "TX":
                ImageFill($image, 330, 370, $color);
                break;
            case "UT":
                ImageFill($image, 254, 306, $color);
                break;
            case "VA":
                ImageFill($image, 465, 320, $color);
                ImageFill($image, 486, 319, $color);
                break;
            case "VIR":
                ImageFill($image, 573, 495, $color);
                break;
            case "VRG":
                ImageFill($image, 577, 490, $color);
                break;
            case "VT":
                ImageFill($image, 500, 265, $color);
                break;
            case "WA":
                ImageFill($image, 215, 238, $color);
                break;
            case "WI":
                ImageFill($image, 395, 260, $color);
                break;
            case "WV":
                ImageFill($image, 450, 310, $color);
                break;
            case "WY":
                ImageFill($image, 280, 270, $color);
                break;
            case "YT":
                ImageFill($image, 190, 120, $color);
                break;
            case "sea":
                ImageFill($image, 10, 10, $color);
                ImageFill($image, 40, 50, $color);
                ImageFill($image, 412, 240, $color);
                ImageFill($image, 412, 263, $color);
                ImageFill($image, 437, 255, $color);
                ImageFill($image, 447, 282, $color);
                ImageFill($image, 469, 269, $color);
                break;
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
        $blue =     ImageColorAllocate($image, 120, 120, 255);
        $gray =     ImageColorAllocate($image, 150, 150, 150);
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
        $darkgreen =        ImageColorAllocate($image, 0, 40, 0);
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
        global  $SP, $ITU, $listenerID, $simple, $test, $lat, $lon, $filter_active, $hide_labels, $hide_placenames;
        global  $type_NDB, $type_TIME, $type_DGPS, $type_NAVTEX, $type_HAMBCN, $type_OTHER, $places, $ID;
        $SP =       strToLower($SP);

        $sql =  "SELECT * FROM `maps` WHERE `SP` = '$SP'";
        $result =   \Rxx\Database::query($sql);
        if (! \Rxx\Database::numRows($result)) {
            return;
        }
        $coords =   \Rxx\Database::fetchArray($result, MYSQL_ASSOC);

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

        if ($test != 1) {
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
                $result =   @\Rxx\Database::query($sql);
                for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
                    $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
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


            $result =    @\Rxx\Database::query($sql);


            for ($i=0; $i<\Rxx\Database::numRows($result); $i++) {
                $row =  \Rxx\Database::fetchArray($result, MYSQL_ASSOC);
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

        } else {
            //Test Mode: show corners and optional test location
            ImageCopyMerge($image, $img_point, $coords['ix1']-4, $coords['iy1']-4, 0, 0, 9, 9, 100);
            ImageString($image, 1, $coords['ix1']+10, $coords['iy1']-5, (float)$coords['lon1'].",".(float)$coords['lat2'], $red);
            ImageString($image, 1, $coords['ix1']+10, $coords['iy1']+5, (float)$coords['ix1'].",".(float)$coords['iy1'], $red);

            ImageCopyMerge($image, $img_point, $coords['ix1']-4, $coords['iy2']-4, 0, 0, 9, 9, 100);
            ImageString($image, 1, $coords['ix1']+10, $coords['iy2']-5, (float)$coords['lon1'].",".(float)$coords['lat1'], $red);
            ImageString($image, 1, $coords['ix1']+10, $coords['iy2']+5, (float)$coords['ix1'].",".(float)$coords['iy2'], $red);

            ImageCopyMerge($image, $img_point, $coords['ix2']-4, $coords['iy1']-4, 0, 0, 9, 9, 100);
            ImageString($image, 1, $coords['ix2']-30, $coords['iy1']-10, (float)$coords['lon2'].",".(float)$coords['lat2'], $red);
            ImageString($image, 1, $coords['ix2']-30, $coords['iy1']+10, (float)$coords['ix2'].",".(float)$coords['iy1'], $red);

            ImageCopyMerge($image, $img_point, $coords['ix2']-4, $coords['iy2']-4, 0, 0, 9, 9, 100);
            ImageString($image, 1, $coords['ix2']-30, $coords['iy2']-10, (float)$coords['lon2'].",".(float)$coords['lat1'], $red);
            ImageString($image, 1, $coords['ix2']-30, $coords['iy2']+10, (float)$coords['ix2'].",".(float)$coords['iy2'], $red);

            $xpos =     $coords['ix2'] -  ((($coords['ix2'] - $coords['ix1'])/($coords['lon2'] - $coords['lon1'])) * ($coords['lon2'] - $lon));
            $ypos =     $coords['iy1'] + ((($coords['lat2'] - $lat) / ($coords['lat2'] - $coords['lat1'])) * ($coords['iy2']-$coords['iy1']));
            ImageCopyMerge($image, $img_point, $xpos-4, $ypos-4, 0, 0, 9, 9, 100);
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
