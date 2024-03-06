<?php

namespace Nurdin\Djawara\Service;

use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Domain\Kapsters;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Model\Kapsters\KapstersAddRequest;
use Nurdin\Djawara\Model\Kapsters\KapstersAddResponse;
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
}
