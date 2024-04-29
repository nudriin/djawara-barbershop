<?php
namespace Nurdin\Djawara\Domain;

class Orders
{
    public int $id;
    public int $account_id;
    public string $order_date;
    public int $total_price;
    public string $status;
    public int $schedule_id;
}