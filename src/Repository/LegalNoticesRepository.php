<?php

namespace App\Repository;

use App\Entity\LegalNotices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LegalNotices>
 *
 * @method LegalNotices|null find($id, $lockMode = null, $lockVersion = null)
 * @method LegalNotices|null findOneBy(array $criteria, array $orderBy = null)
 * @method LegalNotices[]    findAll()
 * @method LegalNotices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegalNoticesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LegalNotices::class);
    }

    public function add(LegalNotices $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LegalNotices $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
