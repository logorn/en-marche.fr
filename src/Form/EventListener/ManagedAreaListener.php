<?php

namespace AppBundle\Form\EventListener;

use AppBundle\Entity\Adherent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ManagedAreaListener implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'removeEmptyManagedArea',
        ];
    }

    public function removeEmptyManagedArea(FormEvent $event): void
    {
        /** @var Adherent $adherent */
        if (!$adherent = $event->getData()) {
            return;
        }

        $senatorManagedArea = $adherent->getSenatorManagedArea();

        if ($senatorManagedArea && !$senatorManagedArea->getTag()) {
            $adherent->revokeSenator();
            $this->em->remove($senatorManagedArea);
        }
    }
}
