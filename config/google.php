<?php

return [
    'api_key'  => env('GOOGLE_KEY', ''),
    'base_url' => env('GOOGLE_BASE_URL', 'https://maps.googleapis.com/maps/api/'),
    'country'  => env('GOOGLE_COUNTRY', 'UA'),
    'language' => env('GOOGLE_LANG', 'uk'),
];