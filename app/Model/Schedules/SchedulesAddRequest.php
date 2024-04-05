<?php
namespace Nurdin\Djawara\Model\Schedules;

class SchedulesAddRequest
{
    public ?int $kapster_id = null;
    public ?int $category_id = null;
    public ?string $dates = null;
    public ?string $times = null;
    public ?string $status = null;
}