<?php

namespace AppBundle\Entity\ManagedArea;

use AppBundle\Entity\ReferentTag;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="managed_area_senator")
 * @ORM\Entity
 */
class SenatorManagedArea extends ManagedArea
{
    use ManagedTag;

    public function __construct(ReferentTag $tag = null)
    {
        $this->tag = $tag;
    }
}
