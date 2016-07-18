<?php
use wmateam\ogp\OpenGraphProtocol;

/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 7/14/16
 * Time: 8:03 PM
 */
class OpenGraphProtocolTest extends PHPUnit_Framework_TestCase
{

    public function testResult()
    {
        $ogp = new OpenGraphProtocol();
        print_r($ogp->result());
    }
}