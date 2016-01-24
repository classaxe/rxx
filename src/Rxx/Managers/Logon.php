<?php

namespace Managers;

class Logon
{
    public function draw()
    {
        global $mode, $user;

        if (isAdmin()) {
            return
                 "<h2>Logon</h2><p>You are now logged on as an Administrator and may perform administrative functions."
                ."<br><br>\nTo log off, select <b>Log Off</b> from the main menu.</p>";
        }
        return
             "<h2>Logon</h2><p>You must logon in order to perform administrative functions.</p>"
            ."<form name='form' action='".system_URL."' method='post'>\n"
            ."<input type='hidden' name='mode' value='$mode'>\n"
            ."<input type='hidden' name='submode' value='logon'>\n"
            ."<br><br><table cellpadding='4' cellspacing='1' border='0' bgcolor='#c0c0c0'>\n"
            ."  <tr>\n"
            ."    <td colspan='2' class='downloadTableHeadings_nosort'>Administrator Logon</td>"
            ."  </tr>\n"
            ."  <tr class='rownormal'>\n"
            ."    <td>Username</td>"
            ."    <td><input name='user' value='$user' size='20'</td>"
            ."  </tr>\n"
            ."  <tr class='rownormal'>\n"
            ."    <td>Password</td>"
            ."    <td><input type='password' name='password' size='20'</td>"
            ."  </tr>\n"
            ."  <tr class='rownormal'>\n"
            ."    <td colspan='2' align='center'><input type='submit' value='Logon'></td>"
            ."  </tr>\n"
            ."</table><script type='text/javascript'>document.form.user.focus();</script>\n";
    }
}
