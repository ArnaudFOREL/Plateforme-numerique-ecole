<?php

namespace App\Repository;

use App\Entity\UserCourse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserCourse|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCourse|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCourse[]    findAll()
 * @method UserCourse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCourse::class);
    }

    // /**
    //  * @return UserCourse[] Returns an array of UserCourse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    public function findOneByCourseAndUser($course, $user): ?UserCourse
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.course = :course')
            ->andWhere('u.user = :user')
            ->setParameter('course', $course)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findWeekUser($user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user = :user')
            ->join('u.course', 'c')
            ->andWhere('c.status = 1')
            ->andWhere('c.endAt < :end')
            ->setParameter('user', $user)
            ->setParameter('end', 'date(now() + (INTERVAL 6 - weekday(now()) DAY))')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFinishUser($user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user = :user')
            ->andWhere('u.progress = 100')
            ->join('u.course', 'c')
            ->andWhere('c.status = 1')
            ->andWhere('c.endAt < :end')
            ->setParameter('user', $user)
            ->setParameter('end', 'date(now() + INTERVAL 6 - weekday(now()) DAY)')
            ->getQuery()
            ->getResult()
        ;
    }
    
}
