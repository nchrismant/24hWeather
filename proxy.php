<?php

use Météo\WeatherConf;

require_once "vendor/autoload.php";

require_once "class/WeatherConf.conf.php";

// Vérification du service demandé
$service = $_GET['service'] ?? '';
if (!$service) {
    http_response_code(400);
    exit("Missing 'service' parameter");
}

switch ($service) {

    // -----------------------------
    // OpenWeather tiles
    // -----------------------------
    case 'openweather':
        if (!isset($_GET['z'], $_GET['x'], $_GET['y'])) {
            http_response_code(400);
            exit("Missing parameters z, x, y for OpenWeather tiles");
        }

        $z = intval($_GET['z']);
        $x = intval($_GET['x']);
        $y = intval($_GET['y']);
        $key = WeatherConf::$openweatherKey;

        $url = "https://tile.openweathermap.org/map/temp_new/$z/$x/$y.png?appid=$key";

        $image = @file_get_contents($url);
        if ($image === false) {
            http_response_code(502);
            exit("Unable to fetch OpenWeather tile");
        }

        header("Content-Type: image/png");
        echo $image;
        exit;

    // -----------------------------
    // Mapbox tiles
    // -----------------------------
    case 'mapbox':
        if (!isset($_GET['z'], $_GET['x'], $_GET['y'])) {
            http_response_code(400);
            exit("Missing parameters z, x, y for Mapbox tiles");
        }

        $z = intval($_GET['z']);
        $x = intval($_GET['x']);
        $y = intval($_GET['y']);
        $token = WeatherConf::$mapboxKey;

        // URL Mapbox
        $url = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/512/$z/$x/$y?access_token=$token";

        $image = @file_get_contents($url);
        if ($image === false) {
            http_response_code(502);
            exit("Unable to fetch Mapbox tile");
        }

        header("Content-Type: image/png");
        echo $image;
        exit;

    default:
        http_response_code(400);
        exit("Unknown service: $service");
}
