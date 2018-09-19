<?php

if (!function_exists('app_class_base_name')) {
    /**
     * Get a class base name
     *
     * @param  string $className
     * @return string  to lower
     */
    function app_class_base_name($className)
    {
        return strtolower(substr(strrchr($className, "\\"), 1));
    }
}

if (!function_exists('app_map_distance')) {
    /**
     * Calculate distance, bearing and more between Latitude/Longitude points
     *
     * @param string $fromPoint 'lat,lng'
     * @param string $toPoint 'lat,lng'
     * @return int
     */
    function app_map_distance($fromPoint, $toPoint)
    {
        $R = 6371e3; //the earth's radius in metres

        list($fromLatA, $fromLngA) = explode(',', $fromPoint);
        list($toLatB, $toLngB) = explode(',', $toPoint);

        $latA  = $fromLatA * (M_PI / 180);
        $lngA  = $fromLngA * (M_PI / 180);
        $latB  = $toLatB * (M_PI / 180);
        $lngB  = $toLngB * (M_PI / 180);
        $subBA = bcsub($lngB, $lngA, 20);

        return round($R * acos(cos($latA) * cos($latB) * cos($subBA) + sin($latA) * sin($latB)));
    }
}
