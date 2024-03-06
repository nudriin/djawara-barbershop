<?php

namespace Nurdin\Djawara\Controller;

use Exception;
use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Helper\ErrorHelper;
use Nurdin\Djawara\Model\Kapsters\KapstersAddRequest;
use Nurdin\Djawara\Repository\KapstersRepository;
use Nurdin\Djawara\Service\KapstersService;

class KapstersController
{
    private KapstersService $kapstersService;

    public function __construct()
    {
        $connection = Database::getConnect();
        $kapstersRepo = new KapstersRepository($connection);
        $this->kapstersService = new KapstersService($kapstersRepo);
    }

    public function add()
    {
        try {
            $json = file_get_contents('php://input');
            $request = json_decode($json);

            if (!isset($request->name) || !isset($request->phone) || !isset($request->profile_pic)) {
                throw new ValidationException("name, phone, and profile_pic is required", 400);
            }

            $addRequest = new KapstersAddRequest();
            $addRequest->name = $request->name;
            $addRequest->phone = $request->phone;
            $addRequest->profile_pic = $request->profile_pic;

            $kapsters = $this->kapstersService->addKapsters($addRequest);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'name' => $kapsters->kapsters->name,
                    'phone' => $kapsters->kapsters->phone,
                    'profile_pic' => $kapsters->kapsters->profile_pic
                ]
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }

    public function getAll()
    {
        try {
            $kapsters = $this->kapstersService->getAllKapsters();

            http_response_code(200);
            echo json_encode([
                'data' => $kapsters->kapsters
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }
}
