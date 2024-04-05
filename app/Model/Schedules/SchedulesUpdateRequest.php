<?php
namespace Nurdin\Djawara\Model\Schedules;

class SchedulesUpdateRequest
{
    public ?int $id = null;
    public ?int $kapster_id = null;
    public ?int $category_id = null;
    public ?string $dates = null;
    public ?string $times = null;
    public ?string $status = null;
}