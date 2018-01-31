<?php

namespace Rxx;

/**
 * Class Logon
 * @package Rxx\Managers
 */
class Labs
{
    /**
     * @return string
     */
    public function draw()
    {
        global $mode, $user;
        return
             "<h2>Labs</h2>"
            ."<p>This is an experimental area.  Use at your own risk!.</p>"
            ."<form name='form' action='".system_URL."' method='post'>\n"
            ."<input type='hidden' name='mode' value='$mode'>\n"
            ."<input type='hidden' name='submode' value='logon'>\n"
            ."</form>\n";
    }
}
