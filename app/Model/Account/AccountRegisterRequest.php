<?php
namespace Nurdin\Djawara\Model\Account;

class AccountRegisterRequest
{
    public ?string $username = null;
    public ?string $password = null;
    public ?string $email = null;
    public ?string $name = null;
    public ?string $phone = null;
    public ?string $address = null;
}