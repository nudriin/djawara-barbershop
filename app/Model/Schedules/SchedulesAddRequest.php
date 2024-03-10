<?php
namespace Nurdin\Djawara\Model\Schedules;

class SchedulesAddRequest
{
    public ?int $kapster_id = null;
    public ?int $category_id = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public ?string $status = null;
}