<?php
namespace Nurdin\Djawara\Domain;

class Account
{
    public int $id;
    public string $username;
    public string $email;
    public string $password;
    public string $name;
    public string $phone;
    public string $role;
    public ?string $profile_pic;
    public ?string $address;
}