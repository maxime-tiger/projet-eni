<?php

namespace App\Filter;

use App\Entity\Campus;
use App\Entity\Sortie;
use DateTime;
use App\Entity\Participant;


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
    public $organisateur;

   
    /** 
     * @var Sortie
     */
    public $passedEvents;
    /**
     * @var DateTime
     */
    public $dateHeureDebut;
 
}