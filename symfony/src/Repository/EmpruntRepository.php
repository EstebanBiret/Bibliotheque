<?php

namespace App\Repository;

use App\Entity\Emprunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emprunt>
 *
 * @method Emprunt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emprunt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emprunt[]    findAll()
 * @method Emprunt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

    /**
     * Get emprunt of $adherent
     * @return Emprunt[] Returns an array of Emprunt objects
     */
    public function findByAdherent($adherent): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.adherent = :val')
            ->setParameter('val', $adherent)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Get emprunt of $livre
     * @return Emprunt[] Returns an array of Emprunt objects
     */
    public function findValidByLivre($livre, $date): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.livre = :val')
            ->setParameter('val', $livre)
            ->andWhere('r.date_retour >= :val2')
            ->setParameter('val2', $date)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

//    public function findOneBySomeField($value): ?Emprunt
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
