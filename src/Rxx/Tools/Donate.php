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
        return "<h2>Thank You!</h2>
    <p>
        This service is offered <strong>guilt-free</strong> and without any form of commercial advertising as a gift to those who enjoy the hobby (art?) of NDB Listening.
    </p>
    <p>
        I am incredibly grateful to our volunteer <a href=\"#footer\">Editors and Administrators</a> who selflessly give their time at great personal cost, but at no charge to the community.<br>
        Our Editors and Administrators are: <strong>Brian Keyte</strong>, <strong>Joachim Rabe</strong>, <strong>Joseph Miller - KJ80</strong>, <strong>Pat Vignoud</strong>, <strong>Peter Conway</strong>, <strong>Roelof Bakker</strong> and <strong>S M O'Kelley</strong>.<br>
        Without their tireless devotion this resource would simply not be able to continue operating.
    </p>
    <p>Thanks also to those listeners who provide their logs for inclusion.</p>

    <br>

    <h2>About our Server Hosting Costs</h2>
    <p>
        For the developer of this system there are one or two additional financial burdens to bear in terms of website hosting and domain registration.
    </p>
    <p class=\"txt_c\">
        <img src=\"" . BASE_PATH . "assets/img/server.png\" alt=\"DL380-G5 server hosting RXX system (circa 2015)\" style=\"float:none\"><br>
        <em>HP / Compaq DL380-G5 formerly used to host RXX system - we're now cloud based</em>
    </p>
    <p>
        The Paypal button below is provided to those persons who specifically want to offer financial support to offset some of these hosting costs.
    </p>
    <p>
        <strong>Please feel under no obligation to do so.</strong>
    </p>
    <p>
        As a guide, a single donation of <strong>$35 CAD</strong> would cover our website hosting costs for a month, while <strong>$14.70 CAD</strong> would pay for domain registration for a whole year.
    </p>

    <form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_top\">
        <div class=\"txt_c\">
            <input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">
            <input type=\"hidden\" name=\"hosted_button_id\" value=\"A356JZL3MKZBG\">
            <input type=\"image\" src=\"https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif\" name=\"submit\" title=\"PayPal - The safer, easier way to pay online!\" alt=\"Donate with PayPal button\">
            <img alt=\"\" src=\"https://www.paypal.com/en_CA/i/scr/pixel.gif\" width=\"1\" height=\"1\">
        </div>
    </form>

    <p>Sincere thanks to you for visiting and using this site, and whether you choose to donate or not, please be blessed by it - you are the reason this site even exists.</p>

    <p>Blessings,<br><br>Martin Francis<br>&lt;&gt;&lt;</p>
    <p>(May 24th, 2020)</p>";
    }
}
