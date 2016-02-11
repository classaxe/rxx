<?php
ini_set('display_errors', 1);
ini_set("mysql.trace_mode", 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', false);

define("DB_HOST", "localhost");
define("DB_USER", "rxx");
define("DB_PASS", "k24l3459");
define("DB_DATABASE", "rxx");

define("NDB", 0);
define("DGPS", 1);
define("TIME", 2);
define("NAVTEX", 3);
define("HAMBCN", 4);
define("OTHER", 5);
define("DSC", 6);

define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'j35g8sc');

// How much signals may be off frequency before being considered wrong
define("swing_LF", 0.6);
// LF is enough to pull signals such as 414 RPB on to correct frequency
define("swing_HF", 1.5);

// Width of a bar of 100% in pixels
define("poll_column_width", 80);
// Height of a results bar in pixels
define("poll_column_height", 14);

define("g_highlight", "#20b020");

define("awardsAdminEmail", "kj8o.ham@gmail.com");
define("awardsAdminName", "Joseph Miller KJ8O");

define("awardsBCCEmail", "Martin@classaxe.com");
define("awardsBCCName", "Martin Francis (Awards copy)");

define("SMTP_HOST",             "mail.classaxe.com");

define("ENABLE_PIWIK", false);

define("READONLY", 0);
