<?php

namespace Nurdin\Djawara\Service;

use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Domain\Schedules;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Model\Schedules\SchedulesAddRequest;
use Nurdin\Djawara\Model\Schedules\SchedulesAddResponse;
use Nurdin\Djawara\Model\Schedules\SchedulesGetAllResponse;
use Nurdin\Djawara\Model\Schedules\SchedulesGetRequest;
use Nurdin\Djawara\Model\Schedules\SchedulesGetResponse;
use Nurdin\Djawara\Model\Schedules\SchedulesUpdateRequest;
use Nurdin\Djawara\Model\Schedules\SchedulesUpdateResponse;
use Nurdin\Djawara\Repository\SchedulesRepository;

class SchedulesService
{
    private SchedulesRepository $schedulesRepo;

    public function __construct(SchedulesRepository $schedulesRepo)
    {
        $this->schedulesRepo = $schedulesRepo;
    }

    public function addSchedules(SchedulesAddRequest $request): SchedulesAddResponse
    {
        $this->validateAddSchedules($request);
        try {
            Database::beginTransaction();
            $schedules = new Schedules();
            $schedules->kapster_id = $request->kapster_id;
            $schedules->category_id = $request->category_id;
            $schedules->start_date = $request->start_date;
            $schedules->end_date = $request->end_date;
            $schedules->status = "AVAILABLE";

            $this->schedulesRepo->save($schedules);
            Database::commitTransaction();

            $response = new SchedulesAddResponse();
            $response->schedules = $schedules;

            return $response;
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function validateAddSchedules(SchedulesAddRequest $request)
    {
        if (
            $request->kapster_id == null || $request->category_id == null || $request->start_date == null || $request->end_date == null ||
            trim($request->kapster_id) == "" || trim($request->category_id) == "" || trim($request->start_date) == "" || trim($request->end_date) == ""
        ) {
            throw new ValidationException("Kapsters_id, category_id, start_date, and end_date is required", 400);
        }
    }

    public function getSchedulesById(SchedulesGetRequest $request): SchedulesGetResponse
    {
        try {
            $schedules = $this->schedulesRepo->findById($request->id);

            if ($schedules == null) {
                throw new ValidationException("Schedule not found", 404);
            }

            $response = new SchedulesGetResponse();
            $response->schedules = $schedules;

            return $response;
        } catch (ValidationException $e) {
            throw $e;
        }
    }

    public function validateGetSchedulesById(SchedulesGetRequest $request)
    {
        if ($request->id == null || trim($request->id) == "") {
            throw new ValidationException("Id is required", 400);
        }
    }

    public function getAllSchedules(): SchedulesGetAllResponse
    {
        try {
            $schedules = $this->schedulesRepo->findAll();
            if ($schedules == null) throw new ValidationException("Schedule not found", 404);

            $response = new SchedulesGetAllResponse();
            $response->schedules = $schedules;

            return $response;
        } catch (ValidationException $e) {
            throw $e;
        }
    }

    public function updateSchedules(SchedulesUpdateRequest $request): SchedulesUpdateResponse
    {
        try {
            Database::beginTransaction();
            $schedules = $this->schedulesRepo->findById($request->id);
            if ($schedules == null) throw new ValidationException("Schedule not found", 404);

            if (isset($request->kapster_id) && $request->kapster_id !== null) $schedules->kapster_id = $request->kapster_id;
            if (isset($request->category_id) && $request->category_id !== null) $schedules->category_id = $request->category_id;
            if (isset($request->start_date) && $request->start_date !== null) $schedules->start_date = $request->start_date;
            if (isset($request->end_date) && $request->end_date !== null) $schedules->end_date = $request->end_date;
            if (isset($request->status) && $request->status !== null && trim($schedules->status) != "") $schedules->status = $request->status;

            $this->schedulesRepo->update($schedules);
            Database::commitTransaction();

            $response = new SchedulesUpdateResponse();
            $response->schedules = $schedules;

            return $response;
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function removeSchedules(SchedulesGetRequest $request)
    {
        $this->validateGetSchedulesById($request);
        try {
            Database::beginTransaction();
            $schedules = $this->schedulesRepo->findById($request->id);
            if($schedules == null) {
                throw new ValidationException("Schedule not found", 404);
            }

            $this->schedulesRepo->remove($schedules->id);
            Database::commitTransaction();
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }
}
