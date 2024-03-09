<?php

namespace Nurdin\Djawara\Service;

use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Domain\Categories;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Model\Categories\CategoriesAddRequest;
use Nurdin\Djawara\Model\Categories\CategoriesAddResponse;
use Nurdin\Djawara\Repository\CategoriesRepository;

class CategoriesService
{
    private CategoriesRepository $categoriesRepo;

    public function __construct(CategoriesRepository $categoriesRepo)
    {
        $this->categoriesRepo = $categoriesRepo;
    }

    public function addCategories(CategoriesAddRequest $request) : CategoriesAddResponse
    {
        $this->validateAddCategories($request);
        try {
            Database::beginTransaction();
            $categories = new Categories();
            $categories->name = $request->name;
            $categories->price = $request->price;

            $this->categoriesRepo->save($categories);
            Database::commitTransaction();

            $response = new CategoriesAddResponse();
            $response->categories = $categories;

            return $response;
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function validateAddCategories(CategoriesAddRequest $request)
    {
        if (
            $request->name == null || $request->price == null ||
            trim($request->name) == "" || trim($request->price) == "" || $request->price < 0
        ) {
            throw new ValidationException("Name and price is required", 400);
        }
    }
}
