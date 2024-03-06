<?php

namespace Nurdin\Djawara\Service;

use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Domain\Kapsters;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Model\Kapsters\KapstersAddRequest;
use Nurdin\Djawara\Model\Kapsters\KapstersAddResponse;
use Nurdin\Djawara\Model\Kapsters\KapstersGetAllResponse;
use Nurdin\Djawara\Model\Kapsters\KapstersGetByIdRequest;
use Nurdin\Djawara\Model\Kapsters\KapstersGetByIdResponse;
use Nurdin\Djawara\Repository\KapstersRepository;

class KapstersService
{
    private KapstersRepository $kapstersRepo;

    public function __construct(KapstersRepository $kapstersRepo)
    {
        $this->kapstersRepo = $kapstersRepo;
    }

    public function addKapsters(KapstersAddRequest $request): KapstersAddResponse
    {
        $this->validateAddKapsters($request);
        try {
            Database::beginTransaction();
            $kapsters = new Kapsters();
            $kapsters->name = $request->name;
            $kapsters->phone = $request->phone;
            $kapsters->profile_pic = $request->profile_pic;

            $this->kapstersRepo->save($kapsters);
            Database::commitTransaction();

            $response = new KapstersAddResponse();
            $response->kapsters = $kapsters;

            return $response;
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function validateAddKapsters(KapstersAddRequest $request)
    {
        if (
            $request->name == null || $request->phone == null || $request->profile_pic == null ||
            trim($request->name) == "" || trim($request->phone) == "" || trim($request->profile_pic) == ""
        ) {
            throw new ValidationException("Name, phone and profile_pic is required", 400);
        }
    }

    public function getAllKapsters(): KapstersGetAllResponse
    {
        try {
            $kapsters = $this->kapstersRepo->findAll();
            if ($kapsters == null) {
                throw new ValidationException("Kapsters not found", 404);
            }

            $response = new KapstersGetAllResponse();
            $response->kapsters = $kapsters;

            return $response;
        } catch (ValidationException $e) {
            throw $e;
        }
    }

    public function getKapstersById(KapstersGetByIdRequest $request): KapstersGetByIdResponse
    {
        $this->validateGetKapstersById($request);
        try {
            $kapsters = $this->kapstersRepo->findById($request->id);
            if ($kapsters == null) {
                throw new ValidationException("Kapsters not found", 404);
            }

            $response = new KapstersGetByIdResponse();
            $response->kapsters = $kapsters;

            return $response;
        } catch (ValidationException $e) {
            throw $e;
        }
    }

    public function validateGetKapstersById(KapstersGetByIdRequest $request)
    {
        if ($request->id == null || trim($request->id) == "") {
            throw new ValidationException("Id is required", 400);
        }
    }
}
