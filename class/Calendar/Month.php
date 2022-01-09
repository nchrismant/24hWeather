<?php

namespace Météo\Calendar;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class Month {

    public $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

    private $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    
    public $month;
    
    public $year;

    public function __construct(?int $month = null, ?int $year = null)
    {
        if($month === null) {
            $month = intval(date('m'));
        }

        if($year === null) {
            $year = intval(date('Y'));
        }

        if($month < 1 || $month > 12) {
            throw new \Exception("Le mois $month n'est pas valide");
        }

        if($year < 1970) {
            throw new \Exception("L'année est inférieure à 1970");
        }

        $this->month = $month;
        $this->year = $year;
    }
    
    /**
     * Récupère la date du 1er jour du mois.
     *
     * @return DateTimeImmutable
     */
    public function getStartingDay() : DateTimeImmutable
    {
        return new DateTimeImmutable("{$this->year}-{$this->month}-01");
    }

    public function __toString()
    {
        return $this->months[$this->month - 1] . ' ' . $this->year;
    }
    
    /**
     * Récupère le nombre de semaines dans un mois.
     *
     * @return int
     */
    public function getWeeks() : int
    {
        $start = $this->getStartingDay();
        $end = $start->modify('+1 month -1 day');
        $startWeek = intval($start->format('W'));
        $endWeek = intval($end->format('W'));
        if($endWeek === 1) {
            $endWeek = intval($end->modify('- 7 days')->format('W')) + 1;
        }
        $weeks = $endWeek - $startWeek + 1;
        if($weeks < 0) {
            $weeks = intval($end->format('W'));
        }

        return $weeks;
    }
    
    /**
     * Regarde si la date est dans le mois actuel.
     *
     * @param  DateTimeInterface $date
     * @return bool
     */
    public function withinMonth(DateTimeInterface $date) : bool
    {
        return $this->getStartingDay()->format('Y-m') === $date->format('Y-m');
    }
    
    /**
     * Retourne le prochain mois.
     *
     * @return Month
     */
    public function nextMonth() : Month
    {
        $month = $this->month + 1;
        $year = $this->year;
        if ($month > 12) {
            $month = 1;
            $year += 1;
        }

        return new Month($month, $year);
    }
    
    /**
     * Retourne le pécédent mois.
     *
     * @return Month
     */
    public function previousMonth() : Month
    {
        $month = $this->month - 1;
        $year = $this->year;
        if ($month < 1) {
            $month = 12;
            $year -= 1;
        }
        
        return new Month($month, $year);
    }

}
?>