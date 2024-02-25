<?php

namespace App\Repository;

use App\Entity\UserSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSubscription>
 *
 * @method UserSubscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSubscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSubscription[]    findAll()
 * @method UserSubscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSubscription::class);
    }

    public function getUserSubscriptions($user, $page, $perPage)
    {
        $em = $this->getEntityManager();
        
        $query = $em->createQuery(
            'SELECT us 
            FROM App\Entity\UserSubscription us 
            INNER JOIN us.subscription s
            WHERE us.user = :user_id'
        )->setParameter('user_id', $user->getId());
        
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
    
    //    /**
    //     * @return UserSubscription[] Returns an array of UserSubscription objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserSubscription
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
