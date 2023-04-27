<?php

namespace App\EventListener;


use App\Entity\User;
use App\Service\MySlugger;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Slugify the title of an entity when update
 */
class AutoSluggerUpdate
{

    private $slugger;

    public function __construct(MySlugger $slugger)
    {
        $this->slugger = $slugger;
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        $entity = $event->getObject();

        if ($entity instanceof User) {
            return;
        }

        $entityManager = $event->getObjectManager();

        //slugify
        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug->toString());
    }
}
