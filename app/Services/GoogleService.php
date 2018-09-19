<?php

namespace App\Services;


use App\Enums\GoogleResponseStatus;

class GoogleService
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $country;

    /**
     * GoogleService constructor.
     * @param string $language
     * @param string $country
     */
    public function __construct($language = 'uk', $country = 'UA')
    {
        $this->apiKey  = config('google.api_key');
        $this->baseUrl = config('google.base_url');
        $this->country = $country ?? config('google.country');

        $this->setLanguage($language);
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language)
    {
        $lang = $this->getLanguages();
        $key  = array_search($language, $lang);

        if ($key !== false) {
            $this->language = $lang[$key];
        } else {
            $this->language = config('google.language');
        }
    }

    /**
     * Get the available languages
     *
     * @return array
     */
    public function getLanguages(): array
    {
        return ['uk', 'ru'];
    }

    /**
     * The Place Autocomplete service is a web service that returns
     * place predictions in response to an HTTP request.
     * The request specifies a textual search string.
     *
     * @link https://developers.google.com/places/web-service/autocomplete)
     *
     * @param string $input
     * @return array
     */
    public function autocomplete($input)
    {
        $query = [
            'key'        => $this->apiKey,
            'type'       => 'geocode',
            'components' => 'country:' . $this->country,
            'language'   => $this->language,
            'input'      => $input,
        ];

        $url = $this->baseUrl . 'place/autocomplete/json?' . http_build_query($query);

        return json_decode($this->getCurl($url));
    }

    /**
     * Once you have a place_id from a Place Search, you can request more details
     * about a point of interest by initiating a Place Details request.
     *
     * @link https://developers.google.com/places/web-service/details
     *
     * @param $place
     * @return mixed
     */
    public function placeDetails($place)
    {
        $query = [
            'key'        => $this->apiKey,
            'components' => 'country:' . $this->country,
            'language'   => $this->language,
            'placeid'    => $place,
        ];

        $url = $this->baseUrl . 'place/details/json?' . http_build_query($query);

        return json_decode($this->getCurl($url));
    }

    /**
     * The Distance Matrix API is a service that provides travel distance and time for a matrix of origins
     * and destinations, based on the recommended route between start and end points.
     *
     * @link https://developers.google.com/maps/documentation/distance-matrix/intro
     * @param string $origins
     * @param string $destinations
     * @param string $mode
     * @return integer Meters
     */
    function getDrivingDistance($origins = 'lat,lng', $destinations = 'lat,lng', $mode = 'driving')
    {
        $query = [
            'key'          => $this->apiKey,
            'origins'      => $origins,
            'destinations' => $destinations,
            'mode'         => $mode,
        ];

        $url  = $this->baseUrl . 'distancematrix/json?' . http_build_query($query);
        $data = json_decode($this->getCurl($url), true);

        if ($data['status'] === GoogleResponseStatus::SUCCESS) {
            return $data['rows'][0]['elements'][0]['distance']['value'];
        }
    }

    /**
     * @param $url
     * @return mixed
     */
    private function getCurl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    /**
     * Reverse Google Polyline algorithm on encoded string.
     *
     * @link https://github.com/emcconville/google-map-polyline-encoding-tool/blob/master/src/Polyline.php
     *
     * @param string $string Encoded string to extract points from.
     * @return array points
     */
    public static function polylineDecode($string)
    {
        $precision = 5;

        $points   = array();
        $index    = $i = 0;
        $previous = [0, 0];

        while ($i < strlen($string)) {
            $shift = $result = 0x00;
            do {
                $bit    = ord(substr($string, $i++)) - 63;
                $result |= ($bit & 0x1f) << $shift;
                $shift  += 5;
            } while ($bit >= 0x20);

            $diff                 = ($result & 1) ? ~($result >> 1) : ($result >> 1);
            $number               = $previous[$index % 2] + $diff;
            $previous[$index % 2] = $number;
            $index++;
            $points[] = $number * 1 / pow(10, $precision);
        }

        return is_array($points) ? array_chunk($points, 2) : array();
    }
}