<?php
namespace Nurdin\Djawara\Model\Account;

class AccountUpdateProfileRequest
{
    public ?string $username = null;
    public ?string $name = null;
    public ?string $phone = null;
    public ?string $profile_pic = null;
    public ?string $address = null;
}
