<?php
require_once "vendor/autoload.php";

use Météo\Connection;
use Météo\Favoris;
use Météo\FavorisTable;
use Météo\User\UserTable;
use Météo\Weather\Exception\HTTPException;
use Météo\Weather\OpenWeather;

if(isset($_POST['newuser'])) {
    $username = $_POST['newuser'];
    $pdo = Connection::getPDO();
    $userTable = new UserTable($pdo);
    if($userTable->exists('username', $username) === true) {
        $response = "Ce nom d'utilisateur est déja utilisé.";
    }
    else {
        $response = '';
    }

    echo $response;
}

if(isset($_POST['newmail'])) {
    $mail = $_POST['newmail'];
    $pdo = Connection::getPDO();
    $userTable = new UserTable($pdo);
    if($userTable->exists('mail', $mail) === true) {
        $responseMail = "Cette adresse e-mail est déja utilisée.";
    }
    else {
        $responseMail = '';
    }

    echo $responseMail;
}

if(isset($_GET['lat'], $_GET['lng'])) {
    $weather = new OpenWeather('94c6cf0868fa5cb930a5e2d71baf0dbf');
    try {
        $id = $weather->getIDByCoordinate($_GET['lat'], $_GET['lng']);
        $today = $weather->getToday($id);
        $str = "<h5>".$today['name'] . ', ' . $today['country']."</h5>";
        $str .= "<div style=\"display: flex; align-items:center;\">";
        $str .= "<img height='50' width='50' src=\"https://openweathermap.org/img/wn/{$today['icon']}@2x.png\" alt=\"icone météo\"/>";
        $str .= "<span>" . ucfirst($today['description']) . " {$today['temp']} °C</span>";
        $str .= "</div>";
        $responseMeteo = $str;
        $responseCity = $today['name'] . ',' . $today['country'];
        $arr = array(
            "city" => $responseCity,
            "meteo" => $responseMeteo
           );           
        echo json_encode($arr);
    } catch (HTTPException $e) {
        $e->getMessage();
        $arr = array(
            "city" => '',
            "meteo" => 'Ville introuvable'
           );           
        echo json_encode($arr);
    }
}

if(isset($_GET['latitude'], $_GET['longitude'], $_GET['id'])) {
    $pdo = Connection::getPDO();
    $weather = new OpenWeather('94c6cf0868fa5cb930a5e2d71baf0dbf');
    $favoris = new Favoris();
    $favorisTable = new FavorisTable($pdo);
    try {
        $id_ville = $weather->getIDByCoordinate($_GET['latitude'], $_GET['longitude']);
        if(!$favorisTable->existsWith2Conditions('id_ville', 'id', $id_ville, $_GET['id'], 'id_ville')) {
            $today = $weather->getToday($id_ville);
            $pays = $today['country'];
            $ville = $today['name'];
            $latitude = $_GET['latitude'];
            $longitude = $_GET['longitude'];
            $id = $_GET['id'];
            $favoris->setId_ville($id_ville);
            $favoris->setPays($pays);
            $favoris->setVille($ville);
            $favoris->setLatitude($latitude);
            $favoris->setLongitude($longitude);
            $favoris->setId($id);
            $favorisTable = new FavorisTable($pdo);
            $favorisTable->createFavoris($favoris);
        }
    } catch (HTTPException $e) {
        $e->getMessage();
    }

    $arr = array(
        "newLat" => $latitude,
        "newLon" => $longitude
       );
       
    echo json_encode($arr);
}

if(isset($_GET['suplatitude'], $_GET['suplongitude'], $_GET['id'])) {
    $pdo = Connection::getPDO();
    $weather = new OpenWeather('94c6cf0868fa5cb930a5e2d71baf0dbf');
    $favorisTable = new FavorisTable($pdo);
    try {
        $id_ville = $weather->getIDByCoordinate($_GET['suplatitude'], $_GET['suplongitude']);
        $favorisTable->deleteWith2Conditions($id_ville, $_GET['id'], 'id_ville', 'id');
    } catch (HTTPException $e) {
        $e->getMessage();
    }
}

?>