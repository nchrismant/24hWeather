<?php

use Météo\Connection;
use Météo\FavorisTable;
use Météo\Weather\Exception\HTTPException;
use Météo\Weather\OpenWeather;

require_once "vendor/autoload.php";

$title = "Page d'accueil - 24h Weather";
$description = "Informations météorologiques actualisées et fiables ! Retrouvez la météo où vous voulez et quand vous le souhaitez !"; 
$keywords = "weather meteo 24/24 7/7";
require_once "./include/header.inc.php";
?>
        <div class="reveal">
            <section class="bg" id="background">
                <h1 id="h1" class="reveal-1">24h Weather</h1>
                <span class="reveal-1 sub-title">Bulletin Météorologique</span>
                <img src="./images/stars.png" id="stars" alt="stars"/>
                <img src="./images/moon.png" id="moon" alt="moon"/>         
            </section>
            <div>
                <form class="search-form search-form-bg reveal-2" action="meteo.php" method="GET">
                    <input type="search" id="ville" name="ville" required="required" placeholder="Rechercher une ville..." class="search-input"/>
                    <button type="button" id="submit" class="search-button">
                    <svg class="submit-button">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" href="#search"></use>
                    </svg>
                    </button>
                    <ul class="list-group" id="result"></ul>
                </form>
                    
                <svg width="0" height="0" display="none">
                    <symbol id="search" viewBox="0 0 32 32">
                        <path d="M 19.5 3 C 14.26514 3 10 7.2651394 10 12.5 C 10 14.749977 10.810825 16.807458 12.125 18.4375 L 3.28125 27.28125 L 4.71875 28.71875 L 13.5625 19.875 C 15.192542 21.189175 17.250023 22 19.5 22 C 24.73486 22 29 17.73486 29 12.5 C 29 7.2651394 24.73486 3 19.5 3 z M 19.5 5 C 23.65398 5 27 8.3460198 27 12.5 C 27 16.65398 23.65398 20 19.5 20 C 15.34602 20 12 16.65398 12 12.5 C 12 8.3460198 15.34602 5 19.5 5 z"/>
                    </symbol>
                </svg>
            </div>
        </div>
        <div class="parent reveal">
            <div class="div1 reveal-1">
                <img src="./images/world.png" alt="carte du monde" id="worldmap"/>
            </div>
            <div class="div2">
                <section>
                    <h2 class="reveal-2"><span class="dis-title">Découvrez</span>La météo au travers du monde</h2>
                    <span class="text reveal-3">Découvrez quand vous voulez et où vous voulez la météo dans le monde grâce à notre carte interactive vous permettant de choisir vos villes et lieux dont vous avez besoin d'un information météorologique. De plus vous pouvez y ajouter vos villes favorites !</span>
                    <div>
                        <a href="monde.html" class="btn reveal-4">Voir plus &#x00A0;<i class="fas fa-arrow-right"></i></a>
                    </div>
                </section>
            </div>
        </div>
        <div class="parent reveal">
            <div class="div3 reveal-1">
                <img src="./images/city.jpg" alt="ville" id="city"/>
            </div>
            <div class="div4">
                <section>
                    <h2 class="reveal-2"><span class="dis-title">Découvrez</span>La météo au travers d'un ville</h2>
                    <span class="text reveal-3">Besoin d'informations météorologiques sur une ville en particulier ? Vous retrouverez ce dont vous avez besoin que ce soit la météo pour aujourd'hui ou bien pour demain !</span>
                    <div>
                        <a href="meteo.php" class="btn reveal-4">Voir plus &#x00A0;<i class="fas fa-arrow-right"></i></a>
                    </div>
                </section>
            </div>
        </div>
<?php
require_once "./include/footer.inc.php";
?>