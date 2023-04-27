<?php

namespace App\EventListener;


use App\Entity\User;
use App\Service\MySlugger;
use Doctrine\Persistence\Event\LifecycleEventArgs;


class AutoSluggerCreate
{

    private $slugger;

    public function __construct(MySlugger $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof User ) {
            return;
        }
        
        $entityManager = $args->getObjectManager();

        //slugify
        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug->toString());
        
        $entityManager->flush();
        
    }
}