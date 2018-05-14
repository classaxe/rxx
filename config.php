<?php

switch(system){
    case 'RNA':
        define("system_ID", "1");
        define("system_title", "Signals Received in N &amp; C America + Hawaii");
        define(
            "system_editor",
            "<script type='text/javascript'>//<!--\n"
            ."document.write(\""
            ." <a title='Contact the DSC Mode Editor' href='mail\"+\"to\"+\":"
            ."peter\"+\"conway\"+\"@\"+\"talk\"+\"talk.\"+\"net"
            ."?subject=".system."%20System'>"
            ."Peter Conway\"+\"<\/a> (for DSC signals)<br />"
            ."<a title='Contact the NDB / Ham Beacon Editor' href='mail\"+\"to\"+\":"
            ."smoketronics\"+\"@\"+\"comcast\"+\".\"+\"net"
            ."?subject=".system."%20System'>S M O'Kelley\"+\"<\/a> (for NDBs and Ham Beacons)<br />"
            ." <a title='Contact the DGPS and Navtex Modes Editor' href='mail\"+\"to\"+\":"
            ."roelof\"+\"@\"+\"ndb\"+\".\"+\"demon\"+\".\"+\"nl"
            ."?subject=".system."%20System'>"
            ."Roelof Bakker\"+\"<\/a> (for DGPS and Navtex signals)<br />"
            ."\");\n"
            ."//--></script>"
        );
        break;
    case 'REU':
        define("system_ID", "2");
        define("system_title", "Signals Received in Europe");
        define(
            "system_editor",
            "<script type='text/javascript'>//<!--\n"
            ."document.write(\""
            ." <a title='Contact the DSC Mode Editor' href='mail\"+\"to\"+\":"
            ."peter\"+\"conway\"+\"@\"+\"talk\"+\"talk.\"+\"net"
            ."?subject=".system."%20System'>"
            ."Peter Conway\"+\"<\/a> (for DSC signals)<br />"
            ."<a title='Contact the NDB Editor' href='mail\"+\"to\"+\":"
            ."aunumero73\"+\"@\"+\"gmail\"+\".\"+\"com"
            ."?subject=".system."%20System'>Pat Vignoud\"+\"<\/a> (for NDBs)<br />"
            ."<a title='Contact the Ham Beacon Editor' href='mail\"+\"to\"+\":"
            ."smoketronics\"+\"@\"+\"comcast\"+\".\"+\"net"
            ."?subject=".system."%20System'>S M O'Kelley\"+\"<\/a> (for Ham Beacons)<br />"
            ." <a title='Contact the DGPS and Navtex Modes Editor' href='mail\"+\"to\"+\":"
            ."roelof\"+\"@\"+\"ndb\"+\".\"+\"demon\"+\".\"+\"nl"
            ."?subject=".system."%20System'>"
            ."Roelof Bakker\"+\"<\/a> (for DGPS and Navtex signals)<br />"
            ."\");\n"
            ."//--></script>"
        );
        break;
    case 'RWW':
        define("system_ID", "3");
        define("system_title", "Signals Received Worldwide");
        define(
            "system_editor",
            "<script type='text/javascript'>//<!--\n"
            ."document.write(\""
            ." <a title='Contact the DSC Mode Editor' href='mail\"+\"to\"+\":"
            ."peter\"+\"conway\"+\"@\"+\"talk\"+\"talk.\"+\"net"
            ."?subject=".system."%20System'>"
            ."Peter Conway\"+\"<\/a> (for DSC signals)<br />"
            ."<a title='Contact the NDB / Ham Beacon Editor' href='mail\"+\"to\"+\":"
            ."smoketronics\"+\"@\"+\"comcast\"+\".\"+\"net"
            ."?subject=".system."%20System'>S M O'Kelley\"+\"<\/a> (for NDBs and Ham Beacons)<br />"
            ." <a title='Contact the DGPS and Navtex Modes Editor' href='mail\"+\"to\"+\":"
            ."roelof\"+\"@\"+\"ndb\"+\".\"+\"demon\"+\".\"+\"nl"
            ."?subject=".system."%20System'>"
            ."Roelof Bakker\"+\"<\/a> (for DGPS and Navtex signals)<br />"
            ."\");\n"
            ."//--></script>"
        );
        break;
}
define("NDB", 0);
define("DGPS", 1);
define("TIME", 2);
define("NAVTEX", 3);
define("HAMBCN", 4);
define("OTHER", 5);
define("DSC", 6);
define("ALL", 99);

define("swing_LF", 0.6);    // How much signals may be off frequency before being considered wrong
define("swing_HF", 1.5);    // LF is enough to pull signals such as 414 RPB on to correct frequency

define("poll_column_width", 80);     // Width of a bar of 100% in pixels
define("poll_column_height", 14);     // Height of a results bar in pixels

define("g_highlight", "#20b020");

define("awardsAdminEmail", "kj8o.ham@gmail.com");
define("awardsAdminName", "Joseph Miller KJ8O");

define("awardsBCCEmail", "Martin@classaxe.com");
define("awardsBCCName", "Martin Francis (Awards copy)");

define("SMTP_HOST", "mail.classaxe.com");
define("ENABLE_PIWIK", false);
