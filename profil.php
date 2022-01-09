<?php

require_once "vendor/autoload.php";

require_once "./class/Mail/PHPMailer/src/Exception.php";
require_once "./class/Mail/PHPMailer/src/PHPMailer.php";
require_once "./class/Mail/PHPMailer/src/SMTP.php";

use Météo\Calendar\EventsTable;
use Météo\Calendar\Month;
use Météo\Connection;
use Météo\Favoris;
use Météo\FavorisTable;
use Météo\Mail\Mail;
use Météo\Table\Exception\NotFoundException;
use Météo\User\Auth;
use Météo\User\Exception\ForbiddenException;
use Météo\User\UserImgTable;
use Météo\User\UserTable;
use Météo\Weather\Exception\HTTPException;
use Météo\Weather\OpenWeather;
use PHPMailer\PHPMailer\PHPMailer;

$pdo = Connection::getPDO();
$auth = new Auth($pdo);
$table = new UserTable($pdo);
$userImgTable = new UserImgTable($pdo);
$userTable = new UserTable($pdo);
$favorisTable = new FavorisTable($pdo);
$weather = new OpenWeather('94c6cf0868fa5cb930a5e2d71baf0dbf');

try {
   $auth->check();
} catch (ForbiddenException $e) {
   header('Location: connexion.html?forbid=1');
   exit();
}

$user = $auth->user();

$userImg = $userImgTable->getUserImg($_SESSION['auth']);

$events = new EventsTable($pdo);
$eventsWithRappel = $events->getEventswithRappelandMail();
$month = new Month($_GET['month'] ?? null, $_GET['year'] ?? null);
$start = $month->getStartingDay();
$start = $start->format('N') === '1' ? $start : $start->modify('last monday');
$weeks = $month->getWeeks();
$end = $start->modify('+' . (6 + 7 * ($weeks - 1)) . ' days');

if(!empty($eventsWithRappel)) {
   foreach($eventsWithRappel as $eventWithRappel) {
      if(!empty($eventWithRappel['date_rappel']) && !empty($eventWithRappel['id_ville']) && !empty($eventWithRappel['mail'])) {
         $weather = new OpenWeather('94c6cf0868fa5cb930a5e2d71baf0dbf');
         try {
            $today = $weather->getToday($eventWithRappel['id_ville']);
            $forecast = $weather->getForecast($eventWithRappel['id_ville']);
         } catch (Exception $e) {
            exit($e->getMessage());
         }
         foreach($forecast as $day) {
            $days[] = ['date' => $day['date']->format('d/m/Y'), 'min' => $day['min'], 'max' => $day['max'], 'description' => $day['description']];
         }
         $rappelDateTime = new DateTime($eventWithRappel['date_rappel']);
         $currentDateTime = new DateTime();
         if($currentDateTime >= $rappelDateTime) {
            $phpmailer = new PHPMailer();
            $mail = new Mail($phpmailer);
            $body = "<!DOCTYPE html>
                <html lang=\"fr\">
                    <body>
                        <h1>Rappel météo de la ville : {$today['name']} du {$rappelDateTime->format('d/m/Y')}</h1>
                        <img src=\"cid:logo_site\">
                        <p>Vous avez demandé un rappel météo pour la ville <b>{$today['name']}</b></p>
                        <p>Il fait actuellement <b>{$today['temp']} °C</b> : " . ucfirst($today['description']) . " à {$today['name']}</p>
                        <p>Voici les prévisions pour les 4 prochains jours :</p>
                        <ul>
                           <li>{$days[0]['date']} : <b>{$days[0]['min']} / {$days[0]['max']} °C</b> " . ucfirst($days[0]['description']) . "</li>
                           <li>{$days[1]['date']} : <b>{$days[1]['min']} / {$days[1]['max']} °C</b> " . ucfirst($days[1]['description']) . "</li>
                           <li>{$days[2]['date']} : <b>{$days[2]['min']} / {$days[2]['max']} °C</b> " . ucfirst($days[2]['description']) . "</li>
                           <li>{$days[3]['date']} : <b>{$days[3]['min']} / {$days[3]['max']} °C</b> " . ucfirst($days[3]['description']) . "</li>
                        </ul>
                        <p>Découvrez plus de jours ainsi que d'informations sur notre site !</p>
                        <p>A bientôt sur <a href=\"https://24hweather.alwaysdata.net/\">24hWeather</a> !</p>
                    </body>
                </html>";
            $mail->sendMail($body, '24hweather@gmail.com', '24hWeather', $eventWithRappel['mail'], 'Rappel météo 24hWeather');
            try {
               $eventwithR = $events->find($eventWithRappel['id_event'], 'id_event');
            } catch (NotFoundException $e) {
               header('Location: 404.php');
               exit();
            } catch (\Error $e) {
               header('Location: 404.php');
               exit();
            }
            $eventwithR->setId_ville(null);
            $eventwithR->setDate_rappel(null);
            $events->updateEvent($eventwithR);
         }
      }   
   }
}

