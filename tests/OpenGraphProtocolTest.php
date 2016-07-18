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
    {//$url = 'http://freq.ir/playlist/14_bb5f7b2dbea9d323e0e8b34ca1737b41';
        $url = 'http://ogp.me';
        $url = 'http://www.aparat.com/v/qjzNG';
        $url = 'https://soundcloud.com/radio-farda/3psndjx3w5e9';
        $url = 'http://www.imdb.com/';
        $url = 'http://www.imdb.com/title/tt1832382/';
        $url = 'http://kimiaconf.ir';
        $ogp = new OpenGraphProtocol($url);
        print_r($ogp->get());
    }
}