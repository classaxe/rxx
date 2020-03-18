<?php
/**
 * Created by PhpStorm.
 * User: module17
 * Date: 16-01-23
 * Time: 8:44 PM
 */

namespace Rxx\Tools;

/**
 * Class Donate
 * @package Rxx\Tools
 */
class Donate
{
    /**
     * @return string
     */
    public static function donate()
    {
        return
            "<h2>About our server costs</h2>\n"
            ."<p>This service is offered <b>guilt-free</b> and without any form of advertising"
            ." as a gift to those who enjoy the hobby (art?) of NDB Listening.<br />\n"
            ."I am incredibly grateful to our site administrators who give their time, "
            ."and to listeners provide their logs for free.<br />"
            ."Without those freewill contributions this system would not be able to continue operating.</p>\n"
            ."<p>However, for the developer of this system there are additionals cost to bear"
            ." in terms of equipment and server-room rackspace costs.</p>"
            ."<p style='text-align:center'>\n"
            ."<img src=\"".BASE_PATH."assets/img/server.png\" alt=\"DL38-G5 server hosting RXX system\" style='float:none'><br>\n"
            ."<i>HP / Compaq DL380-G5 formerly used to host RXX system - we're now cloud based</i></p>"
            ."<h3>Donate Via Paypal:</h3>\n"
            ."<p>The Paypal button below is offered exclusively to those who specifically wish"
            ." to offer financial support to offset some of these costs.</p>\n"
            ."<p>Please note that donations of under \$0.40 or so will be swallowed up in Paypal's admin costs"
            ." (hey, they have to eat as well you know!),<br />\n"
            ."so go wild and pledge a dollar or more if you want your donation to actually reach me :-)</p>"
            ."<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_blank\">"
            ."<div style='text-align:center'><input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">\n"
            ."<input type=\"hidden\" name=\"hosted_button_id\" value=\"GA3BH4BQ2VWYG\">\n"
            ."<input type=\"image\" src=\"https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif\""
            ." border=\"0\" name=\"submit\" alt=\"PayPal - The safer, easier way to pay online!\">\n"
            ."<img alt=\"\ border=\"0\" src=\"https://www.paypalobjects.com/en_US/i/scr/pixel.gif\" width=\"1\" height=\"1\">\n"
            ."</div></form>\n"
            ."<p>Sincere thanks to you for visiting and using this site, and whether you choose to donate"
            ." to its upkeep or not, please be blessed by it - you are the reason this site even exists.</p>"
            ."<p>Blessings,<br />\n"
            ."Martin Francis<br /><br />(March 18<sup>th</sup>, 2019)</p>"
            ;

    }
}
