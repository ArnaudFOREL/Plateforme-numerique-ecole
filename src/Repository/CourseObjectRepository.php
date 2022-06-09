<?php

namespace App\Repository;

use App\Entity\CourseObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CourseObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourseObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourseObject[]    findAll()
 * @method CourseObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseObjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseObject::class);
    }

    // /**
    //  * @return CourseObject[] Returns an array of CourseObject objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    public function findAround($course, $rank)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.course = :course')
            ->andWhere('c.ranking = :rank')
            ->setParameter('course', $course)
            ->setParameter('rank', $rank)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findLast($course)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.course = :course')
            ->setParameter('course', $course)
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    
}
