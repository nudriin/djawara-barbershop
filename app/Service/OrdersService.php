<?php
namespace Nurdin\Djawara\Service;

use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Domain\Orders;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Model\Orders\OrdersAddRequest;
use Nurdin\Djawara\Model\Orders\OrdersAddResponse;
use Nurdin\Djawara\Repository\OrdersRepository;

class OrdersService
{
    private OrdersRepository $orderRepo;

    public function __construct(OrdersRepository $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function addOrders(OrdersAddRequest $request): OrdersAddResponse
    {
        $this->validateAddKapsters($request);
        try {
            Database::beginTransaction();
            $orders = new Orders();
            $orders->account_id = $request->account_id;
            $orders->total_price = $request->total_price;
            $orders->schedule_id = $request->schedule_id;

            $this->orderRepo->save($orders);
            Database::commitTransaction();

            $response = new OrdersAddResponse();
            $response->orders = $orders;

            return $response;
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function validateAddKapsters(OrdersAddRequest $request)
    {
        if (
            $request->account_id == null || $request->total_price == null || $request->schedule_id == null ||
            trim($request->account_id) == "" || trim($request->total_price) == "" || trim($request->schedule_id) == ""
        ) {
            throw new ValidationException("account_id, total_price and schedule_id is required", 400);
        }
    }
}
