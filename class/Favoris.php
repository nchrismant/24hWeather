<?php
namespace Météo;

class Favoris {

    private $id_fav;

    private $id_ville;

    private $pays;

    private $ville;

    private $latitude;

    private $longitude;

    private $id;
    
    /**
     * Get the value of id_fav
     */ 
    public function getId_fav()
    {
        return $this->id_fav;
    }

    /**
     * Set the value of id_fav
     *
     * @return  self
     */ 
    public function setId_fav($id_fav)
    {
        $this->id_fav = $id_fav;

        return $this;
    }

    /**
     * Get the value of id_ville
     */ 
    public function getId_ville()
    {
        return $this->id_ville;
    }

    /**
     * Set the value of id_ville
     *
     * @return  self
     */ 
    public function setId_ville($id_ville)
    {
        $this->id_ville = $id_ville;

        return $this;
    }

    /**
     * Get the value of pays
     */ 
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set the value of pays
     *
     * @return  self
     */ 
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get the value of ville
     */ 
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set the value of ville
     *
     * @return  self
     */ 
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get the value of latitude
     */ 
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set the value of latitude
     *
     * @return  self
     */ 
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get the value of longitude
     */ 
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set the value of longitude
     *
     * @return  self
     */ 
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}


?>