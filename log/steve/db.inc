<?php

function connect_db()
{
    global $config_db_host, $config_db_name, $config_db_pass, $config_db_user;
    define("DATABASE_TYPE", DATABASE_MYSQL);  // define ("DATABASE_TYPE", DATABASE_MSQL);

    $header =  "<body>\n"
        ."<p><font face='Verdana, Arial'><b>Status:</b><br>\n";

    @$ObjMysql= new MySQLi($config_db_host, $config_db_user, $config_db_pass);

    if (!($ObjMysql && @$ObjMysql->select_db($config_db_name))) {
        die(
            "<html><head><title>Database Error</title></head>\n"
            .$header
            ."System account <b>$config_db_user</b>@<b>$config_db_host</b> cannot connect to <b>$config_db_name</b>.</b><br>\n"
            ."<p><b>Next Steps:</b><br>\n"
            ."<ol>\n"
            ."<li>Check the database settings and make corrections if required.</li>\n"
            ."<li>If those settings are correct, and the database <b>$config_db_name</b> does not exist,<br>\n"
            ."create it using this SQL command (as a user with CREATE rights):</li>\n"
            ."<pre><b>CREATE DATABASE</b> `$config_db_name`;</pre></li>\n"
            ."<li>If <b>$config_db_user</b>@<b>$config_db_host</b> does not have access to <b>$config_db_name</b>,<br>\n"
            ."grant it using these SQL commands (as a user with GRANT rights to  <b>$config_db_name</b>) - <br>\n"
            ."Versions of mySQL <b>before 4.0.2</b> should <b>omit the parameter \", <b>Create Temporary Tables</b>\"</b><br>\n"
            ."<pre><b>GRANT</b> <b>Select</b>, <b>Insert</b>, <b>Update</b>, <b>Delete</b>, "
            ."<b>Index</b>, <b>Alter</b>, <b>Create</b>, <b>Drop</b>, <b>References</b>, <b>Create Temporary Tables</b>\n"
            ."      <b>ON</b> `$config_db_name`.*\n"
            ."      <b>TO</b> '$config_db_user'@'$config_db_host'\n"
            ."      <b>IDENTIFIED BY</b> '$config_db_pass';\n\n"
            ."<b>FLUSH PRIVILEGES</b>;</pre></li>"
            ."<li><b>Refresh this page</b> to continue system installation;</li>\n"
        );
    }
    $sql =    "SHOW TABLES";
    if (!$result = $ObjMysql->query($sql)) {
        die("connect_db() Cannot query SQL database");
    }
    if ($result->num_rows === 0) {       // If there was no match, database is empty - rebuild it
        print
            "<html><head><title>Installer</title></head>\n"
            ."<body><h1>Next Steps</h1>"
            ."<h1>Next Steps</h1>\n"
            ."<ol>\n"
            ."    <li>\n"
            ."    <b>Execute these queries</b> to import data into <b>$config_db_name</b><br />"
            ."    <textarea style='width:100%; height: 400px;font-family:\"Courier New\", Courier, monospace'>"
            .file_get_contents("reset.sql")
            ."</textarea>"
            ."    </li>\n"
            ."    <li><b>Refresh this page</b> to access the new system;</li>\n"
            ."</body>\n"
            ."</html>";
    }
    return $ObjMysql;
}
