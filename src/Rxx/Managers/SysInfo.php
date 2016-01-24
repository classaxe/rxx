<?php

namespace Managers;

class SysInfo
{
    public function draw()
    {
        ob_start();
        phpinfo();
        $tmp = ob_get_contents();
        ob_end_clean();
        $out = preg_split("/<body>|<\/body>/i", $tmp);
        $phpinfo = $out[1];

        $changelog = explode("\n", `git log master --pretty=format:"%ad %s" --date=short`);
        foreach ($changelog as &$entry) {
            $bits =     explode(' ', $entry);
            $date =     array_shift($bits);
            $version =  trim(array_shift($bits), ':');
            $details =  implode(' ', $bits);
            $entry =    $date.'  '.pad($version, 7).' '.$details;
        }
        $changelog = implode("\n", $changelog);

        return
             "<div id='phpinfo'>\n"
            ."<table border=\"0\" cellpadding=\"3\">\n"
            ."<tr class='h'><td colspan='2'><h1 class='p'>RNA / REU / RWW SYSTEM</h1></td></tr>\n"
            ."<tr><td class=\"e\">system</td><td class=\"v\">".system."</td></tr>\n"
            ."<tr><td class=\"e\">system_title</td><td class=\"v\">".system_title."</td></tr>\n"
            ."<tr><td class=\"e\">system_URL</td><td class=\"v\">".system_URL."</td></tr>\n"
            ."<tr><td class=\"e\">system_ID</td><td class=\"v\">".system_ID."</td></tr>\n"
            ."<tr><td class=\"e\">system_editor</td><td class=\"v\">".system_editor."</td></tr>\n"
            ."<tr><td class=\"e\">system_date</td><td class=\"v\">".system_date."</td></tr>\n"
            ."<tr><td class=\"e\">system_version</td><td class=\"v\">".system_version."</td></tr>\n"
            ."<tr><td class=\"e\">system_revision</td><td class=\"v\">".system_revision."</td></tr>\n"
            ."<tr><td class=\"e\">Recent Changes</td><td class=\"v\"><pre>".$changelog."</pre></td></tr>\n"
            ."<tr><td class=\"e\">awardsAdminEmail</td><td class=\"v\">".awardsAdminEmail."</td></tr>\n"
            ."<tr><td class=\"e\">awardsAdminName</td><td class=\"v\">".awardsAdminName."</td></tr>\n"
            ."</table>\n"
            ."<br>\n"
            .$phpinfo
            ."</div>";
    }
}
