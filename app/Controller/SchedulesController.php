<?php

namespace Nurdin\Djawara\Controller;

use Exception;
use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Helper\ErrorHelper;
use Nurdin\Djawara\Model\Schedules\SchedulesAddRequest;
use Nurdin\Djawara\Model\Schedules\SchedulesGetRequest;
use Nurdin\Djawara\Model\Schedules\SchedulesUpdateRequest;
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

            if (!isset($request->kapster_id) || !isset($request->category_id) || !isset($request->dates) || !isset($request->times)) {
                throw new ValidationException("kapster_id, category_id, dates and times is required", 400);
            }

            $addRequest = new SchedulesAddRequest();
            $addRequest->kapster_id = $request->kapster_id;
            $addRequest->category_id = $request->category_id;
            $addRequest->dates = $request->dates;
            $addRequest->times = $request->times;

            $schedules = $this->schedulesService->addSchedules($addRequest);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'kapster_id' => $schedules->schedules->kapster_id,
                    'category_id' => $schedules->schedules->category_id,
                    'dates' => $schedules->schedules->dates,
                    'times' => $schedules->schedules->times,
                ]
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }

    public function getById(string $id)
    {
        try {
            if (!isset($id)) throw new ValidationException("Id is required", 400);

            $request = new SchedulesGetRequest();
            $request->id = $id;

            $schedules = $this->schedulesService->getSchedulesById($request);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'id' => $schedules->schedules->id,
                    'kapster_id' => $schedules->schedules->kapster_id,
                    'category_id' => $schedules->schedules->category_id,
                    'dates' => $schedules->schedules->dates,
                    'times' => $schedules->schedules->times,
                    'status' => $schedules->schedules->status
                ]
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }

    public function getAll()
    {
        try {
            $schedules = $this->schedulesService->getAllSchedules();

            http_response_code(200);
            echo json_encode([
                'data' => $schedules->schedules
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }

    public function update(string $id)
    {
        try {
            if (!isset($id)) throw new ValidationException("Id is required", 400);
            $json = file_get_contents('php://input');
            $request = json_decode($json);

            $updateRequest = new SchedulesUpdateRequest();
            $updateRequest->id = $id;
            if (isset($request->kapster_id) && $request->kapster_id != null) $updateRequest->kapster_id = $request->kapster_id;
            if (isset($request->category_id) && $request->category_id != null) $updateRequest->category_id = $request->category_id;
            if (isset($request->dates) && $request->dates != null) $updateRequest->dates = $request->dates;
            if (isset($request->times) && $request->times != null) $updateRequest->times = $request->times;
            if (isset($request->status) && $request->status != null) $updateRequest->status = $request->status;

            $schedules = $this->schedulesService->updateSchedules($updateRequest);
            http_response_code(200);
            echo json_encode([
                'data' => [
                    'id' => $schedules->schedules->id,
                    'kapster_id' => $schedules->schedules->kapster_id,
                    'category_id' => $schedules->schedules->category_id,
                    'dates' => $schedules->schedules->dates,
                    'times' => $schedules->schedules->times,
                    'status' => $schedules->schedules->status,
                ]
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }

    public function remove(string $id)
    {
        try {
            if (!isset($id)) throw new ValidationException("Id is required", 400);

            $request = new SchedulesGetRequest();
            $request->id = $id;
            $this->schedulesService->removeSchedules($request);

            http_response_code(200);
            echo json_encode([
                'data' => 'OK'
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }
}
