<?php
namespace Nurdin\Djawara\Domain;

class Orders
{
    public int $id;
    public int $account_id;
    public int $kapster_id;
    public string $order_date ;
    public string $password;
    public string $name;
    public string $phone;
    public string $role;
    public ?string $profile_pic;
    public ?string $address;
}