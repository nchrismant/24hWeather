<?php

require_once "vendor/autoload.php";

use Météo\Calendar\EventsTable;
use Météo\Calendar\EventValidator;
use Météo\Connection;
use Météo\Table\Exception\NotFoundException;
use Météo\User\Auth;
use Météo\User\Exception\ForbiddenException;
use Météo\Weather\OpenWeather;

$pdo = Connection::getPDO();
$auth = new Auth($pdo);

try {
   $auth->check();
} catch (ForbiddenException $e) {
   header('Location: connexion.html?forbid=1');
   exit();
}

$events = new EventsTable($pdo);
$errors = [];
if(!isset($_GET['id'])) {
    header('Location: 404.php');
    exit();
}

try {
    $event = $events->find($_GET['id'] ?? null, 'id_event');
} catch (NotFoundException $e) {
    header('Location: 404.php');
    exit();
} catch (\Error $e) {
    header('Location: 404.php');
    exit();
}

if(!empty($event->getId_ville())) {
    $weather = new OpenWeather('94c6cf0868fa5cb930a5e2d71baf0dbf');
    try {
        $today = $weather->getToday($event->getId_ville());
    } catch (Exception $e) {
        exit($e->getMessage());
    }
}

if(!empty($today['name'])) {
    $villerappel = $today['name'].",".$today['country'];
} else {
    $villerappel = null;
}

$data = [
    'name' => $event->getName(),
    'date' => $event->getStart()->format('Y-m-d'),
    'start' => $event->getStart()->format('H:i'),
    'end' => $event->getEnd()->format('H:i'),
    'villerappel' => $villerappel,
    'daterappel' => $event->getDate_rappel() ?? null,
    'motif' => $event->getMotif()
];

$errors = [];
if(!empty($_POST)) {
    $data = $_POST;
    $validator = new EventValidator();
    $errors = $validator->validates($data);
    if(empty($errors)) {
        $event->setName($data['name']);
        $event->setMotif($data['motif']);
        $event->setStart(DateTime::createFromFormat('Y-m-d H:i', $data['date'] . ' ' . $data['start'])->format('Y-m-d H:i:s'));
        $event->setEnd(DateTime::createFromFormat('Y-m-d H:i', $data['date'] . ' ' . $data['end'])->format('Y-m-d H:i:s'));
        if(!empty($data['daterappel']) && !empty($data['villerappel']) && !empty($data['addrappel'])) {
            $weather = new OpenWeather('94c6cf0868fa5cb930a5e2d71baf0dbf');
            $city = htmlspecialchars($data["villerappel"]);
            try {
                $id_ville = $weather->getIDByName($city);
            } catch (Exception $e) {
                exit($e->getMessage());
            }
            $event->setId_ville($id_ville);
            $event->setDate_rappel($data['daterappel']);
        } else if(empty($data['addrappel'])) {
            $event->setId_ville(null);
            $event->setDate_rappel(null);
        }
        $event->setId($_SESSION['auth']);
        $events->updateEvent($event);
        header('Location: profil.php?success=1');
        exit();
    }
}

$title = "". htmlspecialchars($event->getName()) . " - 24h Weather";
$description = "Modifiez l'évènement du calendrier de votre choix !"; 
$keywords = "weather meteo 24/24 7/7 event modify calendar";
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
        <div class="back">
            <div id="back" class="arrow arrow--left">
                <span></span>
            </div>
        </div>
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
                        <div class="container">
                            <?php echo "<div class='h2'>Editer l'évènement <small>". htmlspecialchars($event->getName()) ."</small></div>\n"; ?>
                            <form action="#" method="POST" class="form">
                                <div class="row">
                                    <div class="col-sm-6">                                
                                        <div class="form-group">
                                            <label for="name">Titre</label>
                                            <input id="name" type="text" required="required" class="form-control <?php if(isset($errors['name'])) { echo "form-error"; } ?>" name="name" value="<?php if (isset($data['name'])) { echo htmlspecialchars($data['name']); } ?>"/>
                                            <?php
                                            if(isset($errors['name'])) {
                                                echo "<div class=\"text-danger\"><small>" . $errors['name'] . "</small></div>\n";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="date">Date</label>
                                            <input id="date" type="date" required="required" class="form-control <?php if(isset($errors['date'])) { echo "form-error"; } ?>" name="date" value="<?php if (isset($data['date'])) { echo htmlspecialchars($data['date']); } ?>"/>
                                            <?php
                                            if(isset($errors['date'])) {
                                                echo "<div class=\"text-danger\"><small>" . $errors['date'] . "</small></div>\n";
                                            }
                                            ?>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">                                
                                        <div class="form-group">
                                            <label for="start">Début</label>
                                            <input id="start" type="time" required="required" class="form-control <?php if(isset($errors['start'])) { echo "form-error"; } ?>" name="start" value="<?php if (isset($data['start'])) { echo htmlspecialchars($data['start']); } ?>"/>
                                            <?php
                                            if(isset($errors['start'])) {
                                                echo "<div class=\"text-danger\"><small>" . $errors['start'] . "</small></div>\n";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="end">Fin</label>
                                            <input id="end" type="time" required="required" class="form-control" name="end" value="<?php if (isset($data['end'])) { echo htmlspecialchars($data['end']); } ?>"/>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">                                
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="addrappel" type="checkbox" id="switchRappel" <?php if (isset($data['villerappel']) || isset($data['daterappel'])) { echo "checked=\"checked\""; } ?>/>
                                            <label class="form-check-label" for="switchRappel">Ajouter un rappel</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="villerappel">Ville</label>
                                            <input id="villerappel" type="text" required="required" class="form-control" name="villerappel" placeholder="Ville,Code du pays (ex : Paris,FR)" value="<?php if (isset($data['villerappel'])) { echo htmlspecialchars($data['villerappel']); } ?>" readonly="readonly"/>
                                        </div>                                        
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="daterappel">Date du rappel</label>
                                            <input id="daterappel" type="date" required="required" class="form-control" name="daterappel" value="<?php if (isset($data['daterappel'])) { echo htmlspecialchars($data['daterappel']); } ?>" readonly="readonly"/>
                                        </div>                                        
                                    </div>                                
                                </div>
                                <div class="form-group">
                                    <label for="motif">Motif</label>
                                    <textarea name="motif" id="motif" class="form-control"><?php if (isset($data['motif'])) { echo htmlspecialchars($data['motif']); } ?></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Modifier l'évènement</button>
                                </div>
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