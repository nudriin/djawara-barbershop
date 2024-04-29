<?php
namespace Nurdin\Djawara\Model\Orders;


class OrdersAddRequest
{
    public ?string $account_id = null;
    public ?string $total_price = null;
    public ?string $schedule_id = null;
}