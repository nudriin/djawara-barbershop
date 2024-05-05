<?php
namespace Nurdin\Djawara\Repository;

use Nurdin\Djawara\Domain\Kapsters;
use PDO;

class KapstersRepository 
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Kapsters $kapsters) : Kapsters
    {
        $stmt = $this->connection->prepare("INSERT INTO kapsters(name, phone, profile_pic) VALUES (?, ?, ?)");
        $stmt->execute([$kapsters->name, $kapsters->phone, $kapsters->profile_pic]);

        return $kapsters;
    }

    public function findById(string $id) : ?Kapsters
    {
        $stmt = $this->connection->prepare("SELECT * FROM kapsters WHERE id = ?");
        $stmt->execute([$id]);

        if($row = $stmt->fetch()){
            $kapsters = new Kapsters();
            $kapsters->id = $row['id'];
            $kapsters->name = $row['name'];
            $kapsters->phone = $row['phone'];
            $kapsters->profile_pic = $row['profile_pic'];

            return $kapsters;
        } else {
            return null;
        }
    }

    public function findAll() : ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM kapsters ORDER BY name ASC');
        $stmt->execute();

        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
        
    }


    public function update(Kapsters $kapsters) : Kapsters
    {
        $stmt = $this->connection->prepare("UPDATE kapsters SET name = ?, phone = ?, profile_pic = ? WHERE id = ?");
        $stmt->execute([$kapsters->name, $kapsters->phone, $kapsters->profile_pic, $kapsters->id]);

        return $kapsters;
    }

    public function remove(string $id) 
    {
        $stmt = $this->connection->prepare("CALL delete_kapster(?)");
        $stmt->execute([$id]);
    }
}