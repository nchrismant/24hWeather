<?php
namespace Météo;

use DateTime;
use Météo\Table\Table;

class Validator {

    private $data;

    protected $errors = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    
    public function validates(array $data)
    {
        $this->errors = [];
        $this->data = $data;
        return $this->errors;
    }
    
    /**
     * Effectue une validation sur chacune des valeurs des champs donnés avec leurs méthodes de validation.
     *
     * @param  string $field Champ qui comporte la valeur
     * @param  string $method Méthode de validation du champ
     * @param  mixed $parameters Paramètres optionnels d'une méthode de validation
     * @return bool
     */
    public function validate(string $field, string $method, ...$parameters) : bool
    {
        if(empty($this->data[$field])) {
            $this->errors[$field] = "Le champ $field n'est pas rempli";
            return false;
        } 
        else {
            return call_user_func([$this, $method], $field, ...$parameters);
        }
    }
    
    /**
     * Vérifie que la valeur d'un champ à une taille plus grande que la valeur minimale définie.
     *
     * @param  string $field Champ qui comporte la valeur
     * @param  int $length Taille minimal
     * @return bool
     */
    public function minLength(string $field, int $length) : bool
    {
        if (mb_strlen($this->data[$field]) < $length) {
            $this->errors[$field] = "Le champs doit avoir plus de $length caractères";
            return false;
        }
        return true;
    }
    
    /**
     * Vérifie que la valeur d'un champ à une taille plus petite que la valeur maximale définie.
     *
     * @param  string $field Champ qui comporte la valeur
     * @param  int $length Taille maximale
     * @return bool
     */
    public function maxLength(string $field, int $length) : bool
    {
        if (mb_strlen($this->data[$field]) > $length) {
            $this->errors[$field] = "Le champs doit avoir moins de $length caractères";
            return false;
        }
        return true;
    }
    
    /**
     * Vérifie que la valeur d'un champ est une date au format : Y-m-d.
     *
     * @param  string $field Champ qui comporte la valeur
     * @return bool
     */
    public function date(string $field) : bool
    {
        if (DateTime::createFromFormat('Y-m-d', $this->data[$field]) === false) {
            $this->errors[$field] = "La date n'est pas valide";
            return false;
        }
        return true;
    }
    
    /**
     * Vérifie que la valeur d'un champ est un temps au format : H:i.
     *
     * @param  string $field Champ qui comporte la valeur
     * @return bool
     */
    public function time(string $field) : bool
    {
        if (DateTime::createFromFormat('H:i', $this->data[$field]) === false) {
            $this->errors[$field] = "La temps ne semble pas valide";
            return false;
        }
        return true;
    }
    
    /**
     * Vérifie que la valeur d'un champ est avant la valeur d'un autre champ en terme de date et heure.
     *
     * @param  string $startField Champ qui comporte la valeur qui doit être avant
     * @param  string $endField Champ qui comporte la valeur qui doit être apres
     * @return bool
     */
    public function beforeTime(string $startField, string $endField) : bool
    {
        if($this->time($startField) && $this->time($endField)) {
            $start = DateTime::createFromFormat('H:i', $this->data[$startField]);
            $end = DateTime::createFromFormat('H:i', $this->data[$endField]);
            if($start->getTimestamp() > $end->getTimestamp()) {
                $this->errors[$startField] = "Le temps doit être inférieur au temps de fin";
                return false;
            }
            return true;
        }
        return false;
    }
    
    /**
     * Vérifie que la valeur d'un champ est un mail.
     *
     * @param  string $field Champ qui comporte la valeur
     * @return bool
     */
    public function isMail(string $field) : bool
    {
        $mail_explode = explode("@",$this->data[$field]);
        if(!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL) && !checkdnsrr(array_pop($mail_explode),"MX")) {
            $this->errors[$field] = "L'adresse mail ne semble pas valide";
            return false;
        }
        return true;
    }
    
    /**
     * Vérifie que la valeur d'un champ qui comporte un mail existe déja dans une table de la base de données.
     *
     * @param  string $field Champ qui comporte la valeur
     * @param  Table $table Table où on veut vérifier l'existence du mail
     * @return bool
     */
    public function existMail(string $field, Table $table) : bool
    {
        if($table->exists('mail', $this->data[$field]) === true) {
            $this->errors[$field] = "Cette adresse e-mail est déja utilisée.";
            return false;
        }
        return true;
    }
    
    /**
     * Vérifie que la valeur d'un champ qui comporte un nom d'utilisateur existe déja dans une table de la base de données.
     *
     * @param  string $field Champ qui comporte la valeur
     * @param  Table $table Table où on veut vérifier l'existence du nom d'utilisateur
     * @return bool
     */
    public function existUser(string $field, Table $table) : bool
    {
        if($table->exists('username', $this->data[$field]) === true) {
            $this->errors[$field] = "Ce nom d'utilisateur est déja utilisé.";
            return false;
        }
        return true;
    }
    
    /**
     * Vérifie que la valeur d'un champ qui comporte un numéro de téléphone existe déja dans une table de la base de données.
     *
     * @param  string $field Champ qui comporte la valeur
     * @param  Table $table Table où on veut vérifier l'existence du numéro de téléphone
     * @return bool
     */
    public function existTel(string $field, Table $table) : bool
    {
        if($table->exists('telephone', $this->data[$field]) === true) {
            $this->errors[$field] = "Ce numéro de téléphone est déja utilisé.";
            return false;
        }
        return true;
    }

}
?>