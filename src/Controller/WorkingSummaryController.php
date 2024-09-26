<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\WorkingTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkingSummaryController extends AbstractController
{
    /**
     * @throws Exception
     */
    public function getWorkingSummary(Request $request, EntityManagerInterface $em, ParameterBagInterface $params): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['employeeUuid'])) {
            return new JsonResponse(['message' => '"employeeUuid" is required', 'code' => 400], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['date'])) {
            return new JsonResponse(['message' => '"date" is required', 'code' => 400], Response::HTTP_BAD_REQUEST);
        }

        $employeeUuid = $data['employeeUuid'];
        $date = $data['date'];

        $employee = $em->getRepository(Employee::class)->findOneBy(['uuid' => $employeeUuid]);
        if (!$employee) {
            return new JsonResponse(['error' => 'Nieprawidłowy UUID pracownika. Pracownik nie znaleziony.'], 404);
        }

        if (!$this->isValidDate($date)) {
            return new JsonResponse(['error' => 'Nieprawidłowy format daty. Oczekiwany format: YYYY-MM lub YYYY-MM-DD'], 400);
        }

        $workTimes = $em->getRepository(WorkingTime::class)->findByEmployeeAndDate($employee, $date);

        $summary = $this->calculateWorkSummary($workTimes, $params);

        return new JsonResponse([
            'employee_uuid' => $employee->getId(),
            'date' => $date,
            'total_hours' => $summary['totalHours'],
            'regular_hours' => $summary['regularHours'],
            'overtime_hours' => $summary['overtimeHours'],
            'total_payment' => $summary['totalPayment']
        ]);
    }

    private function isValidDate(string $date): bool
    {
        return preg_match('/^\d{4}-\d{2}$/', $date) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
    }

    private function calculateWorkSummary(array $workTimes, ParameterBagInterface $params): array
    {
        $totalMinutes = 0;
        $regularRate = $params->get('hourly_rate');
        $overtimeMultiplier = $params->get('overtime_rate_multiplier');
        $work_hours_norm = $params->get('work_hours_norm') * 60;

        foreach ($workTimes as $workTime) {
            $startTime = $workTime->getStartTime();
            $endTime = $workTime->getEndTime();

            $secondsWorked = $endTime->getTimestamp() - $startTime->getTimestamp();
            $minutesWorked = $secondsWorked / 60;
            $roundedMinutes = round($minutesWorked / 30) * 30;

            $totalMinutes += $roundedMinutes;
        }

        $totalHours = $totalMinutes / 60;
        $totalHoursRounded = round($totalHours * 2) / 2;

        if ($totalHoursRounded > $work_hours_norm / 60) {
            $regularHours = $work_hours_norm / 60;
            $overtimeHours = $totalHoursRounded - $regularHours;
        } else {
            $regularHours = $totalHoursRounded;
            $overtimeHours = 0;
        }

        $totalPayment = ($regularHours * $regularRate) + ($overtimeHours * $regularRate * $overtimeMultiplier);

        return [
            'totalHours' =>  $totalHoursRounded,
            'regularHours' => $regularHours,
            'overtimeHours' => $overtimeHours,
            'totalPayment' => $totalPayment,
        ];
    }
}
