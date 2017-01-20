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
        $url = 'http://www.imdb.com/';
        $url = 'http://www.imdb.com/title/tt1832382/';
        $ogp = new OpenGraphProtocol($url);
        print_r($ogp->get()->getDetails());
    }
}