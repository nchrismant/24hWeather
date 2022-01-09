<?php
namespace Météo;

use Météo\Table\Table;
use PDO;

final class FavorisTable extends Table {

    protected $table = "favoris";
    protected $class = Favoris::class;
    
    /**
     * Récupère les favoris d'un utilisateur.
     *
     * @param  int $id Id de l'utilisateur
     * @return array
     */
    public function getUserFavoris(int $id) : array
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $result = $query->fetchAll();
        return $result;
    }
    
    /**
     * Ajoute un favoris pour un utilisateur.
     *
     * @param  Favoris $favoris Données du favoris
     * @return void
     */
    public function createFavoris(Favoris $favoris) : void
    {
        $id_fav = $this->create([
            'id_ville' => $favoris->getId_ville(),
            'pays' => $favoris->getPays(),
            'ville' => $favoris->getVille(),
            'latitude' => $favoris->getLatitude(),
            'longitude' => $favoris->getLongitude(),
            'id' => $favoris->getId()
        ]);
        $favoris->setId_fav($id_fav);
    }
    
    /**
     * Modifie un favoris d'un utilisateur.
     *
     * @param  Favoris $favoris Données du favoris
     * @return void
     */
    public function updateFavoris(Favoris $favoris) : void
    {
        $this->update([
            'id_ville' => $favoris->getId_ville(),
            'pays' => $favoris->getPays(),
            'ville' => $favoris->getVille(),
            'latitude' => $favoris->getLatitude(),
            'longitude' => $favoris->getLongitude()
        ], $favoris->getId_fav(), 'id_fav');
    }

}
?>