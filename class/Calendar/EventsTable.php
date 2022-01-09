<?php
namespace Météo\Calendar;

use DateTime;
use DateTimeInterface;
use Météo\Table\Table;
use PDO;

final class EventsTable extends Table {

    protected $table = "evenements";
    protected $class = Event::class;

    
    /**
     * Récupère les évènements d'un utilisateur.
     *
     * @param  DateTimeInterface $start Date de début
     * @param  DateTimeInterface $end Date de fin
     * @param  int $id Id de l'utilisateur
     * @return array
     */
    public function getEventsBetween(DateTimeInterface $start, DateTimeInterface $end, int $id) : array
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id AND start BETWEEN '{$start->format('Y-m-d 00:00:00')}' AND '{$end->format('Y-m-d 23:59:59')}' ORDER BY start ASC");
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        $result = $query->fetchAll();
        return $result;
    }
    
    /**
     * Récupère les évènements d'un utlisateur par jour.
     *
     * @param  DateTimeInterface $start Date de début
     * @param  DateTimeInterface $end Date de fin
     * @param  int $id Id de l'utilisateur
     * @return array
     */
    public function getEventsBetweenByDay(DateTimeInterface $start, DateTimeInterface $end, int $id) : array
    {
        $events = $this->getEventsBetween($start, $end, $id);
        $days = [];
        foreach($events as $event) {
            $date = $event->getStart()->format('Y-m-d');
            if(!isset($days[$date])) {
                $days[$date] = [$event];
            }
            else {
                $days[$date][] = $event;
            }
        }
        return $days;
    }
    
    /**
     * Récupère tous les évènements qui ont un rappel prédéfini avec l'id de l'évènement, la date, l'id de la ville, et le mail de l'utilisateur qui à défini ce rappel.
     *
     * @return array
     */
    public function getEventswithRappelandMail() : array
    {
        $query = $this->pdo->prepare("SELECT id_event,date_rappel,id_ville,mail FROM {$this->table} NATURAL JOIN utilisateurs WHERE date_rappel IS NOT NULL");
        $query->execute();
        $result = $query->fetchAll();
        return $result;
    }
    
    /**
     * Créer un évènement pour un utilisateur
     *
     * @param  Event $event L'évenement
     * @return void
     */
    public function createEvent(Event $event) : void
    {
        $id = $this->create([
            'name' => $event->getName(),
            'motif' => $event->getMotif(),
            'start' => $event->getStart()->format('Y-m-d H:i:s'),
            'end' => $event->getEnd()->format('Y-m-d H:i:s'),
            'id_ville' => $event->getId_ville(),
            'date_rappel' => $event->getDate_rappel(),            
            'id' => $event->getId()
        ]);
        $event->setId_event($id);
    }
    
    /**
     * Effectue une modification d'un évènement.
     *
     * @param  Event $event L'évènement
     * @return void
     */
    public function updateEvent(Event $event) : void
    {
        $this->update([
            'name' => $event->getName(),
            'motif' => $event->getMotif(),
            'start' => $event->getStart()->format('Y-m-d H:i:s'),
            'end' => $event->getEnd()->format('Y-m-d H:i:s'),
            'id_ville' => $event->getId_ville(),
            'date_rappel' => $event->getDate_rappel()
        ], $event->getId_event(), 'id_event');
    }

}
?>