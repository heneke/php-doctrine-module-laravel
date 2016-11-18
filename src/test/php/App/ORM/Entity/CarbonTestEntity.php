<?php

namespace HHIT\Doctrine\App\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="carbon_test")
 */
class CarbonTestEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="te_id", type="integer")
     */
    public $id;

    /**
     * @ORM\Column(name="te_time", type="time")
     */
    public $time;

    /**
     * @ORM\Column(name="te_date", type="date")
     */
    public $date;

    /**
     * @ORM\Column(name="te_datetime", type="datetime")
     */
    public $datetime;

    /**
     * @ORM\Column(name="te_datetimetz", type="datetimetz")
     */
    public $datetimetz;
}
