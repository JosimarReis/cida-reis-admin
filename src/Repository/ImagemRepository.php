<?php

namespace App\Repository;

use App\Entity\Imagem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Imagem|null find($id, $lockMode = null, $lockVersion = null)
 * @method Imagem|null findOneBy(array $criteria, array $orderBy = null)
 * @method Imagem[]    findAll()
 * @method Imagem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Imagem::class);
    }

//    /**
//     * @return Imagem[] Returns an array of Imagem objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Imagem
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
