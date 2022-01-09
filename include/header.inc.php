<?php
use Météo\Connection;
use Météo\User\Auth;
use Météo\User\UserImgTable;

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "vendor/autoload.php";
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
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
        <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
        <link rel="icon" href="./images/favicon.ico"/>
    </head>
    <body>
        <header>
            <?php
            if(isset($_SESSION['auth'])) {
                $pdo = Connection::getPDO();
                $auth = new Auth($pdo);
                $user = $auth->user();
                $userImgTable = new UserImgTable($pdo);
                $userImg = $userImgTable->getUserImg($_SESSION['auth']);
            }
            $page = basename($_SERVER["PHP_SELF"]);
            if($page == "index.php") {
                echo "<nav class=\"nav-bar nav-bar-bg\">\n";
            }
            else {
                echo "<nav class=\"nav-bar\">\n";
            }
            ?>
                <label class="logo"><a href="index.php"><img id="logoimg" src="./images/logo.svg" alt="logo du Site"/></a></label>
                <div class="menu-btn">
                    <div class="menu-btn__burger"></div>
                </div>
                <ul class="menu" id="li-menu">
                    <li>
                        <a <?php if($page == 'index.php') { echo "class=\"active\" ";} ?>href="index.php">Accueil</a>
                    </li>
                    <li>
                        <a <?php if($page == 'meteo.php') { echo "class=\"active\" ";} ?>href="meteo.php">Méteo</a>
                    </li>
                    <li>
                        <a <?php if($page == 'world.php') { echo "class=\"active\" ";} ?>href="monde.html">Monde</a>
                    </li>
                    <?php
                    if(isset($_SESSION['auth'])) {
                        echo "<li>\n";
                        echo str_repeat("\t", 6); echo "<button id=\"pop-button\" ";
                        if($page == 'profil.php') { echo "class=\"active\" ";}
                        echo ">";
                        if($user) {
                            $utilisateur = $user->getUsername();
                        }
                        echo $utilisateur;
                        echo "&#x00A0;<i class=\"fas fa-user\"></i>";
                        echo "</button>\n";
                        echo str_repeat("\t", 5); echo "</li>\n";
                    }
                    else {
                        echo "<li>\n";
                        echo str_repeat("\t", 6); echo "<a ";
                        if($page == 'login.php') { echo "class=\"active\" ";}
                        echo "href=\"connexion.html\">Se Connecter\n";
                        echo str_repeat("\t", 6); echo "</a>\n";
                        echo str_repeat("\t", 5); echo "</li>\n";
                    }
                    ?>
                    <li>
                        <div id="toggle">
                            <i class="indicator"></i>
                            <div class="icon"></div>
                        </div>
                    </li>
                </ul>
            </nav>
            <?php
            if(isset($_SESSION['auth'])) {
                echo "<div class=\"popup\" id=\"popup-1\">
                <div class=\"overlay\"></div>
                <div class=\"contentpop\">
                    <div class=\"close-btn\">&#215;</div>
                    <div class=\"profile-user\">\n";
                    if(!empty($userImg)) {
                        echo str_repeat("\t", 6) . "<img src=\"{$userImg->getImg()}?".mt_rand()."\" alt=\"User\" class=\"rounded-circle\" width=\"150\"/>\n";
                    } else {
                        echo str_repeat("\t", 6) . "<img src=\"./images/avatar.png\" alt=\"User\" class=\"rounded-circle\" width=\"150\"/>\n";
                    }
                    echo str_repeat("\t", 5) . "</div>\n";                        
                    if($user) {
                        $utilisateur = $user->getUsername();
                        echo str_repeat("\t", 5); echo "<h2>".$utilisateur."</h2>\n";
                    }                        
                    echo str_repeat("\t", 5); echo "<ul>
                        <li>
                            <a href=\"profil.php\" class=\"link-profile\">Mon profil</a>
                        </li>
                        <li>
                            <form action=\"logout.php\" method=\"POST\">
                                <button type=\"submit\" class=\"link-profile\">Se déconnecter &#x00A0;<i class=\"fas fa-sign-out-alt\"></i></button>
                            </form>
                        </li>                    
                    </ul>
                    <p class='user-desc'>"; if($user->getDescription() === NULL) { echo "Décrivez vous"; } else { echo $user->getDescription(); } echo "</p>
                </div>
            </div>\n";
            }
            ?>
        </header>
        