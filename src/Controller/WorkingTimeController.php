<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\WorkingTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

class WorkingTimeController extends AbstractController
{
    /**
     * @throws Exception
     */
    public function createWorkTime(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['employeeUuid'])) {
            return new JsonResponse(['message' => '"employeeUuid" is required', 'code' => 400], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['startTime'])) {
            return new JsonResponse(['message' => '"startTime" is required', 'code' => 400], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['endTime'])) {
            return new JsonResponse(['message' => '"endTime" is required', 'code' => 400], Response::HTTP_BAD_REQUEST);
        }

        $employeeUuid = $data['employeeUuid'];
        $startTime = new DateTime($data['startTime']);
        $endTime = new DateTime($data['endTime']);

        $employee = $em->getRepository(Employee::class)->findOneBy(['uuid' => $employeeUuid]);
        if (!$employee) {
            return new JsonResponse(['error' => 'Pracownik nie znaleziony'], 404);
        }

        if ($endTime < $startTime) {
            return new JsonResponse(['error' => 'Czas zakończenia nie może być wcześniejszy niż czas rozpoczęcia'], 400);
        }

        $secondsWorked = $endTime->getTimestamp() - $startTime->getTimestamp();
        $hoursWorked = $secondsWorked / 3600;

        if ($hoursWorked > 12) {
            return new JsonResponse(['error' => 'Nie można zarejestrować więcej niż 12 godzin'], 400);
        }

        $existingWork = $em->getRepository(WorkingTime::class)->findOneBy([
            'employee' => $employee,
            'startDay' => new DateTime($startTime->format('Y-m-d'))
        ]);

        if ($existingWork) {
            return new JsonResponse(['error' => 'Pracownik ma już zarejestrowany czas pracy tego dnia'], 400);
        }

        $workTime = new WorkingTime();
        $workTime->setEmployee($employee);
        $workTime->setStartTime($startTime);
        $workTime->setEndTime($endTime);
        $workTime->setStartDay(new DateTime($startTime->format('Y-m-d')));

        $em->persist($workTime);
        $em->flush();

        return new JsonResponse(['message' => 'Czas pracy został dodany!'], 201);
    }
}
