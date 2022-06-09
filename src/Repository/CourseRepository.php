<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    // /**
    //  * @return Course[] Returns an array of Course objects
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
   
   public function findAvailable()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status <> 2')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFinish()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = 2')
            ->orderBy('c.startAt', 'ASC')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findByCategory($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.category = :val')
            ->setParameter('val', $value)
            ->orderBy('c.status', 'ASC')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByClassroom($value, $level = null)
    {
        return $this->createQueryBuilder('c')
            ->join('c.classrooms', 'classroom')
            ->where('classroom = :val')
            ->orWhere('classroom = 99')
            ->orWhere('classroom = :level')
            ->setParameter('val', $value)
            ->setParameter('level', $level)
            ->andWhere('c.status = 1')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
}
