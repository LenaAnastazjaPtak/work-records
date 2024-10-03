<?php

namespace App\Controller;

use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EmployeeController extends AbstractController
{
    public function createEmployee(Request $request, EmployeeService $employeeService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $employee = $employeeService->createEmployee($data);

        return new JsonResponse(['uuid' => $employee->getUuid()], 201);
    }
}
