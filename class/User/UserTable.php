<?php
namespace Météo\User;

use PDO;
use Météo\Table\Exception\NotFoundException;
use Météo\Table\Table;
use Météo\User\User;

final class UserTable extends Table {

    protected $table = "utilisateurs";
    protected $class = User::class;
    
    /**
     * Récupère les informations d'un utilisateur via son nom d'utilisateur.
     *
     * @param  string $username Le nom d'utilisateur
     * @return User
     */
    public function findbyUsername(string $username) : User
    {
        $query = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE username = :username');
        $query->execute(['username' => $username]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $result = $query->fetch();
        if($result === false) {
            throw new NotFoundException($this->table, $username);
        }
        return $result;
    }
    
    /**
     * Récupère les informations d'un utilisateur via son mail.
     *
     * @param  string $mail Mail de l'utilisateur
     * @return User
     */
    public function findbyMail(string $mail) : User
    {
        $query = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE mail = :mail');
        $query->execute(['mail' => $mail]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $result = $query->fetch();
        if($result === false) {
            throw new NotFoundException($this->table, $mail);
        }
        return $result;
    }
    
    /**
     * Ajoute un utilisateur dans la table utilisateurs.
     *
     * @param  User $user Données de l'utilisateur
     * @return void
     */
    public function createUser(User $user) : void
    {
        $id = $this->create([
            'username' => $user->getUsername(),
            'mail' => $user->getMail(),
            'password' => $user->getPassword(),
            'inscription_date' => $user->getInscription_date()->format('Y-m-d H:i:s'),
            'code' => $user->getCode()
        ]);
        $user->setId($id);
    }
    
    /**
     * Modifie un utilisateur dans la table utilisateurs.
     *
     * @param  User $user Données de l'utilisateur
     * @return void
     */
    public function updateUser(User $user) : void
    {
        $this->update([
            'username' => $user->getUsername(),
            'mail' => $user->getMail(),
            'password' => $user->getPassword(),
            'inscription_date' => $user->getInscription_date()->format('Y-m-d H:i:s'),
            'code' => $user->getCode(),
            'activate' => $user->getActivate(),
            'recuperation' => $user->getRecuperation(),
        ], $user->getId());
    }
    
    /**
     * Modifie les informations du profil d'un utilisateur dans la table utilisateurs.
     *
     * @param  User $user Données de l'utilisateur
     * @return void
     */
    public function updateUserProfile(User $user) : void
    {
        $this->update([
            'username' => $user->getUsername(),
            'mail' => $user->getMail(),
            'nom' => $user->getNom(),
            'adresse' => $user->getAdresse(),
            'telephone' => $user->getTelephone(),
            'description' => $user->getDescription()
        ], $user->getId());
    }
}
?>