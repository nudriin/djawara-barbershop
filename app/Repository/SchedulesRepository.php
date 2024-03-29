<?php

namespace Nurdin\Djawara\Repository;

use Nurdin\Djawara\Domain\Schedules;
use PDO;

class SchedulesRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Schedules $schedules): Schedules
    {
        $stmt = $this->connection->prepare("INSERT INTO schedules(kapster_id, category_id, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$schedules->kapster_id, $schedules->category_id, $schedules->start_date, $schedules->end_date, $schedules->status]);

        return $schedules;
    }

    public function findById(string $id): ?Schedules
    {
        $stmt = $this->connection->prepare("SELECT * FROM schedules WHERE id = ?");
        $stmt->execute([$id]);

        if ($row = $stmt->fetch()) {
            $schedules = new Schedules();
            $schedules->id = $row['id'];
            $schedules->kapster_id = $row['kapster_id'];
            $schedules->category_id = $row['category_id'];
            $schedules->start_date = $row['start_date'];
            $schedules->end_date = $row['end_date'];
            $schedules->status = $row['status'];

            return $schedules;
        } else {
            return null;
        }
    }

    public function findAll() : ?array
    {
        $stmt = $this->connection->prepare("SELECT * FROM  schedules ORDER BY start_date DESC");
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    public function update(Schedules $schedules) : Schedules
    {
        $stmt = $this->connection->prepare("UPDATE schedules SET kapster_id = ?, category_id = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?");
        $stmt->execute([$schedules->kapster_id, $schedules->category_id, $schedules->start_date, $schedules->end_date, $schedules->status, $schedules->id]);

        return $schedules;
    }

    public function remove(string $id) 
    {
        $stmt = $this->connection->prepare("DELETE FROM schedules WHERE id = ?");
        $stmt->execute([$id]);
    }
}
