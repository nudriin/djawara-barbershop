<?php
namespace Nurdin\Djawara\Repository;

use Nurdin\Djawara\Domain\Account;
use PDO;

class AccountRepository
{
    private PDO $connection;

    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }

    public function save(Account $account) : Account
    {
        $stmt = $this->connection->prepare("INSERT INTO accounts(username, email, password, name, phone, role, address, profile_pic) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$account->username, $account->email, $account->password, $account->name, $account->phone, $account->role, $account->address, $account->profile_pic]);

        return $account;
    }

    public function findAccount(string $request, string $option) : ?Account
    {
        if(strtolower($option) === 'username'){
            $stmt = $this->connection->prepare("SELECT * FROM accounts WHERE username = ?");
        } else if (strtolower($option) === 'email'){
            $stmt = $this->connection->prepare("SELECT * FROM accounts WHERE email = ?");
        } else if (strtolower($option) === 'id') {
            $stmt = $this->connection->prepare("SELECT * FROM accounts WHERE id = ?");
        }
        $stmt->execute([$request]);

        if ($row = $stmt->fetch()){
            $account = new Account();
            $account->id = $row['id'];
            $account->username = $row['username'];
            $account->email = $row['email'];
            $account->password = $row['password'];
            $account->name = $row['name'];
            $account->phone = $row['phone'];
            $account->role = $row['role'];
            $account->address = $row['address'];
            $account->profile_pic = $row['profile_pic'];
            return $account;
        } else {
            return null;
        }
    }

    public function update(Account $account) : Account
    {
        $stmt = $this->connection->prepare("UPDATE accounts SET name = ?, password = ?, profile_pic = ?, address = ?, phone = ?  WHERE username = ?");
        $stmt->execute([$account->name, $account->password, $account->profile_pic, $account->address, $account->phone, $account->username]);

        return $account;
    }

    public function deleteAccount(string $request, string $option)
    {
        if(strtolower($option) === 'username'){
            $stmt = $this->connection->prepare("DELETE FROM accounts WHERE username = ?");
        } else if(strtolower($option) === 'id') {
            $stmt = $this->connection->prepare("DELETE FROM accounts WHERE id = ?");
        } else if (strtolower($option) === 'email'){
            $stmt = $this->connection->prepare("DELETE FROM accounts WHERE email = ?");
        }

        $stmt->execute([$request]);
    }
}