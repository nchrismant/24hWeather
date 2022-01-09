<?php
namespace Météo\Table;

use \PDO;
use Météo\Table\Exception\NotFoundException;

abstract class Table {

    protected $pdo;
    protected $table = null;
    protected $class = null;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * Rècupère les informations d'une ligne d'une table.
     *
     * @param  int $id Id de la ligne
     * @param  string $primaryName Nom de la clé primaire
     
     */
    public function find (int $id, string $primaryName="id")
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE $primaryName = :id");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $result = $query->fetch();
        if($result === false) {
            throw new NotFoundException($this->table, $id);
        }
        return $result;
    }
    
    /**
     * Vérifie l'existence d'un champ dans une table.
     *
     * @param  string $field Le champ recherché
     * @param  mixed $value La valeur recherchée
     * @param  string $primaryName Le nom de la clé primaire
     * @param  mixed $except
     * @return bool
     */
    public function exists (string $field, $value, string $primaryName="id", ?int $except = null) : bool
    {
        $sql = "SELECT COUNT($primaryName) FROM {$this->table} WHERE $field = ?";
        $params = [$value];
        if ($except !== null) {
            $sql .= " AND id != ?";
            $params[] = $except;
        }
        $query = $this->pdo->prepare($sql);
        $query->execute($params);
        return (int)$query->fetch(PDO::FETCH_NUM)[0] > 0;
    }
    
    /**
     * Vérifie l'existence d'un champ dans une table avec 2 conditions.
     *
     * @param  string $field Le champ recherché
     * @param  string $field2 Le 2eme champ (clé étrangère)
     * @param  mixed $value La valeur recherchée
     * @param  mixed $value2 La 2ème valeur (clé étrangère)
     * @param  string $primaryName Le nom de la clé primaire
     * @return bool
     */
    public function existsWith2Conditions (string $field, string $field2, $value, $value2, string $primaryName="id") : bool
    {
        $sql = "SELECT COUNT($primaryName) FROM {$this->table} WHERE $field = ? AND $field2 = ?";
        $query = $this->pdo->prepare($sql);
        $query->execute(array($value, $value2));
        return (int)$query->fetch(PDO::FETCH_NUM)[0] > 0;
    }
    
    /**
     * Compte le nombre de ligne dans une table.
     *
     * @param  mixed $value Valeur recherchée
     * @param  string $field Champ recherché
     * @return int
     */
    public function count ($value, string $field="id") : int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE $field = ?";
        $query = $this->pdo->prepare($sql);
        $query->execute(array($value));
        return (int)$query->fetch(PDO::FETCH_NUM)[0];
    }
    
    /**
     * Retourne toutes les valeurs du table.
     *
     * @return array
     */
    public function all () : array 
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->pdo->query($sql, PDO::FETCH_CLASS, $this->class)->fetchAll();
    }
    
    /**
     * Supprime une ligne dans une table.
     *
     * @param  int $id ID de la ligne dans la table
     * @param  string $primaryName Nom de la clé primaire
     * @return void
     */
    public function delete (int $id, string $primaryName="id") : void
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE $primaryName = ?");
        $ok = $query->execute([$id]);
        if ($ok === false) {
            throw new \Exception("Impossible de supprimer l'enregistrement #$id dans la table {$this->table}");
        }
    }
    
    /**
     * Supprime une ligne dans une table avec 2 conditions.
     *
     * @param  int $id ID de la ligne dans la table
     * @param  int $id2 ID (clé étrangère) de la ligne dans la table
     * @param  string $primaryName Nom de la clé primaire
     * @param  string $primaryName2 Nom de la clé primaire (clé étrangère)
     * @return void
     */
    public function deleteWith2Conditions (int $id, int $id2, string $primaryName, string $primaryName2) : void
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE $primaryName = ? AND $primaryName2 = ?");
        $ok = $query->execute(array($id, $id2));
        if ($ok === false) {
            throw new \Exception("Impossible de supprimer l'enregistrement #$id dans la table {$this->table}");
        }
    }
        
    /**
     * Créer un enregistrement dans une table.
     *
     * @param  array $data Données à insérer
     * @return int
     */
    public function create (array $data) : int
    {
        $sqlFields = [];
        foreach($data as $key => $value) {
            $sqlFields[] = "$key = :$key";
        }
        $query = $this->pdo->prepare("INSERT INTO {$this->table} SET " . implode(', ', $sqlFields));
        $ok = $query->execute($data);
        if ($ok === false) {
            throw new \Exception("Impossible de créer l'enregistrement dans la table {$this->table}");
        }
        return (int)$this->pdo->lastInsertId();
    }
    
    /**
     * Effectue une modification d'un enregistrement dans une table.
     *
     * @param  array $data Données à modifier
     * @param  int $id Id de l'élèment à modifier
     * @param  string $primaryName Nom de la clé primaire
     * @return void
     */
    public function update (array $data, int $id, string $primaryName="id") : void
    {
        $sqlFields = [];
        foreach($data as $key => $value) {
            $sqlFields[] = "$key = :$key";
        }
        $query = $this->pdo->prepare("UPDATE {$this->table} SET " . implode(', ', $sqlFields) . " WHERE $primaryName = :id");
        $ok = $query->execute(array_merge($data, ['id' => $id]));
        if ($ok === false) {
            throw new \Exception("Impossible de modifier l'enregistrement dans la table {$this->table}");
        }
    }
}
?>