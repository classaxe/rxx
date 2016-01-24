<?php
namespace Rxx;

/**
 * Class phpmailerException
 * @package Rxx
 */
class phpmailerException extends \Exception
{
    /**
     * @return string
     */
    public function errorMessage()
    {
        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
        return $errorMsg;
    }
}
