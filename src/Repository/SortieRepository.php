<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Filter\Filters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    
    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    

    /**
     * @param Filters $filters
     * @param ParticipantInterface $Participant
     * @return Sortie[]
     */
    public function findSearch(Filters $filters ,UserInterface $participant): array
    {
 
       
        //Récupère tous les événements
        $query = $this->createQueryBuilder ('sortie')
            ->select('sortie', 'campus')
            ->join('sortie.campus', 'campus') ;

       
        //Si des filtres sont séléctionnés, afine la recherche
        //Test le champ de texte
        if (!empty($filters->text)) {
            $query = $query
                ->andWhere('sortie.nom LIKE :text')
                ->setParameter('text', "%{$filters->text}%");
        }
        //Récupère les event lié au campus sélectionné
        if (!empty($filters->campus)) {
            $query = $query
                ->andWhere('campus IN (:campus)')
                ->setParameter('campus', $filters->campus);
        }
        //Récupère les event organisé par l'user connecté
        if(!empty($filters->organisateur)){
            $query = $query
                ->andWhere('sortie.organisateur = :organisateur')
                ->setParameter('organisateur', $participant);
        }

        //Récupère les event entre la date de début sélectionné
        if (!empty($filters->dateHeureDebut)) {
            $query = $query
                ->andWhere('sortie.dateHeureDebut > (:dateHeureDebut)')
                ->setParameter('dateHeureDebut', $filters->dateHeureDebut);
        }
        //Et la date de fin sélectionné
        if (!empty($filters->dateLimiteInscription)) {
            $query = $query
                ->andWhere('sortie.dateLimiteInscription < (:dateLimiteInscription)')
                ->setParameter('dateLimiteInscription', $filters->dateLimiteInscription);
        }
        //Récupère les event archivé
        if(!empty($filters->passedEvents)){
            $query = $query
                ->andWhere('event.state = 4');
        }
        //Renvoie des résultats
        return $query->getQuery()->getResult();
        
    }


//    /**
//     * @return Sortie[] Returns an array of Sortie objects
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

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
