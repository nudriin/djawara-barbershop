<?php
namespace Nurdin\Djawara\Controller;

use Exception;
use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Helper\ErrorHelper;
use Nurdin\Djawara\Model\Orders\OrdersAddRequest;
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

            if (!isset($request->account_id) || !isset($request->total_price) || !isset($request->schedule_id)) {
                throw new ValidationException("account_id, total_price, and schedule_id is required", 400);
            }

            $addRequest = new OrdersAddRequest();
            $addRequest->account_id = $request->account_id;
            $addRequest->total_price = $request->total_price;
            $addRequest->schedule_id = $request->schedule_id;

            $orders = $this->ordersService->addOrders($addRequest);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'account_id' => $orders->orders->account_id,
                    'total_price' => $orders->orders->total_price,
                    'schedule_id' => $orders->orders->schedule_id
                ]
            ]);
        } catch (Exception $e) {
            ErrorHelper::errors($e);
        }
    }
}
