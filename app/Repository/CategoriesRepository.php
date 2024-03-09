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
}