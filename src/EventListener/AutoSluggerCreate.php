<?php

namespace App\EventListener;


use App\Entity\User;
use App\Service\MySlugger;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Slugify the title of an entity when persist
 */
class AutoSluggerCreate
{

    private $slugger;

    public function __construct(MySlugger $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        //fetching entity
        $entity = $args->getObject();

         //checking if entity is a User entity
        if ($entity instanceof User) {
            return;
        }

        //fetching Manager
        $entityManager = $args->getObjectManager();

        //slugify
        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug->toString());

        $entityManager->flush();
    }
}
