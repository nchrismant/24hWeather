<?php
namespace Météo\User;

use PDO;
use Météo\User\User;
use Météo\User\Exception\ForbiddenException;

class Auth {

    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Retourne les informations concernant un utilisateur.
     *
     * @return User
     */
    public function user() : User
    {
        $id = $_SESSION['auth'] ?? null;
        if($id === null) {
            return null;
        }
        $query = $this->pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
        $query->execute([$id]);
        $query->setFetchMode(PDO::FETCH_CLASS, User::class);
        $user = $query->fetch();
        return $user ?: null;
    }
        
    /**
     * Vérifie si une session existe et en démarre une si il n'y en a pas.
     *
     * @return void
     */
    public static function check() : void
    {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if(!isset($_SESSION['auth'])) {
            throw new ForbiddenException();
        }
    }
}
?>