<?php

require_once "vendor/autoload.php";

use Météo\Connection;
use Météo\User\Auth;
use Météo\User\Exception\ForbiddenException;
use Météo\User\UserImgTable;
use Météo\User\UserTable;
use Météo\User\UserValidator;
use Météo\Validator;

$pdo = Connection::getPDO();
$auth = new Auth($pdo);
$table = new UserTable($pdo);
$userImgTable = new UserImgTable($pdo);

try {
    $auth->check();
    $user = $auth->user();
} catch (ForbiddenException $e) {
    header('Location: connexion.html?error=1');
    exit();
}

$userImg = $userImgTable->getUserImg($_SESSION['auth']);

$data = [];
$errors = [];
if(!empty($_POST)) {
    $data = $_POST;
    if(!empty($_POST['username'])) {
        $validator = new Validator(['username' => $_POST['username']]);
        if($validator->validate('username', 'existUser', new UserTable($pdo))) {
            $user->setUsername(htmlspecialchars($_POST['username']));
        } else {
            $errors['username'] = "Ce nom d'utilisateur est déja utilisé.";
        }
    }
    if(!empty($_POST['nom'])) {
        $user->setNom(htmlspecialchars($_POST['nom']));
    }
    if(!empty($_POST['mail'])) {
        $validator = new Validator(['mail' => $_POST['mail']]);
        if($validator->validate('mail', 'existMail', new UserTable($pdo))) {
            if($validator->validate('mail', 'isMail')) {
                $user->setMail(htmlspecialchars($_POST['mail']));
            }
            else {
                $errors['mail'] = "L'adresse mail ne semble pas valide";
            }            
        } else {
            $errors['mail'] = "Cette adresse e-mail est déja utilisée.";
        }
    }
    if(!empty($_POST['tel'])) {
        $validator = new Validator(['tel' => $_POST['tel']]);
        if(preg_match('/^\d+$/', $_POST['tel'])) {
            if($validator->validate('tel', 'minLength', 10) && $validator->validate('tel', 'maxLength', 10)) {
                if($validator->validate('tel', 'existTel', new UserTable($pdo))) {
                    $user->setTelephone(htmlspecialchars($_POST['tel']));
                } else {
                    $errors['tel'] = "Ce numéro de téléphone est déja utilisé.";
                }
            } else {
                $errors['tel'] = "Le numéro doit être composé de 10 chiffres";
            }
        } else {
            $errors['tel'] = "Le numéro ne doit contenir que des chiffres";
        }            
    }
    if(!empty($_POST['adresse'])) {
        $user->setAdresse(htmlspecialchars($_POST['adresse']));
    }
    if(!empty($_POST['description'])) {
        $user->setDescription(htmlspecialchars($_POST['description']));
    }
    if(empty($errors)) {
        $table->updateUserProfile($user);
        header('Location: profil.php');
        exit();
    }    
}

