<?php

namespace Nurdin\Djawara\Service;

use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Domain\Categories;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Model\Categories\CategoriesAddRequest;
use Nurdin\Djawara\Model\Categories\CategoriesAddResponse;
use Nurdin\Djawara\Model\Categories\CategoriesGetAllResponse;
use Nurdin\Djawara\Model\Categories\CategoriesGetRequest;
use Nurdin\Djawara\Model\Categories\CategoriesGetResponse;
use Nurdin\Djawara\Model\Categories\CategoriesUpdateRequest;
use Nurdin\Djawara\Model\Categories\CategoriesUpdateResponse;
use Nurdin\Djawara\Repository\CategoriesRepository;

class CategoriesService
{
    private CategoriesRepository $categoriesRepo;

    public function __construct(CategoriesRepository $categoriesRepo)
    {
        $this->categoriesRepo = $categoriesRepo;
    }

    public function addCategories(CategoriesAddRequest $request): CategoriesAddResponse
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
            trim($request->name) == ""
        ) {
            throw new ValidationException("Name and price is required", 400);
        }

        if (trim($request->price) == "" || $request->price < 0) {
            throw new ValidationException("Price must positive and must greater than zero", 400);
        }
    }

    public function getCategoriesById(CategoriesGetRequest $request): CategoriesGetResponse
    {
        $this->validateGetCategoriesById($request);
        try {
            $categories = $this->categoriesRepo->findById($request->id);

            if ($categories == null) {
                throw new ValidationException("Categories not found", 404);
            }

            $response = new CategoriesGetResponse();
            $response->categories = $categories;

            return $response;
        } catch (ValidationException $e) {
            throw $e;
        }
    }

    public function validateGetCategoriesById(CategoriesGetRequest $request)
    {
        if ($request->id == null || trim($request->id) == "") {
            throw new ValidationException("Id is required", 400);
        }
    }

    public function getAllCategories(): CategoriesGetAllResponse
    {
        try {
            $categories = $this->categoriesRepo->findAll();
            if ($categories == null) {
                throw new ValidationException("Categories not found", 404);
            }

            $response = new CategoriesGetAllResponse();
            $response->categories = $categories;

            return $response;
        } catch (ValidationException $e) {
            throw $e;
        }
    }

    public function updateCategories(CategoriesUpdateRequest $request): CategoriesUpdateResponse
    {
        $this->validateUpdateCategories($request);
        try {
            Database::beginTransaction();
            $categories = $this->categoriesRepo->findById($request->id);
            if ($categories == null) {
                throw new ValidationException("Categories not found", 404);
            }

            if (isset($request->name) && $request->name != null) $categories->name = $request->name;
            if (isset($request->price) && $request->price != null) $categories->price = $request->price;

            $this->categoriesRepo->update($categories);
            Database::commitTransaction();

            $response = new CategoriesUpdateResponse();
            $response->categories = $categories;

            return $response;
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function validateUpdateCategories(CategoriesUpdateRequest $request)
    {
        if ($request->id == null || trim($request->id) == "" || trim($request->name) == "" || $request->price < 0) {
            throw new ValidationException("Name cannot blank and price must be positive", 400);
        }
    }
}
