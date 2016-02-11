<?php
/**
 * The main Rxx public-facing application controller
 *
 * @category    Rxx
 * @package     Rxx
 * @description RXX log system
 * @author      Martin Francis <martin@classaxe.com>
 * @created     26/01/2005
 * @license     http://some-license.com Some License
 *
 * PHP Version 5.3
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

$rxx = new Rxx\Rxx(__DIR__);