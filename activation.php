<?php
require_once "vendor/autoload.php";

require_once "./class/Mail/PHPMailer/src/Exception.php";
require_once "./class/Mail/PHPMailer/src/PHPMailer.php";
require_once "./class/Mail/PHPMailer/src/SMTP.php";

use Météo\Connection;
use PHPMailer\PHPMailer\PHPMailer;
use Météo\Mail\Mail;
use Météo\User\UserTable;

if(!empty($_GET['code']) && !empty($_GET['id']) && isset($_GET['code'], $_GET['id'])) {
    $pdo = Connection::getPDO();
    $table = new UserTable($pdo);
    $code = htmlspecialchars($_GET['code']);
    $id = htmlspecialchars($_GET['id']);
    try {
        $u = $table->find($id);
        $inscriptionDateTime = $u->getInscription_date()->format('Y-m-d H:i:s');
        $expireDateTime = new DateTime($inscriptionDateTime);
        $expireDateTime->modify('+2 minutes');
        $expireDateTime->format('Y-m-d H:i:s');
        $currentDateTime = new DateTime();
        if($currentDateTime > $expireDateTime) {
            $expired = "Lien expiré";
            if($u->getActivate() !== 1) {
                $table->delete($id);
            }
        }
        else {  
            if($code === $u->getCode() && $u->getActivate() !== 1) {
                $u->setActivate(1);
                $table->updateUser($u);
                $phpmailer = new PHPMailer();
                $mail = new Mail($phpmailer);
                $body = "<!DOCTYPE html>
                    <html lang=\"fr\">
                        <body>
                            <h1>Inscription sur le Site 24hWeather</h1>
                            <img src=\"cid:logo_site\">
                            <h2>Bienvenue {$u->getUsername()} !</h2>
                            <p>Votre compte est maintenant activé !</p>
                            <p>Ici vous retouverez toutes les informations sur la météo en temps réel et au travers du monde !</p>
                            <p>Vous pouvez dès à présent vous <a href=\"https://24hweather.alwaysdata.net/connexion.html\">connecter</a> afin de modifier votre <a href=\"https://24hweather.alwaysdata.net/profil.php\">profil</a> et d'ajouter vos <a href=\"https://24hweather.alwaysdata.net/profil.php\">préférences</a>.</p>
                        </body>
                    </html>";
                $mail->sendMail($body, '24hweather@gmail.com', '24hWeather', $u->getMail(), 'Inscription 24hWeather');
                header("refresh:3;url=connexion.html?username=" . $u->getUsername());
            }
        }
    } catch (Exception $e) {
        $e->getMessage();
        header('Location: connexion.html');
        exit();
    }
} 
else {
    header('Location: connexion.html?forbid=1');
    exit();
}

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- méta-données -->
        <title>Activation compte - 24h Weather</title>
        <meta name="author" content="nathan"/>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous"/>
        <link type="text/css" rel="stylesheet" title="standard" href="styles.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
        <link rel="icon" href="./images/favicon.ico"/>  
    </head>
    <body class="svg-center">
        <?php
        if(isset($expired)) {
            echo "<div class=\"checked-center\">
                <svg class=\"cross__svg\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 52 52\">
                    <circle class=\"cross__circle\" cx=\"26\" cy=\"26\" r=\"25\" fill=\"none\"/>
                    <path class=\"cross__path cross__path--right\" fill=\"none\" d=\"M16,16 l20,20\" />
                    <path class=\"cross__path cross__path--right\" fill=\"none\" d=\"M16,36 l20,-20\" />
                    </svg>
                </div>            
                <div class=\"checked-text\">
                    {$expired}
                </div>\n";
        }
        else {
            echo "<div class=\"checked-center\">
            <svg id=\"successAnimation\" class=\"animated\" width=\"120\" height=\"120\" viewBox=\"0 0 70 70\">
                <path id=\"successAnimationResult\" fill=\"#D8D8D8\" d=\"M35,60 C21.1928813,60 10,48.8071187 10,35 C10,21.1928813 21.1928813,10 35,10 C48.8071187,10 60,21.1928813 60,35 C60,48.8071187 48.8071187,60 35,60 Z M23.6332378,33.2260427 L22.3667622,34.7739573 L34.1433655,44.40936 L47.776114,27.6305926 L46.223886,26.3694074 L33.8566345,41.59064 L23.6332378,33.2260427 Z\"/>
                <circle id=\"successAnimationCircle\" cx=\"35\" cy=\"35\" r=\"24\" stroke=\"#979797\" stroke-width=\"2\" stroke-linecap=\"round\" fill=\"transparent\"/>
                <polyline id=\"successAnimationCheck\" stroke=\"#979797\" stroke-width=\"2\" points=\"23 34 34 43 47 27\" fill=\"transparent\"/>
            </svg>
        </div>
        <div class=\"checked-text\">
            Votre compte a été activé !
        </div>\n";
        }
        ?>
    </body>
</html>