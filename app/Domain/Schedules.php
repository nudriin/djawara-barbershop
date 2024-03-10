<?php
namespace Nurdin\Djawara\Domain;

class Schedules
{
    public int $id;
    public int $kapster_id;
    public int $category_id;
    public string $start_date;
    public string $end_date;
    public string $status;
    public string $phone;
    public string $role;
    public ?string $profile_pic;
    public ?string $address;
}