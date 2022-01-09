<?php
require_once "vendor/autoload.php";

require_once "./class/Mail/PHPMailer/src/Exception.php";
require_once "./class/Mail/PHPMailer/src/PHPMailer.php";
require_once "./class/Mail/PHPMailer/src/SMTP.php";

use Météo\Connection;
use Météo\Mail\Mail;
use Météo\Table\Exception\NotFoundException;
use Météo\User\User;
use Météo\User\UserTable;
use Météo\User\UserValidator;
use PHPMailer\PHPMailer\PHPMailer;

$user = new User();
$error = false;
$newerror = false;

$phpmailer = new PHPMailer();
$mail = new Mail($phpmailer);

if(!empty($_POST)) {    
    if(!empty($_POST['username']) && !empty($_POST['password'])) {
        $data = $_POST;
        $user->setUsername(htmlspecialchars($_POST['username']));
        $pdo = Connection::getPDO();
        $table = new UserTable($pdo);
        try {
            $u = $table->findbyUsername(htmlspecialchars($_POST['username']));
            if((password_verify(htmlspecialchars($_POST['password']), $u->getPassword()) === true) && $u->getActivate() === 1) {
                session_start();
                $_SESSION['auth'] = $u->getId();
                header("Location: index.php?login=1");
                exit();
            }    
        } catch (NotFoundException $e) {
        }
        $error = true;
    }
    else if(!empty($_POST['newuser']) && !empty($_POST['newmail']) && !empty($_POST['newpass']) && !empty($_POST['confnewpass'])) {
        $data = $_POST;
        $validator = new UserValidator();
        $errors = $validator->validates($data);
        if(empty($errors)) {
            $pdo = Connection::getPDO();
            $table = new UserTable($pdo);
            $password = password_hash(htmlspecialchars($_POST['newpass']), PASSWORD_BCRYPT);
            if(password_verify(htmlspecialchars($_POST['confnewpass']), $password)) {
                $user->setUsername(htmlspecialchars($_POST['newuser']));
                $user->setMail(htmlspecialchars($_POST['newmail']));
                $user->setPassword($password);
                $user->setCode(md5(hash('whirlpool',md5(substr(md5(date('Y-h-s-i')), 0, -24)))));
                $table->createUser($user);
                $phpmailer = new PHPMailer();
                $mail = new Mail($phpmailer);
                
                $body = "<!DOCTYPE html>
                <html lang=\"fr\">
                    <body>
                        <h1>Confirmation mail pour le Site 24hWeather</h1>
                        <img src=\"cid:logo_site\">
                        <p>Activez votre compte en validant votre email <a href=\"https://24hweather.alwaysdata.net/activation.php?code={$user->getCode()}&id={$user->getId()}\">ici</a>.</p>
                    </body>
                </html>";
                $mail->sendMail($body, '24hweather@gmail.com', '24hWeather', $user->getMail(), 'Confirmation mail 24hWeather');
                header("Location: connexion.html?active=1");
                exit();
            }
            $newerror = true;
        }
    }
}

$title = "Se connecter - 24h Weather";
$description = "Connectez vous ou créez un compte afin d'obtenir des privilèges !"; 
$keywords = "weather meteo 24/24 7/7";
require_once "./include/header.inc.php";
?>
        <div class="grid-center">
            <div class="wrapper">
                <div class="title-text">
                    <div class="title login <?php if(isset($errors['newmail']) || isset($errors['newuser']) || $newerror) { echo "login-error"; } ?>" >
                        Formulaire de connexion
                    </div>
                    <div class="title signup">
                        Formulaire d'inscription
                    </div>
                </div>
                <div class="form-container">
                    <div class="slide-controls">
                        <input type="radio" name="slide" id="login" <?php if(isset($errors['newmail']) || isset($errors['newuser']) || $newerror) { echo ""; } else { echo "checked='checked'";} ?>/>
                        <input type="radio" name="slide" id="signup" <?php if(isset($errors['newmail']) || isset($errors['newuser']) || $newerror) { echo "checked='checked'"; } ?> />
                        <label for="login" class="slide login">Connexion</label>
                        <label for="signup" class="slide signup">Inscription</label>
                        <div class="slider-tab"></div>
                    </div>
                    <div class="form-inner">
                        <form action="#" class="login <?php if(isset($errors['newmail']) || isset($errors['newuser']) || $newerror) { echo "login-error"; } ?>" method="POST">
                            <?php
                            if(isset($_GET['active']) && $newerror === false) {
                                echo "<div class='alert alert-success'>Activez votre compte par le biais du mail qui vous à été envoyé !</div>\n";
                            }
                            ?>
                            <div class="field">
                                <input type="text" name="username" placeholder="Identifiant" required="required" value="<?php if (isset($data['username'])) { echo htmlspecialchars($data['username']); } else if(isset($_GET['username'])) { echo htmlspecialchars($_GET['username']); } ?>"/>
                            </div>
                            <div class="field">
                                <input type="password" name="password" placeholder="Mot de passe" required="required"/>
                            </div>
                            <?php
                            if($error) {
                                echo "<div class=\"text-danger\"><small>Identifiant ou mot de passe incorrect</small></div>\n";
                            }
                            ?>
                            <div class="pass-link">
                                <a href="recuperation.php">Mot de passe oublié ?</a>
                            </div>
                            <div class="field btn-submit">
                                <div class="btn-layer"></div>
                                <input type="submit" value="Se connecter"/>
                            </div>
                            <div class="signup-link">
                                Pas inscrit ? <a href="">S'inscrire maintenant</a>
                            </div>
                        </form>
                        <form action="#" class="signup" method="POST">
                            <div class="field">
                                <input type="text" id="newuser" <?php if(isset($errors['newuser'])) { echo "class='form-error'"; } ?> name="newuser" placeholder="Identifiant" required="required" value="<?php if (isset($data['newuser'])) { echo htmlspecialchars($data['newuser']); } ?>"/>
                            </div>
                            <div class="text-danger"><small id="response"></small></div>
                            <?php
                            if(isset($errors['newuser'])) {
                                echo "<div id='error-user' class=\"text-danger\"><small>" . $errors['newuser'] . "</small></div>\n";
                            }
                            ?>
                            <div class="field">
                                <input type="email" id="newmail" <?php if(isset($errors['newmail'])) { echo "class='form-error'"; } ?> name="newmail" placeholder="Adresse e-mail" required="required" value="<?php if (isset($data['newmail'])) { echo htmlspecialchars($data['newmail']); } ?>"/>
                            </div>
                            <div class="text-danger"><small id="responseMail"></small></div>
                            <?php
                            if(isset($errors['newmail'])) {
                                echo "<div id='error-mail' class=\"text-danger\"><small>" . $errors['newmail'] . "</small></div>\n";
                            }
                            ?>
                            <div class="field">
                                <input type="password" name="newpass" placeholder="Mot de passe" required="required"/>
                            </div>
                            <div class="field">
                                <input type="password" name="confnewpass" placeholder="Confirmer mot de passe" required="required"/>
                            </div>
                            <?php
                            if($newerror) {
                                echo "<div class=\"text-danger\"><small>Identifiant ou mot de passe incorrect</small></div>\n";
                            }
                            ?>
                            <div class="field btn-submit">
                                <div class="btn-layer"></div>
                                <input type="submit" value="S'inscrire"/>
                            </div>
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