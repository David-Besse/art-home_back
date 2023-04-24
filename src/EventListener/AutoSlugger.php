<?php

namespace App\EventListener;


use App\Entity\User;
use App\Entity\Artwork;
use App\Entity\Exhibition;
use App\Service\MySlugger;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class AutoSlugger
{
    public function prePersist(	PrePersistEventArgs $args, MySlugger $slugger, ManagerRegistry $doctrine): void
    {
        $entity = $args->getObject();
        dd($entity);
        if ($entity instanceof User ) {
            return;
        }
        
        $entityMangager = $args->getObjectManager();
        //slugify
        $slug = $slugger->slugify($entity->getTitle());
        $entity->setSlug($slug);

        // $entityManager = $doctrine->getManager();
        // $entityManager->persist($entity);
        // $entityManager->flush();
        
    }
}