$events = $events->getEventsBetweenByDay($start, $end, $_SESSION['auth']);

if(isset($_GET['fav'])) {
   $fav = htmlspecialchars($_GET['fav']);
   $favorisTable->delete($fav, 'id_fav');
}

if(isset($_GET['addlat'], $_GET['addlon'])) {
   $favoris = new Favoris();
   $favorisTable = new FavorisTable($pdo);
   try {
      $id_ville = $weather->getIDByCoordinate($_GET['addlat'], $_GET['addlon']);
      if(!$favorisTable->existsWith2Conditions('id_ville', 'id', $id_ville, $_SESSION['auth'], 'id_ville')) {
         $today = $weather->getToday($id_ville);
         $pays = $today['country'];
         $ville = $today['name'];
         $latitude = $_GET['addlat'];
         $longitude = $_GET['addlon'];
         $id = $_SESSION['auth'];
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
}

if(!empty($_POST)) {
   $userTable->delete($_SESSION['auth']);
   session_destroy();
   header('Location: login.php');
   exit();
}

$title = "Mon profil - 24h Weather";
$description = "Créez un compte afin d'obtenir des avantages exclusifs !"; 
$keywords = "weather meteo 24/24 7/7";
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
      <a href="index.php">
         <div class="back">
            <div class="arrow arrow--left">
               <span></span>
            </div>
         </div>
      </a>
      <section id="wrapper">
         <div class="content-tab">
            <!-- Tab links -->
            <div class="tabs">
               <button class="tablinks" id="profil" data-title="Profil"><span data-title="Profil">Mon profil</span></button>
               <button class="tablinks" id="calendrier" data-title="Calendrier"><span data-title="Calendrier">Mon calendrier</span></button>
               <button class="tablinks" id="préférences" data-title="Préférences"><span data-title="Préférences">Mes préférences</span></button>
               <form class="formlinks" action="logout.php" method="POST">
                  <button class="btn-deco" type="submit" data-title="Déconnexion"><span data-title="Déconnexion">Déconnexion</span></button>
               </form>
            </div>
         
            <!-- Tab content -->
            <div class="wrapper_tabcontent">
               <div id="Profil" class="tabcontent">
                  <h3>Profil</h3>
                  <div class="container">
                     <div class="main-body">                  
                        <div class="row gutters-sm">
                           <div class="col-md-4 mb-3">
                              <div class="card">
                                 <div class="card-body">
                                    <div class="d-flex flex-column align-items-center text-center">
                                       <?php
                                       if(!empty($userImg)) {
                                          echo "<img src=\"{$userImg->getImg()}?".mt_rand()."\" alt=\"User\" class=\"rounded-circle\" width=\"150\"/>\n";
                                       } else {
                                          echo "<img src=\"./images/avatar.png\" alt=\"User\" class=\"rounded-circle\" width=\"150\"/>\n";
                                       }
                                       ?>
                                       <div class="mt-3">
                                          <h4><?php echo $user->getUsername(); ?></h4>
                                          <p class="text-secondary mb-1 user-desc-profile"><?php if($user->getDescription() === NULL) { echo "Décrivez vous"; } else { echo $user->getDescription(); } ?></p>
                                       </div>
                                    </div>
                                 </div>                                 
                              </div>
                              <form action="#" method="POST" class="deleteacc">
                                 <button class="btn btn-danger" name="delete" type="submit">Supprimer le compte</button>
                              </form>
                           </div>
                           <div class="col-md-8">
                              <div class="card mb-3">
                                 <div class="card-body">
                                    <div class="row">
                                       <div class="col-sm-3">
                                          <h6 class="mb-0">Nom complet</h6>
                                       </div>
                                       <div class="col-sm-9 text-secondary">
                                          <?php
                                          if($user->getNom() === NULL) {
                                             echo "Non renseigné";
                                          }
                                          else {
                                             echo $user->getNom();
                                          }
                                          ?>
                                       </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                       <div class="col-sm-3">
                                          <h6 class="mb-0">Adresse mail</h6>
                                       </div>
                                       <div class="col-sm-9 text-secondary">
                                          <?php echo $user->getMail(); ?>
                                       </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                       <div class="col-sm-3">
                                          <h6 class="mb-0">Téléphone</h6>
                                       </div>
                                       <div class="col-sm-9 text-secondary">
                                       <?php
                                          if($user->getTelephone() === NULL) {
                                             echo "Non renseigné";
                                          }
                                          else {
                                             echo $user->getTelephone();
                                          }
                                          ?>
                                       </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                       <div class="col-sm-3">
                                          <h6 class="mb-0">Adresse</h6>
                                       </div>
                                       <div class="col-sm-9 text-secondary">
                                       <?php
                                          if($user->getAdresse() === NULL) {
                                             echo "Non renseigné";
                                          }
                                          else {
                                             echo $user->getAdresse();
                                          }
                                          ?>
                                       </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                       <div class="col-sm-12">
                                             <a class="btn btn-profile" href="modifier-profil.html">Modifier</a>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
                                          
               <div id="Calendrier" class="tabcontent">
                  <h3>Calendrier</h3>
                  <div class="calendar">
                     <div class="dflex flex-row align-items-center justify-content-between nx-sm-3">
                        <?php echo "<div class='h2'>{$month->__toString()}</div>\n"; ?>
                        <?php
                        if(isset($_GET['success'])) {
                           echo "<div class='container container-event'>\n";
                           echo "<div class='alert alert-success'>L'évènement à bien été enregistré</div>\n";
                           echo "</div>\n";
                        }                      
                        ?>
                        <div>
                           <a class="btn btn-profile" href="profil.php?month=<?php echo $month->previousMonth()->month; ?>&amp;year=<?php echo $month->previousMonth()->year; ?>#calendar">&lt;</a>
                           <a class="btn btn-profile" href="profil.php?month=<?php echo $month->nextMonth()->month; ?>&amp;year=<?php echo $month->nextMonth()->year; ?>#calendar">&gt;</a>
                        </div>
                     </div>
                     <table id="calendar" class="calendar__table calendar__table--<?php echo $month->getWeeks(); ?>weeks">
                        <?php 
                        for($i = 0; $i < $month->getWeeks(); $i++) {
                           echo "\t\t\t<tr>\n";
                           foreach($month->days as $k => $day) {
                              $date = $start->modify("+" . ($k + $i * 7) . " days");
                              $eventsForDay = $events[$date->format('Y-m-d')] ?? [];
                              $isToday = date('Y-m-d') === $date->format('Y-m-d');
                              if($month->withinMonth($date)) {
                                 if($isToday) {
                                    echo "\t\t\t<td class='is-today'>\n";
                                 }
                                 else {
                                    echo "\t\t\t<td>\n";
                                 }
                              }
                              else {
                                 if($isToday) {
                                    echo "\t\t\t<td class='calendar__othermonth is-today'>\n";
                                 }
                                 else {
                                    echo "\t\t\t<td class='calendar__othermonth'>\n";
                                 }
                              }
                              echo "\t\t\t\t<div class='calendar__weekday'>$day</div>\n";
                              echo "\t\t\t\t<a class='calendar__day' href='ajouter-evenement.html?date={$date->format('Y-m-d')}'>" . $date->format('d') . "</a>\n";
                              foreach($eventsForDay as $event) {
                                 echo "\t\t\t<div class='calendar__event'>\n";
                                 echo "\t\t\t\t" . $event->getStart()->format('H:i') ." - <a href='evenement.html?id={$event->getId_event()}'>" . htmlspecialchars($event->getName()) . "</a>\n";
                                 echo "\t\t\t</div>\n";
                              }
                              echo "\t\t\t</td>\n";
                           }
                           echo "\t\t\t</tr>\n";
                        }
                        ?>
                     </table>

                     <a href="ajouter-evenement.html" class="calendar__button">+</a>
                  </div>
               </div>

               <?php $favoris = $favorisTable->getUserFavoris($_SESSION['auth']); ?>
               <div id="Préférences" class="tabcontent <?php if(empty($favoris)) { echo "favsnull"; } ?>">
                  <h3>Préférences</h3>
                  <div class="container-fluid">
                     <div class="px-lg-5">

                        <div class="row py-5">
                           <div class="col-lg-12 mx-auto">
                              <div class="text-white p-5 shadow-sm rounded banner">
                                 <h2 class="display-4">Vos villes favorites</h2>
                                 <p class="lead">Retrouvez toutes vos villes favorites et modifiez les !</p>
                              </div>
                           </div>
                        </div>

                        <div class="container container-form">
                           <div class="row height d-flex justify-content-center align-items-center">
                              <div class="col-md-6">
                                    <form class="form" action="#" method="GET">
                                       <i class="fa fa-search"></i> <input type="text" id="addfav" name="addfav" required="required" class="form-control form-input" placeholder="Rechercher une ville à ajouter en favoris..."/>
                                       <ul class="list-group" id="resultfavs"></ul>
                                    </form>
                              </div>
                           </div>
                        </div>

                        <?php
                        if(!empty($favoris)) {
                           echo "<div class=\"row\">\n";
                           foreach($favoris as $favori) {
                              $today = $weather->getToday($favori->getId_ville());
                              echo "\t\t\t\t\t<div class=\"col-xl-3 col-lg-4 col-md-6 mb-4\">
                                       <div class=\"bg-white rounded shadow-sm\"><img src=\"{$weather->getFlag($favori->getPays())}\" alt=\"{$favori->getVille()}\" class=\"img-fluid card-img-top\"/>
                                          <div class=\"p-4\">
                                             <h5><a href=\"meteo.php?lat={$favori->getLatitude()}&amp;lon={$favori->getLongitude()}\" class=\"text-dark\">{$favori->getVille()} ({$favori->getPays()})</a></h5>
                                             <img src=\"https://openweathermap.org/img/wn/".$today['icon']."@2x.png\" alt=\"icon\"  height=\"50\" width=\"50\"/>
                                             <p class=\"small text-muted mb-0\"><b>{$today['temp']} °C </b>" . ucfirst($today['description']) . "</p>
                                             <div class=\"d-flex align-items-center justify-content-between rounded-pill bg-light px-3 py-2 mt-4\">
                                                <p class=\"small mb-0\"></p>
                                                <div class=\"badge bg-danger px-3 rounded-pill font-weight-normal\"><a href=\"profil.php?fav={$favori->getId_fav()}\">Supprimer</a></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>\n";
                           }
                           echo "\t\t\t\t</div>\n";
                        }
                        ?>                        
                     </div>
                  </div>     
               </div>
            </div>
         </div>
      </section>

   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
   <script src="script.min.js"></script>
   </body>
</html>