$title = "Éditer mon profil - 24h Weather";
$description = "Modifiez votre profil de la façon dont vous avez envie !"; 
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
        <div class="back">
            <div id="back" class="arrow arrow--left">
                <span></span>
            </div>
        </div>
        <section id="wrapper">
            <div class="content-tab">
                <!-- Tab links -->
                <div class="tabs">
                    <a href="profil.php" id="profil" class="tablinks active" data-title="Profil"><span data-title="Profil">Mon profil</span></a>
                    <a href="profil.php" id="calendrier" class="tablinks" data-title="Calendrier"><span data-title="Calendrier">Mon calendrier</span></a>
                    <a href="profil.php" id="préférences" class="tablinks" data-title="Préférences"><span data-title="Préférences">Mes préférences</span></a>
                    <a href="profil.php" id="paramètres" class="tablinks" data-title="Paramètres"><span data-title="Paramètres">Paramètres</span></a>
                    <form class="formlinks" action="logout.php" method="POST">
                        <button class="btn-deco" type="submit" data-title="Déconnexion"><span data-title="Déconnexion">Déconnexion</span></button>
                    </form>
                </div>
            
                <!-- Tab content -->
                <div class="wrapper_tabcontent">
                    <div id="Profil" class="tabcontent active">
                        <h3>Profil</h3>
                        <div class="container">
                            <div class="main-body">
                            <form id="file-form" action="upload.php" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="file" class="sr-only">Changer l'avatar</label>
                                    <input type="file" id="file" name="file" required="required"/>
                                </div>
                            </form>
                                <form action="#" method="POST">                  
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
                                                            <div class="h4">
                                                                <input type="text" class="form-control form-profile <?php if(isset($errors['username'])) { echo "form-error"; } ?>" name="username" size="10" placeholder="<?php echo $user->getUsername(); ?>" value="<?php if (isset($data['username'])) { echo htmlspecialchars($data['username']); } ?>"/>
                                                            </div>
                                                            <?php
                                                            if(isset($errors['username'])) {
                                                                echo "<div class=\"text-danger\"><small>" . $errors['username'] . "</small></div>\n";
                                                            }
                                                            ?>                                                            
                                                            <p class="text-secondary mb-1"><textarea class="form-control form-profile" name="description" rows="2" cols="22" placeholder="<?php if($user->getDescription() === NULL) { echo "Décrivez vous"; } else { echo $user->getDescription(); } ?>"><?php if (isset($data['description'])) { echo htmlspecialchars($data['description']); } ?></textarea></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <h6 class="mb-0">Nom complet</h6>
                                                        </div>
                                                        <div class="col-sm-9 text-secondary">
                                                            <input type="text" name="nom" class="form-control form-profile" placeholder="<?php if($user->getNom() === NULL) { echo "Non renseigné"; } else { echo $user->getNom(); }?>" value="<?php if (isset($data['nom'])) { echo htmlspecialchars($data['nom']); } ?>"/>
                                                        </div>
                                                    </div>
                                                    <hr/>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <h6 class="mb-0">Adresse mail</h6>
                                                        </div>
                                                        <div class="col-sm-9 text-secondary">
                                                            <input type="text" name="mail" class="form-control form-profile <?php if(isset($errors['mail'])) { echo "form-error"; } ?>" placeholder="<?php if($user->getMail() === NULL) { echo "Non renseigné"; } else { echo $user->getMail(); }?>" value="<?php if (isset($data['mail'])) { echo htmlspecialchars($data['mail']); } ?>"/>
                                                            <?php
                                                            if(isset($errors['mail'])) {
                                                                echo "<div class=\"text-danger\"><small>" . $errors['mail'] . "</small></div>\n";
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <hr/>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <h6 class="mb-0">Téléphone</h6>
                                                        </div>
                                                        <div class="col-sm-9 text-secondary">
                                                            <input type="text" name="tel" class="form-control form-profile <?php if(isset($errors['tel'])) { echo "form-error"; } ?>" placeholder="<?php if($user->getTelephone() === NULL) { echo "Non renseigné"; } else { echo $user->getTelephone(); }?>" value="<?php if (isset($data['tel'])) { echo htmlspecialchars($data['tel']); } ?>"/>
                                                            <?php
                                                            if(isset($errors['tel'])) {
                                                                echo "<div class=\"text-danger\"><small>" . $errors['tel'] . "</small></div>\n";
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
                                                            <input type="text" name="adresse" class="form-control form-profile" placeholder="<?php if($user->getAdresse() === NULL) { echo "Non renseigné"; } else { echo $user->getAdresse(); }?>" value="<?php if (isset($data['adresse'])) { echo htmlspecialchars($data['adresse']); } ?>"/>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-3"></div>
                                                        <div class="col-sm-9 text-secondary">
                                                            <button type="submit" class="profile-btn btn">Sauvegarder</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
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