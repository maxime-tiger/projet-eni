<?php


use App\Entity\Campus;
use App\Entity\Sortie;
use DateTime;
use App\Entity\Participant
;
class Filters
{
    /**
     * @var string
     */
    public $text = '';
    /**
     * @var Campus
     */
    public $campus;
    /**
     * @var Participant
     */
    public $organizer;
    /**
     * @var Participant
     */
    public $subscribed;
    /**
     * @var Participant
     */
    public $notSubscribed;
    /**
     * @var Event
     */
    public $passedEvents;
    /**
     * @var DateTime
     */
    public $dateStart;
    /**
     * @var DateTime
     */
    public $dateEnd;
}