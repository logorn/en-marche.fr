<?php

namespace AppBundle\EntityListener;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\ReferentOrganizationalChart\ReferentPersonLink;
use AppBundle\Repository\AdherentRepository;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class ReferentPersonLinkListener
{
    public function preUpdate(ReferentPersonLink $personLink, PreUpdateEventArgs $preUpdateEventArgs): void
    {
        $adherentRepository = $preUpdateEventArgs->getEntityManager()->getRepository(Adherent::class);

        if (!$email = $personLink->getEmail()) {
            return;
        }

        if (!$adherent = $adherentRepository->findOneByEmail($email)) {
            return;
        }

        $personLink->setAdherent($adherent);
    }
}
