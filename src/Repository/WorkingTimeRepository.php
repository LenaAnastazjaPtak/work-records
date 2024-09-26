<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\WorkingTime;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<WorkingTime>
 */
class WorkingTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkingTime::class);
    }

    //    /**
    //     * @return WorkingTime[] Returns an array of WorkingTime objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WorkingTime
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Znajdź wszystkie wpisy czasu pracy dla danego pracownika na podstawie podanej daty
     *
     * @param Employee $employee
     * @param string $date Format daty: 'YYYY-MM' lub 'YYYY-MM-DD'
     * @return WorkingTime[]   Lista wpisów czasu pracy
     * @throws Exception
     */
    public function findByEmployeeAndDate(Employee $employee, string $date): array
    {
        $queryBuilder = $this->createQueryBuilder('wt')
            ->andWhere('wt.employee = :employee')
            ->setParameter('employee', $employee);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $queryBuilder
                ->andWhere('wt.startDay = :date')
                ->setParameter('date', $date);

        } elseif (preg_match('/^\d{4}-\d{2}$/', $date)) {
            $startOfMonth = $date . '-01';
            $endOfMonth = (new DateTime($startOfMonth))->modify('last day of this month')->format('Y-m-d');

            $queryBuilder->andWhere('wt.startDay BETWEEN :start AND :end')
                ->setParameter('start', $startOfMonth)
                ->setParameter('end', $endOfMonth);
        } else {
            return [];
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
