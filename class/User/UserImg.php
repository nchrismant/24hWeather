<?php
namespace Météo\User;

class UserImg {
    private $id_img;

    private $id;

    private $img_name;

    private $img;    

    /**
     * Get the value of id_img
     */ 
    public function getId_img()
    {
        return $this->id_img;
    }

    /**
     * Set the value of id_img
     *
     * @return  self
     */ 
    public function setId_img($id_img)
    {
        $this->id_img = $id_img;

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

    /**
     * Get the value of img_name
     */ 
    public function getImg_name()
    {
        return $this->img_name;
    }

    /**
     * Set the value of img_name
     *
     * @return  self
     */ 
    public function setImg_name($img_name)
    {
        $this->img_name = $img_name;

        return $this;
    }

    /**
     * Get the value of img
     */ 
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set the value of img
     *
     * @return  self
     */ 
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }
}
?>