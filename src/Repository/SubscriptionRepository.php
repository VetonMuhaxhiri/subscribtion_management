<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @extends ServiceEntityRepository<Subscription>
 *
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }
    
    public function getAllPaginated($page, $perPage)
    {
        $query = $this->createQueryBuilder('s')->getQuery();
        
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $totalItems = count($paginator);

        $paginator
            ->getQuery()
            ->setFirstResult($perPage * ($page-1))
            ->setMaxResults($perPage);
            
        return [
            'rows' => $paginator,
            'total_items' => $totalItems
        ];
    }
    
    public function getUserSubscriptions($user, $page, $perPage)
    {
        $em = $this->getEntityManager();

        $stmt = $em->getConnection()->prepare(
            "SELECT s.id, s.name, s.description, s.price, s.duration 
            FROM subscriptions s
            INNER JOIN user_subscriptions us ON s.id = us.subscription_id AND us.user_id = :user_id
            ORDER BY us.start_date
            ");

            $stmt = $stmt->executeQuery([
                'user_id' => $user->getId(),
            ]);
        
        return $stmt->fetchAllAssociative();
    }

    //    /**
    //     * @return Subscription[] Returns an array of Subscription objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Subscription
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
