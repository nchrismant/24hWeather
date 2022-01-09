<?php
require_once "vendor/autoload.php";

use Météo\Connection;
use Météo\Table\Exception\NotFoundException;
use Météo\User\Auth;
use Météo\User\Exception\ForbiddenException;
use Météo\User\UserImg;
use Météo\User\UserImgTable;

$pdo = Connection::getPDO();
$auth = new Auth($pdo);
$userImgTable = new UserImgTable($pdo);

try {
    $auth->check();
} catch (ForbiddenException $e) {
    header('Location: connexion.html?forbid=1');
    exit();
}

if(isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    $fileType = $_FILES['file']['type'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array('jpg', 'jpeg', 'png');

    if(in_array($fileActualExt, $allowed)) {
        if($fileError === 0) {
            if($fileSize < 1000000) {
                $fileNameNew = "profile".$_SESSION['auth'].".".$fileActualExt;
                $fileDestination = "uploads/".$fileNameNew;
                if(move_uploaded_file($fileTmpName, $fileDestination)) {
                    if($userImgTable->exists('id', $_SESSION['auth']) === false) {
                        $userImg = new UserImg();
                        $userImg->setImg_name($fileName);
                        $userImg->setImg($fileDestination);
                        $userImg->setId($_SESSION['auth']);
                        $userImgTable = new UserImgTable($pdo);
                        $userImgTable->createImg($userImg);
                    } else {
                        try {
                            $userImg = $userImgTable->find($_SESSION['auth'], 'id');
                        } catch (NotFoundException $e) {
                            header('Location: 404.php');
                            exit();
                        } catch (\Error $e) {
                            header('Location: 404.php');
                            exit();
                        }
                        $userImg->setImg_name($fileName);
                        $userImg->setImg($fileDestination);
                        $userImgTable->updateImg($userImg);
                    }
                    header('Location: modifier-profil.html?upload=1');
                    exit();
                }
            } else {
                echo "<p>Votre fichier est trop gros !</p>";
            }
        } else {
            echo "<p>Il y a eu une erreur.</p>";
        }
    } else {
        echo "<p>Vous ne pouvez pas ajouter un fichier de ce type !</p>";
    }
} else {
    header('Location: 404.php');
    exit();
}
?>