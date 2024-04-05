<?php
namespace Nurdin\Djawara\Repository;

use PDO;

class OrdersRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    // public function save(Schedules $schedules): Schedules
    // {
    //     $stmt = $this->connection->prepare("INSERT INTO schedules(kapster_id, category_id, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)");
    //     $stmt->execute([$schedules->kapster_id, $schedules->category_id, $schedules->start_date, $schedules->end_date, $schedules->status]);

    //     return $schedules;
    // }
}