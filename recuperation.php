<?php
require_once "vendor/autoload.php";

require_once "./class/Mail/PHPMailer/src/Exception.php";
require_once "./class/Mail/PHPMailer/src/PHPMailer.php";
require_once "./class/Mail/PHPMailer/src/SMTP.php";

use Météo\Connection;
use Météo\Mail\Mail;
use Météo\Table\Exception\NotFoundException;
use Météo\User\UserTable;
use PHPMailer\PHPMailer\PHPMailer;

$error = false;
$errorNum = false;

$phpmailer = new PHPMailer();
$mail = new Mail($phpmailer);

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!empty($_POST)) {    
    if(!empty($_POST['email'])) {
        $pdo = Connection::getPDO();
        $table = new UserTable($pdo);
        try {
            $u = $table->findbyMail(htmlspecialchars($_POST['email']));
            $_SESSION['mail'] = $u->getMail();
            if($u->getActivate() === 1) {
                $u->setRecuperation(substr(number_format(time() * Rand(),0,'',''),0,6));
                $table->updateUser($u);
                $phpmailer = new PHPMailer();
                $mail = new Mail($phpmailer);
                $body = "<!DOCTYPE html>
                <html lang=\"fr\">
                    <body>
                        <h1>Récupération de votre compte 24hWeather</h1>
                        <img src=\"cid:logo_site\">
                        <p>Bonjour <b>{$u->getUsername()}</b>, vous avez effectué une demande de récupération de mot de passe.
                        Voici votre code de récupération: <b>{$u->getRecuperation()}</b></p>
                        <p>Si ce n'est pas vous qui avez fait la demande, modifiez impérativement votre mot de passe !</p>
                        <p>A bientôt sur <a href=\"https://24hweather.alwaysdata.net/\">24hWeather</a> !</p>
                    </body>
                </html>";
                $mail->sendMail($body, '24hweather@gmail.com', '24hWeather', $u->getMail(), 'Récupération compte 24hWeather');
                $message = "Un e-mail vous à été envoyé à l'adresse : {$_SESSION['mail']}";
            }
            else {
                throw new \Exception("Votre compte n'est pas activé");
            }
        } catch (NotFoundException $e) {
            $error = true;
        }
    }
    else if(!empty($_POST['nombre']) && !empty($_SESSION['mail'])) {
        $nombre = trim(htmlspecialchars($_POST['nombre']));
        $pdo = Connection::getPDO();
        $table = new UserTable($pdo);
        try {
            $u = $table->findbyMail($_SESSION['mail']);
            if($nombre === $u->getRecuperation() && $u->getActivate() === 1) {
                unset($_SESSION['mail']);
                header('Location: changer-mot-de-passe.html?id='.$u->getID());
                exit();
            }
        } catch (NotFoundException $e) {
        }
        $errorNum = true;
    }
}

$title = "Récupération - 24h Weather";
$description = "Récupérez votre compte dès maintenant !"; 
$keywords = "weather meteo 24/24 7/7";
require_once "./include/header.inc.php";
?>
        <div class="grid-center">
            <div class="wrapper">
                <div class="title-text">
                    <div class="title login">
                        Formulaire de récupération
                    </div>
                </div>
                <div class="form-container">
                    <div class="slide-controls">
                        <input type="radio" name="slide" id="login" checked="checked"/>
                        <label for="login" class="slide login slide-recup">Récupération</label>
                        <div class="slider-tab slide-recup"></div>
                    </div>
                    <div class="form-inner">
                        <form action="#" class="login" method="POST">
                            <?php                                            
                            if(!empty($_POST['email']) && !$error) {
                                echo "<div class='alert alert-success'>{$message}</div>\n";
                                echo "<div class=\"field\">
                                <input type=\"text\" name=\"nombre\" placeholder=\"Code envoyé par mail\" required=\"required\"/>
                            </div>
                            <div class=\"field btn-submit\">
                                <div class=\"btn-layer\"></div>
                                <input type=\"submit\" value=\"Vérifier\"/>
                            </div>\n";
                            }
                            else if($errorNum) {
                                echo "<div class=\"field\">
                                <input type=\"text\" name=\"nombre\" placeholder=\"Code envoyé par mail\" required=\"required\"/>
                            </div>
                            <div class=\"text-danger\"><small>Le code ne correspond pas</small></div>\n";
                            echo "<div class=\"field btn-submit\">
                                <div class=\"btn-layer\"></div>
                                <input type=\"submit\" value=\"Vérifier\"/>
                            </div>\n";
                            }
                            else {
                                echo
                            "<div class=\"field\">
                                <input type=\"email\" name=\"email\" placeholder=\"Adresse e-mail\" required=\"required\"/>
                            </div>";
                            if($error) {
                                echo "<div class=\"text-danger\"><small>Adresse e-mail incorrecte</small></div>\n";
                            }
                            echo "<div class=\"field btn-submit\">
                                <div class=\"btn-layer\"></div>
                                <input type=\"submit\" value=\"Récupérer\"/>
                            </div>
                            <div class=\"signup-link\">
                                Pas inscrit ? <a href=\"connexion.html\">S'inscrire maintenant</a>
                            </div>\n";
                            }
                            ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
        <script src="script.min.js"></script>
    </body>
</html>