<?php

/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 7/14/16
 * Time: 8:02 PM
 */
namespace wmateam\ogp;

use DOMDocument;
use Symfony\Component\Yaml\Exception\RuntimeException;
use wmateam\curling\HttpRequest;
use wmateam\curling\HttpResponse;

class OpenGraphProtocol extends DOMDocument
{

    /**
     * @var string
     */
    private $url = null;
    /**
     * @var HttpRequest
     */
    private $request = null;
    /**
     * @var HttpResponse
     */
    private $response = null;
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
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
        'article:published_time', 'article:modified_time', 'article:expiration_time', 'article:author', 'article:section', 'article:tag',
        // book
        'book:author', 'book:isbn', 'book:release_date', 'book:tag',
        // profile
        'profile:first_name', 'profile:last_name', 'profile:username', 'profile:gender'


    );

    /**
     * @var array
     */
    private $mediaType = array(
        // Image
        'og:image', 'og:image:url', 'og:image:secure_url',
        // Video
        'og:video', 'og:video:url', 'og:video:secure_url',
        // Audio
        'og:audio', 'og:audio:url', 'og:audio:secure_url'
    );

    /**
     * @var array
     */
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

    /**
     * OpenGraphProtocol constructor.
     * @param string $url url of target
     */
    public function __construct($url)
    {
        $this->url = $url;
        $this->request = new HttpRequest($this->url);
    }

    /**
     * @param array $queryString
     */
    public function setQueryString($queryString = [])
    {
        if (count($queryString) > 0) {
            $this->request->setQueryString($queryString);
        }
    }

    public function get()
    {
        $this->response = $this->request->get();
        return $this->explorer();
    }

    public function post($data = [])//TODO add post type
    {
        $this->request->post($data);
        return $this->explorer();
    }

    private function explorer()
    {
        $html = $this->response->getBody();

        $internalErrors = libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html);

        libxml_use_internal_errors($internalErrors);
        //$dom->loadHTMLFile('index.html');
        $tags = $dom->getElementsByTagName('meta');


        foreach ($tags as $item) {
            if ($item->hasAttribute('property')) {
                $property = $item->getAttribute('property');
                if (in_array($property, $this->validAttributes)) {
                    if (in_array($property, $this->mediaType)) {
                        $property = explode(':', $property);
                        $this->data[$property[1]][] = $item->getAttribute('content');
                    } else {
                        $this->data[$item->getAttribute('property')] = $item->getAttribute('content');
                    }
                } else if ($item->hasAttribute('name') && $item->getAttribute('name') == 'description') {
                    $this->data['description'] = $item->getAttribute('content');
                }
            }
        }
        // If op not exists
        if (!array_key_exists('op:title', $this->data)) {
            $data['title'] = $dom->getElementsByTagName('title')->item(0)->textContent;
        }
        return $this->aggrigate();
    }

    protected function aggrigate()
    {
        $result = [];
        if (array_key_exists('og:url', $this->data)) {
            $result['url'] = $this->data['og:url'];
        } else {
            $result['url'] = $this->url;
        }
        if (array_key_exists('og:title', $this->data)) {
            $result['title'] = $this->data['og:title'];
        } else if (array_key_exists('og:name', $this->data)) {
            $result['title'] = $this->data['og:name'];
        } else if (array_key_exists('title', $this->data)) {
            $result['title'] = $this->data['title'];
        }

        if (array_key_exists('og:site_name', $this->data)) {
            $result['title'] .= ' - ' . $this->data['og:site_name'];
        }


        if (array_key_exists('og:description', $this->data)) {
            $result['description'] = $this->data['og:description'];
        } else if (array_key_exists('description', $this->data)) {
            $result['title'] = $this->data['description'];
        }


        if (array_key_exists('image', $this->data)) {
            $result['images'] = $this->data['image'];
        }

        if (array_key_exists('video', $this->data)) {
            $result['videos'] = $this->data['video'];
        }

        if (array_key_exists('audio', $this->data)) {
            $result['audios'] = $this->data['audio'];
        }


        if (array_key_exists('og:type', $this->data)) {
            $this->data['og:type'] = explode('.', $this->data['og:type']);
            $result['type'] = $this->data['og:type'][0];
            $result['kind'] = '';
            if (isset($this->data['og:type'][1])) {
                $result['kind'] = $this->data['og:type'][1];
            }

            if ($result['type'] == 'music') {

            }
        }
        return $result;
    }
}

