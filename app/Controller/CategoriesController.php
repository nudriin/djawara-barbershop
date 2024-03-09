<?php

namespace Nurdin\Djawara\Controller;

use Exception;
use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Helper\ErrorHelper;
use Nurdin\Djawara\Model\Categories\CategoriesAddRequest;
use Nurdin\Djawara\Model\Categories\CategoriesGetRequest;
use Nurdin\Djawara\Repository\CategoriesRepository;
use Nurdin\Djawara\Service\CategoriesService;

class CategoriesController
{
    private CategoriesService $categoriesService;

    public function __construct()
    {
        $connection = Database::getConnect();
        $categoriesRepo = new CategoriesRepository($connection);
        $this->categoriesService = new CategoriesService($categoriesRepo);
    }

    public function add()
    {
        try {
            $json = file_get_contents('php://input');
            $request = json_decode($json);

            if (!isset($request->name) || !isset($request->price)) {
                throw new ValidationException("Name and price is required", 400);
            }

            $addRequest = new CategoriesAddRequest();
            $addRequest->name = $request->name;
            $addRequest->price = $request->price;

            $categories = $this->categoriesService->addCategories($addRequest);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'name' => $categories->categories->name,
                    'price' => $categories->categories->price
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
            $request = new CategoriesGetRequest();
            $request->id = $id;

            $categories = $this->categoriesService->getCategoriesById($request);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'id' => $categories->categories->id,
                    'name' => $categories->categories->name,
                    'price' => $categories->categories->price,
                ]
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }

    public function getAll()
    {
        try {
            $categories = $this->categoriesService->getAllCategories();

            http_response_code(200);
            echo json_encode([
                'data' => $categories->categories
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }
}
