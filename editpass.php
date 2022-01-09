<?php
require_once "vendor/autoload.php";

require_once "./class/Mail/PHPMailer/src/Exception.php";
require_once "./class/Mail/PHPMailer/src/PHPMailer.php";
require_once "./class/Mail/PHPMailer/src/SMTP.php";

use Météo\Connection;
use Météo\ForbiddenException;
use Météo\Mail\Mail;
use Météo\Table\Exception\NotFoundException;
use Météo\User\UserTable;
use PHPMailer\PHPMailer\PHPMailer;

$error = false;

$phpmailer = new PHPMailer();
$mail = new Mail($phpmailer);

if(!isset($_GET['id'])) {
    header('Location: connexion.html?forbid=1');
    exit();
}

$id = htmlspecialchars($_GET['id']);
if(!empty($_POST['change_pass']) && !empty($_POST['confchange_pass'])) {
    $pdo = Connection::getPDO();
    $table = new UserTable($pdo);
    try {
        $u = $table->find($id);
        $change_password = password_hash(htmlspecialchars($_POST['change_pass']), PASSWORD_BCRYPT);
        if(password_verify(htmlspecialchars($_POST['confchange_pass']), $change_password)) {
            $u->setPassword($change_password);
            $u->setRecuperation(null);
            $table->updateUser($u);
            header('Location: connexion.html');
            exit();
        }
    } catch (NotFoundException $e) {
    }
    $error = true;
}
    

$title = "Changement de mot de passe - 24h Weather";
$description = "Changez votre mot de passe afin de retrouver l'accès à votre compte !"; 
$keywords = "weather meteo 24/24 7/7";
require_once "./include/header.inc.php";
?>
        <div class="grid-center">
            <div class="wrapper">
                <div class="title-text">
                    <div class="title login">
                        Changement de mot de passe
                    </div>
                </div>
                <div class="form-container">
                    <div class="slide-controls">
                        <input type="radio" name="slide" id="login" checked="checked"/>
                        <label for="login" class="slide login slide-recup">Mot de passe</label>
                        <div class="slider-tab slide-recup"></div>
                    </div>
                    <div class="form-inner">
                        <form action="#" class="login" method="POST">
                            <div class="field">
                                <input type="password" name="change_pass" placeholder="Mot de passe" required="required"/>
                            </div>
                            <div class="field">
                                <input type="password" name="confchange_pass" placeholder="Confirmer mot de passe" required="required"/>
                            </div>
                            <div class="field btn-submit">
                                <div class="btn-layer"></div>
                                <input type="submit" value="Valider"/>
                            </div>
                            <?php
                            if($error) {
                                echo "<div class=\"text-danger\"><small>Le mot de passe doit être le même</small></div>\n";
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