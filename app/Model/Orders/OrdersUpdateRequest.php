<?php
namespace Nurdin\Djawara\Model\Orders;

class OrdersUpdateRequest
{
    public ?int $id = null;
    public ?int $account_id = null;
    public ?int $total_price = null;
    public ?int $schedule_id = null;
    public ?string $status = null;
}