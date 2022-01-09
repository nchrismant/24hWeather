<?php

use Météo\Connection;
use Météo\FavorisTable;
use Météo\Weather\OpenWeather;

require_once "vendor/autoload.php";

$weather = new OpenWeather('94c6cf0868fa5cb930a5e2d71baf0dbf');

$title = "Carte interactive pour des informations mondiales - 24h Weather";
$description = "Retrouvez la météo à travers le monde !"; 
$keywords = "weather meteo 24/24 7/7";
require_once "./include/header.inc.php";
?>
    <h2 id="h2-map">Choisissez une localisation</h2>           
    <div id="map"></div>
    <div id="form-map">
        <form action="meteo.php" method="GET">
            <div class="field">
                <label for="city">Ville selectionnée</label>
                <input type="text" name="ville" id="city" required="required"/>
            </div>
            <div class="field">
                <input id="input-map" class="btn btn-dark" type="submit" value="Valider"/>
            </div>
        </form> 
    </div>
<?php
if(isset($_SESSION['auth'])) {
    $pdo = Connection::getPDO();
    $favorisTable = new FavorisTable($pdo);
    $favoris = $favorisTable->getUserFavoris($_SESSION['auth']);
    if(!empty($favoris)) {
        $fav = [];
        foreach($favoris as $favori) {
            $fav[] = ['lat' => $favori->getLatitude(), 'lon' => $favori->getLongitude()];            
        }
    }
}
require_once "./include/footer.inc.php";
?>