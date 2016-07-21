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
        return new SitePreview($this->response->getBody(), $this->url);
    }

    public function post($data = [])//TODO add post type
    {
        $this->request->post($data);
        //return $this->explorer();
    }
}

