<?php

namespace HHIT\Doctrine\App\ORM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sample")
 */
class SampleEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    public $id;

    /**
     * @var string
     * @ORM\Column(name="value", length=255, nullable=false)
     */
    public $value;
}
