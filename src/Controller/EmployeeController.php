<?php

namespace App\Controller;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends AbstractController
{
    public function createEmployee(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'])) {
            return new JsonResponse(['message' => '"name" is required', 'code' => 400], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['lastname'])) {
            return new JsonResponse(['message' => '"lastname" is required', 'code' => 400], Response::HTTP_BAD_REQUEST);
        }

        $firstName = $data['name'];
        $lastName = $data['lastname'];

        $employee = new Employee($firstName, $lastName);
        $em->persist($employee);
        $em->flush();

        return new JsonResponse(['uuid' => $employee->getUuid()], 201);
    }
}
