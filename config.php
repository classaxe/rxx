<?php

switch(system){
    case 'RNA':
        define("system_ID", "1");
        define("system_title", "Signals Received in N &amp; C America + Hawaii");
        break;
    case 'REU':
        define("system_ID", "2");
        define("system_title", "Signals Received in Europe");
        break;
    case 'RWW':
        define("system_ID", "3");
        define("system_title", "Signals Received Worldwide");
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

define("SMTP_HOST", "213.219.36.56");
define("ENABLE_PIWIK", false);
