<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Artwork;
use App\Entity\Exhibition;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Exhibition>
 *
 * @method Exhibition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exhibition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exhibition[]    findAll()
 * @method Exhibition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExhibitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exhibition::class);
    }

    public function add(Exhibition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Exhibition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * Get exhibition by artist
     */
    public function findAllByArtist(User $artist): array
    {
    return $this->createQueryBuilder('e')
        ->where('e.artist = :artist')
        ->setParameter('artist', $artist)
        ->getQuery()
        ->getResult();
    }

    /**
     * Get exhibition infos and first picture for carrousel in home page
     */
    public function findAllForHomeSQL(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT `exhibition`.`id`, `exhibition`.`title`,`exhibition`.`slug`,`exhibition`.`description`,`artwork`.`picture` 
                FROM `exhibition`
                INNER JOIN `artwork` ON `exhibition`.`id` = `artwork`.`exhibition_id`
                GROUP BY `id`';
        
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Exhibition[] Returns an array of Exhibition objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Exhibition
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
