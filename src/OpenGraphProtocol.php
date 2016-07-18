<?php

/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 7/14/16
 * Time: 8:02 PM
 */
namespace wmateam\ogp;

use DOMDocument;
use wmateam\curling\HttpRequest;

class OpenGraphProtocol extends DOMDocument
{

    private $validAttributes = array(
        // Basic
        'og:title', 'description', 'og:description', 'og:type', 'og:url', 'og:determiner', 'og:local', 'og:local:alternate', 'og:site_name',
        // Image
        'og:image', 'og:image:url', 'og:image:secure_url', 'og:image:type', 'og:image:width', 'og:image:height',
        // Video
        'og:video', 'og:video:url', 'og:video:secure_url', 'og:video:type', 'og:video:width', 'og:video:height',
        'video:actor',
        // Audio
        'og:audio', 'og:audio:url', 'og:audio:secure_url', 'og:audio:type',
        // Music.song
        'music:duration', 'music:album', 'music:album:disc', 'music:album:track', 'music:musician',
        // Music.album
        'music:song', 'music:song:disc', 'music:song:track', 'music:musician', 'music:release_date',
        // Music.playlist
        'music:song', 'music:song:disc', 'music:song:track', 'music:creator',
        // Music.radio_station
        'music:creator',
        // Video.movie
        'video:actor', 'video:actor:role', 'video:director', 'video:writer', 'video:duration', 'video:release_date', 'video:tag',
        // Video.episode
        'video:actor', 'video:actor:role', 'video:director', 'video:writer', 'video:duration', 'video:release_date', 'video:tag', 'video:series',
        // Video.tv_show as video.movie
        // Video.other as video.movie
        // article
        'article:published_time','article:modified_time','article:expiration_time','article:author','article:section','article:tag',
        // book
        'book:author','book:isbn','book:release_date','book:tag',
        // profile
        'profile:first_name','profile:last_name','profile:username','profile:gender'





    );

    private $mediaType = array(
        // Image
        'og:image', 'og:image:url', 'og:image:secure_url',
        // Video
        'og:video', 'og:video:url', 'og:video:secure_url',
        // Audio
        'og:audio', 'og:audio:url', 'og:audio:secure_url'
    );

    private $types = array(
        // Web base
        'website', 'article', 'blog',
        // Entertainment
        'book', 'game', 'movie', 'food',
        //Place
        'city', 'country',
        //people
        'actor', 'author', 'politician',
        //Business
        'company', 'hotel', 'restaurant'
    );

    public function result()
    {
        //$url = 'http://freq.ir/playlist/14_bb5f7b2dbea9d323e0e8b34ca1737b41';
        $url = 'http://ogp.me';
        $url = 'http://www.aparat.com/v/qjzNG';
        $url = 'https://soundcloud.com/radio-farda/3psndjx3w5e9';
        $url = 'http://www.rottentomatoes.com/m/10011268-oceansa';
        /*$url = 'http://www.imdb.com/';
        $url = 'http://www.imdb.com/title/tt1832382/';
        $url = 'http://www.imdb.com/video/imdb/vi2726140953?ref_=tt_pv_vi_aiv_1';*/

        $r = new HttpRequest($url);

        $data = $r->get();
        $html = $data->getBody();


        $internalErrors = libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html);

        libxml_use_internal_errors($internalErrors);
        //$dom->loadHTMLFile('index.html');
        $tags = $dom->getElementsByTagName('meta');

        $data = [];

        // If op not exists
        $data['title'] = $dom->getElementsByTagName('title')->item(0)->textContent;


        foreach ($tags as $item) {
            if ($item->hasAttribute('property')) {
                $property = $item->getAttribute('property');
                if (in_array($property, $this->validAttributes)) {
                    if (in_array($property, $this->mediaType)) {
                        $property = explode(':', $property);
                        $data[$property[1]][] = $item->getAttribute('content');
                    } else {
                        $data[$item->getAttribute('property')] = $item->getAttribute('content');
                    }
                }
            } else if ($item->hasAttribute('name') && $item->getAttribute('name') == 'description') {
                $data['description'] = $item->getAttribute('content');
            }
            //var_dump(['value'=>$item->nodeValue]);
        }
        $this->aggrigate($data);
        return;
        return $data;

        //var_dump($dd->getElementsByTagName('title'));
    }

    protected function aggrigate($data)
    {
        $result = [];

        if (array_key_exists('og:title', $data)) {
            $result['title'] = $data['og:title'];
        } else if (array_key_exists('og:name', $data)) {
            $result['title'] = $data['og:name'];
        } else if (array_key_exists('title', $data)) {
            $result['title'] = $data['title'];
        }

        if (array_key_exists('og:site_name', $data)) {
            $result['title'] .= ' - ' . $data['og:site_name'];
        }


        if (array_key_exists('og:description', $data)) {
            $result['description'] = $data['og:description'];
        } else if (array_key_exists('description', $data)) {
            $result['title'] = $data['description'];
        }


        if (array_key_exists('image', $data)) {
            $result['images'] = $data['image'];
        }

        if (array_key_exists('video', $data)) {
            $result['videos'] = $data['video'];
        }

        if (array_key_exists('audio', $data)) {
            $result['audios'] = $data['audio'];
        }


        if (array_key_exists('og:type', $data)) {
            $data['og:type'] = explode('.', $data['og:type']);
            $result['type'] = $data['og:type'][0];
            $result['kind'] = '';
            if (isset($data['og:type'][1])) {
                $result['kind'] = $data['og:type'][1];
            }

            if ($result['type'] == 'music') {

            }
        }
        print_r($data);
    }
}

