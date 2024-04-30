<?php
namespace Nurdin\Djawara\Controller;

use Exception;
use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Helper\ErrorHelper;
use Nurdin\Djawara\Model\Orders\OrdersAddRequest;
use Nurdin\Djawara\Model\Orders\OrdersGetAllRequest;
use Nurdin\Djawara\Model\Orders\OrdersGetByIdRequest;
use Nurdin\Djawara\Model\Orders\OrdersUpdateRequest;
use Nurdin\Djawara\Repository\OrdersRepository;
use Nurdin\Djawara\Service\OrdersService;

class OrdersController
{
    private OrdersService $ordersService;

    public function __construct()
    {
        $connection = Database::getConnect();
        $ordersRepo = new OrdersRepository($connection);
        $this->ordersService = new OrdersService($ordersRepo);
    }

    public function add()
    {
        try {
            $json = file_get_contents('php://input');
            $request = json_decode($json);

            if (!isset($request->account_id) || !isset($request->schedule_id)) {
                throw new ValidationException("account_id and schedule_id is required", 400);
            }

            $addRequest = new OrdersAddRequest();
            $addRequest->account_id = $request->account_id;
            $addRequest->schedule_id = $request->schedule_id;

            $orders = $this->ordersService->addOrders($addRequest);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'account_id' => $orders->orders->account_id,
                    'schedule_id' => $orders->orders->schedule_id
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

            $request = new OrdersGetByIdRequest();
            $request->id = $id;

            $orders = $this->ordersService->getOrdersById($request);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'id' => $orders->orders->id,
                    'account_id' => $orders->orders->account_id,
                    'order_date' => $orders->orders->order_date,
                    'total_price' => $orders->orders->total_price,
                    'status' => $orders->orders->status,
                    'schedule_id' => $orders->orders->schedule_id
                ]
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }

    public function getAll()
    {
        try {
            $orders = $this->ordersService->getAllOrders();

            http_response_code(200);
            echo json_encode([
                'data' => $orders->orders
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }
    
    public function getAllByAccountId(string $account_id)
    {
        try {
            if (!isset($account_id)) throw new ValidationException("Id is required", 400);

            $request = new OrdersGetAllRequest();
            $request->account_id = $account_id;
            $orders = $this->ordersService->getAllOrdersByAccountId($request);

            http_response_code(200);
            echo json_encode([
                'data' => $orders->orders
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

            $updateRequest = new OrdersUpdateRequest();
            $updateRequest->id = $id;
            if(isset($request->account_id) && $request->account_id != null) $updateRequest->account_id = $request->account_id;
            if(isset($request->total_price) && $request->total_price != null) $updateRequest->total_price = $request->total_price;
            if(isset($request->schedule_id) && $request->schedule_id != null) $updateRequest->schedule_id = $request->schedule_id;
            if(isset($request->status) && $request->status != null) $updateRequest->status = $request->status;

            $orders = $this->ordersService->updateOrders($updateRequest);
            http_response_code(200);
            echo json_encode([
                'data' => [
                    'id' => $orders->orders->id,
                    'account_id' => $orders->orders->account_id,
                    'total_price' => $orders->orders->total_price,
                    'schedule_id' => $orders->orders->schedule_id,
                    'status' => $orders->orders->status
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

            $request = new OrdersGetByIdRequest();
            $request->id = $id;
            $this->ordersService->removeOrders($request);

            http_response_code(200);
            echo json_encode([
                'data' => 'OK'
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }

}
