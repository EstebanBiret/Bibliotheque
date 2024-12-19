<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Get reservations of $adherent
     * @return Reservation[] Returns an array of Reservation objects
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
     * Get reservations of $livre
     * @return Reservation[] Returns an array of Reservation objects
     */
    public function findByLivre($livre): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.livre = :val')
            ->setParameter('val', $livre)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
}
