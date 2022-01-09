<?php

require_once "vendor/autoload.php";

use Météo\Calendar\EventsTable;
use Météo\Connection;
use Météo\Table\Exception\NotFoundException;
use Météo\User\Auth;
use Météo\User\Exception\ForbiddenException;
use Météo\User\UserTable;
use Météo\Weather\OpenWeather;

$pdo = Connection::getPDO();
$auth = new Auth($pdo);
$table = new UserTable($pdo);

try {
   $auth->check();
} catch (ForbiddenException $e) {
   header('Location: connexion.html?forbid=1');
   exit();
}

$user = $auth->user();

$events = new EventsTable($pdo);
if(!isset($_GET['id'])) {
    header('Location: 404.php');
    exit();
}

try {
    $event = $events->find($_GET['id'], 'id_event');
} catch (NotFoundException $e) {
    header('Location: 404.php');
    exit();
}

if(!empty($_POST)) {
    $events->delete($_GET['id'], 'id_event');
    header('Location: profil.php');
    exit();
}

$title = "". htmlspecialchars($event->getName()) . " - 24h Weather";
$description = "Retrouvez l'évènement sur votre calendrier et consultez le !"; 
$keywords = "weather meteo 24/24 7/7 calendar event";
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- méta-données -->
        <?php
        echo "<title>".$title."</title>\n"
        ?>
        <meta name="author" content="nathan"/>
        <?php
        echo "<meta name=\"keywords\" content=\"".$keywords."\"/>\n";
        echo "\t\t<meta name=\"description\" content=\"".$description."\"/>\n";
        ?>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous"/>
        <link type="text/css" rel="stylesheet" title="standard" href="styles.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
        <link rel="icon" href="./images/favicon.ico"/>  
    </head>
    <body>
        <a href="profil.php">
            <div class="back">
                <div id="back" class="arrow arrow--left">
                <span></span>
                </div>
            </div>
        </a>
        <section id="wrapper">
            <div class="content-tab">
                <!-- Tab links -->
                <div class="tabs">
                <a href="profil.php" id="profil" class="tablinks" data-title="Profil"><span data-title="Profil">Mon profil</span></a>
                <a href="profil.php" id="calendrier" class="tablinks active" data-title="Calendrier"><span data-title="Calendrier">Mon calendrier</span></a>
                <a href="profil.php" id="préférences" class="tablinks" data-title="Préférences"><span data-title="Préférences">Mes préférences</span></a>
                <a href="profil.php" id="paramètres" class="tablinks" data-title="Paramètres"><span data-title="Paramètres">Paramètres</span></a>
                <form class="formlinks" action="logout.php" method="POST">
                    <button class="btn-deco" type="submit" data-title="Déconnexion"><span data-title="Déconnexion">Déconnexion</span></button>
                </form>
                </div>
            
                <!-- Tab content -->
                <div class="wrapper_tabcontent">                                          
                    <div id="Calendrier" class="tabcontent active">
                        <h3>Calendrier</h3>
                        <?php echo "<div class='h2'>". htmlspecialchars($event->getName()) ."</div>\n"; ?>
                        <?php 
                        echo "<ul>
                            <li>Date: {$event->getStart()->format('d/m/Y')}</li>
                            <li>Heure de démarrage: {$event->getStart()->format('H:i')}</li>
                            <li>Heure de fin: {$event->getEnd()->format('H:i')}</li>";
                            if(!empty($event->getId_ville() && !empty($event->getDate_rappel()))) {
                                $weather = new OpenWeather('94c6cf0868fa5cb930a5e2d71baf0dbf');
                                try {
                                    $today = $weather->getToday($event->getId_ville());
                                } catch (Exception $e) {
                                    exit($e->getMessage());
                                }
                                $date_rappel = new DateTime($event->getDate_rappel());
                                echo "<li>Ville : {$today['name']}, {$today['country']}</li>
                                    <li>Date de rappel : {$date_rappel->format('d/m/Y')}</li>";
                            }
                            echo "<li>Description: " . htmlspecialchars($event->getMotif()) . "</li>
                        </ul>\n";
                        echo "<div class='dflex flex-row align-items-center justify-content-between'>";
                        echo "<a class='btn btn-event' href='modifier-evenement.html?id={$event->getId_event()}'>Modifier l'évènement</a>";
                        ?>
                        <form action="#" method="POST">
                            <button class="btn btn-danger" name="delete" type="submit">Supprimer l'évènement</button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
    <script src="script.min.js"></script>
    </body>
</html>