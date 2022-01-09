<?php

require_once "vendor/autoload.php";

use Météo\Connection;
use Météo\FavorisTable;
use Météo\Weather\OpenWeather;

$weather = new OpenWeather('94c6cf0868fa5cb930a5e2d71baf0dbf');

if(isset($_GET["ville"]) && !empty($_GET["ville"])) {
    $city = htmlspecialchars($_GET["ville"]);
    try {
        $id = $weather->getIDByName($city);
    } catch (Exception $e) {
        exit($e->getMessage());
    }
}
else if(isset($_GET["lat"]) && isset($_GET["lon"])) {
    $lat = htmlspecialchars($_GET["lat"]);
    $lon = htmlspecialchars($_GET["lon"]);
    try {
        $id = $weather->getIDByCoordinate($lat, $lon);
    } catch (Exception $e) {
        exit($e->getMessage());
    }
}
else {
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $userCoord = $weather->getUserCoord($ip);
    $lat = $userCoord['lat'];
    $lon = $userCoord['lon'];
    try {
        $id = $weather->getIDByCoordinate($lat, $lon);
    } catch (Exception $e) {
        exit($e->getMessage());
    }
    /*$city = "paris,fr";
    try {
        $id = $weather->getIDByName($city);
    } catch (Exception $e) {
        exit($e->getMessage());
    }*/
}
    
try {
    $forecast = $weather->getForecast($id);
    $today = $weather->getToday($id);    
    $sunrise = $today['sunrise']->setTimezone(new \DateTimeZone('Europe/Paris'))->format("H:i:s");
    $sunset = $today['sunset']->setTimezone(new \DateTimeZone('Europe/Paris'))->format("H:i:s");
    $hourly = $weather->getHourly($today['lat'], $today['lon']);
    $citypos[] = ['lat' => $today['lat'], 'lon' => $today['lon']];
} catch (Exception $e) {
    exit($e->getMessage());
}

