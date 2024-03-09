<?php
namespace Nurdin\Djawara\Repository;

use Nurdin\Djawara\Domain\Categories;
use PDO;
class CategoriesRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    } 

    public function  save(Categories $categories) : Categories
    {
        $stmt = $this->connection->prepare("INSERT INTO categories(name, price) VALUES (?, ?)");
        $stmt->execute([$categories->name, $categories->price]);
        
        return $categories;
    }

    public function findById(string $id) : ?Categories
    {
        $stmt = $this->connection->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);

        if($row = $stmt->fetch()){
            $categories = new Categories();
            $categories->id = $row['id'];
            $categories->name = $row['name'];
            $categories->price = $row['price'];

            return $categories;
        } else {
            return null;
        }
    }

    public function findAll() : ?array
    {
        $stmt = $this->connection->prepare("SELECT * FROM categories");
        $stmt->execute();

        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    public function update(Categories $categories) : Categories
    {
        $stmt = $this->connection->prepare("UPDATE categories SET name = ?, price = ? WHERE id = ?");
        $stmt->execute([$categories->name, $categories->price, $categories->id]);

        return $categories;
    }

    public function remove(string $id) 
    {
        $stmt = $this->connection->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
    }
}