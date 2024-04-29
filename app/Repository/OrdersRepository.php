<?php
namespace Nurdin\Djawara\Repository;

use Nurdin\Djawara\Domain\Orders;
use PDO;

class OrdersRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Orders $orders): Orders
    {
        $stmt = $this->connection->prepare("INSERT INTO orders(account_id, total_price, schedule_id) VALUES (?, ?, ?)");
        $stmt->execute([$orders->account_id, $orders->total_price, $orders->schedule_id]);

        return $orders;
    }

    public function findById(string $id) : ?Orders
    {
        $stmt = $this->connection->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);

        if($row = $stmt->fetch()){
            $orders = new Orders();
            $orders->id = $row['id'];
            $orders->account_id = $row['account_id'];
            $orders->order_date = $row['order_date'];
            $orders->total_price = $row['total_price'];
            $orders->status = $row['status'];
            $orders->schedule_id = $row['schedule_id'];

            return $orders;
        } else {
            return null;
        }
    }

    public function findAll() : ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM getallorders ORDER BY order_date ASC');
        $stmt->execute();

        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
        
    }

    public function update(Orders $orders) : Orders
    {
        $stmt = $this->connection->prepare("UPDATE orders SET account_id = ?, total_price = ?, status = ?, schedule_id = ? WHERE id = ?");
        $stmt->execute([$orders->account_id, $orders->total_price, $orders->status, $orders->schedule_id, $orders->id]);

        return $orders;
    }

    public function remove(string $id) 
    {
        $stmt = $this->connection->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$id]);
    }
}