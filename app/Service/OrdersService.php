<?php
namespace Nurdin\Djawara\Service;

use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Domain\Orders;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Model\Orders\OrdersAddRequest;
use Nurdin\Djawara\Model\Orders\OrdersAddResponse;
use Nurdin\Djawara\Model\Orders\OrdersGetAllResponse;
use Nurdin\Djawara\Model\Orders\OrdersGetByIdRequest;
use Nurdin\Djawara\Model\Orders\OrdersGetByIdResponse;
use Nurdin\Djawara\Model\Orders\OrdersUpdateRequest;
use Nurdin\Djawara\Model\Orders\OrdersUpdateResponse;
use Nurdin\Djawara\Repository\OrdersRepository;

class OrdersService
{
    private OrdersRepository $ordersRepo;

    public function __construct(OrdersRepository $ordersRepo)
    {
        $this->ordersRepo = $ordersRepo;
    }

    public function addOrders(OrdersAddRequest $request): OrdersAddResponse
    {
        $this->validateAddOrders($request);
        try {
            Database::beginTransaction();
            $orders = new Orders();
            $orders->account_id = $request->account_id;
            $orders->total_price = $request->total_price;
            $orders->schedule_id = $request->schedule_id;

            $this->ordersRepo->save($orders);
            Database::commitTransaction();

            $response = new OrdersAddResponse();
            $response->orders = $orders;

            return $response;
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function validateAddOrders(OrdersAddRequest $request)
    {
        if (
            $request->account_id == null || $request->total_price == null || $request->schedule_id == null ||
            trim($request->account_id) == "" || trim($request->total_price) == "" || trim($request->schedule_id) == ""
        ) {
            throw new ValidationException("account_id, total_price and schedule_id is required", 400);
        }
    }

    public function getOrdersById(OrdersGetByIdRequest $request): OrdersGetByIdResponse
    {
        try {
            $orders = $this->ordersRepo->findById($request->id);

            if ($orders == null) {
                throw new ValidationException("Orders not found", 404);
            }

            $response = new OrdersGetByIdResponse();
            $response->orders = $orders;

            return $response;
        } catch (ValidationException $e) {
            throw $e;
        }
    }

    public function validateGetSchedulesById(OrdersGetByIdRequest $request)
    {
        if ($request->id == null || trim($request->id) == "") {
            throw new ValidationException("Id is required", 400);
        }
    }

    public function getAllOrders(): OrdersGetAllResponse
    {
        try {
            $orders = $this->ordersRepo->findAll();
            if ($orders == null) {
                throw new ValidationException("Orders not found", 404);
            }

            $response = new OrdersGetAllResponse();
            $response->orders = $orders;

            return $response;
        } catch (ValidationException $e) {
            throw $e;
        }
    }


    public function updateOrders(OrdersUpdateRequest $request) : OrdersUpdateResponse
    {
        $this->validateUpdateOrders($request);
        try {
            Database::beginTransaction();
            $orders = $this->ordersRepo->findById($request->id);

            if($orders == null) {
                throw new ValidationException("Orders not found", 404);
            }

            if(isset($request->account_id) && $request->account_id) $orders->account_id = $request->account_id;
            if(isset($request->total_price) && $request->total_price) $orders->total_price = $request->total_price;
            if(isset($request->schedule_id) && $request->schedule_id) $orders->schedule_id = $request->schedule_id;
            if(isset($request->status) && $request->status) $orders->status = $request->status;

            $this->ordersRepo->update($orders);
            Database::commitTransaction();

            $response = new OrdersUpdateResponse();
            $response->orders = $orders;

            return $response;
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function validateUpdateOrders(OrdersUpdateRequest $request)
    {
        if (
            $request->id == null || trim($request->id) == ""
        ) {
            throw new ValidationException("id is required", 400);
        }
    }

    public function removeOrders(OrdersGetByIdRequest $request)
    {
        $this->validateGetSchedulesById($request);
        try {
            Database::beginTransaction();
            $orders = $this->ordersRepo->findById($request->id);
            if($orders == null) {
                throw new ValidationException("Schedule not found", 404);
            }

            $this->ordersRepo->remove($orders->id);
            Database::commitTransaction();
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }
}