$title = "Météo en temps réel - 24h Weather";
$description = "Retrouvez la météo où vous voulez et quand vous le souhaitez !"; 
$keywords = "weather meteo 24/24 7/7";
require_once "./include/header.inc.php";
?>
        <div class="meteo-form">
            <form class="search-form" action="meteo.php" method="GET">
                <input type="search" id="ville" value="<?php if(isset($today['name'])) { echo $today['name']; } ?>" name="ville" required="required" placeholder="Rechercher une ville..." class="search-input"/>
                <button type="button" id="submit" class="search-button">
                    <svg class="submit-button">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" href="#search"></use>
                    </svg>
                </button>
                <ul class="list-group" id="result"></ul>
            </form>
                
            <svg width="0" height="0" display="none">
                <symbol id="search" viewBox="0 0 32 32">
                    <path d="M 19.5 3 C 14.26514 3 10 7.2651394 10 12.5 C 10 14.749977 10.810825 16.807458 12.125 18.4375 L 3.28125 27.28125 L 4.71875 28.71875 L 13.5625 19.875 C 15.192542 21.189175 17.250023 22 19.5 22 C 24.73486 22 29 17.73486 29 12.5 C 29 7.2651394 24.73486 3 19.5 3 z M 19.5 5 C 23.65398 5 27 8.3460198 27 12.5 C 27 16.65398 23.65398 20 19.5 20 C 15.34602 20 12 16.65398 12 12.5 C 12 8.3460198 15.34602 5 19.5 5 z" />
                </symbol>
            </svg>
        </div>

            
        <div class="meteo">
            <?php
            if(isset($_SESSION['auth'])) {
                $pdo = Connection::getPDO();
                $favorisTable = new FavorisTable($pdo);
                $favoris = $favorisTable->getUserFavoris($_SESSION['auth']);
                $nb_favs = $favorisTable->count($_SESSION['auth']);
                if(!empty($favoris) && $nb_favs !== 0) {
                    echo '<div class="accordion" id="accordionFlushExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                    Villes favorites
                                </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <div class="container container-carousel">';
                                    echo "\t\t<div id=\"carouselFavs\" class=\"carousel carousel-dark slide\" data-bs-ride=\"carousel\">\n";
                                    echo "\t\t\t<div class=\"carousel-indicators\">\n";
                                            for($i = 0; $i < $nb_favs; $i++) {
                                                if($i == 0) {
                                                    echo "\t\t\t\t<button type=\"button\" data-bs-target=\"#carouselFavs\" data-bs-slide-to=\"0\" class=\"active\" aria-current=\"true\" aria-label=\"Slide 1\"></button>\n";
                                                } else {
                                                    $slide_nb = $i+1;
                                                    echo "\t\t\t\t<button type=\"button\" data-bs-target=\"#carouselFavs\" data-bs-slide-to=\"{$i}\" aria-label=\"Slide " . $slide_nb . "\"></button>\n";
                                                }
                                            
                                            }
                                            echo "\t\t\t</div>\n";
                                    echo "\t\t\t<div class=\"carousel-inner\">\n";
                                    foreach($favoris as $key => $favori) {
                                        $todayfav = $weather->getToday($favori->getId_ville());
                                        if($key == 0) {
                                            echo "\t\t<div class=\"carousel-item active\">\n";
                                        } else {
                                            echo "\t\t\t\t<div class=\"carousel-item\">\n";
                                        }
                                        echo "\t\t<a href=\"meteo.php?lat={$favori->getLatitude()}&amp;lon={$favori->getLongitude()}\">";                 
                                        echo "\t\t\t<img src=\"{$weather->getFlag($favori->getPays())}\" class=\"d-block w-100 country\" alt=\"{$favori->getVille()}\"/>
                                                <div class=\"carousel-caption d-md-block\">
                                                    <h5>{$favori->getVille()} ({$favori->getPays()})</h5>
                                                    <img src=\"https://openweathermap.org/img/wn/".$todayfav['icon']."@2x.png\" alt=\"icon\"  height=\"75\" width=\"75\"/>
                                                    <p><b>{$todayfav['temp']} °C </b>" . ucfirst($todayfav['description']) . "</p>
                                                </div>
                                            </a>
                                        </div>\n"; 
                                    }
                                    echo "\t\t\t".'</div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselFavs" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Precédent</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carouselFavs" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Suivant</span>
                                        </button>
                                    </div>                                                        
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>';
                }
            }
            ?>           
            <div class='meteo-global'>
                <div class="city-today">
                    <div class='city-celcius'>
                    <?php
                    if(isset($_SESSION['auth']) && $favorisTable->existsWith2Conditions('id_ville', 'id', $id, $_SESSION['auth'], 'id_ville')) {
                        echo "\t<span class=\"cityname\">{$today['name']} ({$today['country']}) &#x00A0;<button id=\"star\"><i id=\"star-icon\" class=\"fas fa-star checked-star\"></i></button></span>\n";
                    } else {
                        echo "\t<span class=\"cityname\">{$today['name']} ({$today['country']}) &#x00A0;<button id=\"star\"><i id=\"star-icon\" class=\"fas fa-star\"></i></button></span>\n";
                    }
                    echo "\t\t\t<div class='icon-celcius'>\n";
                    echo "\t\t\t<img src=\"https://openweathermap.org/img/wn/".$today['icon']."@2x.png\" alt=\"icon\"/>\n";
                    echo "\t\t\t\t<span class='celcius'>{$today['temp']} °C</span>\n";
                    echo "\t\t\t</div>\n";
                    echo "\t\t\t<ul class='meteo-ul'>\n";
                    echo "\t\t\t<li><b>" . ucfirst($today['description']) . "</b></li>\n";
                    echo "\t\t\t<li>Min : <b>{$today['min']} °C</b> Max : <b>{$today['max']} °C</b></li>\n";
                    echo "\t\t\t</ul>\n";
                    echo "\t\t\t</div>\n";
                    echo "\t\t\t<div class='meteo-info'>\n";
                    echo "\t\t\t\t<ul class='list-group list-group-flush'>\n";        
                    echo "\t\t\t\t<li class='list-group-item'>Température ressentie de <b>{$today['feels_like']} °C</b></li>\n";
                    echo "\t\t\t\t<li class='list-group-item'>Le vent souffle à <b>{$today['speed']} km/h</b></li>\n";
                    echo "\t\t\t\t<li class='list-group-item'>Taux d'humidité de <b>{$today['humidity']} %</b></li>\n";
                    echo "\t\t\t\t<li class='list-group-item'>La visibilité est de <b>{$today['visibility']} m</b></li>\n";
                    echo "\t\t\t\t<li class='list-group-item'>Lever du soleil à <b>{$sunrise}</b></li>\n";
                    echo "\t\t\t\t<li class='list-group-item'>Coucher du soleil à <b>{$sunset}</b></li>\n";
                    echo "\t\t\t\t</ul>\n";
                    ?>
                    </div>
                </div>                    
                <div class="map-meteo">
                    <div id="mapcity"></div>
                </div>
            </div>

            <div class="meteo-all">
                <div class="meteo-hourly">
                    <div class="city-forecast">
                        <span class="cityname">Prévisions horaires</span>
                    </div>
                    <div class="meteo-info">
                        <div class="slider">
                            <a href="#slide-1">1</a>
                            <a href="#slide-2">2</a>
                            <a href="#slide-3">3</a>
                            <a href="#slide-4">4</a>
                            <div class="slides">
                                <div id="slide-1">
                                    <ul class="list-group">
                                    <?php
                                    foreach($hourly as $key => $hour) {
                                        if($key < 6)
                                        echo "\t\t\t\t<li class='list-group-item'>".$hour['date']->setTimezone(new \DateTimeZone('Europe/Paris'))->format("H:i"). " : <img src=\"https://openweathermap.org/img/wn/".$hour['icon']."@2x.png\" alt=\"icon\" height=\"50\" width=\"50\"/> <b>" . $hour['temp'] ." °C </b>" . ucfirst($hour['description']) . "</li>\n";
                                    }
                                    ?>
                                    </ul>
                                </div>
                                <div id="slide-2">
                                    <ul class="list-group">
                                    <?php
                                    foreach($hourly as $key => $hour) {
                                        if($key >= 6 && $key < 12)
                                        echo "\t\t\t\t<li class='list-group-item'>".$hour['date']->setTimezone(new \DateTimeZone('Europe/Paris'))->format("H:i"). " : <img src=\"https://openweathermap.org/img/wn/".$hour['icon']."@2x.png\" alt=\"icon\" height=\"50\" width=\"50\"/> <b>" . $hour['temp'] ." °C </b>" . ucfirst($hour['description']) . "</li>\n";
                                    }
                                    ?>
                                    </ul>
                                </div>
                                <div id="slide-3">
                                    <ul class="list-group">
                                    <?php
                                    foreach($hourly as $key => $hour) {
                                        if($key >= 12 && $key < 18)
                                        echo "\t\t\t\t<li class='list-group-item'>".$hour['date']->setTimezone(new \DateTimeZone('Europe/Paris'))->format("H:i"). " : <img src=\"https://openweathermap.org/img/wn/".$hour['icon']."@2x.png\" alt=\"icon\" height=\"50\" width=\"50\"/>  <b>" . $hour['temp'] ." °C </b>" . ucfirst($hour['description']) . "</li>\n";
                                    }
                                    ?>
                                    </ul>
                                </div>
                                <div id="slide-4">
                                    <ul class="list-group">
                                    <?php
                                    foreach($hourly as $key => $hour) {
                                        if($key >= 18 && $key < 24)
                                        echo "\t\t\t\t<li class='list-group-item'>".$hour['date']->setTimezone(new \DateTimeZone('Europe/Paris'))->format("H:i"). " : <img src=\"https://openweathermap.org/img/wn/".$hour['icon']."@2x.png\" alt=\"icon\" height=\"50\" width=\"50\"/> <b>" . $hour['temp'] ." °C </b>" . ucfirst($hour['description']) . "</li>\n";
                                    }
                                    ?>
                                    </ul>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
                <div class="meteo-daily">
                    <div class="city-forecast">
                        <span class="cityname">Prévisions sur 8 jours</span>
                    </div>
                    <ul class="list-group">
                    <?php
                    foreach($forecast as $day) {
                        echo "\t\t\t\t<li class='list-group-item'>".$day['date']->format('d/m/Y')." : <img src=\"https://openweathermap.org/img/wn/".$day['icon']."@2x.png\" alt=\"icon\" height=\"50\" width=\"50\"/> <b>" .$day['min']. " / " . $day['max'] ." °C </b>" . ucfirst($day['description']) . "</li>\n";
                    }
                    ?>
                    </ul>
                </div>
            </div>
        </div>
<?php
require_once "./include/footer.inc.php";
?>