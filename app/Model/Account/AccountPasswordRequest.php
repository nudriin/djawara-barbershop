<?php
namespace Nurdin\Djawara\Model\Account;

class AccountPasswordRequest
{
    public ?string $username = null;
    public ?string $oldPassword = null;
    public ?string $newPassword = null;

}