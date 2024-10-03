<?php

namespace App\Service;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class EmployeeService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createEmployee(array $data): Employee
    {
        if (empty($data['name'])) {
            throw new InvalidArgumentException('"name" is required');
        }

        if (empty($data['lastname'])) {
            throw new InvalidArgumentException('"lastname" is required');
        }

        $employee = new Employee($data['name'], $data['lastname']);
        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        return $employee;
    }
}
