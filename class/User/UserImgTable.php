<?php
namespace Météo\User;

use Météo\Table\Table;
use PDO;

final class UserImgTable extends Table {

    protected $table = "images_utilisateurs";
    protected $class = UserImg::class;
    
    /**
     * Récupère l'image de l'utilisateur.
     *
     * @param  int $id Id de l'utilisateur
     */
    public function getUserImg(int $id)
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $result = $query->fetch();
        return $result;
    }
    
    /**
     * Ajoute un enregistrement dans la table images utilisateur.
     *
     * @param  UserImg $userimg Données de l'image de l'utilisateur
     * @return void
     */
    public function createImg(UserImg $userimg) : void
    {
        $id_img = $this->create([
            'img_name' => $userimg->getImg_name(),
            'img' => $userimg->getImg(),
            'id' => $userimg->getId()
        ]);
        $userimg->setId_img($id_img);
    }
    
    /**
     * Modifie un eregistrement dans la table images utilisateur.
     *
     * @param  UserImg $userimg Données à modifier.
     * @return void
     */
    public function updateImg(UserImg $userimg) : void
    {
        $this->update([
            'img_name' => $userimg->getImg_name(),
            'img' => $userimg->getImg()
        ], $userimg->getId_img(), 'id_img');
    }

}
?>