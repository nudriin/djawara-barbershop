<?php

namespace Nurdin\Djawara\Controller;

use Exception;
use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Helper\ErrorHelper;
use Nurdin\Djawara\Model\Schedules\SchedulesAddRequest;
use Nurdin\Djawara\Repository\SchedulesRepository;
use Nurdin\Djawara\Service\SchedulesService;

class SchedulesController
{
    private SchedulesService $schedulesService;

    public function __construct()
    {
        $connection = Database::getConnect();
        $schedulesService = new SchedulesRepository($connection);
        $this->schedulesService = new SchedulesService($schedulesService);
    }

    public function add()
    {
        try {
            $json = file_get_contents('php://input');
            $request = json_decode($json);

            if (!isset($request->kapster_id) || !isset($request->category_id) || !isset($request->start_date) || !isset($request->end_date)) {
                throw new ValidationException("kapster_id, category_id, start_date and end_date is required", 400);
            }

            $addRequest = new SchedulesAddRequest();
            $addRequest->kapster_id = $request->kapster_id;
            $addRequest->category_id = $request->category_id;
            $addRequest->start_date = $request->start_date;
            $addRequest->end_date = $request->end_date;

            $schedules = $this->schedulesService->addSchedules($addRequest);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'kapster_id' => $schedules->schedules->kapster_id,
                    'category_id' => $schedules->schedules->category_id,
                    'start_date' => $schedules->schedules->start_date,
                    'end_date' => $schedules->schedules->end_date,
                ]
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }
}
