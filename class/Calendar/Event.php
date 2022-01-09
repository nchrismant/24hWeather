<?php

namespace Météo\Calendar;

use DateTime;
use DateTimeImmutable;

class Event {
    
    private $id_event;

    private $name;

    private $motif;

    private $start;

    private $end;

    private $date_rappel;

    private $id_ville;

    private $id;


    /**
     * Get the value of id_event
     */ 
    public function getId_event()
    {
        return $this->id_event;
    }

    /**
     * Set the value of id_event
     *
     * @return  self
     */ 
    public function setId_event($id_event)
    {
        $this->id_event = $id_event;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of start
     */ 
    public function getStart()
    {
        return new DateTimeImmutable($this->start);
    }

    /**
     * Set the value of start
     *
     * @return  self
     */ 
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get the value of end
     */ 
    public function getEnd()
    {
        return new DateTimeImmutable($this->end);
    }

    /**
     * Set the value of end
     *
     * @return  self
     */ 
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get the value of motif
     */ 
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * Set the value of motif
     *
     * @return  self
     */ 
    public function setMotif($motif)
    {
        $this->motif = $motif;

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
     * Get the value of date_rappel
     */ 
    public function getDate_rappel()
    {
        return $this->date_rappel;
    }

    /**
     * Set the value of date_rappel
     *
     * @return  self
     */ 
    public function setDate_rappel($date_rappel)
    {
        $this->date_rappel = $date_rappel;

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
}

?>